<?php

require __DIR__ . "/../../vendor/autoload.php";
require __DIR__ . "/../conexao.php";
require __DIR__ . "/../classes/agendamento_recebimento.php";
require __DIR__ . "/../classes/intervalo_recebimento.php";

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function encodeJwt($payload, $key) {
  return JWT::encode($payload, $key, 'HS256');
}

function decodeJwt($jwt, $key) {
  return (array) JWT::decode($jwt, new Key($key, 'HS256'));
}

function getPayload($jwt) {
  $jwtParts = explode('.', $jwt);
  $payload = $jwtParts[1];
  $payload = base64_decode($payload);
  $payload = json_decode($payload, true);
  return $payload;
}

function getHeader($jwt) {
  $jwtParts = explode('.', $jwt);
  $header = $jwtParts[0];
  $header = base64_decode($header);
  $header = json_decode($header, true);
  return $header;
}
