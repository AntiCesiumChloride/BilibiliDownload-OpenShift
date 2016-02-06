<?php
/**
 * @author https://ZE3kr.com
**/

require_once( __DIR__.'/include.php' );

if( isset($_GET['page']) && $_GET['page'] > 0 ){
	$pid = $_GET['page'];
} else {
	$pid = 1;
}

$return = GetUrl(substr($_SERVER['REQUEST_URI'],10,-4),$pid);

if( $return['success'] ) {
	header('HTTP/1.1 302 Moved Temporarily');
	header('Location: '.$return['url']);
} elseif($return['code'] == 3) {
	header('HTTP/1.1 404 Not Found');
}
?>