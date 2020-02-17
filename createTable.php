<?php
require_once 'login_db.php';
$conn1 = new mysqli($hn, $un, $pw, $db);

if ($conn1->connect_error)
    die(mysql_fatal_error());

$sql = "CREATE TABLE model (
    `ID` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `User` VARCHAR(20) NOT NULL,
    `Model Name` VARCHAR(50) NOT NULL,
    `Model Info` BINARY(20) NOT NULL
    )";

if ($conn->query($sql) === TRUE) {
    echo "Table created successfully<br>";
} else {
    echo "Error creating table";
}

$conn1->close();
?>