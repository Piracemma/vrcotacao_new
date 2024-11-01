<?php

session_start();

if (!$_SESSION['logado']) {
    header("Location: ../login.php");
    exit;
}

include("conexao.php");
include("global.php");

$cotacao = $_POST["cotacao"];
$data = $_POST["data"];
$codigo = $_POST["codigo"];
$custo = $_POST["custo"] == "" ? 0 : formatDouble($_POST["custo"]);
$tipoembalagem = $_POST["tipoembalagem"];
$observacao = $_POST["observacao"];
$qtdembalagem = $_POST["qtdembalagem"];

if ($custo == 0) {
    excluirItem($codigo, $cotacao);
} else {
    $retorno = validarCusto($codigo, $tipoembalagem, $custo);

    if ($retorno) {
        echo $retorno;
        exit();
    }
    
    if (consultarFinalizado($cotacao) != true) {
        salvarCotacao($cotacao, $data, $codigo, $custo, $observacao, $qtdembalagem);
    }
}
?>