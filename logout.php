<?php
include ( "fn.php" );
session_set_cookie_params(0,"/~drv6/", "web.njit.edu");
$_SESSION=array();
session_destroy();
setcookie("PHPSESSID","", time()-3600, '/~drv6/', "", 0,0);
redirect ("Logged out. Redirecting...", "login.html", "3");
exit;
?>

