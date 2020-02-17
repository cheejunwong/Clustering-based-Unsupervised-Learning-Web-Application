<?php
session_start();

if (isset($_SESSION['username'])) {
echo <<<_END
<html><head><title>Home - Clustering-based Unsupervised Learning</title></head><body>
<h1>Clustering-based Unsupervised Learning</h1>
<br><br>
<h3>Select a mode:</h3>
<p>
<button type="button" onclick="window.location.href = 'user_training.php';">Training</button>

<br>
<br>
<button type="button" onclick="window.location.href = 'user_testing.php';">Testing</button>

</p>

</body></html>

<br><br><br>
Log out:
<button type="button" onclick="window.location.href = 'logout.php';">Log out</button>
_END;
}
else
    session_expire();

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