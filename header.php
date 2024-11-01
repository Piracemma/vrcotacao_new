<?php

if (!$_SESSION['logado']) {
    header("Location: login.php");
    exit;
}
?>

<div id="header" style="font-size: x-large;">
    <div style="float: right; color: #AAA; margin: 10px; padding-left: 10px; padding-right: 5px; border-left: 1px solid #AAA">
        <a href="php/login_sair.php" title="Sair" class="link1" style="font-size: 14px;line-height: 20px;">

            <i class="icon-off" style="margin-top: 1px"></i>
        </a>
    </div>

    <div style="float: right; margin-top: 10px; font-size: 14px; line-height: 20px;">
        <?= $_SESSION["razaosocial"] ?>
    </div>

    <img src="img/logotipo-horizontal.png" style="width: 200px;  margin-top: 16px; margin-left: 25px;">
</div>
