<?php
session_start();

include("conexao.php");
include("global.php");
include("atualizar_tabelas.php");

$codigo = $_POST["codigo"];
$senha = $_POST["senha"];

if ($codigo == "") {
    echo("Código e/ou senha inválido");
    exit();
}

$sql = "SELECT id, razaosocial, senha, id_estado FROM fornecedor WHERE id::varchar(6) = '$codigo' AND id_situacaocadastro = 1";
$rst = pg_query($con, $sql);

$row = pg_fetch_array($rst);

if (!$row || $row["senha"] != $senha) {
    echo("Código e/ou senha inválido");
    exit();
}

// atualizar tabelas do banco de dados
atualizar();

$_SESSION["logado"] = true;
$_SESSION["fornecedor"] = $row["id"];
$_SESSION["razaosocial"] = $row["razaosocial"];
$_SESSION["estado"] = $row["id_estado"];

?>