<?php

require_once 'vendor/autoload.php';

require_once 'init.php';

class SwipeUser
{
    public $id;
    public $photo;
    public $userName;
}

class MatchUser
{
    public $id;
    public $photo;
    public $userName;
    public $location;
}

class LikedUser
{
    public $id;
    public $photo;
    public $userName;
    public $location;
}

class MessageItem
{
    public $id;
    public $photo;
    public $userName;
    public $message;
}

$app->get('/recommend', function ($request, $response, $args) {    
    $usersList = DB::query("SELECT * FROM users ORDER BY RAND() LIMIT 10");

    $data["items"] = array();
    $index = 0;
    $i = 0;
    $old_user = array();
    foreach ($usersList as $user) {
        if($user["id"] == $_SESSION['user']['id'])
            continue;

        if(array_search($user["id"], $old_user))
            continue;
        
        $matchUserList = DB::query("SELECT * FROM matches WHERE user_id=%d and matched_user_id=%d", $_SESSION['user']['id'], $user["id"]);
        if($matchUserList)
            continue;
        
        //$likeUserList = DB::query("SELECT * FROM user_likes WHERE user_id=%d and liked_user_id=%d and operation='like'", $_SESSION['user']['id'], $user["id"]);    
        $likeUserList = DB::query("SELECT * FROM user_likes WHERE user_id=%d and liked_user_id=%d", $_SESSION['user']['id'], $user["id"]);    
        if($likeUserList)
            continue;

        $swipePhoto = DB::queryFirstRow("SELECT * FROM swipe_photos WHERE user_id=%d", $user["id"]);
        if(!$swipePhoto)
            continue;
			
        $filename = $this->get('upload_directory') . $swipePhoto["image_name"];
        if(!file_exists($filename))
            continue;         

        $old_user []= $user["id"];

        debug_to_console($matchUserList);
        debug_to_console($likeUserList);

        $userItem = new SwipeUser();        
        $userItem->index = $index++;
        $userItem->id = $user["id"];
        $userItem->photo = "uploads/swipe_photos/" . $swipePhoto["image_name"];;
        $userItem->userName = $user['userName'];
        $data["items"][] = $userItem;

        if($i++ >= 5) {
            break;
        }        
    }    

    return $this->view->render($response, 'tinder.html.twig', $data);
});

$app->get('/matches', function ($request, $response, $args) {
    if(!check_user_session($response))
        return $response;

    $data["items"] = array();
    $data['nameVal'] = $_SESSION['user']['userName'];
    $data['idVal'] = $_SESSION['user']['id'];

    $id = $_SESSION['user']['id'];
    $matchUserList = DB::query("SELECT * FROM matches WHERE user_id=%d", $id);
    if(!$matchUserList)
    {
        return $this->view->render($response, 'matches.html.twig', $data);
    }

    for ($i = 0; $i < count($matchUserList); $i++) {        
        $m_id = $matchUserList[$i]['matched_user_id'];
        $user = DB::queryFirstRow("SELECT * FROM users WHERE id=%d", $m_id);

        $userItem = new MatchUser();
        $userItem->id = $m_id;

        $photo = get_swipe_photo($m_id);
        $userItem->photo = ($photo == "#") 
                           ? "/images/face.jpg" 
                           : $photo;
        $userItem->userName = $user['userName'];
        $userItem->location = ($user['location'] != null) ? $user['location'] : "Unknown";
        $data["items"][] = $userItem;
    }

    return $this->view->render($response, 'matches.html.twig', $data);
});

$app->post('/swipe', function ($request, $response, $args) {
    if(!check_user_session($response))
        return $response;

    global $log;
    $json = $request->getBody();
    $item = json_decode($json, TRUE);

    $data = [
        "user_id" => $_SESSION['user']['id'],
        "liked_user_id" => $item['id'],
    ];    

    if($item["operation"] == "pass") {
        $data ["operation"] = "pass";
    }else if($item["operation"] == "like") {
        $data ["operation"] = "like";
    } else {
        $response = $response->withStatus(404);
        $res = ["code" => 1, "error" => "invalid operation", "data" => $json];
        $response->getBody()->write(json_encode($res));
        return $response;    
    }

    $likeUserList = DB::query("SELECT * FROM user_likes WHERE user_id=%d and liked_user_id=%d and operation='like'", $item['id'], $_SESSION['user']['id']);    
    $matchUserList = DB::query("SELECT * FROM matches WHERE user_id=%d and matched_user_id=%d", $_SESSION['user']['id'], $item["id"]);    

    $match_flag = 0;
    if($likeUserList && !$matchUserList) { //matches!!
        $match_flag = 1;

        $match_data = [
            "user_id" => $_SESSION['user']['id'],
            "matched_user_id" => $item['id'],
        ];
        DB::insert('matches', $match_data);

        $match_data1 = [
            "user_id" => $item['id'],
            "matched_user_id" => $_SESSION['user']['id'],
        ];
        DB::insert('matches', $match_data1);

        //need to save message to matched_user_id;
        $message = [];
        $message['mtype'] = 0;
        $message['from_id'] = 0;
        $message['to_id'] = $item['id'];
        $message['msg'] = "<strong>Congratulations!</strong> You have a new matches.";    
        DB::insert('messages', $message);        
    }

    DB::insert('user_likes', $data);
    $insertId = DB::insertId();
    $log->debug("Record added id=" . $insertId);
    $response = $response->withStatus(201);
    $res = ["code" => 0, "error" => "", "data" => $match_flag];
    $response->getBody()->write(json_encode($res));


    return $response;
});

