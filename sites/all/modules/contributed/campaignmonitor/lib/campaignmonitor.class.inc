<?php

/**
 * @file
 * Implementation of the CampaignMonitor class, which is a wrapper class for
 * Campaign Monitor v3 API. It's implemented as a Singleton class and instances
 * are creted usign the account variables and the statuc function getConnector()
 * e.g.
 *
 * $account = variable_get('campaignmonitor_account', array());
 * $cm = CampaignMonitor::getConnector($account['api_key'], $account['client_id']);
 *
 * If the class encounters any error in the communication with the Campaign
 * Monitor servers, then the last error can be extracted with getLatestError()
 * function e.g.
 *
 * if (!$cm->unsubscribe($listId, $email)) {
 *   $error = $cm->getLatestError();
 *   form_set_error('', $error['message']);
 * }
 *
 * This class is written by Jesper Kristensen (cableman@linuxdev.dk).
 */

class CampaignMonitor {

  // Used to load the different library parts of the API.
  const CampaignMonitorCLIENT = 'csrest_clients.php';
  const CampaignMonitorLIST = 'csrest_lists.php';
  const CampaignMonitorSubscribers = 'csrest_subscribers.php';
  private $libraryPath = FALSE;


  protected $api_key;
  protected $client_id;


  private $logErrors = FALSE;
  private $errors = array();

  // These variables are used as static cache for the object.
  private $lists = array();
  private $listStats = array();
  private $campaigns = array();
  private $subscribers = array();

  // Holds the object instance (part of the singleton pattern).
  private static $instance;

  /**
   * Private class constructor, which prevents creation of this class directly,
   * use the static function CampaignMonitor::GetConnector().
   *
   * @param string $api_key
   * @param string $client_id
   * @param string $libraryPath optional
   */
  private function __construct($api_key = FALSE, $client_id = FALSE) {

    // Get account information.
    $account = variable_get('campaignmonitor_account', array());

    // Get api/client id if they are defined.
    $this->api_key = $api_key ? $api_key : (isset($account['api_key']) ? $account['api_key'] : FALSE);
    $this->client_id = $client_id ? $client_id : (isset($account['client_id']) ? $account['client_id'] : FALSE);

    // Trying to find the campaign monitor library.
    $this->getLibraryPath();

    // Enable logging.
    $setting = variable_get('campaignmonitor_general', array());
    if (isset($setting['logging'])) {
      $this->logErrors = $setting['logging'];
    }
  }

  /**
   * Add an error the the local stack and call watchdog, if logging is enabled.
   *
   * @param string $type drupal watchdog const. error type
   * @param string $message
   * @param int $code normally the http response code
   */
  private function addError($type, $message, $code = -1) {
    $this->errors[] = array(
      'type' => $type,
      'code' => $code,
      'message' => t($message),
    );
    if ($this->logErrors) {
      $msg = t('Failed with code: @code and message: @msg', array('@code' => $code, '@msg' => $message));
      watchdog('campaignmonitor', $msg, NULL, $type);
    }
  }

  /**
   * Tries to find the path of the Campaign Monitor API library path. If the
   * library is not found a error message is set and FALSE is returned.
   *
   * @return mixed string with library path or FALSE if library is not found
   */
  public function getLibraryPath() {
    if (!$this->libraryPath) {
      $path = FALSE;
      $setting = variable_get('campaignmonitor_general', array());

      // Uses library module to detect the library.
      if (module_exists('libraries')) {
        $libraries = libraries_get_libraries();
        if (isset($libraries['campaignmonitor'])) {
          $this->libraryPath = $libraries['campaignmonitor'];
        }
      }

      // If the library was not found, try use defined path
      if (!$this->libraryPath) {
        if (isset($setting['library_path']) && !empty($setting['library_path'])) {
          // User have defined the library path.
          if (file_exists($setting['library_path'])) {
            $this->libraryPath = $setting['library_path'];
          }
        }
      }
    }

    if (!$this->libraryPath) {
      drupal_set_message(t('The Campaign Monitor PHP integration library was not detected, please see the README for information about installing the library.'), 'error', FALSE);
    }

    return $this->libraryPath;
  }

