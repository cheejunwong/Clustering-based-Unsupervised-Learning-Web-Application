<?php
session_start();

if (isset($_SESSION['username'])) {

    destroy_session_and_data();
    header("HTTP/1.0 401 Unauthorized");
    die(logout_success());

    $un = $_SERVER['PHP_AUTH_USER'];

    echo "$un";
}
else
    session_expire();

function destroy_session_and_data()
{
        $_SESSION = array();	
		setcookie(session_name(), '', time() - 2592000, '/');
        session_destroy();
}

function logout_success(){

    echo "Logged out<br><br>";
    echo "Please <a href='clustering-based_unsupervised_learning.php'>click here</a> to log in.";

}

function session_expire(){
    echo <<<_END
    Session expired.<br>

    Please <a href='login.php'>click here</a> to log in.<br>
    <br>
    Or<br><br>
    Please <a href='user_signup.php'>click here</a> to sign up.
_END;

}
?>