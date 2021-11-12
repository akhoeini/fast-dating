<?php

require_once 'vendor/autoload.php';

require_once 'init.php';

use Slim\Http\UploadedFile;

$container['upload_directory'] = __DIR__ . '/uploads/swipe_photos';

function moveUploadedFile($directory, UploadedFile $uploadedFile)
{
    $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
    $basename = bin2hex(random_bytes(8)); // see http://php.net/manual/en/function.random-bytes.php
    $filename = sprintf('%s.%0.8s', $basename, $extension);

    $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

    return $filename;
}


$app->get('/photo1', function ($request, $response, $args) {
    return $this->view->render($response, 'photo.html.twig');
});

$app->post('/photo1', function ($request, $response, $args) {

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
