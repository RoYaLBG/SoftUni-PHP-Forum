<?php
session_start();
#################################################
#												#
#				ANSR Framework					#
#	@author Ivan Yonkov <ivanynkv@gmail.com>	#	
#												#
#	A very basic MVC framework which has		#
#	default router for routing schema			#
#	/controller/action/. It has two basic		#
#	wrappers for database (mysqli) -> object	#
#	oriented one, and procedural one.			#
#												#
#	If one needs additional configs, wrappers	#
#	or libraries, can follow the namespace		#
#	schema and level of abstraction which can	#
#	be found in each abstract class.			#																											#
#												#
#	The framework uses PHP 5.5.					#
#												#
#	Some features might not work on				#
#	lower versions								#											
#												#																						#
#################################################
define('ROOT', __DIR__ . DIRECTORY_SEPARATOR);
define('HOST', '//'.$_SERVER['HTTP_HOST'] . str_replace(basename($_SERVER['PHP_SELF']), '', $_SERVER['PHP_SELF']));
include 'Autoload/DefaultLoader.php';
Autoload\DefaultLoader::registerAutoload();

\ANSR\View::addStyle('main.css');

\ANSR\View::setHeader('header.php');

\ANSR\View::setFooter('footer.php');


\ANSR\Library\DependencyContainer\AppStarter::createApp('MySQLi_Procedural', 'DefaultRouter', 'development');

