<?php
include("enums.php");

session_start();

if (!$_SESSION['logado']) {
  header("Location: ../login.php");
  exit;
}

require_once("services/parametro_agendamento_recebimento_service.php");
require_once("services/agendamento_service.php");
require_once("classes/agendamento_recebimento.php");
require_once("classes/intervalo_recebimento.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  handlePost();
} else if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  handleGet();
} else if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
  handleDelete();
}



function handleGet()
{

  if (!isset($_GET['idLoja'])) {
    $response = array('error' => 'idLoja parameter is missing in the request.');
    $jsonResponse = json_encode($response);
    echo $jsonResponse;
    exit();
  }

  if (!isset($_GET['idLoja'])) {
    $response = array('error' => 'idLoja parameter is missing in the request.');
    $jsonResponse = json_encode($response);
    echo $jsonResponse;
    exit();
  }

  if (!isset($_GET['ano'])) {
    $response = array('error' => 'ano parameter is missing in the request.');
    $jsonResponse = json_encode($response);
    echo $jsonResponse;
    exit();
  }

  if (!isset($_GET['mes'])) {
    $response = array('error' => 'mes parameter is missing in the request.');
    $jsonResponse = json_encode($response);

    echo $jsonResponse;
    exit();
  }

  $idLoja = $_GET['idLoja'];
  $mes = str_pad($_GET['mes'], 2, '0', STR_PAD_LEFT);
  $ano = $_GET['ano'];

  $data = "$ano-$mes-01";
  $agendamentosPorDia = getAgendamentosPorDiaByLojaAndDataMes($idLoja, $data);


  $agendamentoPorDiaResponse  = array();

  foreach ($agendamentosPorDia as $dia => $agendamentos) {

    $agendamentosResponse = array();

    foreach ($agendamentos as $agendamento) {
      $agendamentoArray = array(
        'id' => $agendamento->id,
        'dataHoraInicio' => $agendamento->dataHoraInicio,
        'dataHoraTermino' => $agendamento->dataHoraTermino,
        'fromLoggedFornecedor' => $agendamento->idFornecedor == $_SESSION['fornecedor']
      );
      $agendamentosResponse[] = $agendamentoArray;
    }

    $agendamentoPorDiaResponse[$dia] = $agendamentosResponse;
  }

  $response = array(
    'items' => $agendamentoPorDiaResponse,
  );

  echo json_encode($response);
  exit();
}

function handlePost()
{
  try {
    $requestBody = file_get_contents('php://input');
    $requestData = json_decode($requestBody);

    $dataHoraInicio = $requestData->dataHoraInicio;
    $dataHoraTermino = $requestData->dataHoraTermino;

    if (!isset($dataHoraInicio)) {
      echo json_encode(['error' => 'Hora Início inválida']);
      exit();
    }

    if (!isset($dataHoraTermino)) {
      echo json_encode(['error' => 'Hora Término inválida']);
      exit();
    }

    $idLoja = $requestData->idLoja;

    if (!isset($idLoja) || $idLoja < 1) {
      echo json_encode(['error' => 'Loja inválida']);
      exit();
    }

    $idPedidos = $requestData->idPedidos;

    if (!isset($idPedidos) || count($idPedidos) < 1) {
      echo json_encode(['error' => 'Pedidos inválidos']);
      exit();
    }

    $idFornecedor = (int) $_SESSION['fornecedor'];

    validarPedidos($idPedidos, $idFornecedor, $idLoja);

    $agendamento = new AgendamentoRecebimento();
    $agendamento->dataHoraInicio = date('Y-m-d H:i:s', $dataHoraInicio);
    $agendamento->dataHoraTermino = date('Y-m-d H:i:s', $dataHoraTermino);
    $agendamento->idLoja = $idLoja;
    $agendamento->idFornecedor = $idFornecedor;
    $agendamento->idLoja = $idLoja;

    $intervalo = new IntervaloRecebimento($agendamento->dataHoraInicio, $agendamento->dataHoraTermino);

    $diaDaSemana =  (int) $intervalo->dataHoraInicio->format('w') + 1;

    $parametroAgendamentoRecebimento = getParametroDiaSemanaByIdLoja($idLoja)[$diaDaSemana];
    validarIntervalo($intervalo, $parametroAgendamentoRecebimento);
    validarDisponibilidadeDoca($agendamento, $parametroAgendamentoRecebimento);
    salvarAgendamento($agendamento, $idPedidos);
    echo json_encode(['success' => 'Agendamento realizado com sucesso']);
    exit();
  } catch (Exception $ex) {
    echo json_encode(['error' => 'deu ruim. ' . $ex->getMessage()]);
    exit();
  }
}


function handleDelete()
{
  $url = $_SERVER['REQUEST_URI'];
  $parts = explode('/', $url);
  $idAgendamento = end($parts);

  try {
    $idAgendamento = (int) $idAgendamento;
  } catch (Exception $ex) {
    echo json_encode(['error' => 'idAgendamento inválido']);
    exit();
  }

  if (!isAgendamentoFromLoggedFornecedor($idAgendamento)) {
    echo json_encode(['error' => 'Agendamento não pertence ao fornecedor logado!']);
    exit();
  }

  try {
    removerAgendamento($idAgendamento);
    http_response_code(204);
    exit();
  } catch (Exception $ex) {
    echo json_encode(['error' => 'deu ruim. ' . $ex->getMessage()]);
    exit();
  }

}
