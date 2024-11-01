<?php
//session_start(); //! Todos as paginas que usam ja tem o "session_start()"

if (!$_SESSION['logado']) {
    header("Location: login.php");
    exit;
}
?>

<div id="header" style="font-size: x-large;">
    
    <img src="img/logotipo-horizontal.png" style="width: 200px; margin-left: 25px;">
    
    <div>
        
        <div style="display:inline-block; margin-top: 10px; font-size: 14px; line-height: 20px; font-weight: bold;">
            <?= $_SESSION["razaosocial"] ?>
        </div>

        <div style="display:inline-block; color: #AAA; margin: 10px; padding-left: 10px; padding-right: 5px; border-left: 1px solid #AAA">
            <a href="php/login_sair.php" title="Sair" class="link1" style="font-size: 14px; display:flex; justify-content: space-between; align-items: center; color: #BE1111;">
    
                <span style="margin-right: 3px;font-weight: bold;">Sair</span>

                <svg xmlns="http://www.w3.org/2000/svg" style="color: #BE1111;" width="18" height="18" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H8m12 0-4 4m4-4-4-4M9 4H7a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h2"/>
                </svg>

            </a>
        </div>

    </div>

</div>
