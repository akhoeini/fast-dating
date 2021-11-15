<?php

require_once 'vendor/autoload.php';

require_once 'init.php';

function verifyUploadedPhoto($photo, &$fileName) {
    if ($photo->getError() !== UPLOAD_ERR_OK) {
        return "Error uploading photo " . $photo->getError();
    }
    if ($photo->getSize() > 1024*1024) { // 1MB max
        return "File too big. 1MB max is allowed";
    }
    $info = getimagesize($photo->file);
    if (!$info) {
        return "File is not an image";
    }
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
    $filenameWithoutExtension = pathinfo($photo->getClientFilename(), PATHINFO_FILENAME);
    // Note: keeping the original extension is dangerious and would allow for code injection - very dangerous
    $sanitizedFileName = mb_ereg_replace('([^A-Za-z0-9_-])', '_', $filenameWithoutExtension);
    $fileName = 'uploads/' . $sanitizedFileName . "." . $ext;
    return TRUE;
}

function debug_to_console($data) {
    /*
    $output = $data;
    if (is_array($output))
        $output = json_encode($output);

    echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
    */
}

function check_user_session(&$response){    
    if (!isset($_SESSION['user'])) { // refuse if user not logged in
        $response = $response->withStatus(301);
        $res = ["code" => 1, "error" => "session ends"];    
        $response->getBody()->write(json_encode($res));
        return false;
    }
    return true;
}

function get_swipe_photo($userId) {
    $ret = "#";
    $swipePhoto = DB::queryFirstRow("SELECT * FROM swipe_photos WHERE user_id=%d", $userId);
    if($swipePhoto) {
        $ret = "/uploads/swipe_photos/" . $swipePhoto["image_name"] . "?v=" . time();
    } 
    return $ret;
}