$app->get('/newlogin', function ($request, $response, $args) {
    return $this->view->render($response, 'newlogin.html.twig');
});

// STATE 2&3: receiving submission
$app->post('/newlogin', function ($request, $response, $args) use ($log) {
    $email = $request->getParam('email');
    $password = $request->getParam('password');
    //
    $record = DB::queryFirstRow("SELECT id, userName,email,password FROM users WHERE email=%s", $email);
    $loginSuccess = false;
    if ($record) {
        global $passwordPepper;
        $pwdPeppered = hash_hmac("sha256", $password, $passwordPepper);
        $pwdHashed = $record['password'];
        if ($password == $pwdHashed) {
            $loginSuccess = true;
        }
        // WARNING: only temporary solution to allow for old plain-text passwords to continue to work
        // Plain text passwords comparison
        else if ($record['password'] == $password) {
            $loginSuccess = true;
        }
    }
    //
    if (!$loginSuccess) {
        $log->info(sprintf("Login failed for email %s from %s", $email, $_SERVER['REMOTE_ADDR']));
        return $this->view->render($response, 'newlogin.html.twig', [ 'error' => true ]);
    } else {
        unset($record['password']); // for security reasons remove password from session
        $_SESSION['user'] = $record; // remember user logged in
        $log->debug(sprintf("Login successful for email %s, uid=%d, from %s", $email, $record['id'], $_SERVER['REMOTE_ADDR']));
        
        $userName = $_SESSION['user']['userName'];
        $userId = $_SESSION['user']['id'];

        $data = ['nameVal' => $userName, 'idVal' => $userId];
        $data['photoVal'] = get_swipe_photo($userId);
    
        return $this->view->render($response, 'home.html.twig', $data );
    }
});

$app->get('/register', function ($request, $response, $args) {
    return $this->view->render($response, 'register.html.twig');
});

$app->post('/register', function ($request, $response, $args) {
    $name = $request->getParam('name');
    $email = $request->getParam('email');
    $pass1 = $request->getParam('pass1');
    $pass2 = $request->getParam('pass2');

    global $log;
    $log->info(sprintf("register %s %s %s", $name, $email, $pass1));
    //
    $errorList = array();
    //
    $result = verifyUserName($name);
    if ($result != TRUE) { $errorList[] = $result; }

    // verify email
    if (filter_var($email, FILTER_VALIDATE_EMAIL) == FALSE) {
        array_push($errorList, "Email does not look valid");
        $email = "";
    } else {
        // is email already in use?
        $record = DB::queryFirstRow("SELECT id FROM users WHERE email=%s", $email);
        if ($record) {
            array_push($errorList, "This email is already registered");
            $email = "";
        }
    }
    //
    $result = verifyPasswordQuailty($pass1, $pass2);
    if ($result != TRUE) { $errorList[] = $result; }
    //
    if ($errorList) {
        return $this->view->render($response, 'register.html.twig',
            [ 'errorList' => $errorList, 'v' => ['name' => $name, 'email' => $email ]  ]);
    } else {
        //
        global $passwordPepper;
        $pwdPeppered = hash_hmac("sha256", $pass1, $passwordPepper);
        $pwdHashed = password_hash($pwdPeppered, PASSWORD_DEFAULT); // PASSWORD_ARGON2ID);
        DB::insert('users', ['userName' => $name, 'email' => $email, 'password' => $pass1]);

        return $this->view->render($response, 'newlogin.html.twig', ['email' => $email ]);
    }
});

// used via AJAX
$app->get('/isemailtaken/[{email}]', function ($request, $response, $args) {
    $email = isset($args['email']) ? $args['email'] : "";
    $record = DB::queryFirstRow("SELECT id FROM users WHERE email=%s", $email);
    if ($record) {
        return $response->write("Email already in use");
    } else {
        return $response->write("");
    }
});

