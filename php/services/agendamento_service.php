<?php

require_once __DIR__ . "/../config_timezone.php";
require_once __DIR__ . "/../conexao.php";
require_once __DIR__ . "/../classes/agendamento_recebimento.php";
require_once __DIR__ . "/../classes/intervalo_recebimento.php";


function getAgendamentosByLojaAndDataDia($idLoja, $data)
{
  global $con;

  $sql = "SELECT id, datahorainicio, datahoratermino, id_fornecedor, id_loja";
  $sql .= " FROM agendamentorecebimento";
  $sql .= " WHERE DATE_TRUNC('day', datahorainicio) = DATE '$data'";
  $sql .= " AND id_loja = $idLoja";

  $rst = pg_query($con, $sql);

  $agendamentos = array();

  while ($row = pg_fetch_array($rst)) {
    $agendamento = new AgendamentoRecebimento();
    $agendamento->id = (int) $row['id'];
    $agendamento->dataHoraInicio = $row['datahorainicio'];
    $agendamento->dataHoraTermino = $row['datahoratermino'];
    $agendamento->idFornecedor = (int) $row['id_fornecedor'];
    $agendamento->idLoja = (int) $row['id_loja'];

    $agendamentos[] = $agendamento;
  }

  return $agendamentos;
}

function getAgendamentosPorDiaByLojaAndDataMes($idLoja, $data)
{
  global $con;

  $sql = "SELECT id, datahorainicio, datahoratermino, id_fornecedor, id_loja,";
  $sql .= " EXTRACT(DAY FROM datahorainicio) as dia";
  $sql .= " FROM agendamentorecebimento";
  $sql .= " WHERE DATE_TRUNC('month', datahorainicio) = DATE '$data'";
  $sql .= " AND id_loja = $idLoja";
  $sql .= " ORDER BY datahorainicio, datahoratermino DESC";

  $rst = pg_query($con, $sql);

  $agendamentosPorDia = array();

  while ($row = pg_fetch_array($rst)) {
    $dia = $row['dia'];


    if (!isset($agendamentosPorDia[$dia])) {
      $agendamentosPorDia[$dia] = array();
    }

    $agendamento = new AgendamentoRecebimento();
    $agendamento->id = (int) $row['id'];
    $agendamento->dataHoraInicio = $row['datahorainicio'];
    $agendamento->dataHoraTermino = $row['datahoratermino'];
    $agendamento->idFornecedor = (int) $row['id_fornecedor'];
    $agendamento->idLoja = (int) $row['id_loja'];

    $agendamentosPorDia[$dia][] = $agendamento;
  }

  return $agendamentosPorDia;
}


function validarPedidos($idPedidos, $idFornecedor, $idLoja)
{
  if (!arePedidosFromFornecedor($idPedidos, $idFornecedor)) {
    echo json_encode(array('error' => 'Existem pedidos que não são do fornecedor logado.'));
    exit();
  }

  if (!arePedidosFromLoja($idPedidos, $idLoja)) {
    echo json_encode(array('error' => 'Existem pedidos que não são da loja selecionada.'));
    exit();
  }

  if (arePedidosAlreadyAgendados($idPedidos)) {
    echo json_encode(array('error' => 'Existem pedidos que já foram agendados.'));
    exit();
  }

  if (arePedidosNaoEntreguesOuEntreguesParcialmente($idPedidos)) {
    echo json_encode(array('error' => 'Existem pedidos que não foram entregues ou foram entregues parcialmente.'));
    exit();
  }
}

function validarIntervalo(IntervaloRecebimento $intervalo, ParametroAgendamentoRecebimento $parametroAgendamentoRecebimento)
{
  $dataHoraInicio = $intervalo->dataHoraInicio;
  $dataHoraTermino = $intervalo->dataHoraTermino;

  $dataHoraInicioParametrizada = DateTime::createFromFormat('H:i', $parametroAgendamentoRecebimento->horarioInicio);
  $dataHoraInicioParametrizada->setDate($dataHoraInicio->format('Y'), $dataHoraInicio->format('m'), $dataHoraInicio->format('d'));

  $dataHoraTerminoParametrizada = DateTime::createFromFormat('H:i', $parametroAgendamentoRecebimento->horarioTermino);
  $dataHoraTerminoParametrizada->setDate($dataHoraTermino->format('Y'), $dataHoraTermino->format('m'), $dataHoraTermino->format('d'));

  if ($dataHoraInicio < $dataHoraInicioParametrizada) {
    echo json_encode(array('error' => 'O horário de chegada deve ser igual ou posterior ao horário de início parametrizado.'));
    exit();
  }

  if ($dataHoraTermino > $dataHoraTerminoParametrizada) {
    echo json_encode(array('error' => 'O horário de partida deve ser igual ou anterior ao horário de término parametrizado.'));
    exit();
  }

  if ($dataHoraInicio > $dataHoraTermino) {
    echo json_encode(array('error' => 'O horário de chegada deve ser anterior que o horário de término.', 'data' => $dataHoraInicio));
    exit();
  }

  $today = new DateTime();
  $today->setTime(0, 0, 0);
  if ($dataHoraInicio < $today) {
    echo json_encode(array('error' => 'O horário de chegada deve ser posterior a data atual.'));
    exit();
  }

  $diff = $dataHoraTermino->getTimestamp() - $dataHoraInicio->getTimestamp();
  if ($diff < $parametroAgendamentoRecebimento->tempoRecebimentoSegundos) {
    echo json_encode(array('error' => 'O tempo entre chegada e partida deve ser igual ou maior ao tempo de recebimento configurado.'));
    exit();
  }
}


