<?php

require_once "services/senha_service.php";


$senha = $_POST['senha'];
$confirmacaoSenha = $_POST['confirmacaoSenha'];
$token = $_POST['token'];

header('Content-Type: application/json; charset=utf-8');


if (!$token) {
  echo json_encode(['error' => 'Token não informado']);
  exit();
}

if (!isJwtValido($token)) {
  echo json_encode(['error' => 'Token inválido']);
  exit();
}

if (!$senha) {
  echo json_encode(['error' => 'Senha não informada']);
  exit();
}

if (!$confirmacaoSenha) {
  echo json_encode(['error' => 'Confirmação de senha não informada']);
  exit();
}

if (!ctype_digit($senha)) {
  echo json_encode(['error' => 'Senha só pode possuir números']);
  exit();
}

if (!ctype_digit($confirmacaoSenha)) {
  echo json_encode(['error' => 'Confirmação de senha só pode possuir números']);
  exit();
}

if ($senha !== $confirmacaoSenha) {
  echo json_encode(['error' => 'As senhas não conferem']);
  exit();
}

if (strlen($senha) !== 6) {
  echo json_encode(['error' => 'A senha deve conter exatamente 6 dígitos']);
  exit();
}



$idFornecedor = getIdFornecedorFromToken($token);

$senhaAtual = getSenhaAtualFornecedor($idFornecedor);

if ($senhaAtual === $senha) {
  echo json_encode(['error' => 'A nova senha não pode ser igual à senha atual']);
  exit();
}

try {
  alterarSenha($senha, $idFornecedor);
  echo json_encode(['message' => 'Senha alterada com sucesso']);
} catch (Exception $e) {
  echo json_encode([
    'error' => 'Erro ao alterar senha: ' . $e->getMessage(),
  ]);
}
