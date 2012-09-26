<?php

/**************************************************************
 * Functions ...
 * Get the logged in user public details from emailing nofb users
 * + extract post data from invite.php (fb uid and email+name)
 * + get the nofb user details from concat string
 * + Image thumbnail base64
 * + Prepare POST data from invite.php
 * + Data capture functions
/**************************************************************/


function getPublicUser(Facebook $facebook, $uid) {

    try {
        $public_user = $facebook->api('/' . $uid); // Assume I provide with a user id ;)
        
    } catch (FacebookApiException $e) {
        error_log($e);
    }

    return $public_user;
}


function extract_post_data($post) {

    $selected_friends = array();

    while (list ($k, $v) = each($post)) {
	
        // the choice of the delimiter came from webkit
        //browsers that natively replace dots with underscores in POST values
        $post_key = explode("%_%", $k);  

        if (!is_array($post_key)) { continue; }
        
        if ('friend' != $post_key[0]) { continue; }

        if (is_numeric($post_key[1])) {
            $facebook_uid = (int)$post_key[1]; // this is a facebook profile

        } else {
            $facebook_uid = base64_encode($post_key[1]); // this is a no facebook person
        }
        
        array_push( $selected_friends, $facebook_uid );
    }
    
    //if (count($selected_friends) < 5 || count($selected_friends) > 9) {
    if (empty ($selected_friends)) {
        return false;
    }

    return $selected_friends;
}


function extract_nofb_post_data($string) {
    
    $split = explode('+', $string);
    $name = str_replace('_', ' ', $split[1]);
    $email = str_replace('_', '.', $split[0]);
    
    return is_array($split) ? array( $email, $name ) : false;
}


function resizeThumbnail($gdResource) {
    
	$isLandscape = Image::isLandscape(
                                        Image::getX($gdResource),
                                        Image::getY($gdResource));
	
	$ratio = Image::getAspectRatio($gdResource);
			
	$newX = Image::getNewProportionateX($ratio, 89);
	$newY = Image::getNewProportionateY($ratio,89);

	$dstOffsetX = 0;
	$dstOffsetY = 0;
        
        if (!$isLandscape) {
            $dstX = $newX;
            $dstY = 89;
            
        } else {
            $dstX = 89;
            $dstY = $newY;
            
            $dstOffsetY = ((89 - Image::getY($gdResource)) / 2) + 4;
        }
	
	$canvas = imagecreatetruecolor($dstX, 89);

	imagecopyresampled($canvas,
                           $gdResource,
                           $dstOffsetX,
                           $dstOffsetY,
                           0,
                           0,
                           $dstX,
                           $dstY,
                           Image::getX($gdResource),
                           Image::getY($gdResource));
	
	return $canvas; // GD
}


function insert_competition_entry(array $data, $uid) {
    
    $name = mysql_real_escape_string( $data['entrant_name'] );
    $email = mysql_real_escape_string( $data['entrant_email'] );
    $phone = mysql_real_escape_string( $data['entrant_telephone'] );
    $venue = mysql_real_escape_string( $data['venue'] );
    $opt_in = mysql_real_escape_string( $data['opt_in'] );
    
    $time = time();
    
    $query = "INSERT INTO competition_entries (id, uid, name, email, phone, venue, opt_in, date) VALUES (";
    $query .= "'', ";
    $query .= "'$uid' ,";
    $query .= "'$name', ";
    $query .= "'$email', ";
    $query .= "'$phone', ";
    $query .= "'$venue', ";
    $query .= "'$opt_in', ";
    $query .= "'$time');";

    $resource = mysql_query($query);
    mysql_free_result($resource);
    
    return $resource;    
}


function has_already_entered($uid) {
    
    $query = "SELECT * FROM competition_entries WHERE uid = '" . $uid . "'";
    $i = 0;
    $resource = mysql_query($query);
    while ($data = mysql_fetch_assoc($resource)) {
        $i++;
    }
    mysql_free_result($resource);

    return 0 == $i ? false : true;   
}

function getEntries() {
     
    $results = array();
    
    $query = "SELECT * FROM competition_entries";
    $resource = mysql_query($query);
    while ($data = mysql_fetch_assoc($resource)) {

        unset ($data['uid']); // we don't need facebook user id
        $data['date'] = !is_null($data['date']) ? date('d/m/Y', $data['date']) : null; // convert to a human date
        $results[] = array_values($data); // array_values because fputcsv() accept only scallar arrays
    }
    mysql_free_result($resource);
    
    return $results; 
}

function build_csv_entry($data, $key, $handler, $delimiter = ',', $enclosure = '"', $eol = "\n") {
		
    $line = fputcsv($handler, $data, $delimiter, $enclosure);
    if ($line === false) {
            return false;
    }

    return $line; // int number of char written
}