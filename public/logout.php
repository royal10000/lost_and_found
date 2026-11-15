<?php
require_once __DIR__."/../includes/session.php";
Session::logOut();
header("Location:index.php");

?>