<?php

require_once __DIR__ . "/../config_timezone.php";
require_once __DIR__ . "/../conexao.php";
require_once "jwt_service.php";
require_once "email_service.php";
require_once __DIR__ . "/../dot_env.php";

function isCnpjFromFornecedor($cnpj, $idFornecedor)
{
  global $con;

  $sql = "SELECT id FROM fornecedor WHERE cnpj = '$cnpj' AND id = $idFornecedor";

  $rst = pg_query($con, $sql);

  return pg_num_rows($rst) > 0;
}

function alterarSenha($novaSenha, $idFornecedor)
{
  global $con;
  $sql = "UPDATE fornecedor SET senha = '$novaSenha' WHERE id = $idFornecedor";
  $rst = pg_query($con, $sql);
  return pg_affected_rows($rst) > 0;
}

function isSenhaValida($novaSenha)
{
  return preg_match('/^[0-9]{6}$/', $novaSenha);
}

function getIdFornecedorFromToken($jwt)
{
  $payload = getPayload($jwt);
  return $payload['id'];
}

function isJwtValido($jwt)
{
  try {
    $idFornecedor = getIdFornecedorFromToken($jwt);
    $secret = getSecret($idFornecedor);
    $payload = decodeJwt($jwt, $secret);
    $expiraEm = $payload['exp'];
    return $expiraEm >= time();
  } catch (Exception $e) {
    return false;
  }
}

function getSenhaAtualFornecedor($idFornecedor)
{
  global $con;
  $sql = "SELECT senha FROM fornecedor WHERE id = $idFornecedor";
  $rst = pg_query($con, $sql);
  $row = pg_fetch_array($rst);
  return $row['senha'];
}

function getNomeFantasiaFornecedor($idFornecedor)
{
  global $con;
  $sql = "SELECT nomefantasia FROM fornecedor WHERE id = $idFornecedor";
  $rst = pg_query($con, $sql);
  $row = pg_fetch_array($rst);
  return $row['nomefantasia'];
}

function getSecret($idFornecedor)
{
  $senhaAtual = getSenhaAtualFornecedor($idFornecedor);
  $secret = "$_ENV[SECRET_KEY] $senhaAtual";
  return $secret;
}

function generateJwtEsqueciSenha($idFornecedor)
{
  $secret = getSecret($idFornecedor);
  $date = new DateTime();
  $date->modify('+2 hours');
  $timestampInSeconds = $date->getTimestamp();

  $payload = [
    'exp' => $timestampInSeconds,
    'id' => $idFornecedor
  ];

  $jwt = encodeJwt($payload, $secret);

  return $jwt;
}

function fornecedorTemEmail($idFornecedor)
{
  $contato = getContatoFornecedor($idFornecedor);
  return !empty($contato['email']);
}

function getContatoFornecedor($idFornecedor)
{
  global $con;

  $sql = "SELECT nome, email FROM fornecedorcontato WHERE id_fornecedor = $idFornecedor ORDER BY id_tipocontato LIMIT 1";
  $rst = pg_query($con, $sql);
  $row = pg_fetch_array($rst);

  $nome = $row['nome'];
  $email = $row['email'];

  return ['nome' => $nome, 'email' => $email];
}



function enviaEmail($idFornecedor)
{
  $jwt = generateJwtEsqueciSenha($idFornecedor);
  $linkBase = $_ENV['LINK_BASE'];
  $link = "$linkBase/alterar_senha.php?token=$jwt";
  $contato = getContatoFornecedor($idFornecedor);


  $body = '
  <!DOCTYPE html>
<html>

<head>
  <meta name="viewport" content="width=device-width" />
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title>Simple Transactional Email</title>
  <style>
    @import url("https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap");

    * {
      margin: 0;
      padding: 0;
      font-family: Roboto, Arial, sans-serif;
      text-align: center;
      color: #455A64;
      background-color: #FEFEFE;
    }

    header {
      width: 300px;
      height: 60px;

      margin: 0 auto;
    }

    div.header__vr-logo {
      width: 20%;
      background-image: url(https://storage.googleapis.com/imagem-aplicativos/logotipo/logo-vr.png);
      background-size: 100% 35px;
      background-repeat: no-repeat;
      background-position: center center;
    }

    h1 {
      color: #FB9039;
      font-size: 1.25rem;

      margin-bottom: 1rem;
    }

    .separator {
      height: 1px;
      margin: 1.5rem 0;
      background-color: #455A64;

      opacity: .5;
    }

    .container {
      display: table;
      width: 100%;
      height: 70px;
    }

    div.row {
      display: table-cell;
      vertical-align: middle;
    }

    .btn {
      height: 2rem;
      padding: 1rem 2.5rem;

      border: none;
      background-color: transparent;

      border-radius: 0.5rem;

      cursor: pointer;
      white-space: nowrap;

      font-size: 1.25srem;
      font-weight: bold;

      text-transform: uppercase;
      text-decoration: none;
      text-align: center;

      margin: 0 auto;
    }

    .btn-primary {
      background-color: #FB9039;
      color: #FEFEFE !important;
      border: none;
    }

    footer .footer__vr-logo--container {
      width: 200px;
      height: 50px;

      margin: 0 auto;
    }

    .footer__vr-logo {
      width: 100%;
      height: 40px;
      background-image: url(https://storage.googleapis.com/imagem-aplicativos/logotipo/vrsoftware.png);
      background-size: 100% 40px;
      background-repeat: no-repeat;
      background-position: center center;
    }

    footer p.footer__copy {
      font-size: .75rem;
    }

    header {
      display: table;
      align-items: center;
    }

    header * {
      display: table-cell;
      vertical-align: middle;
      font-size: 2.1rem;
      color: #000;
    }

  </style>
</head>

<body>
  <div class="container">
    <div class="row">
      <header>
          <div class="header__vr-logo"></div>
          <h1>VR Cotação</h1>
      </header>
    </div>
  </div>

  <div class="separator"></div>

  <h1>Redefinição de Senha</h1>
  <p>Olá ' . "$contato[nome]." . '</p>
  <p>Recebemos uma solicitação para redefinir sua senha do VR Cotação.</p>
  <p>Para alterar sua senha, clique no botão abaixo.</p>

  <div class="container">
    <div class="row">
      <a class="btn btn-primary" href="' . $link . '">
        Alterar Senha
      </a>
    </div>
  </div>

  <p>Este e-mail de redefinição de senha ficará válido por até <strong>2 horas</strong>.</p>
  <p>Nenhuma alteração foi efetuada em sua conta ainda.</p>
  <p>Caso não tenha solicitado a redefinição de senha, por favor, desconsidere este e-mail.</p>

  <div class="separator"></div>

  <footer>
    <div class="container">
      <div class="row">
        <div class="footer__vr-logo--container">
          <div class="footer__vr-logo"></div>
        </div>
      </div>
    </div>
    <p class="footer__copy">Copyright &copy; 2023 VR Software, todos os direitos reservados</p>
  </footer>

</body>

</html>
  ';

  sendEmail('noreply', $contato['email'], 'VR Cotação', $body);
}
