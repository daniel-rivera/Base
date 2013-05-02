<?php
	require_once('web.php');	
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title><?php $web->titulo(TRUE); ?></title>
 
<link rel="stylesheet" href="style.css" type="text/css" />
 
<!--[if IE]>
	<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
<!--[if lte IE 7]>
	<script src="js/IE8.js" type="text/javascript"></script><![endif]-->
<!--[if lt IE 7]>
	<link rel="stylesheet" type="text/css" media="all" href="ie6.css"/><![endif]-->
</head>
 
<body id="index" class="home">

<header id="banner" class="body">
	<h1><a href="#"><?php $web->titulo(FALSE); ?></a></h1>
 
	<nav><ul><?php $web->navegacion(); ?></ul></nav>
</header><!-- /#banner -->

<?php $web->featured(); ?>

<?php $web->contenido(); ?>

<?php $web->articulos(); ?>

<section id="extras" class="body">
	<div class="blogroll">
		<h2>blogroll</h2>
		<ul>
 
			<li><a href="#" rel="external">HTML5 Doctor</a></li>
			<li><a href="#" rel="external">HTML5 Spec (working draft)</a></li>
			<li><a href="/t/grethel/" rel="external">Grethel's Project</a></li>
			
			<li><a href="#" rel="external">Smashing Magazine</a></li>
			<li><a href="#" rel="external">W3C</a></li>
			<li><a href="/t/jelliot/" rel="external">Elliot's Project</a></li>
			
			<li><a href="#" rel="external">Wordpress</a></li>
			<li><a href="#" rel="external">Wikipedia</a></li>
			<li><a href="/t/jesus/" rel="external">Jesus's Project</a></li>
			
			<li><a href="#" rel="external">HTML5 Doctor</a></li>
			<li><a href="#" rel="external">HTML5 Spec (working draft)</a></li>
			<li><a href="/t/edgard/" rel="external">Edgard's Project</a></li>
			
		</ul>
	</div><!-- /.blogroll -->
 
	<div class="social">
		<h2>social</h2>
		<ul>
 
			<li><a href="http://delicious.com/" rel="me">delicious</a></li>
			<li><a href="http://facebook.com/" rel="me">facebook</a></li>
			<li><a href="http://respuesta42.mx/feed/" rel="alternate">rss</a></li>
			<li><a href="http://twitter.com/" rel="me">twitter</a></li>
 
		</ul>
	</div><!-- /.social -->
</section><!-- /#extras -->

<footer id="contentinfo" class="body">
	<address id="about" class="vcard body">
		<span class="primary">
			<strong><a href="#" class="fn url">Práctica Final</a></strong>
 
			<span class="role">Sistemas de Información para Internet</span>
		</span><!-- /.primary -->
		
		<img src="anahuac.gif" alt="Anahuac Logo" class="photo" />
		<span class="bio">Aplicará las bases de la arquitectura de la información mediante la cual se estructuran los contenidos de un sitio web. Diseñará sistemas de navegación capaces de despertar el interés de explorar el sitio Web.</span>
 
	</address><!-- /#about -->
	<p>April 2013 <a href="http://academy.uax.edu.mx/moodle2/course/view.php?id=3">Moodle 2</a>.</p>
</footer><!-- /#contentinfo -->

<?php $web->close(); ?>

<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-40367910-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

</body>
</html>