<?php
	require_once('form.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>Edit</title>
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
	<h1><a href="#"><?php $form->titulo(FALSE); ?></a></h1>
 
	<nav><ul><?php $form->navegacion(); ?></ul></nav>
</header><!-- /#banner -->

<?php $form->contenido(); ?>

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

<?php $form->close(); ?>
</body>
</html>