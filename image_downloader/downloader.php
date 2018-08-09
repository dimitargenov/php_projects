<?php

include "config.php";

function loginAndDownload($total, $baseUrl, $albumName, $fromId, $credentials) {
	$user = $credentials['user'];
	$password = $credentials['pass'];

	$dir = "/home/dimitar/";
	$prefix = "xtr";
	//$path = build_unique_path($dir);
	$path = tempnam($dir, $prefix);
	$referer = "http://www.spt-photo.org/";

	//login
	$loginUrl="http://www.spt-photo.org/phzone2/login.php"; 
	$postinfo = "user=".$user."&pass=".$password;

	$cookie_file_path = $path."/cookie.txt";

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_NOBODY, false);
	curl_setopt($ch, CURLOPT_URL, $loginUrl);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file_path);
	//set the cookie the site has for certain features, this is optional
	curl_setopt($ch, CURLOPT_COOKIE, "cookiename=0");
	curl_setopt($ch, CURLOPT_USERAGENT,
	    "Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.7.12) Gecko/20050915 Firefox/1.0.7");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_REFERER, $referer);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);

	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postinfo);
	$answer = curl_exec($ch);
	if (curl_error($ch)) {
		echo curl_error($ch);
	}

	for($i = $fromId;$i <= ($total + $fromId);$i++) {
		downloadImages($baseUrl, $i, $albumName, $ch);
	}
	$info = curl_getinfo($ch);
	curl_close($ch);
}

function downloadImages($baseUrl, $id, $albumName, $ch) {
	curl_setopt($ch, CURLOPT_URL, $baseUrl . $id);
	curl_setopt($ch, CURLOPT_POST, false);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "");
	$fp = fopen('albums/' . $albumName . '/image' . $id . '.jpg', 'wb');
	curl_setopt($ch, CURLOPT_FILE, $fp);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	$output = curl_exec($ch);
	$info = curl_getinfo($ch);
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	fclose($fp);	
}

$baseUrl = 'http://www.spt-photo.org/phzone2/img3.php?tip=download&stp=1228&phot=';
$total = 10;
$fromId = 1;
$albumName = 'ExtremeCup';

loginAndDownload($total, $baseUrl, $albumName, $fromId, $credentials);


