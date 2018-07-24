<?php
	session_start();
	session_unset();
	session_destroy();
	header("Location: Login.php");
?>
<a href="Login.php">Powrot do logowania</a>;