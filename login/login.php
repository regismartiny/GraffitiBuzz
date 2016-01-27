<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';
$config = 'config.php';

session_start();

// check for erros and whatnot
$error = "";

if (isset($_GET["error"])) {
	$error = '<b style="color:red">' . trim(strip_tags($_GET["error"])) . '</b><br /><br />';
}

if (isset($_GET['provider']) && $_GET["provider"]) {
	try {
		$hybridAuth = new Hybrid_Auth($config);
		$provider = @trim(strip_tags($_GET["provider"]));
		$adapter = $hybridAuth->authenticate($provider);
		$hybridAuth->redirect("profile.php?provider=$provider");
	} catch (Exception $e) {
		echo 'Error: ' . $e->getMessage();
	}
}

?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
<form action="<?=$_SERVER['PHP_SELF']?>" method="GET">
	<input type="hidden" name="provider" value="Facebook"/>
	<input type="submit" value="Login com Facebook!"/>
</form>
<form action="<?=$_SERVER['PHP_SELF']?>" method="GET">
	<input type="hidden" name="provider" value="Twitter"/>
	<input type="submit" value="Login com Twitter!"/>
</form>
<form action="<?=$_SERVER['PHP_SELF']?>" method="GET">
	<input type="hidden" name="provider" value="Google"/>
	<input type="submit" value="Login com Google!"/>
</form>




<?php
// try to get already authenticated provider list
try {
	$hybridauth = new Hybrid_Auth($config);

	$connected_adapters_list = $hybridauth->getConnectedProviders();

	if (count($connected_adapters_list)) {
		?>
    <td align="left" valign="top">
		<fieldset>
			<legend>Providers you are logged with</legend>
			<?php
foreach ($connected_adapters_list as $adapter_id) {
			echo '&nbsp;&nbsp;<a href="profile.php?provider=' . $adapter_id . '">Switch to <b>' . $adapter_id . '</b>  account</a><br />';
		}
		?>
		</fieldset>
	</td>
<?php
}
} catch (Exception $e) {
	echo "Ooophs, we got an error: " . $e->getMessage();

	echo " Error code: " . $e->getCode();

	echo "<br /><br />Please try again.";

	echo "<hr /><h3>Trace</h3> <pre>" . $e->getTraceAsString() . "</pre>";
}
?>
  </tr>
</table>


</body>
</html>