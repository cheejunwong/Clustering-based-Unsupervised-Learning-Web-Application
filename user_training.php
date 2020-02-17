<?php
session_start();
if (isset($_SESSION['username']))
{
require_once 'login_db.php';
$conn = new mysqli($hn, $un, $pw, $db); 

if ($conn->connect_error) {
    die(mysql_fatal_error());
}

if(!$conn->query("DESCRIBE `model`")) {
    require_once "createTable.php";
}


echo <<<_END
<html><head><title>Model Training - Clustering-based Unsupervised Learning</title></head><body>
<script>
function validate(form) {
    fail = validateNum(form.scores_text.value)
    fail += validateNum(form.num_cluster.value)

    if (fail == "") return true
    else { alert(fail); return false }
}

function validateNum(field)
{
	if (field == "") return "No input was entered.\\n"
	else if (!/[a-z]/.test(field) || !/[A-Z]/.test(field) || !/[0-9]/.test(field))
		return "Inputs are numbers only.\\n"
	return ""
}
</script>


<form method="post" action = "user_training.php" onSubmit = "return validate(this);">
    <h2>Model Training</h2>
    Submit a text file:<br>
    <input required type="file" accept=".txt" name="filename"><br><br>
    <br>Model name:<br>
    <input required type="text" name="model_name"><br><br>
    <br>Number of cluster:<br>
    <input required type="text" name="num_cluster"><br><br>
    <button type="submit" name="submit_file">Submit</button>
</form>

_END;


if($_FILES['filename']['name'] != ''){
    $name = $_FILES['filename']['name'];

    if($_FILES['filename']['type'] == 'text/plain')
        $ext = true;
    
    
    if($ext) {
        
        $data = sanitizeString(sanitizeMySQL($conn, file_get_contents($_FILES["filename"]['tmp_name'])));
        $model_name = sanitizeString(sanitizeMySQL($conn, $_POST['model_name']));
        $num_cluster = sanitizeString(sanitizeMySQL($conn, $_POST['num_cluster']));

        if(is_numeric($data)){
            
            $data = str_replace("\n", " ", $data);
            $data = str_replace(",", " ", $data);
            $data = str_replace(", ", " ", $data);

            $num = converter($data);

            $output = shell_exec('k-means_clustering_algorithm.py ' . $num . $num_cluster);

            $stmt = $conn->prepare("INSERT INTO model (`User`, `Model Name`, `Model Info`)VALUES (?, ?, ?)");
            $stmt->bind_param('sss', $_SESSION['username'], $model_name, $num_cluster);
            $stmt->execute();
            
            print_r($output);
            echo "Model inserted";
    
            $stmt->close();
        }
        else
            die(file_error());

    }
    else {
        echo "'$name' is not an accepted text file."; 
                
    }
}else
    echo "No file uploaded.";



echo <<<_END
<br><br><p><b>OR</b></p><br>

<form method="post" action = "user_training.php" onSubmit = "return validate(this);">
    Submit scores:<br>
    <input required type="text" name="scores_text"><br>
    <br>Model name:<br>
    <input required type="text" name="model_name"><br><br>
    <br>Number of cluster:<br>
    <input required type="text" name="num_cluster"><br><br>
    <button type="submit" name="submit_text">Submit</button>
    
</form>

_END;

$data = sanitizeString(sanitizeMySQL($conn, $_POST['scores_text']));
$model_name = sanitizeString(sanitizeMySQL($conn, $_POST['model_name']));
$num_cluster = sanitizeString(sanitizeMySQL($conn, $_POST['num_cluster']));

        if(is_numeric($data)){
            
            $data = str_replace("\n", " ", $data);
            $data = str_replace(",", " ", $data);
            $data = str_replace(", ", " ", $data);

            $num = converter($data);

            $output = shell_exec('k-means_clustering_algorithm.py ' . $num . $num_cluster);

            $stmt = $conn->prepare("INSERT INTO model (`User`, `Model Name`, `Model Info`)VALUES (?, ?, ?)");
            $stmt->bind_param('sss', $_SESSION['username'], $model_name, $num_cluster);
            $stmt->execute();
            
            print_r($output);
            echo "Model inserted";
    
            $stmt->close();
        }

 
echo <<<_END
<br><br><br>
Log out:
<button type="button" onclick="window.location.href = 'logout.php';">Log out</button>
</body></html>
_END;

$conn->close();


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

function validateNum($field)
{
	if ($field == "") return "No text was entered.<br>";
    else if (!preg_match("/[0-9]/", $field))
		return "Inputs are numbers only<br>";
	return "";
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

?>