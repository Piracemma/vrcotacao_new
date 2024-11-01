<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<?php
include("php/versao.php");

$versao = getVersao();
$nome = getNome();
$data = getData();

?>

<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" media="all" type="text/css" href="css/vr.css">
    <link rel="stylesheet" media="all" type="text/css" href="css/bootstrap.min.css">
    <link rel="shortcut icon" type="image/x-icon" href="img/favicon.ico" />

    <title><?= $nome ?></title>

    <script type="text/javascript" src="js/vr.js"></script>
    <script type="text/javascript" src="js/ajax.js"></script>

    <script type="text/javascript">
        function entrar() {
            oXMLhttp = criaXMLHttpRequest();

            var url = "php/login_entrar.php";
            var param = "";

            param += "codigo=" + document.getElementById("codigo").value + "&";
            param += "senha=" + document.getElementById("senha").value;

            oXMLhttp.open("POST", url, true);
            oXMLhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

            oXMLhttp.onreadystatechange = function() {
                if (oXMLhttp.readyState == 1) {
                    document.getElementById("mensagem_entrar").innerHTML = "<img src=\"img/aguarde.gif\">&nbsp;&nbsp;&nbsp;Aguarde";
                }

                if (oXMLhttp.readyState == 4 && oXMLhttp.status == 200) {
                    var result = oXMLhttp.responseText;

                    if (result.length > 0) {
                        document.getElementById("mensagem_entrar").innerHTML = result;
                    } else {
                        top.location = "index.php";
                    }
                }
            }

            oXMLhttp.send(param);
        }
    </script>

    <style>
        body {
            background: #FAFAFA
        }

        .login {
            position: absolute;
            top: 50%;
            left: 50%;
            margin-top: -142px;
            margin-left: -225px;
            width: 450px;
            height: 284px;
            border-radius: 1.13044rem;
            background: linear-gradient(180deg, #FEFEFE 0%, #DCDCDC 100%);
        }

        .senha {
            width: 180px;
            height: 113px;
            left: 235px;
            position: absolute;
            top: 105px;
            background: transparent;
            color: #000;
            text-align: center
        }


        .esqueci-senha {
          margin-top: 1rem;
        }

        .esqueci-senha__link {
          font-size: 13px;
          margin-left: 76px;
          height: 22px;
          width: 76px;
          text-decoration-line: underline;
          color: #000;
        }

        #mensagem_entrar {
            position: absolute;
            text-align: center;
            left: 50%;
            top: 50%;
            margin-left: -225px;
            margin-top: -190px;
            width: 450px;
            height: 40px;
            color: #000;
            vertical-align: middle
        }
    </style>
</head>

<body onload="document.getElementById('codigo').focus();">
    <div id="mensagem_entrar"></div>

    <div class="login">
        <div class="logotipo"></div>
        <img src="img/logotipo.png" style="  margin-top: 45px;  margin-left: 35px; height: 200px">

        <div class="senha">
            <table>
                <tr>
                    <td width="70px" height="35px" style="font-size: 11px; vertical-align: middle; line-height: 15px; padding-left: 12px;">Código</td>
                    <td><input type="text" name="codigo" id="codigo" size="14" maxlength="12" onblur="maiusculo(this)" style=" width: 85px; padding-left: 0px; height: 12px;"></td>
                </tr>
                <tr>
                    <td width="70px" height="35px" style="font-size: 11px; vertical-align: middle; line-height: 15px; padding-left: 12px;">Senha</td>
                    <td><input type="password" name="senha" id="senha" size="14" maxlength="12" onblur="maiusculo(this);" onkeydown="if (event.keyCode === 13) entrar();" style=" width: 85px; padding-left: 0px; height: 12px;"></td>
                </tr>
            </table>
              <a href="#" class="btn btn-mini" onclick="entrar()" style="font-size: 13px;  margin-left: 51px;margin-top: 2px;width: 76px;height: 22px; margin-left: 76px;"><i class="icon-lock" style="margin-top: 1px"></i> Entrar</a>
              <div class="esqueci-senha">
                <a href="esqueci_senha.php" class="esqueci-senha__link">Esqueci a Senha</a>
              </div>
        </div>
    </div>
    <div id="footer">
        <?php
        include("footer.php");
        ?>
    </div>
</body>

</html>
