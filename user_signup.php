<?php

require_once 'login_db.php';
$conn = new mysqli($hn, $un, $pw, $db); 

if ($conn->connect_error) {
    die(mysql_fatal_error());
}

if(!$conn->query("DESCRIBE `user_info`")) {
    require_once "createTable_user.php";
}

$username = $email = $password = "";


if(isset($_POST['username']))
    $username = sanitizeMySQL($conn, $_POST['username']);

if(isset($_POST['email']))
    $email = sanitizeMySQL($conn, $_POST['email']);

if(isset($_POST['password']))
    $password = sanitizeMySQL($conn, $_POST['password']);

$fail = validateEmail($email);
$fail .= validatePassword($password);
$fail .= validateUsername($username);

echo "<html><head><title>Sign Up - Clustering-based Unsupervised Learning</title>";

if($fail ==  ""){
    $salt = generateSalt();
    $token = hash('ripemd128', "$salt$password");

    $stmt = $conn->prepare("INSERT INTO user_info (`Username`, `Salt`, `Password`, `Email`) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('ssss', $username, $salt, $token, $email);


    if (!$stmt->execute()) {
        die(signup_error());
    } 
    else                
        signup_success();
    
    $stmt->close();
    exit;
}

echo <<<_END
<h1>Sign Up</h1>
<script>
	function validate(form) {
		fail = validateUsername(form.username.value)
		fail += validatePassword(form.password.value)
		fail += validateEmail(form.email.value)
		
		if (fail == "") return true
		else { alert(fail); return false }
	}

function validateUsername(field)
{
	if (field == "") return "No Username was entered.\\n";
	else if (field.length < 5)
		return "Usernames must be at least 5 characters.\\n"
	else if (/[^a-zA-Z0-9_-]/.test(field))
		return "Only a-z, A-Z, 0-9, - and _ allowed in Usernames.\\n"
	return ""
}

function validatePassword(field)
{
	if (field == "") return "No Password was entered.\\n"
	else if (field.length < 6)
		return "Password must be at least 6 characters.\\n"
	else if (!/[a-z]/.test(field) || !/[A-Z]/.test(field) || !/[0-9]/.test(field))
		return "Password required one each of a-z, A-Z and 0-9.\\n"
	return ""
}

function validateEmail(field)
{
	if (field == "") return "No Email was entered.\\n"
	else if (!((field.indexOf(".") > 0) && (field.indexOf("@") > 0)) || /[^a-zA-Z0-9.@_-]/.test(field))
		return "Email address is invalid.\\n"
	return ""
}

</script>
</head>
<body>
<form method="post" action = "user_signup.php" onSubmit = "return validate(this);">
    <br>Username:<br>
    <input required type="text" maxlength = "20" name="username"><br><br>
    <br>E-mail:<br>
    <input required type="text" maxlength = "50" name="email"><br><br>
    <br>Password:<br>
    <input required type="password" maxlength = "32"  name="password">
    <br>Must contain at least one number and one uppercase and lowercase letter, and at least 6 or more characters<br>
    <br><br>
    <button type="submit" name="submit">Submit</button>
</form>
</body>
_END;

function generateSalt() {
    $length = 10;
    $charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789/\\][{}\'";:?.>,<!@#$%^&*()-_=+|';

    $saltString = "";

    for ($i = 0; $i < $length; $i++) {
        $saltString .= $charset[mt_rand(0, strlen($charset) - 1)];
    }

    return $saltString;
}

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
    We are sorry.<br>

    Please click the back button on your browser and try again.<br>
    
    Thank you.
_END;
}

function signup_error(){

    echo "Invalid username. Please reload the page and try again.";

}

function signup_success(){

    echo <<<_END
    Account created. Thank you.
    <br>
    <br>
    Click here to login:
    <br>
    <button type="button" onclick="window.location.href = 'login.php';">User Log in</button>

_END;
}

function validateUsername($field)
{
	if ($field == "") return "No Username was entered.<br>";
	else if (strlen($field) < 5)
		return "Usernames must be at least 5 characters.<br>";
	else if (preg_match("/[^a-zA-Z0-9_-]/", $field))
		return "Only a-z, A-Z, 0-9, - and _ allowed in Usernames.<br>";
	return "";
}

function validatePassword($field)
{
	if ($field == "") return "No Password was entered.<br>";
	else if (strlen($field)< 6)
		return "Password must be at least 6 characters.<br>";
    else if (!preg_match("/[a-z]/", $field) || 
             !preg_match("/[A-Z]/", $field) || 
             !preg_match("/[0-9]/", $field))
		return "Password required one each of a-z, A-Z and 0-9.<br>";
	return "";
}

function validateEmail($field)
{
	if ($field == "") return "No Email was entered.<br>";
    else if (!((strpos($field, ".") > 0) && 
            (strpos($field, "@") > 0)) || 
            preg_match("/[^a-zA-Z0-9.@_-]/", $field))
		return "Email address is invalid.<br>";
	return "";
}



echo '</html>';

$conn->close();
?>