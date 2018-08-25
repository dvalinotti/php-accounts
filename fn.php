<?php
session_start();
include ("account.php");
error_reporting(E_ERROR | E_PARSE | E_NOTICE);
ini_set( 'display_errors' , 1 );

$db = mysqli_connect($hostname, $username, $password , $project);

if (mysqli_connect_errno()){
	echo "Failed to connect to MySQL ... " . mysqli_connect_error();
	exit();
}

function auth ($user, $pass){
    global $db;
	$pass = mysqli_real_escape_string($db, $pass);
	$password_hash = sha1($pass);
	
    $s = "SELECT * FROM A2 WHERE user = '$user' AND passHash='$password_hash'";
    $t = mysqli_query($db,$s) or die(mysqli_error($db));
    $num = mysqli_num_rows($t);
	
	if ($num > 0) {
		return true;
    }
    else {
		return false;
    }
}

function redirect ($message, $targetfile, $delay){
	echo "<br>$message<br>";
	header( "refresh:$delay; url=".$targetfile );
	exit();
}

function gatekeeper(){
	if ( !(isset($_SESSION["logged"])) )
	{
		redirect ("Access denied, redirecting ...", "login.html", "5");
	}

}

function show($u, &$output, $mail_flag, $a, $num){
	global $db;
	$s = "SELECT * FROM A2 WHERE account='$a' AND user='$u'";
	($t = mysqli_query($db,$s)) or die (mysqli_error($db));
	//$num = mysqli_num_rows($t); 
	//echo "<br>num is $num<br>";

	$output.="<br><table border=1>
		  <tr>
		  <th>User</th>
		  <th>Password</th>
		  <th>Initial Balance</th>
		  <th>Current Balance</th>
		  <th>Recent Transaction</th>
		  <th>Account</th>
		  <th>PassHash</th>
		  </tr>";
	
	while ($r = mysqli_fetch_array($t, MYSQLI_ASSOC)){
		$user = $r["user"];
		$pass = $r["pass"];	
		$initial = $r["initial"];
		$current = $r["current"];
		$recent_trans = $r["recent_trans"];
		$account = $r["account"];
		$passHash = $r["passHash"];
		
		//$output.="Username: $user | Password: $pass | Initial Balance: $initial | Current Balance: $current | Most Recent Transaction: $recent_trans | Account: $account<br>";
		$output.="Account Information:<tr><td>" . $user . "</td>" .
			"<td>" . $pass . "</td>" .
			"<td>" . $initial . "</td>" .
			"<td>" . $current . "</td>" .
			"<td>" . $recent_trans . "</td>".
			"<td>" . $account . "</td>".
			"<td>" . $passHash . "</td>";
			
	}
	$output.="</tr></table><br><br>";
	if (empty($num)){ 
		$s2 = "SELECT * FROM T2 WHERE account = '$a' AND user = '$u'";
	}
	else {
		$s2 = "SELECT * FROM T2 WHERE account = '$a' AND user = '$u' LIMIT $num";
	}
	($t2 = mysqli_query($db,$s2)) or die (mysqli_error($db));
	$num2 = mysqli_num_rows($t2); 
	//echo "<br>num is $num2<br>";
	
	if ($num2 > 0) {
		$output.="Transactions:<table border=1>
		  <tr>
		  <th>User</th>
		  <th>Type</th>
		  <th>Amount</th>
		  <th>Date</th>
		  <th>Mail Receipt</th>
		  <th>Account</th>
		  </tr>";
		
		while ($r2 = mysqli_fetch_array($t2, MYSQLI_ASSOC)){
			$user = $r2["user"];
			$type = $r2["type"];
			$amount = $r2["amount"];
			$date = $r2["date"];
			$mail_receipt = $r2["mail_receipt"];
		
	
			//$output.="Username: $user | Transaction Type: $type | Amount: $amount | Date: $date | Mail Receipt: $mail_receipt<br>";
			$output.="<tr>
				  <td>" . $user . "</td>" .
				  "<td>" . $type . "</td>" .
				  "<td>" . $amount . "</td>" .
				  "<td>" . $date . "</td>" .
				  "<td>" . $mail_receipt . "</td>".
				  "<td>" . $account . "</td>";
		}
	}
	
	$output.="</tr></table";
	if($mail_flag == "Yes"){
		mailer($u, $output);
	}
}

