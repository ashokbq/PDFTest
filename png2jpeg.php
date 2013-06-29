<?php

if( isset($_GET["path"]) && $_GET["path"] != "" &&
    file_exists("docs/".$_GET["path"]) &&                                   // file need to exist
    basename(realpath("docs/" . $_GET["path"])) == $_GET["path"] &&         // basename needs to equal filename
    strlen($_GET["path"]) < 100 &&                                          // less than 100 chars allowed in path
    preg_match("=^[^/?*;:{}\\\\]+\.png+$=", $_GET["path"]))                 // no funny characters allowed and needs to end with .png
{
    $path = "docs/" . $_GET["path"];

    //getting extension type (jpg, png, etc)
    $type = explode(".", $path);
    $ext = strtolower($type[sizeof($type)-1]);
    $ext = (!in_array($ext, array("jpeg","png","gif"))) ? "jpeg" : $ext;
    
	// get the sector in question
	$sector = $_GET["sector"];
    $image_filename = basename($path);

	if( isset($_GET["sector"])){ // if trying to get a sector/slice of the file
        $cachefilename = dirname($path) . "/" . substr($image_filename,0,strripos($image_filename,".")) . "_" . $sector . ".jpeg";

        if(!file_exists($cachefilename)){
            //get image size
            $size = getimagesize($path);
            $width = $size[0];
            $height = $size[1];

            //get source image
            $func = "imagecreatefrom".$ext;
            $source = $func($path);

            //setting default values
            $new_width = $width * .25;
            $new_height = $height * .25;
            $k_w = 1;
            $k_h = 1;
            $src_x =0;
            $src_y =0;
            $margin_x =0;
            $margin_y =0;

            switch($sector){
                // top 50%, left 50%
                case "l1t1":
                    $src_x = 0;
                    $src_y = 0;
                break;
                case "l2t1":
                    $src_x = $width * .25;
                    $src_y = 0;
                break;
                case "l1t2":
                    $src_x = 0;
                    $src_y = $height * .25;
                break;
                case "l2t2":
                    $src_x = $width * .25;
                    $src_y = $height * .25;
                break;

                // top 50%, right 50%
                case "r1t1":
                    $src_x = $width * .5;
                    $src_y = 0;
                break;
                case "r2t1":
                    $src_x = $width * .75;
                    $src_y = 0;
                break;
                case "r1t2":
                    $src_x = $width * .5;
                    $src_y = $height * .25;
                break;
                case "r2t2":
                    $src_x = $width * .75;
                    $src_y = $height * .25;
                break;

                //bottom 50%, left 50%
                case "l1b1":
                    $src_x = 0;
                    $src_y = $height * .5;
                break;
                case "l2b1":
                    $src_x = $width * .25;
                    $src_y = $height * .5;
                break;
                case "l1b2":
                    $src_x = 0;
                    $src_y = $height * .75;
                break;
                case "l2b2":
                    $src_x = $width * .25;
                    $src_y = $height * .75;
                break;

                // bottom 50%, right 50%
                case "r1b1":
                    $src_x = $width * .5;
                    $src_y = $height * .5;
                break;
                case "r2b1":
                    $src_x = $width * .75;
                    $src_y = $height * .5;
                break;
                case "r1b2":
                    $src_x = $width * .5;
                    $src_y = $height * .75;
                break;
                case "r2b2":
                    $src_x = $width * .75;
                    $src_y = $height * .75;
                break;
            }

            // adjusting for rounding
            $margin_x = $src_x - floor($src_x);
            $margin_y = $src_y - floor($src_y);

            $output = imagecreatetruecolor( $new_width, $new_height	);

            imagecopyresampled( $output, $source,  0, 0, $src_x-(1-$margin_x), $src_y-(1-$margin_y),
                                $new_width, $new_height,
                                $width * .25+(1-$margin_x)*2, $height * .25+(1-$margin_y)*2);
            //free resources
            ImageDestroy($source);

            //output image header
            header('Content-Type: image/jpeg');

            // try to output the image
            if (is_writable(dirname($path))) {
                imagejpeg($output, $cachefilename);
            }
            imagejpeg($output);

            //free resources
            ImageDestroy($output);
        }else{
            header('Content-Type: image/jpeg');
            echo file_get_contents($cachefilename);
        }
	}else{
	    $cachefilename = dirname($path) . "/" . substr($image_filename,0,strripos($image_filename,".")) . ".jpeg";

        if(!file_exists($cachefilename)){
            $size = getimagesize($path);
            $width = $size[0];
            $height = $size[1];

            //get source image
            $func = "imagecreatefrom".$ext;
            $source = $func($path);

            $output = imagecreatetruecolor( $width, $height	);
            imagecopyresampled( $output, $source, 0, 0, 0, 0, $width, $height, $width, $height );

            //free resources
            ImageDestroy($source);

            //output image header
            header('Content-Type: image/jpeg');

            if (is_writable(dirname($path))) {
                imagejpeg($output, $cachefilename);
            }
            imagejpeg($output);

            //free resources
            ImageDestroy($output);
        }else{
            header('Content-Type: image/jpeg');
            echo file_get_contents($cachefilename);
        }
	}
}
?>
