<?php
include_once "db.php";

//////////////////////////////////////////GLobal variables declaration and inicialization/////////////////////////////////////////////
// +-4,2MB max file size
$MAX_FILE_SIZE = 4404019;
$SAVE_PATH = "uploads/";

$TIMEZONE = "America/Sao_Paulo";

//gif|jpg|png
$ALLOWED_FORMATS = "jpg|jpeg";

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////Utilitary functions//////////////////////////////////////////////////////
function gps($coordinate, $hemisphere) {
    if ($coordinate != "undefined") {
        for ($i = 0; $i < 3; $i++) {
            $part = explode('/', $coordinate[$i]);
            if (count($part) == 1) $coordinate[$i] = $part[0];
            else if (count($part) == 2) $coordinate[$i] = floatval($part[0]) / floatval($part[1]);
            else $coordinate[$i] = 0;
        }
        list($degrees, $minutes, $seconds) = $coordinate;
        $sign = ($hemisphere == 'W' || $hemisphere == 'S') ? -1 : 1;
        return $sign * ($degrees + $minutes / 60 + $seconds / 3600);
    } 
    else return "$coordinate";
}

function getExifData($image, $exifattribute) {
    $exif = exif_read_data($image);
    if (isset($exif[$exifattribute])) $exifData = $exif[$exifattribute];
    else $exifData = "undefined";
    return $exifData;
}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////Main process///////////////////////////////////////////////////////////////
// Assure necessary variables are set
if (!isset($_POST)) echo "No '_POST' set";
elseif (!isset($_FILES['photos'])) echo "No 'photos' set";
elseif (!$_SERVER['REQUEST_METHOD'] == "POST") echo "REQUEST_METHOD is not POST";
else {
    
    // Loop $_FILES to execute all files
    for ($i = 0; $i < count($_FILES['photos']['name']); $i++) {
        $fileName = $_FILES['photos']['name'][$i];
        
        // The file name
        $fileTmpLoc = $_FILES['photos']['tmp_name'][$i];
        
        // File in the PHP tmp folder
        $fileType = $_FILES['photos']['type'][$i];
        
        // The type of file it is
        $fileSize = $_FILES['photos']['size'][$i];
        
        // File size in bytes
        $fileErrorMsg = $_FILES['photos']['error'][$i];
        
        // 0 for false... and 1 for true
        // Specific Error Handling if you need to run error checking
        if ($fileErrorMsg == 4) {
            continue;
            
            // Skip file if any error found
            
        } 
        elseif (!$fileTmpLoc) {
            
            // if file not chosen
            echo "ERROR: Please browse for a file before clicking the upload button.";
            exit();
        } 
        else if ($fileSize > $MAX_FILE_SIZE) {
            
            // if file is larger than we want to allow
            echo "ERROR: Your file was larger than $MAX_FILE_SIZE in file size.";
            unlink($fileTmpLoc);
            exit();
        } 
        else if (!preg_match("/.($ALLOWED_FORMATS)$/i", $fileName)) {
            
            // This condition is only if you wish to allow uploading of specific file types
            echo "ERROR: Your image was not $ALLOWED_FORMATS.";
            unlink($fileTmpLoc);
            exit();
        } 
        else if (!getimagesize($fileTmpLoc)) {
            
            // Check if image file is a actual image or fake image
            echo "File is not an image.";
            unlink($fileTmpLoc);
            exit();
        } 
        else {
            
            /*
            // Place it into your "uploads" folder move using the move_uploaded_file() function
            move_uploaded_file($fileTmpLoc, "$SAVE_PATH$fileName");
            // Check to make sure the uploaded file is in place where you want it
            if (!file_exists("$SAVE_PATH$fileName")) {
                echo "ERROR: File not uploaded<br /><br />";
                echo "Check folder permissions on the target uploads folder is 0755 or looser.<br /><br />";
                echo "Check that your php.ini settings are set to allow over 2 MB files, they are 2MB by default.";
                exit();
            }
            //$image = "$SAVE_PATH$fileName";
            // Display things to the page so you can see what is happening for testing purposes
            echo "<br><br>The file named <strong>$fileName</strong> uploaded successfuly.<br />";
            echo "It is <strong>$fileSize</strong> bytes in size.<br />";
            echo "It is a <strong>$fileType</strong> type of file.<br />";
            echo "The Error Message output for this upload is: $fileErrorMsg<br /><br />";
            */
            
            //Read and show JPG Metadata
            $image = $fileTmpLoc;
            
            //Thumbnail generation
            $thumbnail = exif_thumbnail($image, $width, $height, $type);
            echo "<br><br><img  width='$width' height='$height' src='data:image;base64," . base64_encode($thumbnail) . "'>";
            
            ///show all exif data
            /*$exif = exif_read_data($image, 'ANY_TAG', true);
            foreach ($exif as $key => $section) {
                echo "<p>$key:</p>";
                foreach ($section as $name => $val) {
                        echo "$key.$name: $val<br>";
                        if (count($val) > 1) {
                            foreach ($val as $subname => $subval) {
                                echo "$key.$name.$subname: $subval<br>";
                            }
                        }
                }
            }*/
            $lon = gps(getExifData($image, "GPSLongitude"), getExifData($image, 'GPSLongitudeRef'));
            $lat = gps(getExifData($image, "GPSLatitude"), getExifData($image, 'GPSLatitudeRef'));
            $gpscoord = "$lat,$lon";
            $datetimeorig = getExifData($image, "DateTimeOriginal");
            $orientation = getExifData($image, "Orientation");
            $imagelength = getExifData($image, "ExifImageLength");
            $imagewidth = getExifData($image, "ExifImageWidth");
            $make = getExifData($image, 'Make');
            $software = getExifData($image, 'Software');
            $model = getExifData($image, "Model");
            $artist = getExifData($image, 'Artist');
            $flash = getExifData($image, "Flash");
            $scenecapturetype = getExifData($image, "SceneCaptureType");
            date_default_timezone_set($TIMEZONE);
            $uploadtime = date('Y:m:d H:i:s', time());;
            $metadados = "<p>Arquivo: $fileName<br>Tamanho: $fileSize bytes<br>Data criacao: $datetimeorig" . "<br>Orientacao: $orientation<br>Largura: $imagewidth<br>Altura: $imagelength" . "<br>Fabricante: $make<br>Software: $software<br>Modelo da camera: $model" . "<br>Artist: $artist<br>Flash: $flash<br>SceneCaptureType: $scenecapturetype" . "<br>Coord. GPS: <a href='http://www.google.com/maps/place/$gpscoord'>$gpscoord<a/></p>";
            echo "$metadados";
            
            //Load File
            $fp = fopen($image, 'r');
            $content = fread($fp, filesize($image));
            $content = addslashes($content);
            fclose($fp);
            
            //delete temporary file
            unlink($fileTmpLoc);
            
            //Save data to Database
            // Create connection
            $conn = new mysqli($servername, $username, $password, $dbname);
            
            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            $sql = "INSERT INTO $tablename (fileName, fileSize, uploadTime, data, jpgCreationTime, jpgOrientation, jpgWidth, jpgHeight, jpgMake, jpgSoftware, jpgCameraModel, jpgFlash, jpgSceneCaptureType, jpgGPSCoord)
            VALUES ('$fileName','$fileSize','$uploadtime','$content','$datetimeorig','$orientation','$imagewidth','$imagelength','$make','$software','$model','$flash','$scenecapturetype','$gpscoord')";
            
            //
            if ($conn->query($sql) === TRUE) {
                echo "Upload concluído com sucesso";
            } 
            else {
                echo "Erro: " . $sql . "<br>" . $conn->error;
            }
            $conn->close();
        }
    }
}
?>