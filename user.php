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
    $userInterests = DB::queryFirstColumn("SELECT interests.name FROM userinterest, interests WHERE userinterest.userId=%i AND userinterest.interestId=interests.id", $args['uId']);
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
    $userInterests = DB::queryFirstColumn("SELECT interests.name FROM userinterest, interests WHERE userinterest.userId=%i AND userinterest.interestId=interests.id", $userId);
    if (!$userInfo) {
        throw new \Slim\Exception\NotFoundException($request, $response);
    } else {
        return $this->view->render($response, 'editprofile.html.twig', ['u' => $userInfo, 'pics' => $userPhotos, 'interests' => $userInterests]);
    }
});

$app->patch('/edit-profile', function ($request, $response) use ($log) {
    if ($_SESSION['user']['id']) {
        $userId = $_SESSION['user']['id'];
    } else {
        $response = $response->withStatus(302);
        return $response->withHeader('Location', '/login');
    };
    $json = $request->getBody();
    $profile = json_decode($json, TRUE);
 
    // success
    $valuesList = ['firstName' => $profile['firstName'], 'location' => $profile['location'], 'username' => $profile['username'], 'email' => $profile['email'], 'genderId' => $profile['gender'], 'userLookingForId' => $profile['genderLF'], 'bio' => $profile['bio']];
    DB::update('users', $valuesList, "id=%i", $userId);
    $log->debug(sprintf("User with Id=%s updated", $userId));
    //return $this->view->render($response, 'editprofile.html.twig');
    $res = ["code" => 0, "error" => ""];
    $response->getBody()->write(json_encode($res));
    return $response;
});