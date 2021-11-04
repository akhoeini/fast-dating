<?php

require_once 'vendor/autoload.php';

require_once 'init.php';

// $app->get('/register', function .....);

// Define app routes
$app->get('/signup', function ($request, $response, $args) {
   return $this->view->render($response, 'signup.html.twig');
});

$app->get('/login', function ($request, $response, $args) {
    return $this->view->render($response, 'login.html.twig');
 });

 $app->get('/profile', function ($request, $response, $args) {
    return $this->view->render($response, 'editprofile.html.twig');
 });

$app->get('/profile/{uId:[0-9]+}', function($request, $response, $args) {

    $userInfo = DB::queryFirstRow("SELECT * FROM users WHERE id=%i", $args['uId']);
    if (!$userInfo) {
        throw new \Slim\Exception\NotFoundException($request, $response);
    } else {
        // Will display 'edit profile' button if user is viewing own profile
        $isOwnProfile = ($args['uId'] == $_SESSION['userId']);
        return $this->view->render($response, 'profile.html.twig', ['u' => $userInfo, 'ownProfile' => $isOwnProfile] );
    }
});

class MatchUser
{
    public $id;    
    public $photo;
    public $userName;
    public $location;
}

$app->get('/matches', function ($request, $response, $args) {
    $myCar = new MatchUser();
    $myCar->id = 'red';
    $myCar->photo = 'red';
    $myCar->userName = 'sedan';
    $myCar->location = 'sedan';
    
    $cars["categories"] = array($myCar, $myCar, $myCar, $myCar);    

    return $this->view->render($response, 'matches.html.twig', $cars);
 });

