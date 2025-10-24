<?php
// config.php
$host = 'localhost';
$user = 'root';
$pass = ''; // shyiramo password ya DB niba ihari
$db = 'mordekai';

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
