<?php
session_start();
include ( "account.php" ) ;
include ( "fn.php" ) ;

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors' , 1);

if (mysqli_connect_errno()){
	echo "Failed to connect to MYSQL ..." . mysqli_connect_error();
	exit();
}
echo "Attempting login ...<br><br>";

$user = $_GET['user'];
$pass = $_GET['pass'];
$delay = $_GET['delay'];

echo "Username: $user<br>";
echo "Wait $delay seconds<br>";

if (! auth($user,$pass)){
    redirect ("Incorrect login information - redirecting to login page.", "login.html", $delay);
}
else {
    global $db;
    $s = "SELECT * FROM A WHERE user='$user' and pass='$pass';";
    $t = mysqli_query($db,$s) or die(mysqli_error($db));
    $r = mysqli_fetch_array($t,MYSQLI_ASSOC);


    $_SESSION["logged"] = true;
    $_SESSION["user"]	= $user;
    $_SESSION["current_Balance"] = $r['current'];
    $_SESSION["email"] = $r["mail"];


    redirect ("You are now logged in. Redirecting to application form ...","transactionform.php",$delay);
}
?>