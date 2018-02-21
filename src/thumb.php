<?php 
if (!isset($_GET['img'])) header( 'Location: https://mangadex.com/' );
// The file you are resizing 
$file = $_GET['img']; 

// This sets it to a .jpg, but you can change this to png or gif 
header('Content-type: image/jpeg'); 

$thumbFile = preg_replace('/\\.[^.\\s]{3,4}$/', '', $file);

$large = false;

if(isset($_GET['size']) && $_GET['size'] === 'large') {
  $thumbFile .= '.large.jpg';
  $large = true;
}
else {
  $thumbFile .= '.thumb.jpg';
}

if (file_exists($thumbFile)) {
  readfile($thumbFile);
  exit;
}

// Setting the resize parameters
list($width, $height) = getimagesize($file); 


$modwidth = 100; 
if ($large) {
  $modwidth = 300;
}
$modheight = $modwidth / $width * $height; 
 
// Creating the Canvas 
$tn= imagecreatetruecolor($modwidth, $modheight); 
$value = explode(".", $_GET['img']);
$ext = strtolower(end($value));

switch ($ext) {
	case "jpg":
	case "jpeg":
		$image = ImageCreateFromJPEG($file); 
	break;
	
	case "png":
		$image = ImageCreateFromPNG($file); 
	break;
	
	case "gif":
		$image = ImageCreateFromGIF($file); 
	break;
	
	default:
		exit;
		
}
 
// Resizing our image to fit the canvas 
imagecopyresampled($tn, $image, 0, 0, 0, 0, $modwidth, $modheight, $width, $height); 
 
// Save to file
imagejpeg($tn, $thumbFile, 95);

//Free memory
imagedestroy($tn);

//Send file
readfile($thumbFile);
?> 