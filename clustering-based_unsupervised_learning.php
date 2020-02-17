<?php

require_once 'login_db.php';
$conn = new mysqli($hn, $un, $pw, $db); 

if ($conn->connect_error) {
    die(mysql_fatal_error());
}

if(!$conn->query("DESCRIBE `user_info`")) {
    require_once "createTable_admin.php";
}

echo <<<_END
<html><head><title>Clustering-based Unsupervised Learning</title></head><body>
<h1>Clustering-based Unsupervised Learning</h1>
<p>
<button type="button" value = "login" onclick="window.location.href = 'login.php';">User Log In</button>

<br>
<br>
<button type="button" onclick="window.location.href = 'user_signup.php';">User Sign Up</button>

</p>
</body></html>
_END;

function sanitizeString($var) {
    $var = stripslashes($var);
    $var = strip_tags($var);
    $var = htmlentities($var);
    return $var;
}

function sanitizeMySQL($connection, $var) {
    $var = $connection->real_escape_string($var);
    $var = sanitizeString($var);
    return $var;
}

function mysql_fatal_error(){
	echo <<<_END
    We are sorry.

    Please click the back button on your browser and try again.
    
    Thank you.
_END;
}

    
$conn->close();
?>