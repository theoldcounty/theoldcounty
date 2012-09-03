<?php
/**
 * @file
 * Zen theme's implementation to display the basic html structure of a single
 * Drupal page.
 *
 * Variables:
 * - $css: An array of CSS files for the current page.
 * - $language: (object) The language the site is being displayed in.
 *   $language->language contains its textual representation. $language->dir
 *   contains the language direction. It will either be 'ltr' or 'rtl'.
 * - $html_attributes: String of attributes for the html element. It can be
 *   manipulated through the variable $html_attributes_array from preprocess
 *   functions.
 * - $html_attributes_array: Array of html attribute values. It is flattened
 *   into a string within the variable $html_attributes.
 * - $rdf_namespaces: All the RDF namespace prefixes used in the HTML document.
 * - $grddl_profile: A GRDDL profile allowing agents to extract the RDF data.
 * - $head_title: A modified version of the page title, for use in the TITLE
 *   tag.
 * - $head_title_array: (array) An associative array containing the string parts
 *   that were used to generate the $head_title variable, already prepared to be
 *   output as TITLE tag. The key/value pairs may contain one or more of the
 *   following, depending on conditions:
 *   - title: The title of the current page, if any.
 *   - name: The name of the site.
 *   - slogan: The slogan of the site, if any, and if there is no title.
 * - $head: Markup for the HEAD section (including meta tags, keyword tags, and
 *   so on).
 * - $default_mobile_metatags: TRUE if default mobile metatags for responsive
 *   design should be displayed.
 * - $styles: Style tags necessary to import all CSS files for the page.
 * - $scripts: Script tags necessary to load the JavaScript files and settings
 *   for the page.
 * - $skip_link_anchor: The HTML ID of the element that the "skip link" should
 *   link to. Defaults to "main-menu".
 * - $skip_link_text: The text for the "skip link". Defaults to "Jump to
 *   Navigation".
 * - $page_top: Initial markup from any modules that have altered the
 *   page. This variable should always be output first, before all other dynamic
 *   content.
 * - $page: The rendered page content.
 * - $page_bottom: Final closing markup from any modules that have altered the
 *   page. This variable should always be output last, after all other dynamic
 *   content.
 * - $classes: String of classes that can be used to style contextually through
 *   CSS. It should be placed within the <body> tag. When selecting through CSS
 *   it's recommended that you use the body tag, e.g., "body.front". It can be
 *   manipulated through the variable $classes_array from preprocess functions.
 *   The default values can contain one or more of the following:
 *   - front: Page is the home page.
 *   - not-front: Page is not the home page.
 *   - logged-in: The current viewer is logged in.
 *   - not-logged-in: The current viewer is not logged in.
 *   - node-type-[node type]: When viewing a single node, the type of that node.
 *     For example, if the node is a Blog entry, this would be "node-type-blog".
 *     Note that the machine name of the content type will often be in a short
 *     form of the human readable label.
 *   The following only apply with the default sidebar_first and sidebar_second
 *   block regions:
 *     - two-sidebars: When both sidebars have content.
 *     - no-sidebars: When no sidebar content exists.
 *     - one-sidebar and sidebar-first or sidebar-second: A combination of the
 *       two classes when only one of the two sidebars have content.
 *
 * @see template_preprocess()
 * @see template_preprocess_html()
 * @see zen_preprocess_html()
 * @see template_process()
 */

class Utils{
	function get_tag_name($tid) {
		global $base_url;
		$query = db_select('taxonomy_term_data', 't');
		$query
				->condition('t.tid', $tid, '=')
				->fields('t', array('tid', 'name'));
		$result = $query->execute();

		$taxonomy_path_alias = "";

		$term = taxonomy_term_load($tid);
		$termpath = taxonomy_term_uri($term);
		$taxonomy_path_alias = drupal_lookup_path('alias', $termpath['path']);


		//print_r($taxonomy_path_alias);

		foreach ($result as $row) {
			return '<a href="'.$base_url.'/'.$taxonomy_path_alias.'">'.$row->name.'</a>';
		}
	}
}

