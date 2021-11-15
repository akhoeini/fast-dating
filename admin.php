<?php

require_once 'vendor/autoload.php';

require_once 'init.php';

// $app->get('/register', function .....);

// Define app routes
$app->get('/admin/signup', function ($request, $response, $args) {
    return $this->view->render($response, 'signup.html.twig');
});


use Firebase\JWT\JWT;
use Firebase\JWT\Key;


$app->post('/admin/signup', function ($request, $response, $args) {
    

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
    $user = DB::queryFirstRow("SELECT * FROM users WHERE email=%s OR userName=%s", $item['email'], $item['username']);
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
        "nbf" => $item['userName'],
        "role" => "admin",
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

$app->post('/admin/login', function ($request, $response, $args) {
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
    $user = DB::queryFirstRow("SELECT * FROM users WHERE email=%s AND `password`=%s AND `role`=%s" , $item['email'], $item['password'],"admin");
    if ($user) {
        $today=date("Y-m-d");
        $payload = array(
            "iss" => "fastdating.org",
            "aud" => "fastdating.ca",
            "iat" => $user['id'],
            "nbf" => $user['userName'],
            "role" => "admin",
            "exd" => date('Y-m-d', strtotime('+1 month', strtotime($today)))
        );
        $jwt = JWT::encode($payload, $privateKey, 'RS256');
        
        DB::update('users', ['token' => $jwt], "id=%i", $user['id']);
        $responseArray = Array (
            "id" => $user['id'],
            "token" => $jwt
        );
    
        $response = $response->withStatus(201);
        $response->getBody()->write(json_encode($responseArray));
        return $response;
    }else{
        $response = $response->withStatus(400);
        $response->getBody()->write(json_encode("400 -username or password is wrong"));
        return $response;
    }
});

$app->get('/admin/login', function ($request, $response, $args) {
    return $this->view->render($response, 'login.html.twig');
});

$app->get('/admin/main', function ($request, $response, $args) {
        return $this->view->render($response, './admin/index.html');
});


$app->get('/admin/users', function ($request, $response, $args) {
    // $token=$request->getHeader("Authorization");
    // $response=validateToken($token,$response);
    // if($response){
    //     return $response;
    // }
    $queryParams = $request->getQueryParams();
    $sortBy = isset($queryParams['sortBy']) ? $queryParams['sortBy'] : "id";
    if (!in_array($sortBy, ['id', 'userName', 'email', 'birthDate','location','firstName'])) {
        $response = $response->withStatus(400);
        $response->getBody()->write(json_encode("400 - invalid sortBy value"));
        return $response;
    }
    $list = DB::query("SELECT id,userName,birthDate,`email`,`location`,firstName FROM users ORDER BY %l",$sortBy);
    $json = json_encode($list, JSON_PRETTY_PRINT);
    $response->getBody()->write($json);
    return $response;

});

$app->get('/admin/genders', function ($request, $response, $args) {
    // $token=$request->getHeader("Authorization");
    // $response=validateToken($token,$response);
    // if($response){
    //     return $response;
    // }
   
    $list = DB::query("SELECT genderId,COUNT(*) as `count` FROM users GROUP BY genderId;");
    $json = json_encode($list, JSON_PRETTY_PRINT);
    $response->getBody()->write($json);
    return $response;

});

$app->delete('/admin/users/{id:[0-9]+}', function ($request, $response, $args) {
    global $log;
    $token=$request->getHeader("Authorization");
    $response=validateToken($token,$response);
    if($response){
        return $response;
    }
    $id = $args['id'];
    DB::delete('users', "id=%i", $args['id']);
    $log->debug("Record todos deleted id=" . $id);
    // code is always 200
    // return true if record actually deleted, false if it did not exist in the first place
    $count = DB::affectedRows();
    $json = json_encode($count != 0, JSON_PRETTY_PRINT); // true or false
    return $response->getBody()->write($json);
});
    
   




function validateToken($token,$response){
    $publicKey = <<<EOD
    -----BEGIN PUBLIC KEY-----
    MIGeMA0GCSqGSIb3DQEBAQUAA4GMADCBiAKBgF4ARAWwdBHW9RFIbSVSVhDj+ngN
    lLQwWyRr5Vxz+3Dinjl+fMYpuSqrsRr9KVMOqO6L2EV6SBv0T/x3WxSHBNSjXK50
    LB4eAfbX+Ga/LPOD7Gk2gJMZAGWW2uTvbE2FCeicfOoh7dwiT+PrvdY95EcAeYao
    g1M5RmOWZGX/JB/BAgMBAAE=
    -----END PUBLIC KEY-----
    EOD;
    try{
        $decoded = JWT::decode($token, new Key($publicKey, 'RS256'));
        $decoded_array = (array) $decoded;
    }catch(Exception $e){
        $response = $response->withStatus(403);
        $response->getBody()->write(json_encode("403 - authentication failed"));
        return $response;
    }
    if(!$decoded_array){
        $response = $response->withStatus(403);
        $response->getBody()->write(json_encode("403 - authentication failed"));
        return $response;
    }elseif($decoded_array['role']!='admin'){
        $response = $response->withStatus(403);
        $response->getBody()->write(json_encode("403 - authentication failed"));
        return $response;
    }elseif($decoded_array['exd']<date("Y-m-d")){
        $response = $response->withStatus(403);
        $response->getBody()->write(json_encode("403 - authentication failed"));
        return $response;
    }
    return null;
}






