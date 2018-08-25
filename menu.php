<?php
include("account.php");
error_reporting(E_ERROR | E_PARSE | E_NOTICE);
ini_set( 'display_errors' , 1 );

$db = mysqli_connect($hostname, $username, $password , $project);

if (mysqli_connect_errno()){
	echo "Failed to connect to MySQL ... " . mysqli_connect_error();
	exit();
}

function displaymenu($u, $output) {
	global $db;
	$s = "SELECT * FROM A2 WHERE user = '$u'";
	($t = mysqli_query($db,$s)) or die (mysqli_error($db));
	
	//begin menu
	echo "<select name=\"account\">";
	
	while ($r = mysqli_fetch_array($t, MYSQLI_ASSOC)){
		$user = $r["user"];
		$pass = $r["pass"];	
		$account = $r["account"];
		$current = $r["current"];
		
		echo "<option value=\"$account\">";
		echo $account . ": $" . $current;
		echo "</option>";
	}
	echo "</select>";
}
?>