<?php
function LoadGif($imgname)
{
    /* Attempt to open */
    $im = @imagecreatefromgif($imgname);

    /* See if it failed */
    if(!$im)
    {
        /* Create a blank image */
        $im = imagecreatetruecolor (150, 30);
        $bgc = imagecolorallocate ($im, 255, 255, 255);
        $tc = imagecolorallocate ($im, 0, 0, 0);

        imagefilledrectangle ($im, 0, 0, 150, 30, $bgc);

        /* Output an error message */
        imagestring ($im, 1, 5, 5, 'Error loading ' . $imgname, $tc);
    }

    return $im;
}

header('Content-Type: image/gif');

$img = LoadGif('http://staging.flashartofwar.com/wp-content/themes/flashartofwar/images/thumbnails/thumb4.gif');

imagegif($img);
imagedestroy($img);
?>