$app->get('/logout', function ($request, $response, $args) use ($log) {
    $log->debug(sprintf("Logout successful for uid=%d, from %s", @$_SESSION['user']['id'], $_SERVER['REMOTE_ADDR']));
    unset($_SESSION['user']);
    //return $this->view->render($response, 'newlogin.html.twig');
    //$response = $response->withStatus(201);
    return $response->withHeader('Location', '/');
});

function verifyPasswordQuailty($pass1, $pass2) {
    if ($pass1 != $pass2) {
        return "Passwords do not match";
    } else {
        /*
        // FIXME: figure out how to use case-sensitive regexps with Validator
        if (!Validator::length(6,100)->regex('/[A-Z]/')->validate($pass1)) {
            return "VALIDATOR. Password must be 6-100 characters long, "
                . "with at least one uppercase, one lowercase, and one digit in it";
        } */
        if ((strlen($pass1) < 6) || (strlen($pass1) > 100)
                || (preg_match("/[A-Z]/", $pass1) == FALSE )
                || (preg_match("/[a-z]/", $pass1) == FALSE )
                || (preg_match("/[0-9]/", $pass1) == FALSE )) {
            return "Password must be 6-100 characters long, "
                . "with at least one uppercase, one lowercase, and one digit in it";
        }
    }
    return TRUE;
}

// these functions return TRUE on success and string describing an issue on failure
function verifyUserName($name) {
    if (preg_match('/^[a-zA-Z0-9\ \\._\'"-]{4,50}$/', $name) != 1) { // no match
        return "Name must be 4-50 characters long and consist of letters, digits, "
            . "spaces, dots, underscores, apostrophies, or minus sign.";
    }
    return TRUE;
}

function verifyUploadedPhotoMime($photo, &$mime = null) {
    if ($photo->getError() != 0) {
        return "Error uploading photo " . $photo->getError();
    } 
    if ($photo->getSize() > 1024*1024) { // 1MB
        return "File too big. 1MB max is allowed.";
    }
    $info = getimagesize($photo->file);
    if (!$info) {
        return "File is not an image";
    }
    // echo "\n\nimage info\n";
    // print_r($info);
    if ($info[0] < 200 || $info[0] > 1000 || $info[1] < 200 || $info[1] > 1000) {
        return "Width and height must be within 200-1000 pixels range";
    }
    $ext = "";
    switch ($info['mime']) {
        case 'image/jpeg': $ext = "jpg"; break;
        case 'image/gif': $ext = "gif"; break;
        case 'image/png': $ext = "png"; break;
        default:
            return "Only JPG, GIF and PNG file types are allowed";
    } 
    if (!is_null($mime)) {
        $mime = $info['mime'];
    }
    return TRUE;
}

$app->get('/likes', function ($request, $response, $args) {
    $data["items"] = array();
    $data['nameVal'] = $_SESSION['user']['userName'];
    $data['idVal'] = $_SESSION['user']['id'];
    $data['likeFlag'] = 0;

    $id = $_SESSION['user']['id'];
    $userList = DB::query("SELECT * FROM user_likes WHERE liked_user_id=%d and operation='like'", $id);   
    if(!$userList) {
        return $this->view->render($response, 'likes.html.twig', $data);
    }
    
    for ($i = 0; $i < count($userList); $i++) {        
        $m_id = $userList[$i]['user_id'];

        $matchUserList = DB::query("SELECT * FROM matches WHERE user_id=%d and matched_user_id=%d", $id, $m_id);
        if($matchUserList)
            continue;

        $user = DB::queryFirstRow("SELECT userName, location FROM users WHERE id=%d", $m_id);

        $userItem = new LikedUser();
        $userItem->id = $m_id;
        
        $photo = get_swipe_photo($m_id);
        $userItem->photo = ($photo == "#") 
                           ? "/images/face.jpg" 
                           : $photo;
        $userItem->userName = $user['userName'];
        $userItem->location = ($user['location'] != null) ? $user['location'] : "Unknown";
        $data["items"][] = $userItem;
    }

    return $this->view->render($response, 'likes.html.twig', $data);    
});

$app->get('/likes1', function ($request, $response, $args) {
    $data["items"] = array();
    $data['nameVal'] = $_SESSION['user']['userName'];
    $data['idVal'] = $_SESSION['user']['id'];
    $data['likeFlag'] = 1;

    $id = $_SESSION['user']['id'];
    $userList = DB::query("SELECT * FROM user_likes WHERE user_id=%d and operation='like'", $id);   
    if(!$userList) {
        return $this->view->render($response, 'likes.html.twig', $data);
    }
    
    for ($i = 0; $i < count($userList); $i++) {        
        $m_id = $userList[$i]['liked_user_id'];

        $matchUserList = DB::query("SELECT * FROM matches WHERE user_id=%d and matched_user_id=%d", $id, $m_id);
        if($matchUserList)
            continue;

        $user = DB::queryFirstRow("SELECT userName, location FROM users WHERE id=%d", $m_id);

        $userItem = new LikedUser();
        $userItem->id = $m_id;
        
        $photo = get_swipe_photo($m_id);
        $userItem->photo = ($photo == "#") 
                           ? "/images/face.jpg" 
                           : $photo;
        $userItem->userName = $user['userName'];
        $userItem->location = ($user['location'] != null) ? $user['location'] : "Unknown";
        $data["items"][] = $userItem;
    }

    return $this->view->render($response, 'likes.html.twig', $data);    
});


