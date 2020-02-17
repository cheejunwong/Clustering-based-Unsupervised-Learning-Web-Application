<?php
require_once 'login_db.php';
$conn1 = new mysqli($hn, $un, $pw, $db);

if ($conn1->connect_error)
    die(mysql_fatal_error());

$sql = "CREATE TABLE user_info (
    `ID` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `Username` VARCHAR(20) NOT NULL UNIQUE,
    `Salt` CHAR(10) NOT NULL,
    `Password` CHAR(32) NOT NULL,
    `Email` VARCHAR(50) NOT NULL
    )";

if ($conn->query($sql) === TRUE) {
    echo "Table created successfully<br>";
} else {
    echo "Error creating table";
}


$conn1->close();
?>