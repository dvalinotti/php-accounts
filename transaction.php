<a href="transactionform.php">Make another transaction</a><br>
<a href="logout.php"> Log out</a>
<br>
<?php
session_start();

include("fn.php");
include("account.php");

$user   = $_SESSION["user"];
$choice = $_GET["choice"];
$amount = $_GET["amount"];
$mail_flag = $_GET["mail"];
$account = $_GET["account"];
global $out;

switch ($choice) {
    case 'D':
        deposit($user, $amount, $mail_flag, $account);
        echo "<br>Transaction Completed!";
        break;
    
    case 'W':
        withdraw($user, $amount, $mail_flag, $account);
        echo "<br>Transaction Completed! <br><br>";
		break;

    case 'S':
		$num = $_GET["number"];
        show($user, $out, $mail_flag, $account, $num);
		echo $out;
        break;
}
?>
