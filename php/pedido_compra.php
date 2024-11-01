<?php
include("enums.php");

session_start();

header('Content-Type: application/json');

if (!$_SESSION['logado']) {
  header("Location: ../login.php");
  exit;
}

include("conexao.php");

if (!isset($_GET['idLoja'])) {
  $response = array('error' => 'idLoja parameter is missing in the request.');
  $jsonResponse = json_encode($response);
  echo $jsonResponse;
  exit();
}

$idLoja = $_GET['idLoja'];
$idFornecedor = $_SESSION['fornecedor'];

$sql = "SELECT p.id, p.id_loja, l.descricao as descricaoloja, p.datacompra, p.dataentrega, p.valortotal,";
$sql .= " CASE  WHEN par.id IS NOT NULL THEN TRUE ELSE FALSE END AS agendado";
$sql .= " FROM pedido p";
$sql .= " LEFT JOIN pedidoagendamentorecebimento par ON par.id_pedido = p.id";
$sql .= " INNER JOIN loja l ON p.id_loja = l.id";
$sql .= " WHERE p.id_fornecedor = $idFornecedor";
$sql .= " AND p.id_loja = $idLoja";
$sql .= " AND p.id_tipoatendidopedido IN (" . TipoAtendidoPedido::NAO_ENTREGUE . ", " . TipoAtendidoPedido::ENTREGA_PARCIAL . ")";
$sql .= " AND p.id_situacaopedido = " . SituacaoPedido::FINALIZADO;
$sql .= " ORDER BY p.dataentrega";

$rst = pg_query($con, $sql);

$pedidoCompra = array();

while ($row = pg_fetch_array($rst)) {
  $pedidoCompra[] = array(
    'id' => $row['id'],
    'idLoja' => $row['id_loja'],
    'descricaoLoja' => $row['descricaoloja'],
    'dataCompra' => $row['datacompra'],
    'dataEntrega' => $row['dataentrega'],
    'valorTotal' => (float) $row['valortotal'],
    'agendado' => $row['agendado'] == 't' ? true : false,
  );
}

$response = array(
  'items' => $pedidoCompra,
);

$jsonResponse = json_encode($response);
echo $jsonResponse;
