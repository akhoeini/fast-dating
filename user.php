<?php

require_once 'vendor/autoload.php';

require_once 'init.php';

// $app->get('/register', function .....);

// Define app routes
$app->get('/signup', function ($request, $response, $args) {
    return $this->view->render($response, 'signup.html.twig');
});


use Firebase\JWT\JWT;
use Firebase\JWT\Key;


$app->post('/signup', function ($request, $response, $args) {
    $publicKey = <<<EOD
-----BEGIN PUBLIC KEY-----
MIGeMA0GCSqGSIb3DQEBAQUAA4GMADCBiAKBgF4ARAWwdBHW9RFIbSVSVhDj+ngN
lLQwWyRr5Vxz+3Dinjl+fMYpuSqrsRr9KVMOqO6L2EV6SBv0T/x3WxSHBNSjXK50
LB4eAfbX+Ga/LPOD7Gk2gJMZAGWW2uTvbE2FCeicfOoh7dwiT+PrvdY95EcAeYao
g1M5RmOWZGX/JB/BAgMBAAE=
-----END PUBLIC KEY-----
EOD;

$privateKey = <<<EOD
-----BEGIN RSA PRIVATE KEY-----
MIICWgIBAAKBgF4ARAWwdBHW9RFIbSVSVhDj+ngNlLQwWyRr5Vxz+3Dinjl+fMYp
uSqrsRr9KVMOqO6L2EV6SBv0T/x3WxSHBNSjXK50LB4eAfbX+Ga/LPOD7Gk2gJMZ
AGWW2uTvbE2FCeicfOoh7dwiT+PrvdY95EcAeYaog1M5RmOWZGX/JB/BAgMBAAEC
gYAXIIGb1Ln52aUZx3PzBrreFPj+qHi5jFwgLduUT4TBVUAQbSpNpt5DvVIpjbep
E6ZEamufTGKJXiZ/uu3RsxZg3ffn9bMRi5dDLpfBye4yG+Ugbxzq5qWYIhi91F82
xwQxDmxKhPMQT/YhmOGp2GRgzE/EHGLrgVMW+pOeS/t0AQJBAKbKctzJb401V//7
zTVEQtRst99qs5PMV7q3cO0jFNCVoXf6XUQfIm5PDFuYQthr3JxhXevci4eD/C6L
eGrnqlECQQCQRzMOSZu5nztcatfgaCZVE2gcowTerZBflfV70sr90nV2OR7JpoFC
mmESuwelg7N6P+iHMXubrlSk9tOMFFJxAkAZ58nNVxAXa5iebrqhsld67OPmNIlt
xEg//OvyOQermgH5Q46m3PsZDPgLZevD94TNWSYgUyHsy2goxorOd+rxAkBOgb+h
XaJT/fYiEq1HGcUJ9BZpxrbmqFDwAjxi1U/Jj9SEsQ40sdqSMEj0FTtS7/ggZFgW
AHH/Q3whi4GPLpuxAkAG+vRW0s55YRQtFIJcmqRuDRBzTC5Gf5ibDT9nhMxawZej
dSAD/KAX5AdqXDZqkfdibRh5E4dW9aEv2KJ/o3un
-----END RSA PRIVATE KEY-----
EOD;
    $json = $request->getBody();
    $item = json_decode($json, TRUE); // true makes it return an associative array instead of an object

    //check if any user exists with this username and password
    $user = DB::queryFirstRow("SELECT * FROM users WHERE email=%s OR username=%s", $item['email'], $item['username']);
    if ($user) {
        $response = $response->withStatus(400);
        $response->getBody()->write(json_encode("400 - user already exists please change your email or username"));
        return $response;
    }
   


    DB::insert('users', $item);
    $insertId = DB::insertId();
    $today=date("Y-m-d");
    $payload = array(
        "iss" => "fastdating.org",
        "aud" => "fastdating.ca",
        "iat" => $insertId,
        "nbf" => $item['username'],
        "exd" => date('Y-m-d', strtotime('+1 month', strtotime($today)))
    );
    $jwt = JWT::encode($payload, $privateKey, 'RS256');
    
    DB::update('users', ['token' => $jwt], "id=%i", $insertId);
    $responseArray = Array (
        "id" => $insertId,
        "token" => $jwt
    );

    $response = $response->withStatus(201);
    $response->getBody()->write(json_encode($responseArray));
    return $response;
});

$app->get('/login', function ($request, $response, $args) {
    return $this->view->render($response, 'login.html.twig');
});

$app->get('/recommend', function ($request, $response, $args) {
    return $this->view->render($response, 'tinder.html.twig');
});

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
        $isOwnProfile = ($args['uId'] == $_SESSION['userId']);
        return $this->view->render($response, 'profile.html.twig', ['u' => $userInfo, 'pics' => $userPhotos, 'interests' => $userInterests, 'ownProfile' => $isOwnProfile]);
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
    $data["items"] = array();
    for ($i = 0; $i < 4; $i++) {
        $id = rand(1, 1000);
        $photo = rand(1, 100);
        $userItem = new MatchUser();
        $userItem->id = $id;
        $userItem->photo = $photo;
        $userItem->userName = 'sedan' . $id;
        $userItem->location = 'Montreal';
        $data["items"][] = $userItem;
    }

    return $this->view->render($response, 'matches.html.twig', $data);
});
