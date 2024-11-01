<?php

require_once("services/senha_service.php");

header('Content-Type: application/json; charset=utf-8');

$cnpj = $_POST['cnpj'];
$idFornecedor = $_POST['codigo'];

if (!$cnpj) {
  echo json_encode(['error' => 'CNPJ não informado']);
  exit();
}

if (!ctype_digit($cnpj)) {
  echo json_encode(['error' => 'CNPJ inválido']);
  exit();
}

if (!$idFornecedor) {
  echo json_encode(['error' => 'Código de acesso não informado']);
  exit();
}

if (!ctype_digit($idFornecedor)) {
  echo json_encode(['error' => 'Código de acesso inválido']);
  exit();
}

if (!isCnpjFromFornecedor($cnpj, $idFornecedor)) {
  echo json_encode(['error' => 'Desculpe, mas não encontramos a sua conta. Revise os dados digitados e tente novamente.']);
  exit();
}

if (!fornecedorTemEmail($idFornecedor)){
  echo json_encode(['error' => 'Desculpe, mas não encontramos o seu email. Entre em contato com o suporte.']);
  exit();
}

try {
  enviaEmail($idFornecedor);
  echo json_encode(['message' => 'Email enviado com sucesso']);
  exit();
} catch (Exception $e) {
  echo json_encode([
    'error' => 'Erro ao enviar email',
    'exception' => $e->getMessage()
  ]);
  exit();
}