  /**
   * Helper function that loads the part of the API defined in the $file
   * variable. This function is mostly used by create[xxx]Obj functions below.
   *
   * @param string $file the name of the API file to include
   * @return boolean TRUE of success else FALSE
   */
  private function libraryLoad($file) {
    if ($this->getLibraryPath()) {
      @require_once $this->libraryPath . '/' . $file;
      return TRUE;
    }
    $this->addError(WATCHDOG_ERROR, t('Unable to load client library.'));
    return FALSE;
  }

  /**
   * Create API client object.
   *
   * @return CS_REST_Clients campaign monitor client object or FALSE
   */
  private function createClientObj() {
    if ($this->libraryLoad(self::CampaignMonitorCLIENT)) {
      return new CS_REST_Clients($this->client_id, $this->api_key);
    }
    $this->addError(WATCHDOG_ERROR, t('Failed to locate the client library.'));
    return FALSE;
  }

  /**
   * Create API list object.
   *
   * @param string $listId campaign monitor list id
   * @return CS_REST_Lists campaign monitor list object or FALSE
   */
  private function createListObj($listId) {
    if ($this->libraryLoad(self::CampaignMonitorLIST)) {
      return new CS_REST_Lists($listId, $this->api_key);
    }
    $this->addError(WATCHDOG_ERROR, t('Failed to locate the list library.'));
    return FALSE;
  }

  /**
   * Create API subscribers object.
   *
   * @param type $listId campaign monitor list id
   * @return CS_REST_Subscribers campaign monitor subscriber object or FALSE
   */
  private function createSubscriberObj($listId) {
    if ($this->libraryLoad(self::CampaignMonitorSubscribers)) {
      return new CS_REST_Subscribers($listId, $this->api_key);
    }
    $this->addError(WATCHDOG_ERROR, t('Failed to locate the subscribe library.'));
    return FALSE;
  }

  /**
   * Create an unix timestamp based on the cache timeout set in the administration
   * interface.
   *
   * @return Unix timestamp
   */
  private function getCacheTimeout() {
    $options = variable_get('campaignmonitor_general', array());
    return time() + (isset($options['cache_timeout']) ? $options['cache_timeout'] : '360');
  }

  /**
   * Implements a singleton pattern that returns a instance of this object. The
   * function requires campaign monitor account keys to create the connection.
   * These keys can be found at the campaign monitor homepage and should be
   * entered in the administration interface and the object can be create like
   * below.
   *
   * $account = variable_get('campaignmonitor_account', array());
   * $cm = CampaignMonitor::getConnector($account['api_key'], $account['client_id']);
   *
   * @param string $api_key
   * @param string $client_key
   * @param string $libraryPath
   * @return object CampaingMonitor
   */
  public static function getConnector($api_key = FALSE, $client_key = FALSE) {
    if (($api_key && $client_key) || !isset(self::$instance)) {
      $class = __CLASS__;
      self::$instance = new $class($api_key, $client_key);
    }
    return self::$instance;
  }

  /**
   * Returns the lastest error from the stack of possible errors encountered
   * during communication with campaign monitor servers.
   *
   * @return array with an error code and message.
   */
  public function getLatestError() {
    if (!empty($this->errors)) {
      $last = $this->errors[count($this->errors) - 1];
      return array(
        'code' => $last['code'],
        'message' => $last['message'],
      );
    }
    else {
      return array(
        'code' => 1,
        'message' => t('There dos not seam to been any errors.')
      );
    }
  }

  /**
   * Returns the internal error array with the format below.
   *
   * $errors[] = array(
   *   'type' => [watchdog error type],
   *   'code' => [error code],
   *   'message' => [message],
   * );
   *
   * @return array of errors or FALSE if the array is empty
   */
  public function getErrors() {
    if (count($this->errors)) {
      return $this->errors;
    }
    return FALSE;
  }

  /**
   * Reset the internal error array to the empty array.
   */
  public function resetErrors() {
    $this->errors = array();
  }

