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
    $userPhotos = DB::query("SELECT photo.name, photo.description, photo.url FROM albums, photo WHERE albums.ownerId=%i AND albums.id=photo.albumId", $args['uId']);
    $userInterests = DB::queryFirstColumn("SELECT interests.name FROM userInterest, interests WHERE userInterest.userId=%i AND userInterest.interestId=interests.id", $args['uId']);
    if (!$userInfo) {
        throw new \Slim\Exception\NotFoundException($request, $response);
    } else {
        // Will display 'edit profile' button if user is viewing own profile
        $isOwnProfile = ($args['uId'] == $_SESSION['userId']);
        return $this->view->render($response, 'profile.html.twig', ['u' => $userInfo, 'pics' => $userPhotos, 'interests' => $userInterests, 'ownProfile' => $isOwnProfile] );
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
    $cars["items"] = array();    
    for($i = 0; $i < 4; $i++)
    {
        $id = rand(1, 1000);
        $userItem = new MatchUser();
        $userItem->id = $id;
        $userItem->photo = 'red';
        $userItem->userName = 'sedan' . $id;
        $userItem->location = 'Montreal';        
        $cars["items"] []= $userItem;        
    }

    return $this->view->render($response, 'matches.html.twig', $cars);
 });

