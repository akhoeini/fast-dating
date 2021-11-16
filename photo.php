<?php

require_once 'vendor/autoload.php';

require_once 'init.php';

use Slim\Http\UploadedFile;

function moveUploadedFile($directory, UploadedFile $uploadedFile)
{
    $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
    $basename = bin2hex(random_bytes(8)); // see http://php.net/manual/en/function.random-bytes.php
    $filename = sprintf('%s.%0.8s', $basename, $extension);

    $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

    return $filename;
}


$app->get('/photo1', function ($request, $response, $args) {
    if(!check_user_session($response))
        return $response;

    $id = $_SESSION['user']['id'];
    $data = [ 'photo' => '#' ];
    $swipePhoto = DB::queryFirstRow("SELECT * FROM swipe_photos WHERE user_id=%d", $id);
    if($swipePhoto) {
        $filename = $this->get('upload_directory') . $swipePhoto["image_name"];    
        if(file_exists($filename)){
            $data = [ 'photo' => "/uploads/swipe_photos/" . $swipePhoto["image_name"] . "?v=" . time() ];    
        }            
    }        

    //debug_to_console("uploads/swipe_photos/" . $swipePhoto["image_name"]);
        
    return $this->view->render($response, 'photo.html.twig', $data);
});

$app->post('/photo1', function ($request, $response, $args) {
    if(!check_user_session($response))
        return $response;

    $errorList = array();
    // verify image
    $hasPhoto = false;
    $mimeType = "";
    $uploadedImage = $request->getUploadedFiles()['image'];
    if ($uploadedImage->getError() != UPLOAD_ERR_NO_FILE) { // was anything uploaded?
        // print_r($uploadedImage->getError());
        $hasPhoto = true;
        $result = verifyUploadedPhotoMime($uploadedImage, $mimeType);
        if ($result !== TRUE) {
            $errorList[] = $result;
        } 
    }
    if(!$hasPhoto) {
        array_push($errorList, "Photo missing or invalid photo");
    }

    if ($hasPhoto) {
        $directory = $this->get('upload_directory');
        $uploadedImagePath = moveUploadedFile($directory, $uploadedImage);
    }

    if ($errorList) {
        return $this->view->render($response, 'photo.html.twig',
            [ 'errorList' => $errorList ]);
    }

    $id = $_SESSION['user']['id'];

    $swipePhoto = DB::queryFirstRow("SELECT * FROM swipe_photos WHERE user_id=%d", $id);
    if(!$swipePhoto)      
        DB::insert('swipe_photos', ['user_id' => $_SESSION['user']['id'], 'image_name' => $uploadedImagePath]);                  
    else
        DB::update('swipe_photos', ['user_id' => $_SESSION['user']['id'], 'image_name' => $uploadedImagePath], 
                    "user_id=%d", $id);                  

    array_push($errorList, "Photo updated successfully");
    return $this->view->render($response, 'photo.html.twig', [ 'errorList' => $errorList ]);
});

$app->post('/photo2', function ($request, $response, $args) {
    if(!check_user_session($response))
        return $response;

    $id = $_SESSION['user']['id'];        
    $json = $request->getBody();

    $item = json_decode($json, TRUE);

    $img = $item['data'];
    global $log;
    $log->debug(json_encode($img));

    $image_info = getimagesize($img);

    $extension = (isset($image_info["mime"]) ? explode('/', $image_info["mime"] )[1]: "");

    //$log->debug(json_encode($$extension));

    if($extension == "png")
        $img = str_replace('data:image/png;base64,', '', $img);    
    else if($extension == "jpeg")
        $img = str_replace('data:image/jpeg;base64,', '', $img);
    else {
        $res = ["code" => 1, "error" => $extension];    
        $response->getBody()->write(json_encode($res));
        return $response;
    }        

	$img = str_replace(' ', '+', $img);
	$data = base64_decode($img);
	$filename = $this->get('upload_directory') . $id . ".". $extension;
	$success = file_put_contents($filename, $data);

    $swipePhoto = DB::queryFirstRow("SELECT * FROM swipe_photos WHERE user_id=%d", $id);
    if(!$swipePhoto)      
        DB::insert('swipe_photos', ['user_id' => $_SESSION['user']['id'], 'image_name' => $id . "." . $extension]);                  
    else
        DB::update('swipe_photos', ['user_id' => $_SESSION['user']['id'], 'image_name' => $id . "." . $extension], 
                    "user_id=%d", $id);

    //return $this->view->render($response, 'photo.html.twig', [ 'photo' => '/uploads/swipe_photos/' . $id . "." . $extension ]);
    $res = ["code" => 0, "error" => "", "data" => '/uploads/swipe_photos/' . $id . "." . $extension . "?v=" . time() ];    
    $response->getBody()->write(json_encode($res));
    return $response;    
});

$app->post('/profile_photo', function ($request, $response, $args) {
    if(!check_user_session($response))
        return $response;

    $id = $_SESSION['user']['id'];        
    $json = $request->getBody();

    $item = json_decode($json, TRUE);

    $img = $item['data'];
    global $log;
    $log->debug(json_encode($img));

    $image_info = getimagesize($img);

    $extension = (isset($image_info["mime"]) ? explode('/', $image_info["mime"] )[1]: "");

    //$log->debug(json_encode($$extension));

    if($extension == "png")
        $img = str_replace('data:image/png;base64,', '', $img);    
    else if($extension == "jpeg")
        $img = str_replace('data:image/jpeg;base64,', '', $img);
    else {
        $res = ["code" => 1, "error" => $extension];    
        $response->getBody()->write(json_encode($res));
        return $response;
    }        

	$img = str_replace(' ', '+', $img);
	$data = base64_decode($img);
    $fname = $id . '_' . time();
	$filename = $this->get('upload_directory1') . $fname . ".". $extension;
	$success = file_put_contents($filename, $data);
   
    $insertId = 0;
    $album = DB::queryFirstRow("SELECT * FROM albums WHERE ownerId=%d", $id);
    if(!$album) {
        DB::insert('albums', ['ownerId' => $id]);      
        $insertId = DB::insertId();
    } else {
        $insertId = $album['id'];
    }    
    
    DB::insert('photo', ['name' => 'test', 'albumId' => $insertId, 'url' => '/uploads/profile_photos/' . $fname . "." . $extension ]);                  

    $res = ["code" => 0, "error" => ""];    
    $response->getBody()->write(json_encode($res));
    return $response;
});

$app->delete('/profile_photo', function ($request, $response, $args) {
    if(!check_user_session($response))
        return $response;

    $id = $_SESSION['user']['id'];        
    $json = $request->getBody();

    $item = json_decode($json, TRUE);

    $img = $item['url'];
    
    DB::delete('photo', 'url=%s', $img);                  

    $res = ["code" => 0, "error" => ""];
    $response->getBody()->write(json_encode($res));
    return $response;
});