  /**
   * Gets all list from campaign monitor found under the client id given during
   * object creation. The list is returned as a keyed array and cached in the
   * cache table, so it may not always return the newest information.
   *
   * The array has the format below, be aware that the same cache is used be the
   * functions getListDetails and getCustomFields. This means that the information
   * returned by this function may contain extend list information, if any of
   * these functions have been called.
   *
   * $list[$id] = array(
   *  'name' => 'List name',
   * );
   *
   * @return array of lists avaliable from campaign monitor or FALSE on failure
   */
  public function getLists() {
    if (empty($this->lists)) {
      if (($cache = cache_get('campaignmonitor_lists')) && !empty($cache->data)) {
        // Cache information found.
        $this->lists = $cache->data;
      }
      else {
        // Create list object and get the lists, the lists are saved in the local
        // cache.
        if ($obj = $this->createClientObj()) {
          $result = $obj->get_lists();
          if ($result->was_successful()) {
            foreach ($result->response as $list) {
              $this->lists[$list->ListID] = array(
                'name' => $list->Name,
              );
            }
            cache_set('campaignmonitor_lists', $this->lists, 'cache');
          }
          else {
            $this->addError(WATCHDOG_ERROR, $result->response->Message, $result->http_status_code);
          }
        }
        else {
          return FALSE;
        }
      }
    }

    return $this->lists;
  }

  /**
   * Gets list details from camapign monitor, the information is cached locally
   * and may be out-dated. It fetches the unsubscribe link, confirmation success
   * page and confirmed opt in options.
   *
   * @param string $listId campaign monitor list id
   * @return array with the information or FALSE on failure
   */
  public function getListDetails($listId) {
    // If lists have not been loaded yet, get them as they build the basic cache.
    if (empty($this->lists)) {
      $this->getLists();
    }

    // Test that the listId is valided.
    if (!isset($this->lists[$listId])) {
      $this->addError(WATCHDOG_ERROR, t('Unknown list id @listID.', array('@listID' => $listId)));
      return FALSE;
    }

    // If list details is not set, create list object and fetch the information
    // from campaign monitor servers.
    if (!isset($this->lists[$listId]['details'])) {
      if ($obj = $this->createListObj($listId)) {
        $result = $obj->get();
        if ($result->was_successful()) {
          // Convert the return objec into a keyed array.
          $this->lists[$listId]['details'] = array();
          foreach ($result->response as $key => $value) {
            if (!in_array($key, array('ListID', 'Title'))) {
              $this->lists[$listId]['details'][$key] = $value;
            }
          }

          // Update the cache with list details.
          cache_set('campaignmonitor_lists', $this->lists, 'cache');
        }
        else {
          $this->addError(WATCHDOG_ERROR, $result->response->Message, $result->http_status_code);
          return FALSE;
        }
      }
      else {
        return FALSE;
      }
    }

    return $this->lists[$listId]['details'];
  }

  /**
   * Fetch costum fields for a given list, then information is stored locally in
   * the list cache. The information is stored as an keyed array on the list
   * arrary under "CustomFields" key.
   *
   * @param string $listId
   * @return array with the information or FALSE on failure
   */
  public function getCustomFields($listId) {
    // If lists have not been loaded yet, get them as they build the basic cache.
    if (empty($this->lists)) {
      $this->getLists();
    }

    // Test that the listId is valided.
    if (!isset($this->lists[$listId])) {
      $this->addError(WATCHDOG_ERROR, t('Unknown list id @listID.', array('@listID' => $listId)));
      return FALSE;
    }

    // If custom field are not set on the list, then create list object and
    // fetch custom field into a keyed array.
    if (!isset($this->lists[$listId]['CustomFields'])) {
      if ($obj = $this->createListObj($listId)) {
        $result = $obj->get_custom_fields();
        if ($result->was_successful()) {
          $this->lists[$listId]['CustomFields'] = array();
          foreach ($result->response as $field) {
            foreach ($field as $name => $details) {
              $this->lists[$listId]['CustomFields'][$field->Key][$name] = $details;
            }
          }

          // Update cache with list details.
          cache_set('campaignmonitor_lists', $this->lists, 'cache');
        }
        else {
          $this->addError(WATCHDOG_ERROR, $result->response->Message, $result->http_status_code);
        }
      }
      else {
        return FALSE;
      }
    }

    return $this->lists[$listId]['CustomFields'];
  }

