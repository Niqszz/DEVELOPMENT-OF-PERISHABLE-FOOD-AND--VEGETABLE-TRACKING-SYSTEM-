<?php
$servername ='127.0.0.1';  // MySQL Host
$username ='root';     // MySQL Username
$password = '';
$database = "spoilprev";

$conn = mysqli_connect($hostname, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "Database connection is OK<br>";

if (isset($_POST["temperature"]) && isset($_POST["humidity"]) && isset($_POST["deviceId"])) {

    $t = $_POST["temperature"];
    $h = $_POST["humidity"];
    $deviceId = $_POST["deviceId"]; // Capture the deviceId sent from ESP32

    // SQL query to update the record where deviceId matches
        $sql = "UPDATE environment_sensor
                SET temperature = ".$t.", humidity = ".$h."
                WHERE deviceId = '".$deviceId."'";

    if (mysqli_query($conn, $sql)) {
        echo "\nRecord updated successfully for deviceId: ".$deviceId;
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}
?>
