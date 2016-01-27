<?php
// config and whatnot
require_once dirname(__DIR__) . '/vendor/autoload.php';
$config = 'config.php';

try {
	$hybridauth = new Hybrid_Auth($config);

	// logout the user from $provider
	$hybridauth->logoutAllProviders();

	// return to login page
	$hybridauth->redirect("login.php");
} catch (Exception $e) {
	echo "<br /><br /><b>Oh well, we got an error :</b> " . $e->getMessage();

	echo "<hr /><h3>Trace</h3> <pre>" . $e->getTraceAsString() . "</pre>";
}