  /**
   * Get all information avaliable about a given list. This is done by calling
   * getLists(), getListDetails() and getCustomFields(), henc building the
   * local list cache.
   *
   * @param string $listId unique list id
   * @return array with list information or FALSE on failure
   */
  public function getExtendedList($listId) {
    // If lists have not been loaded yet, get them as they build the basic cache.
    if (empty($this->lists)) {
      $this->getLists();
    }

    // Test that the listId is valided.
    if (!isset($this->lists[$listId])) {
      $this->addError(WATCHDOG_ERROR, t('Unknown list id @listID.', array('@listID' => $listId)));
      return FALSE;
    }

    // Load list details and custom fields (it's is_array has it may return an
    // empty array).
    if (!$this->getListDetails($listId) || !is_array($this->getCustomFields($listId))) {
      $this->addError(WATCHDOG_ERROR, t('Could not retrive extended information for @listID.', array('@listID' => $listId)));
      return FALSE;
    }

    return $this->lists[$listId];
  }

  /**
   * Update remote list information. The options array should have the fields:
   * Title, UnsubscribePage, ConfirmedOptIn and ConfirmationSuccessPage. If you
   * do not wich to set these used the empty string.
   *
   * @param string $listId unique list id
   * @param array $options with information to update
   * @return boolean TRUE on success else FALSE
   */
  public function updateList($listId, $options = array()) {
    // Make sure that list is loaded.
    if (!$this->getListDetails($listId)) {
      $this->addError(WATCHDOG_ERROR, t('Could not retrive update list information for @listID.', array('@listID' => $listId)));
      return FALSE;
    }

    // Get list object and update the list.
    if ($obj = $this->createListObj($listId)) {
      // @todo: check that the options are correct.
      $result = $obj->update($options);
      if ($result->was_successful()) {

        // Update cache local list.
        $this->lists[$listId]['name'] = $options['Title'];
        $this->lists[$listId]['details']['UnsubscribePage'] = $options['UnsubscribePage'];
        $this->lists[$listId]['details']['ConfirmedOptIn'] = $options['ConfirmedOptIn'];
        $this->lists[$listId]['details']['ConfirmationSuccessPage'] = $options['ConfirmationSuccessPage'];

        // Update the cache.
        cache_set('campaignmonitor_lists', $this->lists, 'cache');
        return TRUE;
      }
      else {
        $this->addError(WATCHDOG_ERROR, $result->response->Message, $result->http_status_code);
      }
    }
    return FALSE;
  }

  /**
   * Fetch stats about a given list, which includes number of subscribers and
   * unsubscribers. The information is temporally store locally in the cache. The
   *  default timeout is 360 seconds.
   *
   * @param string $listId unique list id
   * @return array with stats or FALSE on failure
   */
  public function getListStats($listId) {
    $fetch = FALSE;
    if (!isset($this->listStats[$listId])) {
      // Not found inside objec, try cache.
      if (($cache = cache_get('campaignmonitor_list_stats')) && !empty($cache->data)) {
        // Cache information found.
        $this->listStats = $cache->data;
        if (!isset($this->listStats[$listId])) {
          // Not found inside cache either.
          $fetch = TRUE;
        }
      }
      else {
        // No cache found or expired.
        $fetch = TRUE;
      }
    }

    if ($fetch) {
      if ($obj = $this->createListObj($listId)) {
        // Get stats from camapign monitor.
        $result = $obj->get_stats();
        if ($result->was_successful()) {
          $this->listStats[$listId] = (array)$result->response;

          // Update the cache.
          cache_set('campaignmonitor_list_stats', $this->listStats, 'cache', $this->getCacheTimeout());
        }
        else {
          $this->addError(WATCHDOG_ERROR, $result->response->Message, $result->http_status_code);
          return FALSE;
        }
      }
      else {
        return FALSE;
      }
    }

    return $this->listStats[$listId];
  }

  /**
   * Delete a list from campaign monitor, this action can not be reverted. The
   * list is also removed from the local cache.
   *
   * @param type $listId unique list id
   * @return boolean TRUE on success else FALSE
   */
  public function deleteList($listId) {
    if ($obj = $this->createListObj($listId)) {
      $result = $obj->delete();
      if ($result->was_successful()) {
        unset($this->lists[$listId]);
        cache_set('campaignmonitor_lists', $this->lists, 'cache');
        return TRUE;
      }
      else {
        $this->addError(WATCHDOG_ERROR, $result->response->Message, $result->http_status_code);
        return FALSE;
      }
    }
    return FALSE;
  }

