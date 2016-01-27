<?php
require 'vendor/autoload.php';

include_once 'server/db.php';

$app = new \Slim\Slim();

//Create a Slim database singleton resource that will return the database connection instance when called.
// Set singleton value
$app->container->singleton('db', function () {
	try {
		$db = new PDO('mysql:host=localhost;dbname=graffiti', 'graffiti', 'graffitipass',
			[\PDO::ATTR_PERSISTENT => false]);
	} catch (PDOException $e) {
		die('Error!: ' . $e->getMessage());
	}

	return $db;
}
);

//Another singleton resource that returns an instance of HybridAuth is also created.
$app->container->singleton('hybridInstance', function () {
	$instance = new Hybrid_Auth('config.php');

	return $instance;
});

//Instantiate the application model class by passing the database connection as a parameter.
$model = new \Model\App_Model($app->db);

//The authenticate function below when added as a parameter to a route, redirects users to the login page if they aren’t logged in.
$authenticate = function ($app) {
	return function () use ($app) {
		$app->hybridInstance;
		$session_identifier = Hybrid_Auth::storage()->get('user');

		if (is_null($session_identifier) && $app->request()->getPathInfo() != '/login/') {
			$app->redirect('/login/');
		}
	};
};

//Redirect all logged out users to the login page when they visit the application home or index page.
$app->get('/', $authenticate($app));

//Below is the route definition of the social login links. I.e. when the link http://slim.local/login/facebook is clicked,
//the user is redirected to Facebook by HybridAuth for authorization. Same goes for Twitter http://slim.local/login/twitter,
//Google http://slim.local/login/google and every other supported provider.
$app->get('/login/:idp', function ($idp) use ($app, $model) {
	try {
		$adapter = $app->hybridInstance->authenticate(ucwords($idp));
		$user_profile = $adapter->getUserProfile();

		if (empty($user_profile)) {
			$app->redirect('/login/?err=1');
		}

		$identifier = $user_profile->identifier;

		if ($model->identifier_exists($identifier)) {
			$model->login_user($identifier);
			$app->redirect('/welcome/');
		} else {
			$register = $model->register_user(
				$identifier,
				$user_profile->email,
				$user_profile->firstName,
				$user_profile->lastName,
				$user_profile->photoURL
			);

			if ($register) {
				$model->login_user($identifier);
				$app->redirect('/welcome/');
			}

		}

	} catch (Exception $e) {
		echo $e->getMessage();
	}
}
);

//Here is the code for the logout route.
$app->get('/logout/', function () use ($app, $model) {
	$app->hybridInstance;
	$model->logout_user();
	Hybrid_Auth::logoutAllProviders();
	$app->redirect('/login/');
}
);

//The route for the welcome page where users are redirected to when they log in:
$app->get('/welcome/', $authenticate($app), function () use ($app, $model) {
	$app->render('welcome.php', ['model' => $model]);
}
);

//Finally, run the Slim application.
// app/index.php
$app->run();

?>