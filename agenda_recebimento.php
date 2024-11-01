<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<?php
session_start();

if (!$_SESSION['logado']) {
  header("Location: login.php");
  exit;
}

include_once("php/conexao.php");
include_once("php/global.php");
include_once("php/versao.php");

$versao = getVersao();
$nome = getNome();

function populateLojasSelect()
{
  $lojasAtivas = getLojaDescricaoAtivas();

  foreach ($lojasAtivas as $loja) {
    echo "<option value='$loja[id]'>$loja[descricao]</option>";
  }
}

?>

<html>

<head>
  <title><?= $nome ?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <link rel="stylesheet" media="all" type="text/css" href="css/vr.css">
  <link rel="stylesheet" media="all" type="text/css" href="css/bootstrap.min.css">
  <link rel="shortcut icon" type="image/x-icon" href="img/favicon.ico" />
  <link rel="stylesheet" type="text/css" href="css/jquery/ui.all.css" />
  <link rel="stylesheet" type="text/css" href="css/agenda-recebimento.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,400;0,500;0,700;1,400;1,500;1,700&display=swap" rel="stylesheet">
  <div style="position: fixed; width: 100%;">
    <style type="text/css">
      .legenda {
        position: fixed;
        text-align: left;
        width: 100%;
        left: 0px;
        border-bottom: 1px solid;
        border-color: #B1B1B1;
        padding-bottom: 13px;
        background-color: #FFFFFF;
        height: 20px;
        padding-top: 10px;
      }
    </style>
  </div>
</head>

