<?php

session_start();
if (isset($_SESSION['username']))
{

require_once 'login_db.php';
$conn = new mysqli($hn, $un, $pw, $db); 

if ($conn->connect_error) {
    die(mysql_fatal_error());
}
$username = $_SESSION['username'];
$query = "SELECT * FROM model WHERE (`User` LIKE '$username')";
$result = $conn->query($query);

if (!$result) 
    die(mysql_fatal_error());

else if ($result->num_rows == 0) {
        die(no_model_exists());
}
else if($result->num_rows > 0){


    echo <<<_END
<html><head><title>Testing - Clustering-based Unsupervised Learning</title></head><body>

<form method="post" action = "user_testing.php" enctype="multipart/form-data">
    <h2>Model Testing</h2>
    <br>Submit a text file:<br>
    <input required type="file" accept=".txt" name="filename"><br><br>

    
_END;

if ($result->num_rows > 0){

    echo "Select a model: ";
    echo "<select name = model_name>";

    while($row = $result->fetch_assoc()){
            
        $i = $row['Model Name'];
        echo ("<option value = $i>$i</option>");
            
    }

    echo "</select><br><br>";

}

echo <<<_END
<button type="submit" name="submit">Submit</button>
</form>
_END;

if($_FILES['filename']['name'] != ''){
    $name = $_FILES['filename']['name'];

    if($_FILES['filename']['type'] == 'text/plain')
        $ext = true;
    
    
    if($ext) {
        
        $data = sanitizeString(sanitizeMySQL($conn, file_get_contents($_FILES["filename"]['tmp_name'])));
        $model_name = $_POST['model_name'];
        $query = "SELECT * FROM model WHERE (`User` LIKE '$username' AND `Model Name` LIKE '$model_name')";
        $result_1 = $conn->query($query);
        

        if(is_numeric($data)){
            
            $data = str_replace("\n", " ", $data);
            $data = str_replace(",", " ", $data);
            $data = str_replace(", ", " ", $data);
            
            $num = converter($data);

            $array = $result_1->fetch_assoc();
            $num_cluster = $array['Model Info'];

            $output = shell_exec('k-means_clustering_algorithm.py ' . $num . $num_cluster);

            
            print_r("$output");
    
            
        }
        else
            die(file_error());
        $result_1->close();
    }
    else {
        echo "'$name' is not an accepted text file."; 
                
    }
}else
    echo "No file uploaded.";

            
    echo <<<_END
        <br><br><br>
        Log out:
        <button type="button" onclick="window.location.href = 'logout.php';">Log out</button>
        </body></html>
_END;
    $conn->close();

}

}
else
    session_expire();


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
    We are sorry.

    Please click the back button on your browser and try again.
    
    Thank you.
_END;
}

function no_model_exists(){
    echo <<<_END
    No model exists.
    <br><br>
    Click the button to train a model:
    <button type="button" onclick="window.location.href = 'user_training.php';">Train Model</button>
    <br>
    
_END;

}

function destroy_session_and_data()
	{
		session_start();
		$_SESSION = array();	
		setcookie(session_name(), '', time() - 2592000, '/');
		session_destroy();
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

function file_error(){
    echo <<<_END
    Uploaded file does not contain numbers.<br>

    Please <a href='user_training.php'>click here</a> to upload file.<br>

_END;
}

function converter($data){

    
    return explode(" ", $data);

}
$result->close();
?>