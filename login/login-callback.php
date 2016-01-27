<?php
/**
 * HybridAuth
 * http://hybridauth.sourceforge.net | http://github.com/hybridauth/hybridauth
 * (c) 2009-2014, HybridAuth authors | http://hybridauth.sourceforge.net/licenses.html
 */

// ------------------------------------------------------------------------
//	HybridAuth End Point
// ------------------------------------------------------------------------

require_once dirname(__DIR__) . '/vendor/autoload.php';

if (isset($_REQUEST['hauth_start']) || isset($_REQUEST['hauth_done'])) {
	Hybrid_Endpoint::process();
}
