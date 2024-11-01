<?php

if (!$_SESSION['logado']) {
    header("Location: login.php");
    exit;
}
?>

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<div id="menu">
    <a href="index.php" id="menuitem">
        <i class="icon-home icon-white"></i>
        <strong>Principal</strong>
    </a>

    <a href="cotacao.php" id="menuitem">
        <i class="icon-shopping-cart icon-white"></i>
        <strong>Cotação</strong>
    </a>

    <a href="agenda_recebimento.php" id="menuitem">
        <strong>Agendar Recebimento</strong>
    </a>
</div>
