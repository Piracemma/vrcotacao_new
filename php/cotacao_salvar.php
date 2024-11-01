<?php

session_start();

if (!$_SESSION['logado']) {
    header("Location: ../login.php");
    exit;
}

include("conexao.php");
include("global.php");

try {
    pg_query($con, "BEGIN");

    $cotacao = $_POST["cotacao"];
    $data = $_POST["data"];
    $vcodigo = $_POST["codigo"];
    $vcusto = $_POST["custo"];
    $vtipoembalagem = $_POST["tipoembalagem"];
    $vobservacao = $_POST["observacao"];
    $vqtdembalagem = $_POST["qtdembalagem"];
    
    reset($vcodigo);

    $i = 0;
    $erro = false;

    $erroPrazo = false;

    while (each($vcodigo)) {
        $codigo = $vcodigo[$i];
        $custo = $vcusto[$i] == "" ? 0 : formatDouble($vcusto[$i]);
        $tipoembalagem = $vtipoembalagem[$i];
        $observacao = $vobservacao[$i];
        $qtdembalagem = $vqtdembalagem[$i];
        
        if ($custo == 0) {
            excluirItem($codigo, $cotacao);
        } else if ($custo > 0) {
            
            if (validarCusto($codigo, $tipoembalagem, $custo)) {
                $erro = true;
            } else {
                if (validarCotacao() == 0) {
                    $erroPrazo = true;
                } else {
                    salvarCotacao($cotacao, $data, $codigo, $custo, $observacao, $qtdembalagem);
                }
            }
        }

        $i++;
    }

    pg_query($con, "COMMIT");

    if ($erro) {
        throw new Exception("Alguns custos estão fora da margem permitida e não serão salvos.");
    }

    if ($erroPrazo) {
        throw new Exception("Período expirado para a esta cotação!");
    }

    if (consultarFinalizado($cotacao) == true) {
        throw new Exception("Esta cotação já foi finalizada e não pode ser alterada.");
    }
} catch (Exception $ex) {
    pg_query($con, "ROLLBACK");
    echo($ex->getMessage());
}
?>