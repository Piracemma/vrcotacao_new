<?php
//session_start(); //! Todos as paginas que usam ja tem o "session_start()"

$index = '';
$cotacao = '';
$agenda = '';

if (!$_SESSION['logado']) {
    header("Location: login.php");
    exit;
}

if(strpos($_SERVER['REQUEST_URI'], '/index.php') !== false) {
    $index = "color: #00d7ac !important;";
} else if(strpos($_SERVER['REQUEST_URI'], '/cotacao.php') !== false) {
    $cotacao = "color: #00d7ac !important;";
} else if(strpos($_SERVER['REQUEST_URI'], '/agenda_recebimento.php') !== false) {
    $agenda = "color: #00d7ac !important;";
}
?>
<div id="menu">
    <a href="index.php" class="menuitem" style="display:flex; justify-content: space-between; align-items: center;">
    
        <svg class="menuicone" style="<?= $index ?>" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
            <path fill-rule="evenodd" d="M11.293 3.293a1 1 0 0 1 1.414 0l6 6 2 2a1 1 0 0 1-1.414 1.414L19 12.414V19a2 2 0 0 1-2 2h-3a1 1 0 0 1-1-1v-3h-2v3a1 1 0 0 1-1 1H7a2 2 0 0 1-2-2v-6.586l-.293.293a1 1 0 0 1-1.414-1.414l2-2 6-6Z" clip-rule="evenodd"/>
        </svg>

        <strong class="menuitem-texto" style="<?= $index ?>">Principal</strong>
    </a>

    <a href="cotacao.php" class="menuitem" style="display:flex; justify-content: space-between; align-items: center;">

        <svg class="menuicone" style="<?= $cotacao ?>" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
            <path fill-rule="evenodd" d="M4 4a1 1 0 0 1 1-1h1.5a1 1 0 0 1 .979.796L7.939 6H19a1 1 0 0 1 .979 1.204l-1.25 6a1 1 0 0 1-.979.796H9.605l.208 1H17a3 3 0 1 1-2.83 2h-2.34a3 3 0 1 1-4.009-1.76L5.686 5H5a1 1 0 0 1-1-1Z" clip-rule="evenodd"/>
        </svg>

        <strong class="menuitem-texto" style="<?= $cotacao ?>;">Cotação</strong>

    </a>

    <a href="agenda_recebimento.php" class="menuitem" style="display:flex; justify-content: space-between; align-items: center;">

        <svg class="menuicone" style="<?= $agenda ?>" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
            <path fill-rule="evenodd" d="M8 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1h2a2 2 0 0 1 2 2v15a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h2Zm6 1h-4v2H9a1 1 0 0 0 0 2h6a1 1 0 1 0 0-2h-1V4Zm-3 8a1 1 0 0 1 1-1h3a1 1 0 1 1 0 2h-3a1 1 0 0 1-1-1Zm-2-1a1 1 0 1 0 0 2h.01a1 1 0 1 0 0-2H9Zm2 5a1 1 0 0 1 1-1h3a1 1 0 1 1 0 2h-3a1 1 0 0 1-1-1Zm-2-1a1 1 0 1 0 0 2h.01a1 1 0 1 0 0-2H9Z" clip-rule="evenodd"/>
        </svg>

        <strong class="menuitem-texto" style="<?= $agenda ?>">Agendar Recebimento</strong>
    </a>
</div>
