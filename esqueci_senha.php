<?php

require_once "php/versao.php";
require_once "php/services/senha_service.php";

session_start();

if ($_SESSION['logado']) {
  header("Location: index.php");
  exit;
}

$versao = getVersao();
$nome = getNome();
$data = getData();
?>

<!DOCTYPE html>

<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <link rel="stylesheet" media="all" type="text/css" href="css/vr.css">
  <link rel="stylesheet" media="all" type="text/css" href="css/bootstrap.min.css">
  <link rel="shortcut icon" type="image/x-icon" href="img/favicon.ico" />
  <link rel="stylesheet" type="text/css" href="css/esqueci-senha.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,400;0,500;0,700;1,400;1,500;1,700&display=swap" rel="stylesheet">

  <title>
    <?= $nome ?>
  </title>

  <script type="text/javascript" src="js/vr.js"></script>
  <script type="text/javascript" src="js/ajax.js"></script>
  <script type="text/javascript" src="js/esqueci-senha.js" defer></script>
</head>

<body>

  <div class="esqueci-senha-container">
    <div class="logotipo"></div>
    <img src="img/logotipo.png" style="margin-top: 45px;  margin-left: 35px; height: 200px">


    <div id="form-container" class="hideable-container">
      <form id="form">
        <label for="cnpj">Digite o CNPJ</label>
        <input id="cnpj" type="text" name="cnpj" pattern="\d{2}\.\d{3}\.\d{3}/\d{4}-\d{2}" maxlength="18">

        <label for="codigo">Código de acesso</label>
        <input id="codigo" type="text" name="codigo" pattern="\d*" maxlength="6" />
      </form>

      <span id="erro" class="hidden"></span>

      <div class="action-buttons">
        <button type="button" class="button button--primary" id="enviar">
          <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none">
            <mask id="mask0_250_38" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="15" height="15">
              <rect x="0.326172" y="0.347778" width="14" height="14" fill="#D9D9D9" />
            </mask>
            <g mask="url(#mask0_250_38)">
              <path d="M6.38668 8.96811L4.76631 7.33153C4.7015 7.26672 4.62869 7.23431 4.54789 7.23431C4.46665 7.23431 4.38823 7.27212 4.31261 7.34774C4.24779 7.41255 4.21539 7.48817 4.21539 7.57459C4.21539 7.66101 4.24779 7.73663 4.31261 7.80144L6.0302 9.51903C6.12742 9.61626 6.24884 9.66487 6.39446 9.66487C6.54051 9.66487 6.66215 9.61626 6.75937 9.51903L10.3242 5.95422C10.389 5.8894 10.4214 5.81638 10.4214 5.73515C10.4214 5.65434 10.3836 5.57613 10.308 5.50051C10.2432 5.4357 10.1675 5.40329 10.0811 5.40329C9.99471 5.40329 9.91909 5.4357 9.85427 5.50051L6.38668 8.96811ZM7.3265 13.1811C6.51631 13.1811 5.75755 13.0272 5.0502 12.7196C4.34242 12.4115 3.72668 11.9954 3.20298 11.4713C2.67884 10.9476 2.26273 10.3318 1.95465 9.62403C1.64699 8.91669 1.49316 8.15792 1.49316 7.34774C1.49316 6.53755 1.64699 5.77857 1.95465 5.07079C2.26273 4.36345 2.67884 3.74771 3.20298 3.22357C3.72668 2.69987 4.34242 2.28397 5.0502 1.97589C5.75755 1.66823 6.51631 1.5144 7.3265 1.5144C8.13668 1.5144 8.89566 1.66823 9.60344 1.97589C10.3108 2.28397 10.9265 2.69987 11.4507 3.22357C11.9744 3.74771 12.3903 4.36345 12.6983 5.07079C13.006 5.77857 13.1598 6.53755 13.1598 7.34774C13.1598 8.15792 13.006 8.91669 12.6983 9.62403C12.3903 10.3318 11.9744 10.9476 11.4507 11.4713C10.9265 11.9954 10.3108 12.4115 9.60344 12.7196C8.89566 13.0272 8.13668 13.1811 7.3265 13.1811Z" fill="#3D3D3D" />
            </g>
          </svg>
          <span>Enviar</span>
        </button>
        <button type="button" class="button button--secondary" id="sair">
          <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none">
            <rect x="2.49316" y="2.01428" width="10.6655" height="10.6655" rx="5.33275" fill="#545454" stroke="#545454" />
            <path d="M5.49219 5.01404L7.80467 7.32652C7.81605 7.33791 7.83452 7.33791 7.84591 7.32652L10.1584 5.01404M10.1584 9.68024L7.84591 7.36776C7.83452 7.35637 7.81605 7.35637 7.80467 7.36776L5.49219 9.68024" stroke="white" stroke-width="1.02083" stroke-linecap="round" />
          </svg>
          <span>Sair</span>
        </button>
      </div>
    </div>


    <div id="info-container" class="hidden hideable-container">
      <h3>E-mail de verificação</h3>

      <p>Enviamos um e-mail para o seu endereço cadastrado. Verifique o mesmo para alterar sua senha.</p>

      <button id="voltar" class="button button--primary">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none">
          <mask id="mask0_263_337" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="15" height="15">
            <rect x="0.978516" y="0.260864" width="14" height="14" fill="#D9D9D9" />
          </mask>
          <g mask="url(#mask0_263_337)">
            <path d="M7.03903 8.88119L5.41866 7.24462C5.35384 7.17981 5.28103 7.1474 5.20023 7.1474C5.119 7.1474 5.04057 7.18521 4.96495 7.26082C4.90014 7.32564 4.86773 7.40126 4.86773 7.48768C4.86773 7.5741 4.90014 7.64971 4.96495 7.71453L6.68254 9.43212C6.77977 9.52934 6.90119 9.57795 7.0468 9.57795C7.19285 9.57795 7.31449 9.52934 7.41171 9.43212L10.9765 5.86731C11.0413 5.80249 11.0737 5.72947 11.0737 5.64823C11.0737 5.56743 11.0359 5.48922 10.9603 5.4136C10.8955 5.34879 10.8199 5.31638 10.7335 5.31638C10.6471 5.31638 10.5714 5.34879 10.5066 5.4136L7.03903 8.88119ZM7.97884 13.0942C7.16866 13.0942 6.40989 12.9403 5.70254 12.6327C4.99477 12.3246 4.37903 11.9085 3.85532 11.3843C3.33119 10.8606 2.91508 10.2449 2.60699 9.53712C2.29933 8.82977 2.14551 8.07101 2.14551 7.26082C2.14551 6.45064 2.29933 5.69166 2.60699 4.98388C2.91508 4.27653 3.33119 3.66079 3.85532 3.13666C4.37903 2.61295 4.99477 2.19706 5.70254 1.88897C6.40989 1.58132 7.16866 1.42749 7.97884 1.42749C8.78903 1.42749 9.54801 1.58132 10.2558 1.88897C10.9631 2.19706 11.5789 2.61295 12.103 3.13666C12.6267 3.66079 13.0426 4.27653 13.3507 4.98388C13.6583 5.69166 13.8122 6.45064 13.8122 7.26082C13.8122 8.07101 13.6583 8.82977 13.3507 9.53712C13.0426 10.2449 12.6267 10.8606 12.103 11.3843C11.5789 11.9085 10.9631 12.3246 10.2558 12.6327C9.54801 12.9403 8.78903 13.0942 7.97884 13.0942Z" fill="#3D3D3D" />
          </g>
        </svg>
        <span>Voltar</span>
      </button>

      <div id="email-nao-recebido">
        <span><b>Não recebeu o e-mail?</b></span>
        <div id="email-nao-recebido__info">
          <p>aguarde 60 segundos para fazer uma nova solicitação</p>
        </div>

        <div id="email-nao-recebido__resend" class="hidden">
          <a id="reenviar">Reenviar</a>
        </div>
      </div>

    </div>

  </div>

  <?php include 'footer.php'; ?>

</body>

</html>
