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

$type = substr($_SERVER['PHP_SELF'],-3);
$return = GetUrl(substr($_SERVER['PHP_SELF'],11,-4),$pid,$type);

if( $return['success'] ) {
	header('HTTP/1.1 200 OK');
	echo <<<HTML
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
	<title>Video</title>
	<style>
	* { margin: 0; }
	video,.video-js { width: 100vw !important; height: 100vh !important; position: absolute !important; top:0; left: 0; }
	</style>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no"/>
	<link href="//cdnjs-com.b0.upaiyun.com/ajax/libs/video.js/5.7.0/video-js.min.css" rel="stylesheet">
	<!--[if lte IE 8]><script type="text/javascript" charset="utf-8" src="//cdnjs-com.b0.upaiyun.com/ajax/libs/video.js/5.7.0/ie8/videojs-ie8.min.js" async="async"></script><![endif]-->
	<script type="text/javascript" charset="utf-8" src="//cdnjs-com.b0.upaiyun.com/ajax/libs/video.js/5.7.0/video.min.js" async="async"></script>
</head>
<body>
	<video id="video" class="video-js vjs-big-play-centered" controls preload="auto" data-setup="{}" poster="{$return['pic']}"><source src="{$return['url']}" type="video/{$type}"></video>

</body>
</html>

HTML;
} elseif($return['code'] == 3) {
	header('HTTP/1.1 404 Not Found');
	echo <<<HTML
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
	<title>Video</title>
	<style>
	* { margin: 0; }
	</style>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no"/>
	<link href="//cdnjs-com.b0.upaiyun.com/ajax/libs/video.js/5.7.0/video-js.min.css" rel="stylesheet">
	<!--[if lte IE 8]><script type="text/javascript" charset="utf-8" src="//cdnjs-com.b0.upaiyun.com/ajax/libs/video.js/5.7.0/ie8/videojs-ie8.min.js" async="async"></script><![endif]-->
	<script type="text/javascript" charset="utf-8" src="//cdnjs-com.b0.upaiyun.com/ajax/libs/video.js/5.7.0/video.min.js" async="async"></script>
</head>
<body>
	<h1 style="margin: 1em;z-index: 1;">Sorry, video not find.</h1>

</body>
</html>

HTML;
}
?>