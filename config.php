<?php

$hostname = "http://localhost/news-site";

$username = "root";
$password = "";
$server = 'localhost';
$db = 'news_site';


$conn = mysqli_connect($server, $username, $password, $db);
// Check connection
// if (!$conn) {
//   die("Connection failed: " . mysqli_connect_error());
// }

?>