function arePedidosFromFornecedor($idPedidos, $idFornecedor)
{
  global $con;

  $idPedidosString = implode(',', $idPedidos);
  $sql = "SELECT id FROM pedido WHERE id IN ($idPedidosString) AND id_fornecedor = $idFornecedor";
  $rst = pg_query($con, $sql);


  return pg_num_rows($rst) == count($idPedidos);
}

function arePedidosFromLoja($idPedidos, $idLoja)
{
  global $con;

  $idPedidosString = implode(',', $idPedidos);
  $sql = "SELECT id FROM pedido WHERE id IN ($idPedidosString) AND id_loja = $idLoja";

  $rst = pg_query($con, $sql);


  return pg_num_rows($rst) == count($idPedidos);
}

function arePedidosAlreadyAgendados($idPedidos)
{
  global $con;

  $idPedidosString = implode(',', $idPedidos);
  $sql = "SELECT id FROM pedidoagendamentorecebimento WHERE id_pedido IN ($idPedidosString)";

  $rst = pg_query($con, $sql);

  return pg_num_rows($rst) == count($idPedidos);
}

function arePedidosNaoEntreguesOuEntreguesParcialmente($idPedidos)
{
  global $con;

  $idPedidosString = implode(',', $idPedidos);
  $sql = "SELECT id FROM pedido WHERE id IN ($idPedidosString) AND id_tipoatendidopedido NOT IN (" . TipoAtendidoPedido::NAO_ENTREGUE . ", " . TipoAtendidoPedido::ENTREGA_PARCIAL . ")";

  $rst = pg_query($con, $sql);

  return pg_num_rows($rst) == count($idPedidos);
}

function validarDisponibilidadeDoca(AgendamentoRecebimento $agendamentoRecebimento, ParametroAgendamentoRecebimento $parametroAgendamentoRecebimento)
{
  $intervaloRecebimento = new IntervaloRecebimento($agendamentoRecebimento->dataHoraInicio, $agendamentoRecebimento->dataHoraTermino);

  $agendamentos = getAgendamentosByLojaAndDataDia($agendamentoRecebimento->idLoja, $intervaloRecebimento->dataHoraInicio->format('Y-m-d'));

  $intervalosRecebimentosDoDia = array_map(function ($agendamento) {
    return new IntervaloRecebimento($agendamento->dataHoraInicio, $agendamento->dataHoraTermino);
  }, $agendamentos);

  $contador = 0;
  foreach ($intervalosRecebimentosDoDia as $intervaloRecebimentoDoDia) {
    if ($intervaloRecebimento->compartilhaIntervalo($intervaloRecebimentoDoDia)) {
      $contador++;
    }
  }

  if ($contador >= $parametroAgendamentoRecebimento->quantidadeDocas) {
    echo json_encode(array('error' => 'Não há docas disponíveis para o intervalo selecionado.'));
    exit();
  }
}


function salvarAgendamento(AgendamentoRecebimento $agendamento, $idPedidos)
{
  global $con;

  pg_query($con, "BEGIN");

  try {
    $sql = "INSERT INTO agendamentorecebimento (datahorainicio, datahoratermino, id_fornecedor, id_loja)";
    $sql .= " VALUES ('" . $agendamento->dataHoraInicio . "', '" . $agendamento->dataHoraTermino . "', " . $agendamento->idFornecedor . ", " . $agendamento->idLoja . ") RETURNING id";

    $rst = pg_query($con, $sql);
    $idAgendamento = pg_fetch_object($rst)->id;

    foreach ($idPedidos as $idPedido) {
      $sql = "INSERT INTO pedidoagendamentorecebimento (id_pedido, id_agendamentorecebimento) VALUES ($idPedido, $idAgendamento)";
      pg_query($con, $sql);
    }

    pg_query($con, "COMMIT");
  } catch (Exception $e) {
    pg_query($con, "ROLLBACK");
    throw $e;
  }
}


function removerAgendamento($idAgendamentoRecebimento)
{
  global $con;

  pg_query($con, "BEGIN");
  try {
    $sql = "DELETE FROM pedidoagendamentorecebimento WHERE id_agendamentorecebimento = $idAgendamentoRecebimento";
    pg_query($con, $sql);

    $sql = "DELETE FROM agendamentorecebimento WHERE id = $idAgendamentoRecebimento";
    pg_query($con, $sql);

    pg_query($con, "COMMIT");
  } catch (Exception $ex) {
    pg_query($con, "ROLLBACK");
    throw $ex;
  }
}

function isAgendamentoFromLoggedFornecedor($idAgendamentoRecebimento)
{
  global $con;

  $sql = "SELECT id FROM agendamentorecebimento WHERE id = $idAgendamentoRecebimento AND id_fornecedor = " . $_SESSION['fornecedor'];
  $rst = pg_query($con, $sql);

  return pg_num_rows($rst) > 0;
}
