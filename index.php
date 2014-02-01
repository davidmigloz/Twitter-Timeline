<!DOCTYPE HTML>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="author" content="www.davidmiguel.com"/>
	<title>Timeline Twitter</title>
	<link rel="shortcut icon" href="img/favicon.ico">
	<link rel="stylesheet" href="css/font-awesome.min.css">	
	<link rel="stylesheet" href="css/main.css">
</head>
<body>
	<header id="titulo">
		<h1>Timeline Twitter</h1>
	</header>
	<section id="timeline">
<?php
include('lib/twitter-timeline/timeline.php');
mostrarTweets('ABI2burgos', './lib/twitter-timeline/tweets.txt'); 
?>
	</section>
</body>
</html>

