<?php
require_once 'login_db.php';
$conn = new mysqli($hn, $un, $pw, $db); 

if ($conn->connect_error) {
    die(mysql_fatal_error());
}

if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
    $un_temp = sanitizeString(sanitizeMySQL($conn, $_SERVER['PHP_AUTH_USER']));
    $pw_temp = sanitizeString(sanitizeMySQL($conn, $_SERVER['PHP_AUTH_PW']));
    
	$query = "SELECT * FROM user_info WHERE Username='$un_temp'";
	$result = $conn->query($query);

    if (!$result) 
        die(mysql_fatal_error());

	else if ($result->num_rows) 
		{
			$row = $result->fetch_array(MYSQLI_NUM);
			$result->close();
			$salt = $row[2];

			$token = hash('ripemd128', "$salt$pw_temp");

			if ($token == $row[3]) {
            
                session_start();
                
                $_SESSION['username'] = $un_temp;
                    
                login_success();

                
            } else {
                header("WWW-Authenticate: " .
                    "Basic realm=\"User Area2\"");
                header("HTTP/1.0 401 Unauthorized");
                die(password_fatal_error());
            }
        } else {
                header("WWW-Authenticate: " .
                    "Basic realm=\"User Area3\"");
                header("HTTP/1.0 401 Unauthorized");
                die(password_fatal_error());
        }
} else {
    header("WWW-Authenticate: " .
        "Basic realm=\"User Area4\"");
    header("HTTP/1.0 401 Unauthorized");
    die(password_fatal_error());
}

function sanitizeString($var) {
    $var = stripslashes($var);
    $var = strip_tags($var);
    $var = htmlentities($var);
    return $var;
}

function sanitizeMySQL($conn, $var) {
    $var = $conn->real_escape_string($var);
    $var = sanitizeString($var);
    return $var;
}


function mysql_fatal_error(){
	echo <<<_END
    We are sorry.<br>

    Please click the back button on your browser and try again.<br>
    
    Thank you.
_END;
}

function password_fatal_error(){
    
    echo <<<_END
    Invalid username / password combination.<br>

    Please <a href='login.php'>click here</a> to log in.<br>
    <br>
    Or<br><br>
    Please <a href='user_signup.php'>click here</a> to sign up.
_END;
}


function login_success(){

    header("Location: user_area.php");

}

$conn->close();
?>