<?php
require_once __DIR__ . '/../../vendor/autoload.php';
include_once __DIR__ . "/../conexao.php";
require_once __DIR__ . "/../dot_env.php";


function getEmailConfig()
{
  $config = [
    'host' => $_ENV['MAIL_HOST'],
    'username' => $_ENV['MAIL_USERNAME'],
    'password' => $_ENV['MAIL_PASSWORD'],
    'port' => $_ENV['MAIL_PORT'],
    'secure' => $_ENV['MAIL_ENCRYPTION']
  ];
  return $config;
}

function sendEmail($fromLabel, $to, $subject, $body)
{
  $config = getEmailConfig();

  $mail = new PHPMailer(true);
  $mail->CharSet = 'UTF-8';
  $mail->isSMTP();
  $mail->Host = $config['host'];
  $mail->SMTPAuth = true;
  $mail->Username = $config['username'];
  $mail->Password = $config['password'];
  $mail->SMTPSecure = $config['secure'];
  $mail->Port = $config['port'];

  $mail->setFrom($config['username'], $fromLabel);
  $mail->addAddress($to);

  $mail->isHTML(true);
  $mail->Subject = $subject;
  $mail->Body = $body;

  $mail->send();
  return true;
}