$app->post('/tick', function ($request, $response, $args) {
    if(!check_user_session($response))
        return $response;

    $id = $_SESSION['user']['id'];

    //check if this user has message
    $message = DB::queryFirstRow("SELECT * FROM messages where to_id=%d and recd=0", $id);
    if(!$message) {
        $res = ["flag" => 0, "data" => ""];
        $response->getBody()->write(json_encode($res));    
        return $response;
    }

    //debug_to_console(json_encode($message));
    //update 
    DB::update("messages", ['recd' => 1], "id=%d", $message['id']);    

    $data = [
             "mtype" => $message['mtype'], 
             "from_id" => $message['from_id'], 
             "to_id" => $message['to_id'], 
             "msg" => $message['msg'],
             "sent" => $message['sent']
            ];
    $res = ["flag" => 1, "data" => $data];
    
    if($res['flag'] == 1) {
        //global $log;
        //$log->info(json_encode($message, JSON_NUMERIC_CHECK));    
    }

    $response->getBody()->write(json_encode($res, JSON_NUMERIC_CHECK));

    return $response;
});        

$app->get('/about', function ($request, $response, $args) {
    return $this->view->render($response, 'about.html.twig');
});

$app->post('/message', function ($request, $response, $args) {
    if(!check_user_session($response))
        return $response;

    $id = $_SESSION['user']['id'];

    $json = $request->getBody();

    global $log;
    $log->info($json);
    
    $item = json_decode($json, TRUE);

    $text = $item['msgText'];
    $toId = $item['toId'];
    debug_to_console($json);

    //need to save message to matched_user_id;
    $message = [];
    $message['mtype'] = 1;
    $message['from_id'] = $id;
    $message['to_id'] = $toId;
    $message['msg'] = $text;
    DB::insert('messages', $message);

    $res = ["flag" => 1];
    $response->getBody()->write(json_encode($res));
    
    return $response;
});

$app->get('/message', function ($request, $response, $args) {
    $data["items"] = array();
    $data['nameVal'] = $_SESSION['user']['userName'];
    $data['idVal'] = $_SESSION['user']['id'];

    $id = $_SESSION['user']['id'];
    $messageList = DB::query("SELECT * FROM messages WHERE to_id=%d and mtype=1", $id);   
    if(!$messageList) {
        return $this->view->render($response, 'messages.html.twig', $data);
    }
    
    for ($i = 0; $i < count($messageList); $i++) {        
        $m_id = $messageList[$i]['from_id'];

        $user = DB::queryFirstRow("SELECT userName FROM users WHERE id=%d", $m_id);

        $messageItem = new MessageItem();
        $messageItem->id = $messageList[$i]['id'];
        
        $photo = get_swipe_photo($m_id);
        $messageItem->photo = ($photo == "#") 
                           ? "/images/face.jpg" 
                           : $photo;
        $messageItem->userName = $user['userName'];
        $messageItem->message = $messageList[$i]['msg'];
        $data["items"][] = $messageItem;

        DB::update("messages", ['recd' => 1], "id=%d", $messageItem->id); 
    }

    return $this->view->render($response, 'messages.html.twig', $data);    
});

$app->post('/delmessage', function ($request, $response, $args) {
    if(!check_user_session($response))
        return $response;

    $id = $_SESSION['user']['id'];

    $json = $request->getBody();

    global $log;
    $log->info($json);
    
    $item = json_decode($json, TRUE);

    $res = ["flag" => 0];

    $msgId = $item['msgId'];
    $message = DB::queryFirstRow("SELECT * FROM messages WHERE id=%d", $msgId);   
    if(!$message) {
        $res['error'] = "Invaid msg id " . $msgId;
        $response->getBody()->write(json_encode($res));
        return $response;
    }
    if($message['to_id'] != $id){
        $res['error'] = "Invaid msg id or receiver";
        $response->getBody()->write(json_encode($res));
        return $response;
    }    

    DB::delete('messages', "id=%i", $msgId);

    $res = ["flag" => 1, "msgId" => $msgId];
    $response->getBody()->write(json_encode($res, JSON_NUMERIC_CHECK));
    
    return $response;    
});