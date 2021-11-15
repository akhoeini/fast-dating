<?php

require_once 'vendor/autoload.php';

require_once 'init.php';

// $app->get('/register', function .....);

// Define app routes

$app->get('/profile', function ($request, $response, $args) {
    return $this->view->render($response, 'editprofile.html.twig');
});

$app->get('/profile/{uId:[0-9]+}', function ($request, $response, $args) {

    $userInfo = DB::queryFirstRow("SELECT * FROM users WHERE id=%i", $args['uId']);
    $userPhotos = DB::query("SELECT photo.name, photo.description, photo.url FROM albums, photo WHERE albums.ownerId=%i AND albums.id=photo.albumId", $args['uId']);
    $userInterests = DB::queryFirstColumn("SELECT interests.name FROM userInterest, interests WHERE userInterest.userId=%i AND userInterest.interestId=interests.id", $args['uId']);
    if (!$userInfo) {
        throw new \Slim\Exception\NotFoundException($request, $response);
    } else {
        // Will display 'edit profile' button if user is viewing own profile
        $isOwnProfile = ($args['uId'] == $_SESSION['user']['id']);
        return $this->view->render($response, 'profile.html.twig', ['u' => $userInfo, 'pics' => $userPhotos, 'interests' => $userInterests, 'ownProfile' => $isOwnProfile]);
    }
});

$app->get('/edit-profile', function ($request, $response, $args) {
    if ($_SESSION['user']['id']) {
        $userId = $_SESSION['user']['id'];
    } else {
        $response = $response->withStatus(302);
        return $response->withHeader('Location', '/login');
    };
    $userInfo = DB::queryFirstRow("SELECT * FROM users WHERE id=%i", $userId);
    $userPhotos = DB::query("SELECT photo.name, photo.description, photo.url FROM albums, photo WHERE albums.ownerId=%i AND albums.id=photo.albumId", $userId);
    $userInterests = DB::queryFirstColumn("SELECT interests.name FROM userInterest, interests WHERE userInterest.userId=%i AND userInterest.interestId=interests.id", $userId);
    if (!$userInfo) {
        throw new \Slim\Exception\NotFoundException($request, $response);
    } else {
        return $this->view->render($response, 'editprofile.html.twig', ['u' => $userInfo, 'pics' => $userPhotos, 'interests' => $userInterests]);
    }
});

