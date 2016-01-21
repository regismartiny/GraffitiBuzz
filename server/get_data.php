<?php
include_once "db.php";

if ( isset($_GET['id']) && isset($_GET['info']) ) {
    header('Access-Control-Allow-Origin: *');
    
    $id = $_GET['id'];
    $info = $_GET['info'];

    //consulta
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    if ($id != "*"){
        // do some validation here to ensure id is safe
        $id = intval($_GET['id']);
        $sql = "SELECT * FROM $tablename WHERE id=$id";
    }
    else{
        $sql = "SELECT * FROM $tablename";
    }
    
    if ($result = $conn->query($sql)) {
        $dataArray = array(array());
        $i = 0;
        while ($row = $result->fetch_assoc()) {
            $fileContent = $row["data"];
            //// thumbnail generation
            if ($info == '1') {
                    
                /// thumbnail genertion with disk caching - faster but uses more disk
                /*
                //md5($name.$date.$widthNew.$heightNew).“.jpg”  - for unique file identifier
                $file = 'tmp/'. uniqid() . '.jpg';
                $success = file_put_contents($file, $data);
                $thumbnail = exif_thumbnail($file, $width, $height, $type);
                $contents = base64_encode($thumbnail);
                */
                
                /// thumbnail generation in-memory
                $im = imagecreatefromstring($fileContent);
                
                // get originalsize of image
                $width = imagesx($im);
                $height = imagesy($im);
                
                // Set thumbnail-width to 250 pixel
                $imgw = 250;
                
                // calculate thumbnail-height from given width to maintain aspect ratio
                $imgh = $height / $width * $imgw;
                
                // create new image using thumbnail-size
                $thumb = imagecreatetruecolor($imgw, $imgh);
                
                // copy original image to thumbnail
                imagecopyresampled($thumb, $im, 0, 0, 0, 0, $imgw, $imgh, ImageSX($im), ImageSY($im));
                
                // show thumbnail on screen
                //header("Content-type: image/jpeg");
                //$out = imagejpeg($thumb);
                //print($out);
                ob_start();
                
                // Let's start output buffering.
                imagejpeg($thumb);
                
                //This will normally output the image, but because of ob_start(), it won't.
                $fileContent = ob_get_contents();
                
                //Instead, output above is saved to $contents
                ob_end_clean();
                
                //End the output buffer.
                // clean memory
                imagedestroy($im);
                imagedestroy($thumb);
                
                /// json enconde
                $dataArray = array('Id' => $row["id"], 'FileName' => $row["fileName"], 'FileSize' => $row["fileSize"], 'UploadTime' => $row["uploadTime"], 'CreationTime' => $row["jpgCreationTime"], 'Thumbnail' => base64_encode($fileContent), 'Orientation' => $row["jpgOrientation"], 'ImageWidth' => $row["jpgWidth"], 'ImageHeight' => $row["jpgHeight"], 'Make' => $row["jpgMake"], 'Software' => $row["jpgSoftware"], 'Model' => $row["jpgCameraModel"], 'Flash' => $row["jpgFlash"], 'SceneCaptureType' => $row["jpgSceneCaptureType"], 'GpsCoord' => $row["jpgGPSCoord"], 'Artist' => '');
            } 
            elseif($info == '2') 
            {    
                //return full image
                header("Content-type: image/jpg");
                echo ($fileContent);
            }
            elseif($info == '3')
            {
                $dataArray[$i] = array('id' => $row["id"], 'gpsCoord' => $row["jpgGPSCoord"]);
                $i++;
            }
        }
        $result->close();
        header("Content-type: text/javascript");
        echo json_encode($dataArray);
    } 
    else {
        echo json_encode("0 results");
    }
    $conn->close();
}
?>