?><!DOCTYPE html>
<!--[if IEMobile 7]><html class="iem7" <?php print $html_attributes; ?>><![endif]-->
<!--[if lte IE 6]><html class="lt-ie9 lt-ie8 lt-ie7" <?php print $html_attributes; ?>><![endif]-->
<!--[if (IE 7)&(!IEMobile)]><html class="lt-ie9 lt-ie8" <?php print $html_attributes; ?>><![endif]-->
<!--[if IE 8]><html class="lt-ie9" <?php print $html_attributes; ?>><![endif]-->
<!--[if (gte IE 9)|(gt IEMobile 7)]><!--><html <?php print $html_attributes . $rdf_namespaces; ?>><!--<![endif]-->

<head profile="<?php print $grddl_profile; ?>">
  <?php print $head; ?>
  <title><?php print $head_title; ?></title>

  <?php if ($default_mobile_metatags): ?>
    <meta name="MobileOptimized" content="width">
    <meta name="HandheldFriendly" content="true">
    <meta name="viewport" content="width=device-width">
  <?php endif; ?>
  <meta http-equiv="cleartype" content="on">

  <?php print $styles; ?>
  <?php print $scripts; ?>
  <?php if ($add_respond_js): ?>
    <!--[if lt IE 9]>
    <script src="<?php print $base_path . $path_to_zen; ?>/js/html5-respond.js"></script>
    <![endif]-->
  <?php elseif ($add_html5_shim): ?>
    <!--[if lt IE 9]>
    <script src="<?php print $base_path . $path_to_zen; ?>/js/html5.js"></script>
    <![endif]-->
  <?php endif; ?>
</head>
<body class="<?php print $classes; ?>" <?php print $attributes;?>>
	<div id="fb-root"></div>

	<script>(function(d, s, id) {
	  var js, fjs = d.getElementsByTagName(s)[0];
	  if (d.getElementById(id)) return;
	  js = d.createElement(s); js.id = id;
	  js.src = "//connect.facebook.net/en_GB/all.js#xfbml=1&appId=383449641705024";
	  fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));</script>

  <div id="godPane">
		<div class="wrapper">
			<div class="facebook">
				<div class="godtitle">
					<div class="wraps">
						<h2>Follow us on Facebook</h2>
						Join our facebook fan page!
					</div>
				</div>
				<div class="fbwrapper">
					<div class="fb-like-box" data-href="http://www.facebook.com/theoldcounty" data-width="730" data-show-faces="true" data-stream="false" data-header="false"></div>
				</div>
			</div>
			<div class="newsletter">
				<div class="nwwrapper">
					<h2>Newsletter</h2>
					Type your mail and press enter, we promise you to send only interesting content!

					<?php
						$lists = CampaignMonitor::getConnector()->getLists();
						if ($lists) {
							$listarray = array_keys($lists);
							$delta = $listarray['0'];
							$block['content'] = drupal_get_form('campaignmonitor_subscribe_form', $delta);
							print render($block['content']);
						}
					?>
					<button><span>S</span>ubscribe</button>

				</div>
			</div>
			<div class="twitter">
				<div class="godtitle">
					<div class="wraps">
						<h2>Follow us on Twitter</h2>
						Join our twitter fan page!
					</div>
				</div>
				<div class="twwrapper">
					<h2>The Old County</h2>
					<?php
						//$block = module_invoke('aggregator', 'block_view', 'feed-1');
						//$contentsblock = $block['content'];

						//print $contentsblock;
					?>

						<script charset="utf-8" src="http://widgets.twimg.com/j/2/widget.js"></script>
						<script>
						new TWTR.Widget({
						  version: 2,
						  type: 'profile',
						  rpp: 5,
						  interval: 5000,
						  width: 'auto',
						  height: 300,
						  theme: {
							shell: {
							  background: 'transparent',
							  color: '#000000'
							},
							tweets: {
							  background: 'transparent',
							  color: '#000000',
							  links: 'gold'
							}
						  },
						  features: {
							scrollbar: false,
							loop: false,
							live: false,
							behavior: 'default'
						  }
						}).render().setUser('theoldcounty').start();
						</script>

				</div>
			</div>
		</div>
  </div>
  <?php if ($skip_link_text && $skip_link_anchor): ?>
    <p id="skip-link">
      <a href="#<?php print $skip_link_anchor; ?>" class="element-invisible element-focusable"><?php print $skip_link_text; ?></a>
    </p>
  <?php endif; ?>
  <?php print $page_top; ?>
  <?php print $page; ?>
  <?php print $page_bottom; ?>
</body>
</html>
