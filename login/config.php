<?php
/**
 * HybridAuth
 * http://hybridauth.sourceforge.net | http://github.com/hybridauth/hybridauth
 * (c) 2009-2014, HybridAuth authors | http://hybridauth.sourceforge.net/licenses.html
 */

// ----------------------------------------------------------------------------------------
//	HybridAuth Config file: http://hybridauth.sourceforge.net/userguide/Configuration.html
// ----------------------------------------------------------------------------------------

return
array(
	"base_url" => "https://www.graffiti.buzz/login/login-callback.php",

	"providers" => array(
		// openid providers
		"OpenID" => array(
			"enabled" => false,
		),

		"Yahoo" => array(
			"enabled" => false,
			"keys" => array("key" => "", "secret" => ""),
		),

		"AOL" => array(
			"enabled" => false,
		),

		"Google" => array(
			"enabled" => true,
			"keys" => array("id" => "790454626694-3qsld9sicha5doo32jes9hegp8jgkcae.apps.googleusercontent.com", "secret" => "pDDcKCUfQmElhbTNylY6zFGl"),
		),

		"Facebook" => array(
			"enabled" => true,
			"keys" => array("id" => "441905576015354", "secret" => "69aad24cef2310e7c495643e6e221b1d"),
			"trustForwarded" => false,
			"scope" => "email, public_profile",
		),

		"Twitter" => array(
			"enabled" => true,
			"keys" => array("key" => "5J4abluqk3yQ2XoGvPweHItWW", "secret" => "RnNbEqhRfTe1esfS4PJN1yGu0KdBcQrhW6JBglo0EmLGHnxXd9"),
		),

		// windows live
		"Live" => array(
			"enabled" => false,
			"keys" => array("id" => "", "secret" => ""),
		),

		"LinkedIn" => array(
			"enabled" => false,
			"keys" => array("key" => "", "secret" => ""),
		),

		"Foursquare" => array(
			"enabled" => false,
			"keys" => array("id" => "", "secret" => ""),
		),
	),

	// If you want to enable logging, set 'debug_mode' to true.
	// You can also set it to
	// - "error" To log only error messages. Useful in production
	// - "info" To log info and error messages (ignore debug messages)
	"debug_mode" => false,

	// Path to file writable by the web server. Required if 'debug_mode' is not false
	"debug_file" => "",
);