function deposit ($u, $amnt, $mail_flag, $a){
	$trans_date = date('Y-m-d H:i:s');
	
	if ($a < 0){
		$message = "Deposit amount cannot be negative.";
		echo "<script type='text/javascript'>alert('$message');</script>";
		exit();
	}
	
	global $db;
	$u = mysqli_real_escape_string($db,$u);
	$s = "INSERT INTO T2 (user, type, amount, date, mail_receipt, account) VALUES ('$u', 'D', '$amnt', '$trans_date', 'Y', '$a')";
	($t = mysqli_query($db,$s)) or die (mysqli_error($db));
	echo "<br>*********************<br><br>";
	echo "<br>Successful deposit into account $u<br>";
	
	$s = "SELECT current FROM A2 WHERE user='$u' AND account='$a'";
	($t = mysqli_query($db,$s)) or die (mysqli_error($db));
	while ($r = mysqli_fetch_array($t, MYSQLI_ASSOC)){
		$curr = $r["current"];
	}
	echo $curr;
	$curr = $curr + $amnt;
	echo $curr;
	$s = "UPDATE A2 SET current = '$curr', recent_trans='$trans_date' WHERE user = '$u' AND account='$a'";
	($t = mysqli_query($db,$s)) or die(mysqli_error($db));
	
	$s = "SELECT * FROM T2 WHERE user='$u' AND account='$a'";
	($t = mysqli_query($db,$s)) or die (mysqli_error($db));
	$output="";
	show($u, $output,$mail_flag, $a, 100);
	
	echo $output;
}

function withdraw ($user, $amnt, $mail_flag, $a, &$output){
	$trans_date = date('Y-m-d H:i:s');
	
	global $db;
	
	if ($amnt < 0){
		$output="Amount cannot be lower than 0.";
	}
	$u = mysqli_real_escape_string($db,$user);
	$s = "SELECT * from A2 WHERE user='$user' AND account='$a'";
	($t = mysqli_query($db,$s)) or die (mysqli_error($db));	
	while ($r = mysqli_fetch_array($t, MYSQLI_ASSOC)){
		$current = $r["current"];
	}
	
	if ($current < $amnt) { 
		$output="Amount cannot be greater than current balance";
	}
	$s = "INSERT INTO T2 (user, type, amount, date, mail_receipt, account) VALUES ('$user', 'W', '$amnt', '$trans_date', 'Y', '$a')";
	($t = mysqli_query($db,$s)) or die (mysqli_error($db));
	
	$current = $current - $amnt;
	$s = "UPDATE A2 SET current = '$current', recent_trans='$trans_date' WHERE user = '$u' AND account='$a'";
	($t = mysqli_query($db,$s)) or die(mysqli_error($db));
	
	$s = "SELECT * FROM T2 WHERE user='$u' AND account='$a'";
	($t = mysqli_query($db,$s)) or die (mysqli_error($db));
	$output="";
	show($u, $output,$mail_flag,$a, 100);
	
	echo $output;
}

function getdata ($name) {
	global $db;
	$temp = $_GET["user"];
	$temp = mysqli_real_escape_string($db,$temp);
	echo "<br>$name is: $temp<br>";
	return $temp;
}

function mailer ($user, $output) { 
	global $db;
	
	$s = "SELECT * FROM A2 WHERE user='$user'";
	($t = mysqli_query($db,$s)) or die (mysqli_error($db));
	while ($r = mysqli_fetch_array($t, MYSQLI_ASSOC)){
		$mail = $r["mail"];
	}
	
	$mail = "drv6@njit.edu";
	
	mail($mail, "SQL RESULT", $output);
	echo "Output mailed to " . $mail . "!<br><br>";
}
?>
