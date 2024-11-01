<?php

session_start();

header('Content-Type: application/json');

if (!$_SESSION['logado']) {
  header("Location: ../login.php");
  exit;
}

include("services/parametro_agendamento_recebimento_service.php");


if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  handleGet();
}

function handleGet()
{
  if (!isset($_GET['idLoja'])) {
    $response = array('error' => 'idLoja parameter is missing in the request.');
    $jsonResponse = json_encode($response);
    header('Content-Type: application/json');
    echo $jsonResponse;
    exit();
  }

  $idLoja = $_GET['idLoja'];
  $parametroAgendaRecebimento = getParametroDiaSemanaByIdLoja(($idLoja));
  $response = array(
    'items' => $parametroAgendaRecebimento,
  );

  $jsonResponse = json_encode($response);
  echo $jsonResponse;
}