<body>
  <div id="container">



    <dialog id="modal-mensagem">
    </dialog>

    <dialog id="modal-select-pedido-compra">
      <div class="dialog-container">

        <header>
          <h4>Pedido de Compra</h4>
          <button id="close-pedido-compra" class="close-button">X</button>
        </header>

        <table>
          <thead>
            <tr>
              <th class="centered-column">
                <input type="checkbox" id="select-all-pedido-compra">
              </th>
              <th>Nº Pedido</th>
              <th>Data de Compra</th>
              <th>Data de Entrega</th>
              <th>Valor</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
        <div class="action-buttons">
          <button type="button" id="agendar-pedido" class="button button-primary" disabled>
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
              <mask id="mask0_201_154" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="14" height="14">
                <rect width="14" height="14" fill="#D9D9D9" />
              </mask>
              <g mask="url(#mask0_201_154)">
                <path d="M6.06051 8.62037L4.44014 6.9838C4.37533 6.91898 4.30252 6.88657 4.22171 6.88657C4.14048 6.88657 4.06205 6.92438 3.98644 7C3.92162 7.06482 3.88921 7.14043 3.88921 7.22685C3.88921 7.31327 3.92162 7.38889 3.98644 7.4537L5.70403 9.1713C5.80125 9.26852 5.92267 9.31713 6.06829 9.31713C6.21434 9.31713 6.33597 9.26852 6.4332 9.1713L9.99801 5.60648C10.0628 5.54167 10.0952 5.46864 10.0952 5.38741C10.0952 5.30661 10.0574 5.2284 9.98181 5.15278C9.91699 5.08796 9.84137 5.05556 9.75495 5.05556C9.66854 5.05556 9.59292 5.08796 9.5281 5.15278L6.06051 8.62037ZM7.00033 12.8333C6.19014 12.8333 5.43137 12.6795 4.72403 12.3719C4.01625 12.0638 3.40051 11.6477 2.87681 11.1235C2.35267 10.5998 1.93656 9.98407 1.62847 9.2763C1.32082 8.56895 1.16699 7.81019 1.16699 7C1.16699 6.18982 1.32082 5.43083 1.62847 4.72306C1.93656 4.01571 2.35267 3.39997 2.87681 2.87583C3.40051 2.35213 4.01625 1.93624 4.72403 1.62815C5.43137 1.3205 6.19014 1.16667 7.00033 1.16667C7.81051 1.16667 8.56949 1.3205 9.27727 1.62815C9.98462 1.93624 10.6004 2.35213 11.1245 2.87583C11.6482 3.39997 12.0641 4.01571 12.3722 4.72306C12.6798 5.43083 12.8337 6.18982 12.8337 7C12.8337 7.81019 12.6798 8.56895 12.3722 9.2763C12.0641 9.98407 11.6482 10.5998 11.1245 11.1235C10.6004 11.6477 9.98462 12.0638 9.27727 12.3719C8.56949 12.6795 7.81051 12.8333 7.00033 12.8333Z" fill="#3D3D3D" />
              </g>
            </svg>
            <span>Agendar Pedido</span>
          </button>
          <button type="button" id="sair-pedido-compra" class="button button-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
              <rect x="1.66699" y="1.66655" width="10.6655" height="10.6655" rx="5.33275" fill="#545454" stroke="#545454" />
              <path d="M4.66602 4.6662L6.97849 6.97868C6.98988 6.99007 7.00835 6.99007 7.01974 6.97868L9.33221 4.6662M9.33221 9.3324L7.01974 7.01992C7.00835 7.00853 6.98988 7.00853 6.97849 7.01992L4.66602 9.3324" stroke="white" stroke-width="1.02083" stroke-linecap="round" />
            </svg>
            <span>Sair</span>
          </button>
        </div>
      </div>

    </dialog>



    <dialog id="modal-agendamento">
      <div class="dialog-container">

        <header>
          <h4>Agendamento</h4>
          <button id="close-agendamento" class="close-button">X</button>
        </header>

        <span id="modal-agendamento__info"></span>
        <table>
          <thead>
            <tr>
              <th>Hora Início</th>
              <th>Hora Término</th>
              <th>Situação Agendada</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
        <span>*O agendamento pode variar conforme cadastro de docas no ERP.</span>

        <header>
          <h4>Digite o horário para recebimento</h4>
        </header>

        <p id="agendamento-tempo-minimo-info">O horário escolhido, deve respeitar o tempo mínimo de <b>1 hora</b> entre a chegada e a partida.</p>

        <form id="horarios-agendamento">
          <div class="form-group">
            <label for="horaInicio">Hora de chegada</label>
            <input type="text" name="horaInicio" id="horaInicio" pattern="^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$" placeholder="00:00"></input>
          </div>
          <div class="form-group">
            <label for="horaTermino">Hora de partida</label>
            <input type="text" name="horaTermino" id="horaTermino" pattern="^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$" placeholder="00:00" required></input>
          </div>

        </form>
        <span id="horarios-agendamento-erro"></span>



        <div class="action-buttons">
          <button type="button" id="salvar-agendamento" class="button button-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="15" viewBox="0 0 14 15" fill="none">
              <mask id="mask0_201_471" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="14" height="15">
                <rect y="0.500244" width="14" height="14" fill="#D9D9D9" />
              </mask>
              <g mask="url(#mask0_201_471)">
                <path d="M6.06051 9.12061L4.44014 7.48404C4.37533 7.41922 4.30252 7.38682 4.22171 7.38682C4.14048 7.38682 4.06205 7.42462 3.98644 7.50024C3.92162 7.56506 3.88921 7.64067 3.88921 7.72709C3.88921 7.81351 3.92162 7.88913 3.98644 7.95394L5.70403 9.67154C5.80125 9.76876 5.92267 9.81737 6.06829 9.81737C6.21434 9.81737 6.33597 9.76876 6.4332 9.67154L9.99801 6.10672C10.0628 6.04191 10.0952 5.96888 10.0952 5.88765C10.0952 5.80685 10.0574 5.72864 9.98181 5.65302C9.91699 5.5882 9.84137 5.5558 9.75495 5.5558C9.66854 5.5558 9.59292 5.5882 9.5281 5.65302L6.06051 9.12061ZM7.00033 13.3336C6.19014 13.3336 5.43137 13.1797 4.72403 12.8721C4.01625 12.564 3.40051 12.1479 2.87681 11.6238C2.35267 11.1001 1.93656 10.4843 1.62847 9.77654C1.32082 9.06919 1.16699 8.31043 1.16699 7.50024C1.16699 6.69006 1.32082 5.93108 1.62847 5.2233C1.93656 4.51595 2.35267 3.90021 2.87681 3.37607C3.40051 2.85237 4.01625 2.43648 4.72403 2.12839C5.43137 1.82074 6.19014 1.66691 7.00033 1.66691C7.81051 1.66691 8.56949 1.82074 9.27727 2.12839C9.98462 2.43648 10.6004 2.85237 11.1245 3.37607C11.6482 3.90021 12.0641 4.51595 12.3722 5.2233C12.6798 5.93108 12.8337 6.69006 12.8337 7.50024C12.8337 8.31043 12.6798 9.06919 12.3722 9.77654C12.0641 10.4843 11.6482 11.1001 11.1245 11.6238C10.6004 12.1479 9.98462 12.564 9.27727 12.8721C8.56949 13.1797 7.81051 13.3336 7.00033 13.3336Z" fill="#3D3D3D" />
              </g>
            </svg>
            <span>
              Agendar
            </span>
          </button>
          <button type="button" id="sair-agendamento" class="button button-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="15" viewBox="0 0 14 15" fill="none">
              <rect x="1.66699" y="2.16679" width="10.6655" height="10.6655" rx="5.33275" fill="#545454" stroke="#545454" />
              <path d="M4.66602 5.16644L6.97849 7.47892C6.98988 7.49031 7.00835 7.49031 7.01974 7.47892L9.33221 5.16644M9.33221 9.83264L7.01974 7.52016C7.00835 7.50878 6.98988 7.50878 6.97849 7.52016L4.66602 9.83264" stroke="white" stroke-width="1.02083" stroke-linecap="round" />
            </svg>
            <span>Sair</span>
          </button>
        </div>
      </div>

    </dialog>




    <dialog id="modal-horarios-agendados">
      <div class="dialog-container">

        <header>
          <h4>Horários Agendados</h4>
          <button id="close-horarios-agendados" class="close-button">X</button>
        </header>

        <table>
          <thead>
            <tr>
              <th>Hora Início</th>
              <th>Hora Término</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>

        <div class="action-buttons">
          <button type="button" id="agendar-novo-horario" class="button button-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
              <rect x="1.16602" y="1.16655" width="11.6655" height="11.6655" rx="5.83275" fill="#545454" />
              <path d="M7 3.6998L7 6.97014C7 6.98624 7.01306 6.9993 7.02916 6.9993L10.2995 6.9993M7 10.2988L7 7.02846C7 7.01236 6.98694 6.9993 6.97084 6.9993L3.7005 6.9993" stroke="#DDDDDD" stroke-width="1.02083" stroke-linecap="round" />
            </svg>
            <span>Agendar novo horário</span>
          </button>
          <button type="button" id="sair-horarios-agendados" class="button button-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
              <rect x="1.16602" y="1.16655" width="11.6655" height="11.6655" rx="5.83275" fill="#545454" />
              <path d="M4.66602 4.6662L6.97849 6.97868C6.98988 6.99007 7.00835 6.99007 7.01974 6.97868L9.33221 4.6662M9.33221 9.3324L7.01974 7.01992C7.00835 7.00853 6.98988 7.00853 6.97849 7.01992L4.66602 9.3324" stroke="#DDDDDD" stroke-width="1.02083" stroke-linecap="round" />
            </svg>
            <span>Sair</span>
          </button>
        </div>
      </div>

    </dialog>



    <dialog id="modal-remover-agendamento">
      <div class="dialog-container">

        <header>
          <h4>Remover Agendamento</h4>
          <button id="close-remover-agendamento" class="close-button">X</button>
        </header>

        <p>Deseja realmente remover o agendamento abaixo?</p>

        <table>
          <thead>
            <tr>
              <th>Hora Início</th>
              <th>Hora Término</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>

        <div class="action-buttons">
          <button type="button" id="remover-agendamento" class="button button-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
              <mask id="mask0_201_545" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="14" height="14">
                <rect width="14" height="14" fill="#D9D9D9" />
              </mask>
              <g mask="url(#mask0_201_545)">
                <path d="M6.05953 8.62037L4.43916 6.9838C4.37435 6.91898 4.30154 6.88657 4.22074 6.88657C4.1395 6.88657 4.06108 6.92438 3.98546 7C3.92064 7.06482 3.88824 7.14043 3.88824 7.22685C3.88824 7.31327 3.92064 7.38889 3.98546 7.4537L5.70305 9.1713C5.80027 9.26852 5.92169 9.31713 6.06731 9.31713C6.21336 9.31713 6.335 9.26852 6.43222 9.1713L9.99703 5.60648C10.0618 5.54167 10.0943 5.46864 10.0943 5.38741C10.0943 5.30661 10.0564 5.2284 9.98083 5.15278C9.91602 5.08796 9.8404 5.05556 9.75398 5.05556C9.66756 5.05556 9.59194 5.08796 9.52713 5.15278L6.05953 8.62037ZM6.99935 12.8333C6.18916 12.8333 5.4304 12.6795 4.72305 12.3719C4.01527 12.0638 3.39953 11.6477 2.87583 11.1235C2.35169 10.5998 1.93558 9.98407 1.6275 9.2763C1.31984 8.56895 1.16602 7.81019 1.16602 7C1.16602 6.18982 1.31984 5.43083 1.6275 4.72306C1.93558 4.01571 2.35169 3.39997 2.87583 2.87583C3.39953 2.35213 4.01527 1.93624 4.72305 1.62815C5.4304 1.3205 6.18916 1.16667 6.99935 1.16667C7.80953 1.16667 8.56851 1.3205 9.27629 1.62815C9.98364 1.93624 10.5994 2.35213 11.1235 2.87583C11.6472 3.39997 12.0631 4.01571 12.3712 4.72306C12.6789 5.43083 12.8327 6.18982 12.8327 7C12.8327 7.81019 12.6789 8.56895 12.3712 9.2763C12.0631 9.98407 11.6472 10.5998 11.1235 11.1235C10.5994 11.6477 9.98364 12.0638 9.27629 12.3719C8.56851 12.6795 7.80953 12.8333 6.99935 12.8333Z" fill="#545454" />
              </g>
            </svg>
            <span>Remover</span>
          </button>
          <button type="button" id="sair-remover-agendamento" class="button button-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
              <rect x="1.16602" y="1.16655" width="11.6655" height="11.6655" rx="5.83275" fill="#545454" />
              <path d="M4.66602 4.6662L6.97849 6.97868C6.98988 6.99007 7.00835 6.99007 7.01974 6.97868L9.33221 4.6662M9.33221 9.3324L7.01974 7.01992C7.00835 7.00853 6.98988 7.00853 6.97849 7.01992L4.66602 9.3324" stroke="#DDDDDD" stroke-width="1.02083" stroke-linecap="round" />
            </svg>
            <span>Sair</span>
          </button>
        </div>
      </div>

    </dialog>

    <div style="position: fixed; width: 100%;">



      <?php
      include_once("header.php");
      include_once("menu.php");
      ?>
    </div>

    <div id="content" style="padding-left: 0px; ">
      <div id="aguarde" style="display: none; background: url(img/aguarde.gif) no-repeat; background-position: center;font-size: 14px; font-weight: bold;text-align: center; margin-top: 138px;"> <br><br><br>Aguarde </div>
      <div class="main-content">


        <div class="agenda-recebimento">
          <div class="agenda-recebimento__seletor">
            <select class="seletor__loja" name="loja" id="loja">
              <?=
              populateLojasSelect();
              ?>
            </select>

            <div class="seletor__mes">
              <button id="previous-month-button">&lt;</button>
              <div id="current-month"></div>
              <button id="next-month-button">&gt;</button>
            </div>
          </div>

          <div class="agenda-recebimento__calendario">
            <div class="calendario" id="calendar"></div>
          </div>

          <div class="agenda-recebimento__legenda">

            <div class="agenda-recebimento-legenda__container">
              <div class="agenda-recebimento-legenda__cor agenda-recebimento-legenda__cor--verde"></div>
              <span>Agenda Disponível</span>
            </div>

            <div class="agenda-recebimento-legenda__container">
              <div class="agenda-recebimento-legenda__cor agenda-recebimento-legenda__cor--vermelho"></div>
              <span>Agenda Não Disponível</span>
            </div>

            <div class="agenda-recebimento-legenda__container">
              <div class="agenda-recebimento-legenda__cor agenda-recebimento-legenda__cor--azul"></div>
              <span>Agendado</span>
            </div>

          </div>

        </div>
      </div>

      <br>
      <div id="mensagem_salvar"></div>
    </div>


    <div id="footer">
      <?php
      include("footer.php");
      ?>
    </div>

  </div>

  <script type="text/javascript" src="js/jquery-1.3.2.js"></script>
  <script type="text/javascript" src="js/jquery/ui.core.js"></script>
  <script type="text/javascript" src="js/jquery/ui.datepicker.js"></script>
  <script type="text/javascript" src="js/jquery/i18n/ui.datepicker-pt-BR.js"></script>
  <script type="text/javascript" src="js/jquery/effects.core.js"></script>
  <script type="text/javascript" src="js/jquery/effects.blind.js"></script>
  <script type="text/javascript" src="fancybox/jquery.fancybox-1.3.4.pack.js"></script>
  <script type="module" src="js/agenda_recebimento/agenda_recebimento.js"></script>
  <link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox-1.3.4.css" media="screen" />

  <script type="text/javascript">
  </script>

  <script type="text/javascript" src="js/ajax.js"></script>
  <script type="text/javascript" src="js/vr.js"></script>
  <script type="text/javascript" src="js/menu.js"></script>

</body>

</html>
