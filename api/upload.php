<?php
$targetDir = "../uploads/articles/";
$fileName = time() . "_" . basename($_FILES["file"]["name"]);
$targetFile = $targetDir . $fileName;

$ext = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
$allowed = ['jpg', 'jpeg', 'png'];

if (in_array($ext, $allowed)) {
    move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile);
    echo "Uploaded";
} else {
    echo "Invalid file";
} 
