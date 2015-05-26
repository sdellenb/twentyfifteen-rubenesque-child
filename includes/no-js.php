<?php
	function gaParseCookie() {
		if (isset($_COOKIE['_ga'])) {
			list($version,$domainDepth, $cid1, $cid2) = explode('.', $_COOKIE["_ga"], 4);
			$contents = array('version' => $version, 'domainDepth' => $domainDepth, 'cid' => $cid1 . '.' . $cid2);
			$cid = $contents['cid'];
		} else {
			$cid = gaGenerateUUID();
		}
		return $cid;
	}
	
	function gaGenerateUUID() {
		return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			mt_rand(0, 0xffff), mt_rand(0, 0xffff), //32 bits for "time_low"
			mt_rand(0, 0xffff), //16 bits for "time_mid"
			mt_rand(0, 0x0fff) | 0x4000, //16 bits for "time_hi_and_version", Four most significant bits holds version number 4
			mt_rand(0, 0x3fff) | 0x8000, //16 bits, 8 bits for "clk_seq_hi_res", 8 bits for "clk_seq_low", Two most significant bits holds zero and one for variant DCE1.1
			mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff) //48 bits for "node"
		);
	}
	
	function gaSendData($data) {
		$getString = 'https://ssl.google-analytics.com/collect';
		$getString .= '?payload_data&';
		$getString .= http_build_query($data);
		$result = wp_remote_get($getString);
		return $result;
	}
	
	//First, log the pageview
	$data = array(
		'v' => 1,
		'tid' => 'UA-63124546-1', //Add your own Google Analytics account number here!
		'cid' => gaParseCookie(),
		't' => 'pageview',
		'dh' => $_GET['h'], //Document Hostname
		'dp' => $_GET['p'], //Page
		'dt' => $_GET['t'] //Title
	);
	gaSendData($data);
	
	//Send the "JavaScript Disabled" event
	$data = array(
		'v' => 1,
		'tid' => 'UA-63124546-1', //Add your own Google Analytics account number here!
		'cid' => gaParseCookie(),
		't' => 'event',
		'ec' => 'JavaScript Disabled', //Category
		'ea' => $_GET['t'], //Action
		//'el' => 'label' //Label
	);
	gaSendData($data);
	
?>