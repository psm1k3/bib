<?php
function correctURL($url) {
# 	if(substr($url,0,7) !== 'http://' && substr($url,0,8) !== 'https://')
    if(preg_match("^http:\/\/|https:\/\/",$url))
		return 'http://' . $url;
	return $url;
}

function isAbsolutePath($filename) {
	if($filename) {
		if($filename[0] == '/')
			return True;
		else return False;
	}
}

function correctFilename($url, $filename, $DEBUG=0) {
	$url = correctURL($url);
	$url_parts = parse_url($url);
	if(isset($url_parts['scheme'])) $url_scheme = $url_parts['scheme'];
	else $url_scheme = Null;
	if(isset($url_parts['host'])) $url_host = $url_parts['host'];
	else $url_host = Null;
	if(isset($url_parts['path'])) $url_path = $url_parts['path'];
	else $url_path = Null;
	if($DEBUG) printf("URL: %s\nURL-Path: %s\nURL-Host: %s\nFilename: %s\n\n",$url, $url_path, $url_host, $filename);
	switch(True) {
		case substr($filename,0,2) === '//' || (substr($filename,0,4) === 'http' || substr($filename,0,5) === 'https'):
			if($DEBUG) echo "Case 1\n";
			return $filename;
		case substr_count($url,'/') == 2:
			if($DEBUG) echo "Case 2\n";
			if(isAbsolutePath($filename)) {
				return $url . $filename;
			}
			else {
				return $url . '/' . $filename;
			}
		case substr_count($url,'/') == 3 && strlen($url_path) == 1:
			if($DEBUG) echo "Case 3\n";
			if(isAbsolutePath($filename))
				return substr($url,0,-1) . $filename;
			else
				return $url . $filename;
		case substr($url,-1) === '/' && !isAbsolutePath($filename):
			if($DEBUG) echo "Case 4\n";
			return $url . $filename;
		case substr_count($url_path,'/') == 1 && !isAbsolutePath($filename):
			if($DEBUG) echo "Case 5\n";
			return $url_scheme . "://" . $url_host . "/" . $filename;
		case isset($url_parts['path']) && strlen($url_parts['path']) > 1 && isAbsolutePath($filename):
			if($DEBUG) echo "Case 6\n";
			return "http://" . $url_parts['host'] . $filename;
		default:
			if($DEBUG) echo "Default\n";
			break;
	}
}
#$url = "https://cryptme.de/register.php";
#$filename = "css/style.css";
#print correctFilename($url,$filename,1);

/*

2 f00l.de /style.css
2 f00l.de style.css
3 f00l.de/ /style.css
4 f00l.de/blog/ style.css
5 https://cryptme.de/register.php css/style.css

*/

?>