  /**
   * Create a new list at the campaign monitor servers. The side-effect is that
   * the local cache is cleared.
   *
   * @param string $title
   * @param string $unsubscribePage optional
   * @param boolean $confirmedOptIn optional
   * @param string $confirmationSuccessPage optional
   * @return boolean TRUE on success else FALSE
   */
  public function createList($title, $unsubscribePage = '', $confirmedOptIn = FALSE, $confirmationSuccessPage = '') {
    if ($obj = $this->createListObj(NULL)) {
      $result = $obj->create($this->client_id, array(
        'Title' => check_plain($title),
        'UnsubscribePage' => check_plain($unsubscribePage),
        'ConfirmedOptIn' => $confirmedOptIn,
        'ConfirmationSuccessPage' => check_plain($confirmationSuccessPage),
      ));
      if ($result->was_successful()) {
        // Clear the cache, so list informations can be retrived again.
        $this->clearCache();
        return TRUE;
      }
      else {
        $this->addError(WATCHDOG_ERROR, $result->response->Message, $result->http_status_code);
        return FALSE;
      }
    }
    return FALSE;
  }

  /**
   * Get basic information about campaigns in the form of a keyed array. The
   * information is store locally in a temporally cache. The array is formatted
   * as below.
   *
   * $campaigns[$id] => array(
   *   'Name' => 'Campaign Name',
   *   'Subject' => 'Campaign subject line',
   *   'Sent' => 'Unix timestamp',
   *   'Recipients' => 'The number of recipients',
   *   'Link' => 'Online URL to the campaign',
   * );
   *
   * @return array with the campaigns or FALSE on failure
   */
  public function getCampaigns() {
    if (empty($this->campaigns)) {
      if (($cache = cache_get('campaignmonitor_campaigns')) && !empty($cache->data)) {
        // Cache information found.
        $this->campaigns = $cache->data;
      }
      else {
        if ($obj = $this->createClientObj()) {
          $result = $obj->get_campaigns();
          if ($result->was_successful()) {
            // Build an array foreach campaign returned.
            foreach ($result->response as $campaign) {
              $this->campaigns[$campaign->CampaignID] = array(
                'Name' => $campaign->Name,
                'Subject' => $campaign->Subject,
                'Sent' => strtotime($campaign->SentDate),
                'Recipients' => $campaign->TotalRecipients,
                'Link' => $campaign->WebVersionURL,
              );
            }
            // Save campaigns in the cache.
            cache_set('campaignmonitor_campaigns', $this->campaigns, 'cache', $this->getCacheTimeout());
          }
          else {
            $this->addError(WATCHDOG_ERROR, $result->response->Message, $result->http_status_code);
            return FALSE;
          }
        }
        else {
          return FALSE;
        }
      }
    }
    return $this->campaigns;
  }

  /**
   * Get values entered by the subscriber, when she/he subscribed to a given
   * list.
   *
   * @param string $listId unique list id
   * @param string $email e-mail address to identify the subscriber
   * @return array with subscriber information or FALSE on failure
   */
  public function getSubscriber($listId, $email) {
    $fetch = FALSE;
    if (!isset($this->subscribers[$listId . $email])) {
      // Not found inside objec, try cache.
      if (($cache = cache_get('campaignmonitor_subscribers')) && !empty($cache->data)) {
        // Cache information found.
        $this->subscribers = $cache->data;
        if (!isset($this->subscribers[$listId . $email])) {
          // Not found inside cache either.
          $fetch = TRUE;
        }
      }
      else {
        // No cache found or expired.
        $fetch = TRUE;
      }
    }

    if ($fetch) {
      if ($obj = $this->createSubscriberObj($listId)) {
        $this->subscribers[$listId . $email] = array();
        $result = $obj->get($email);
        if ($result->was_successful()) {
          foreach ($result->response as $key => $value) {
            if ($key == 'CustomFields') {
              // Make the custom fields object into a keyed array.
              $this->subscribers[$listId . $email][$key] = array();
              foreach ($value as $field) {
                $this->subscribers[$listId . $email][$key][$field->Key] = $field->Value;
              }
            }
            else {
              $this->subscribers[$listId . $email][$key] = $value;
            }
          }
          // Save the subscriber information in the cache.
          cache_set('campaignmonitor_subscribers', $this->subscribers, 'cache', $this->getCacheTimeout());
        }
        else {
          $this->addError(WATCHDOG_ERROR, $result->response->Message, $result->http_status_code);
          return array();
        }
      }
      else {
        return FALSE;
      }
    }

    return $this->subscribers[$listId . $email];
  }

