<?php

include_once "db.php";
//consulta
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}
$sql = "SELECT * FROM $tablename";
if ($result = $conn->query($sql)) {
	$n = 0;
	echo '<div id="map-wrap" style="width:1200px;height:500px"><p>Pesquisando as artes mais próximas de você...</p></div><div id="dom-target" style="display: none;">';

    /* for testing -shows all data in database
    echo '<table align="center" valign="center" border="">';
    echo "<th>id</th><th>fileName</th><th>fileSize</th><th>creationTime</th><th>data</th><th>jpgOrientation</th><th>jpgWidth</th><th>jpgHeight</th><th>jpgMake</th><th>jpgSoftware</th><th>jpgCameraModel</th><th>jpgFlash</th><th>jpgSceneCaptureType</th><th>jpgGPSCoord</th><th>ownerName</th><th>ownerEmail</th>";
    */
    
    /* fetch associative array */
    while ($row = $result->fetch_assoc()) {
    	echo '<div id="id_' . $n . '">' . $row["id"] . '</div><div id="jpgGPS_' . $n . '">' . $row["jpgGPSCoord"] . '</div>';

        /* for testing -shows all data in database
        echo "<tr>";
        echo "<td>".$row["id"]."</td><td>".$row["fileName"]."</td><td>".$row["fileSize"]."</td><td>".$row["creationTime"]."</td><td></td><td>".$row["jpgOrientation"]."</td><td>".$row["jpgWidth"]."</td><td>".$row["jpgHeight"]."</td><td>".$row["jpgMake"]."</td><td>".$row["jpgSoftware"]."</td><td>".$row["jpgCameraModel"]."</td><td>".$row["jpgFlash"]."</td><td>".$row["jpgSceneCaptureType"]."</td><td>".$row["jpgGPSCoord"]."</td><td>".$row["ownerName"]."</td><td>".$row["ownerEmail"]."</td>";
        echo "</tr>";
        */
        $n++;
    }
    echo '<div id="dom-target-end" style="display: none;"></div></div>';
    
    //echo "</table>";
    /* free result set */
    $result->close();
} 
else {
  	echo "0 results";
}
$conn->close();

?>