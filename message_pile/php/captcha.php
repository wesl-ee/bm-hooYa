<?php
/*
MicroTXT - A tiny PHP Textboard Software
Copyright (c) 2016 Kevin Froman (https://ChaosWebs.net/)

MIT License
*/
include('settings.php');

// Securely generate captcha
$captchaBytes = 3;

$solve = mt_rand(1, 2);
$max = 100;
$num1 = 0;
$num2 = 0;
$calcAnswer = 0;
if ($solve == 1){
  $num1 = mt_rand(0, $max);
  $num2 = mt_rand(0, $max);
  $calcAnswer = $num1 + $num2;
  $_SESSION['captchaVal'] = strval($calcAnswer);
  $text = 'Solve: ' . strval($num1) . ' + ' . strval($num2);
}
else{
  $text = bin2hex(openssl_random_pseudo_bytes($captchaBytes));
  $_SESSION['captchaVal'] = $text;
}

// Create a blank image and add some text
$im = imagecreatefromjpeg("captchabg.jpg");
$text_color = imagecolorallocate($im, 233, 14, 91);

  $linecolor = imagecolorallocate($im, 0xCC, 0xCC, 0xCC);
  // draw random lines on canvas
  for($i=0; $i < 6; $i++) {
    imagesetthickness($im, mt_rand(1,3));
    imageline($im, 0, mt_rand(0,30), 120, mt_rand(0,30), $linecolor);

  }

if ($solve == 1){
  imagestring($im, 5, 10, 10,  $text, $text_color);
}
else{
imagestring($im, 5, mt_rand(1, 100), mt_rand(1, 20),  $text, $text_color);
}

// Set the content type header - in this case image/jpeg
header('Content-Type: image/jpeg');

// Output the image
imagejpeg($im);

// Free up memory
imagedestroy($im);
?>