  /**
   * Check if a given user, identifyed by e-mail address, is subscribed to a
   * given list.
   *
   * @param string $listId unique list id
   * @param string $email
   * @return boolean TRUE if subscribed else FALSE
   */
  public function isSubscribed($listId, $email) {
    $result = $this->getSubscriber($listId, $email);
    if (!empty($result)) {
      if ($result['State'] == 'Active') {
        return TRUE;
      }
    }
    return FALSE;
  }

  /**
   * Remove subscriber form local cache, which force the data to be fetched form
   * campaign monitor at next request. This function should be used in connection
   * with updating subscriber information.
   *
   * @param type $listId
   * @param type $email
   */
  public function removeSubscriberFromCache($listId, $email) {
    if (($cache = cache_get('campaignmonitor_subscribers')) && !empty($cache->data)) {
      // Cache information found.
      $this->subscribers = $cache->data;
      if (isset($this->subscribers[$listId . $email])) {
        // Subscriber found in the cahce, so remove it.
        unset($this->subscribers[$listId . $email]);
        cache_set('campaignmonitor_subscribers', $this->subscribers, 'cache', $this->getCacheTimeout());
      }
    }
  }

  /**
   * Subscribe a user to a given list, with information entered. If the user is
   * already subscribed to the list, her/his informations are updated with the
   * new values.
   *
   * @param string $listId unique list id
   * @param string $email e-mail address that identifies the user
   * @param string $name optional
   * @param array $customFields optional
   * @return boolean TRUE on success else FALSE
   */
  public function subscribe($listId, $email, $name = '', $customFields = array()) {
    if ($obj = $this->createSubscriberObj($listId)) {
      $result = $obj->add(array(
          'EmailAddress' => $email,
          'Name' => $name,
          'CustomFields' => $customFields,
          'Resubscribe' => TRUE,
      ));
      if (!$result->was_successful()) {
        $this->addError(WATCHDOG_ERROR, $result->response->Message, $result->http_status_code);
        return FALSE;
      }
      $this->removeSubscriberFromCache($listId, $email);
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Unsubscribe a gien user, identified by e-mail address, from a given list.
   *
   * @param string $listId unique list id
   * @param string $email
   * @return boolean TRUE on success else FALSE
   */
  public function unsubscribe($listId, $email) {
    if ($obj = $this->createSubscriberObj($listId)) {
      $result = $obj->unsubscribe($email);
      if (!$result->was_successful()) {
        $this->addError(WATCHDOG_ERROR, $result->response->Message, $result->http_status_code);
        return FALSE;
      }
      $this->removeSubscriberFromCache($listId, $email);
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Updates the subscriber e-mail address for a given list.
   *
   * @param array $listIds
   *   List id.
   *
   * @param string $oldEmail
   *   The old e-mail address.
   *
   * @param string $email
   *   The new e-mail address.
   */
  public function updateSubscriberEmail($listId, $oldEmail, $email) {
    if ($obj = $this->createSubscriberObj($listId)) {
      $result = $obj->update($oldEmail, array(
        'EmailAddress' => $email,
        'Resubscribe' => TRUE,
      ));
      if (!$result->was_successful()) {
        $this->addError(WATCHDOG_ERROR, $result->response->Message, $result->http_status_code);
        return FALSE;
      }
      // Remove the old e-mail address form the subscriber cache.
      $this->removeSubscriberFromCache($listId, $oldEmail);
      return TRUE;
    }
  }

  /**
   * Clears all the caches used by this wrapper object.
   */
  public function clearCache() {
    cache_clear_all('campaignmonitor_lists', 'cache');
    cache_clear_all('campaignmonitor_list_stats', 'cache');
    cache_clear_all('campaignmonitor_campaigns', 'cache');
    cache_clear_all('campaignmonitor_subscribers', 'cache');
  }
}