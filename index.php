<?php
require_once 'vendor/autoload.php';
require_once 'init.php';
require_once 'utils.php';

// Define app routes below
require_once 'user.php';
require_once 'admin.php';
require_once 'matches.php';
require_once 'photo.php';

$app->get('/', function ($request, $response, $args) {

     if (!isset($_SESSION['user'])) { // refuse if user not logged in
         $response = $response->withStatus(201);
         return $this->view->render($response, 'newlogin.html.twig');
     }

    $userName = $_SESSION['user']['userName'];
    $userId = $_SESSION['user']['id'];

    //debug_to_console($userName);
    //debug_to_console($userId);
    $data = ['nameVal' => $userName, 'idVal' => $userId];
    $data['photoVal'] = get_swipe_photo($userId);

    //get the chat room name
    $data['chatRoomList'] = [];

    $chatroomList = DB::query("SELECT * FROM room");
    if(!$chatroomList) {        
        return $this->view->render($response, 'home.html.twig', $data);
    }    

    $data['chatRoomList'] = $chatroomList;

    return $this->view->render($response, 'home.html.twig', $data );

});


// Run app - must be the last operation
// if you forget it all you'll see is a blank page
$app->run();