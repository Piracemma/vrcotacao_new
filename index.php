<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<?php
session_start();

if (!$_SESSION['logado']) {
    header("Location: login.php");
    exit;
}

include_once("php/versao.php");
include_once("php/conexao.php");
include("php/global.php");

$versao = getVersao();
$nome = getNome();

$sql = "SELECT mensagem FROM precotacaofornecedormensagem";
$rst = pg_query($con, $sql);

// $row = pg_fetch_all($rst);
$row = pg_fetch_array($rst);

$mensagem = htmlspecialchars($row["mensagem"], ENT_NOQUOTES, 'UTF-8');
// $mensagem = utf8_encode($row["mensagem"]);
?>

<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" media="all" type="text/css" href="css/vr.css">
    <link rel="shortcut icon" type="image/x-icon" href="img/favicon.ico" />
    <link rel="stylesheet" media="all" type="text/css" href="css/bootstrap.min.css">

    <title><?= $nome ?></title>

    <script type="text/javascript" src="js/menu.js"></script>
</head>

<body>
    <div id="container">

        <?php
        include("header.php");
        include("menu.php"); 
        ?>

        <div id="content">
            <div>
                <?php
                    $cotacoes = listarCotacaoAberta();

                    if(!empty($cotacoes)){
                
                ?>
                    <h2 style="color: #00d7ac; font-size: x-large; font-weight: bold; margin-bottom: 10px; line-height: normal;">Cotações:</h2>
                <?php

                        foreach($cotacoes as $cotacao) {
                ?>

                            <div style="border:2px solid #00d7ac; border-radius: 10px; margin:15px 0px; box-shadow: 1px 2px 3px 1px rgba(0,0,0,0.5); display: flex; justify-content:space-between; align-items:center; padding: 15px;">
                                <p style="margin: 0px;"><?= $cotacao['id'] ?> - <?= $cotacao['descricao'] ?></p>
                                <a href="cotacao.php?cotacao=<?= $cotacao['id'] ?>" class="botao">Entrar</a>
                            </div>

                <?php   
                        }
                    } else {
                ?>
                        <div style="display: flex; justify-content:center; font-size:large; margin:15px 0px; color: #a1a1a1;">Você não possui nenhuma cotação aberta no momento.</div>
                <?php } ?>
            </div>
        </div>

        <div id="footer">
            <?php
            include("footer.php");
            ?>
        </div>

    </div>
</body>

</html>