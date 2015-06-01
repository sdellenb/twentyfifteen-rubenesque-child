<?php
/* Use WordPress functions within this file. Without it, it will crash on line 31. */
define('WP_USE_THEMES', false);
require_once('/srv/www/sites/rubenesque.ch/wp-blog-header.php');

// Based on code from https://gist.github.com/chrisblakley/e1f3d79b6cecb463dd8a.

// Google Analytics Tracking ID.
$ga_tracking_id = 'UA-63124546-1';

//Parse the GA Cookie
function gaParseCookie() {
	if (isset($_COOKIE['_ga'])) {
		list($version, $domainDepth, $cid1, $cid2) = explode('.', $_COOKIE["_ga"], 4);
		$contents = array('version' => $version, 'domainDepth' => $domainDepth, 'cid' => $cid1 . '.' . $cid2);
		$cid = $contents['cid'];
	} else {
		$cid = gaGenerateUUID();
	}
	return $cid;
}

//Generate UUID
//Special thanks to stumiller.me for this formula.
function gaGenerateUUID() {
	return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
		mt_rand(0, 0xffff), mt_rand(0, 0xffff),
		mt_rand(0, 0xffff),
		mt_rand(0, 0x0fff) | 0x4000,
		mt_rand(0, 0x3fff) | 0x8000,
		mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
	);
}

//Send Data to Google Analytics
//For parameters, see https://developers.google.com/analytics/devguides/collection/protocol/v1/parameters
function gaSendData($data) {
	$getString = 'https://ssl.google-analytics.com/collect';
	$getString .= '?payload_data&';
	$getString .= http_build_query($data);
	$result = wp_remote_get($getString);
	return $result;
}

//Send Pageview Function for Server-Side Google Analytics
//https://developers.google.com/analytics/devguides/collection/protocol/v1/devguide#page
function ga_send_pageview($hostname=null, $page=null, $title=null) {
	$data = array(
		'v' => 1,
		'tid' => $ga_tracking_id,
		'cid' => gaParseCookie(),
		't' => 'pageview',
		'dh' => $hostname, //Document Hostname "gearside.com"
		'dp' => $page, //Page "/something"
		'dt' => $title //Title
	);
	gaSendData($data);
}

//Send Event Function for Server-Side Google Analytics
//https://developers.google.com/analytics/devguides/collection/protocol/v1/devguide#event
function ga_send_event($category=null, $action=null, $label=null) {
	$data = array(
		'v' => 1,
		'tid' => $ga_tracking_id,
		'cid' => gaParseCookie(),
		't' => 'event',
		'ec' => $category, //Category (Required)
		'ea' => $action, //Action (Required)
		'el' => $label //Label
	);
	gaSendData($data);
}

if (isset($_GET['h']) && isset($_GET['p']) && isset($_GET['t'])) {
	if ($_GET['h'] == 'www.rubenesque.ch') {
		ga_send_pageview($_GET['h'], $_GET['p'], $_GET['t']);
		ga_send_event('JavaScript Disabled', $_GET['t'], $_GET['t']);
		// Prevent a 404, set the appropriate content header for: 'nothing to see here'.
		header("HTTP/1.0 204 No Content");
	} else {
		// Block all analytics spam trying to set a different host.
		// as seen with "to use this feature visit: EVENT-TRACKING.COM"
		// For more information, see
		// https://www.topdraw.com/blog/spammers-now-targeting-google-analytics-events/
		// and http://www.g1440.com/2015/05/google-analytics-event-tracking-spam/
		// Note: Still won't help if they guessed the tracking ID and are spamming from somewhere else..
		header("HTTP/1.0 403 Forbidden");
	}
} else {
	// Missing parameter, bad request.
	header("HTTP/1.0 400 Bad Request");
}
