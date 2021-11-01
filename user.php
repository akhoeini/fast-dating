<?php

require_once 'vendor/autoload.php';

require_once 'init.php';

// $app->get('/register', function .....);

// Define app routes
$app->get('/login', function ($request, $response, $args) {
    return $this->view->render($response, 'login.html.twig');
});

$app->get('/profile/{uId:[0-9]+}', function($request, $response, $args) {

    $userInfo = DB::queryFirstRow("SELECT * FROM users WHERE id=%i", $args['uId']);

    // Display 'edit profile' button if user is viewing own profile
    if ($userId = $_SESSION['userId']) {
        return $this->view->render($response, 'profile.html.twig', ['ownProfile' => true, 'uId' => $userId]);
    }

});
