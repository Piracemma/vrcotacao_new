<?php

session_start();

header('Content-Type: application/json');

if (!$_SESSION['logado']) {
  header("Location: ../login.php");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $response = array('senha' => 'vrc0t4c40');
    $jsonResponse = json_encode($response);
    header('Content-Type: application/json');
    echo $jsonResponse;
}
