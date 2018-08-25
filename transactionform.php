<?php
session_start();
include("fn.php");
include("account.php");
include("menu.php");
gatekeeper();

echo"<p align=\"center\">Welcome ". $_SESSION['user']. "!";
?>

<style>
	#amount {display:none;}
	#number {display:none;}
	#field {width:70%;margin:auto;}
	#logout {text-align:center;display:block;}
</style>

<script>
	function appear() {
		amt = document.getElementById('amount');
		chc = document.getElementById('choice');
		num = document.getElementById('number');
		$menuval = chc.value;
		
		if ($menuval == "D") { 
			amt.style.display="inline";
			num.style.display="none";
			return;
		}
		if ($menuval == "W") {
			amt.style.display="inline";
			num.style.display="none";
			return;
		}
		else {
			amt.style.display="none";
			num.style.display="inline";
			return;
		}
	}
	
	//checkbox send zero input
	if(document.getElementById("mail").checked) {
		document.getElementById('mailhidden').disabled = true;
	}
	
	
</script>

<script type="text/javascript">
	"use strict";
	var ptrbox = document.getElementById("autologout");
	
	document.addEventListener("click", slowdown);
	document.addEventListener("keypress",slowdown);
	document.addEventListener("mouseover",slowdown);
	var timer;
	
	slowdown();
	
	function slowdown () {
		clearTimeout(timer);
		timer=setTimeout(messenger, 4000);
	}
	
	function messenger(){
		setTimeout(out, 3000);
		//document.getElementById("logouttime").innerHTML = "Logging out soon ...";
		
	}
	
	function out () {
		if (ptrbox.checked) {return;}
		window.location.href="logout.txt";
	}
</script>

<form action="transaction.php">
	<fieldset id="field"><legend> Transaction Window </legend>
		Amount:	<input type="number"  step="1"  name="amount" id="amount"><br><br>
		Number: <input type="number"  step="1"  name="number" id="number"><br><br>
		
		Account:
		<?php
			$user = $_SESSION["user"];
			displaymenu($user);
		?>
		
		<br><br>Service:
		<select name="choice" id="choice" onchange="appear()">
			<option selected>Choose</option>
			<option value="S">Show</option>
			<option value="D">Deposit</option>
			<option value="W">Withdraw</option>
		</select>
		<br><br>
		<input type="checkbox" id="mail" name="mail" value="No"> Email Receipt<br><br>
		<input type="hidden"   id="mailhidden" name="mail" value="No">
		<br><br>
		<input type="checkbox" id="autologout" name="autologout" checked="checked">Stop Auto-Logout<br><br>
		<input type="submit" name="Submit">
	</fieldset>
	<br>
</form>
<a id="logout" href="logout.php">Logout</a>
<span id="logouttime"></span>