<?php

session_start();

unset($_SESSION['logged_in']);
unset($_SESSION['account_type']);
unset($_SESSION['email']);
setcookie(session_name(), "", time() - 360);
session_destroy();

header("Location: index.php");

?>