<?php
session_start();

$_SESSION["logado"] = false;

header("Location: ../index.php");
?>