<?php
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
    header("Cache-Control: no-cache, must-revalidate");
	define('SERVERHOST','http://badc0.de/bib/' . basename(__FILE__));
	libxml_use_internal_errors(true);
	include('modules/functions.php');
	if(isset($_GET['url']) && !empty($_GET['url'])) {
		$url = $_GET['url'];
	}
	else {
		$url = "http://heise.de/";
	}
	$dom = new DOMDocument;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_ENCODING, "UTF-8");
	$html = curl_exec($ch);
    $html = utf8_decode($html);
	$url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

#		$httpstatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	$dom->loadHTML($html);
	$links = $dom->getElementsByTagName('link');
	foreach($links as $link) {
		$old_href = $link->getAttribute('href');
		$link->removeAttribute('href');
		$new_href = correctFilename($url,$old_href);
		$link->setAttribute('href',$new_href);
	}
	$as = $dom->getElementsByTagName('a');
	foreach($as as $a) {
		$old_href = $a->getAttribute('href');
		$a->removeAttribute('href');
		$new_href = SERVERHOST . '?url=' . correctFilename($url, $old_href);
		$a->setAttribute('href',$new_href);
	}
	$imgs = $dom->getElementsByTagName('img');
	foreach($imgs as $img) {
		$old_src = $img->getAttribute('src');
		$img->removeAttribute('src');
		$new_src = correctFilename($url, $old_src);
		$img->setAttribute('src', $new_src);
	}


	echo $dom->saveHTML();
    curl_close($ch);
?>
