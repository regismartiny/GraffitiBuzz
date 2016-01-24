<?php
session_start();
# login-callback.php
require_once __DIR__ . '/vendor/autoload.php';
# login.php
$fb = new Facebook\Facebook([
  'app_id' => '441905576015354',
  'app_secret' => '69aad24cef2310e7c495643e6e221b1d',
  'default_graph_version' => 'v2.5',
  ]);

$helper = $fb->getRedirectLoginHelper();
try {
  $accessToken = $helper->getAccessToken();
} catch(Facebook\Exceptions\FacebookResponseException $e) {
  // When Graph returns an error
  echo '<br>Graph returned an error: ' . $e->getMessage();
  exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
  // When validation fails or other local issues
  echo '<br>Facebook SDK returned an error: ' . $e->getMessage();
  exit;
}

if (isset($accessToken)) {
  // Logged in!
  $_SESSION['facebook_access_token'] = (string) $accessToken;

  /////make an entity from de accessToken string
  ///to use inside the redirect page
  $expires = time() + 60 * 60 * 2;
  $accessToken = new Facebook\Authentication\AccessToken($_SESSION['facebook_access_token'], $expires);
  //echo '<br>acessToken received: '.$accessToken->getValue();
  $fb->setDefaultAccessToken($accessToken);
  //echo '<br>expiresAt: '.$accessToken->getExpiresAt();
  ///

  // Now you can redirect to another page and use the
  // access token from $_SESSION['facebook_access_token']


  try {

  $response = $fb->get('/me');
  $object = $response->getGraphObject();
  $user = $response->getGraphUser();
  $location = $user->getLocation();


  echo '<br>Logged in as ' . $user->getName();
  echo '<br>Email (object): ' . $object->getProperty('email');
  //echo '<br>Email: ' . $user->getEmail();
  echo '<br>Aniversário (object): ' . $object->getProperty('birthday');
  echo '<br>Aniversário: ' . $user->getBirthday();
  echo '<br>Cidade: ' . $location->getCity();
  echo '<br>Estado: ' . $location->getState();
  echo '<br>País: ' . $location->getCountry();

} catch(Facebook\Exceptions\FacebookResponseException $e) {
  // When Graph returns an error
  echo '<br>Graph returned an error: ' . $e->getMessage();
  exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
  // When validation fails or other local issues
  echo '<br>Facebook SDK returned an error: ' . $e->getMessage();
  exit;
}




}else if($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['error'])){
    echo '<br>PermissÃ£o nÃ£o concedida';
}
?>