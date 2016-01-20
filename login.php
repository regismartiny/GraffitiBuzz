<?php
session_start();

require_once __DIR__ . '/vendor/autoload.php';
# login.php
$fb = new Facebook\Facebook([
  'app_id' => '441905576015354',
  'app_secret' => '69aad24cef2310e7c495643e6e221b1d',
  'default_graph_version' => 'v2.2',
  ]);

$helper = $fb->getRedirectLoginHelper();
$permissions = ['email', 'user_likes']; // optional
$loginUrl = $helper->getLoginUrl('http://www.graffiti.buzz/login-callback.php', $permissions);

echo '<a href="' . $loginUrl . '">Log in with Facebook!</a>';
?>