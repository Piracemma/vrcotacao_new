<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<?php
session_start();

if (!$_SESSION['logado']) {
    header("Location: login.php");
    exit;
}

include("php/conexao.php");
include("php/global.php");
include("php/versao.php");

$versao = getVersao();
$nome = getNome();
?>

<html>

<head>
    <title><?= $nome?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" media="all" type="text/css" href="css/vr.css">
    <link rel="stylesheet" media="all" type="text/css" href="css/bootstrap.min.css">
    <link rel="shortcut icon" type="image/x-icon" href="img/favicon.ico" />
    <link rel="stylesheet" type="text/css" href="css/jquery/ui.all.css" />
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

<body onload="javascript: consultar(0)">
    <div id="container">

        <div style="position: fixed; width: 100%;">
            <?php
            include("header.php");
            include("menu.php");
            ?>
        </div>

        <div id="content" style="padding-left: 0px; ">
            <div id="aguarde" style="display: none; background: url(img/aguarde.gif) no-repeat; background-position: center;font-size: 14px; font-weight: bold;text-align: center; margin-top: 138px;"> <br><br><br>Aguarde </div>
            <div id="tabela_cotacao" style="margin-top: 82px; background-color: #FFFFFF;"></div>
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
    <script type="text/javascript" src="js/exceljs/exceljs.js"></script>

    <link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox-1.3.4.css" media="screen" />

    <script type="text/javascript">
        function runEffect() {
            $("#tabela_cotacao").show('blind', 500);
        };
    </script>

    <script type="text/javascript" src="js/ajax.js"></script>
    <script type="text/javascript" src="js/vr.js"></script>
    <script type="text/javascript" src="js/menu.js"></script>

    <script type="text/javascript">
        function consultar(id) {
            oXMLhttp = criaXMLHttpRequest();

            var url = "php/cotacao_consultar.php";
            var param = "cotacao=" + id;

            oXMLhttp.open("POST", url, true);
            oXMLhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

            document.getElementById("tabela_cotacao").innerHTML = "<div style=\"background: url(img/aguarde.gif) no-repeat; background-position: center;font-size: 14px; font-weight: bold;text-align: center;\"> <br><br><br>Aguarde </div>"

            oXMLhttp.onreadystatechange = function() {
                if (oXMLhttp.readyState == 1) {
                    document.getElementById("tabela_cotacao").innerHTML = exibirMensagem("Aguarde...", "aguarde.gif");
                    document.getElementById("mensagem_salvar").innerHTML = "";
                }

                if (oXMLhttp.readyState == 4 && oXMLhttp.status == 200) {
                    var result = oXMLhttp.responseText;
                    document.getElementById("tabela_cotacao").innerHTML = result;

                    document.getElementById("footer").style.position = "fixed";
                }
            }

            oXMLhttp.send(param);
        }

        function salvar() {
            oXMLhttp = criaXMLHttpRequest();

            var url = "php/cotacao_salvar.php";
            var param = "";

            i = 0;

            document.getElementById("tabela_cotacao").style.display = 'none';
            document.getElementById("aguarde").style.display = 'block';

            while (document.getElementById("custo[" + i + "]")) {
                param += "codigo[" + i + "]=" + document.getElementById("codigo[" + i + "]").value + "&";
                param += "custo[" + i + "]=" + document.getElementById("custo[" + i + "]").value + "&";
                param += "tipoembalagem[" + i + "]=" + document.getElementById("tipoembalagem[" + i + "]").value + "&";
                param += "qtdembalagem[" + i + "]=" + document.getElementById("qtdembalagem[" + i + "]").value + "&";
                param += "observacao[" + i + "]=" + document.getElementById("observacao[" + i + "]").value + "&";
                i++;
            }

            var tabela_cotacao_anterior = document.getElementById("tabela_cotacao").value;

            param += "cotacao=" + document.getElementById("cotacao").value + "&";
            param += "data=" + document.getElementById("datacotacao").value;

            oXMLhttp.open("POST", url, true);
            oXMLhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

            oXMLhttp.onreadystatechange = function() {
                if (oXMLhttp.readyState == 1) {
                    document.getElementById("mensagem_salvar").innerHTML = exibirMensagem("Aguarde...", "aguarde.gif");
                }

                if (oXMLhttp.readyState == 4 && oXMLhttp.status == 200) {
                    document.getElementById("tabela_cotacao").style.display = 'block';
                    document.getElementById("tabela_cotacao").value = tabela_cotacao_anterior;
                    document.getElementById("aguarde").style.display = 'none';

                    var result = oXMLhttp.responseText;

                    if (result.length > 0) {
                        fancyMsgErro(result);
                    } else {
                        fancyMsg("Salvo com sucesso!");
                    }

                }
            }

            oXMLhttp.send(param);
        }

        function fancyMsg(msg) {
            jQuery.fancybox({
                'modal': true,
                'content': "<div style=\"margin:1px;width:150px;\"><i class='icon-exclamation-sign'></i> " + msg + "<div style=\"text-align:right;margin-top:10px;\">\n\
                     <input class='btn btn-mini' type=\"button\" onclick=\"jQuery.fancybox.close();\" value=\"  Ok  \"></div>\n\
                    </div>"
            });
        }

        function fancyMsgErro(msg) {
            jQuery.fancybox({
                'modal': true,
                'content': "<div style=\"margin:1px;width:90%;\"><i class='icon-warning-sign'></i>  " + msg + "<div style=\"text-align:right;margin-top:10px;\">\n\
                     <input class='btn btn-mini' type=\"button\" onclick=\"jQuery.fancybox.close();\" value=\"  Ok  \"></div>\n\
                    </div>"
            });
        }

        function validarCusto(i) {

            erro = false;

            if (document.getElementById("custo[" + i + "]").value === "") {
                document.getElementById("mensagem_custo[" + i + "]").innerHTML = "";
            }

            if (erro) {
                return;
            }

            var oXMLhttpCusto = new Array();
            oXMLhttpCusto[i] = criaXMLHttpRequest();

            var url = "php/cotacao_validarcusto.php";
            var param = "";

            param += "cotacao=" + document.getElementById("cotacao").value + "&";
            param += "data=" + document.getElementById("datacotacao").value + "&";
            param += "codigo=" + document.getElementById("codigo[" + i + "]").value + "&";
            param += "custo=" + document.getElementById("custo[" + i + "]").value + "&";
            param += "tipoembalagem=" + document.getElementById("tipoembalagem[" + i + "]").value + "&";
            param += "observacao=" + document.getElementById("observacao[" + i + "]").value + "&";

            oXMLhttpCusto[i].open("POST", url, true);
            oXMLhttpCusto[i].setRequestHeader("Content-type", "application/x-www-form-urlencoded");

            oXMLhttpCusto[i].onreadystatechange = function() {
                if (oXMLhttpCusto[i].readyState == 4 && oXMLhttpCusto[i].status == 200) {
                    var result = oXMLhttpCusto[i].responseText;

                    if (result == ">") {
                        document.getElementById("mensagem_custo[" + i + "]").innerHTML = "<i class=\"icon-arrow-up\" title=\"Custo acima da margem permitida\"></i>";
                    } else if (result == "<") {
                        document.getElementById("mensagem_custo[" + i + "]").innerHTML = "<i class=\"icon-arrow-down\" title=\"Custo abaixo da margem permitida\"></i>";
                    } else {
                        document.getElementById("mensagem_custo[" + i + "]").innerHTML = "";
                    }
                }
            }

            oXMLhttpCusto[i].send(param);
        }

        function acertarCustoFamilia(fld, index) {
            var familia = document.getElementById("familia[" + index + "]").value

            if (familia == "" || familia == "0") {
                return;
            }

            i = 0;

            while (document.getElementById("custo[" + i + "]")) {
                if (document.getElementById("familia[" + i + "]").value == familia && i != index) {
                    document.getElementById("custo[" + i + "]").value = fld.value;

                    validarCusto(i);
                }

                i++;
            }
        }

        async function exportaXlsx() {

            const workbook = new ExcelJS.Workbook();

            const cboCotacao = document.getElementById("cboCotacao");
            const selectedIndex = cboCotacao.selectedIndex;
            const nomeCotacao = document.getElementById("cboCotacao").options[selectedIndex].text;

            const colCodigo = 1;
            const colDescricao = 2;
            const colMercadologico = 3;
            const colCodigobarras = 4;
            const colQuantidade = 5;
            const colEmbalagem = 6;
            const colCusto = 7;
            const colObservacao = 8;

            const primeiraLinhaConteudo = 11;

            fetch('assets/layout_cotacao.xlsx')
                .then(response => response.arrayBuffer())
                .then(data => workbook.xlsx.load(data))
                .then(async () => {
                    const worksheet = workbook.getWorksheet(1);

                    const cell = worksheet.getCell('B1');
                    cell.value = nomeCotacao;

                    let i = 0;

                    while (document.getElementById("codigoexp[" + i + "]")) {
                        const codigo = document.getElementById("codigoexp[" + i + "]").innerHTML;
                        const descricao = document.getElementById("descricaoexp[" + i + "]").innerHTML;
                        const mercadologico = document.getElementById("mercadologicoexp[" + i + "]").value;
                        const codigobarras = document.getElementById("codigobarrasexp[" + i + "]").innerHTML.replaceAll('<br>', '\n');
                        const quantidade = document.getElementById("quantidadeexp[" + i + "]").innerHTML;
                        const embalagem = document.getElementById("embalagemexp[" + i + "]").innerHTML;
                        const custo = document.getElementById("custo[" + i + "]").value;
                        const observacao = document.getElementById("observacao[" + i + "]").value;

                        const row = worksheet.getRow(primeiraLinhaConteudo + i);

                        row.getCell(colCodigo).value = codigo;
                        row.getCell(colDescricao).value = descricao;
                        row.getCell(colMercadologico).value = mercadologico;
                        row.getCell(colCodigobarras).value = codigobarras;
                        row.getCell(colQuantidade).value = quantidade;
                        row.getCell(colEmbalagem).value = embalagem;
                        row.getCell(colCusto).value = isNaN(custo) ? "" : custo;
                        row.getCell(colObservacao).value = observacao;

                        i++;
                        
                    }

                    const encontrarMaiorString = (strings) => strings.reduce((longest, current) => current.length > longest.length ? current : longest, '');

                    //Ajusta largura das colunas
                    worksheet.columns.forEach(column => {
                        let maxLength = 0;

                        column.eachCell(cell => {
                            if (cell.row >= primeiraLinhaConteudo - 1) {
                                let columnLength;

                                if (typeof cell.value === 'string' || cell.value instanceof String) {
                                    let linhas = cell.value.split('\n');
                                    columnLength = encontrarMaiorString(linhas).length;

                                } else if (typeof cell.value === 'number') {
                                    columnLength = cell.value.toString().length;

                                } else if (cell.value instanceof Date) {
                                    columnLength = cell.value.toISOString().length;

                                } else if (cell.value && typeof cell.value === 'object' && 'text' in cell.value) {
                                    columnLength = cell.value.text.length;

                                } else {
                                    columnLength = 10; // Tamanho padrão para valores desconhecidos ou nulos

                                }

                                if (columnLength > maxLength) {
                                    maxLength = columnLength;
                                }
                            }
                    });

                        column.width = maxLength + 5; // Adiciona uma margem
                    });

                    // Ajusta altura das linhas
                    worksheet.eachRow((row, rowNumber) => {
                        let maxLineHeight = 1;
                        row.eachCell({ includeEmpty: true }, cell => {
                            let lines;
                            if (typeof cell.value === 'string' || cell.value instanceof String) {
                                lines = cell.value.split('\n').length;
                            } else if (typeof cell.value === 'number') {
                                lines = 1;
                            } else if (cell.value instanceof Date) {
                                lines = 1;
                            } else if (cell.value && typeof cell.value === 'object' && 'text' in cell.value) {
                                lines = cell.value.text.split('\n').length;
                            } else {
                                lines = 1; // Altura padrão para valores desconhecidos ou nulos
                            }

                            if (lines > maxLineHeight) {
                                maxLineHeight = lines;
                            }
                        });

                        row.height = maxLineHeight * 15; // Estimativa de altura por linha.
                    });

                    // Proteção de celulas
                    worksheet.columns.forEach((column, columnIndex) => {
                            column.eachCell((cell) => {
                                cell.protection = {
                                    locked: true,
                                };
                            });
                        
                    });

                    //Desbloqueia coluna Custo depois da linha 11
                    worksheet.getColumn(colCusto).eachCell((cell) => {
                        if (cell.row >= primeiraLinhaConteudo) {
                            cell.protection = { locked: false };
                        }
                    });

                    //Desbloqueia coluna observação depois da linha 11
                    worksheet.getColumn(colObservacao).eachCell((cell) => {
                        if (cell.row >= primeiraLinhaConteudo) {
                            cell.protection = { locked: false };
                        }
                    });

                    const senha = await getSenhaXLSX();

                    worksheet.protect(senha, {
                        autoFilter: true, //Permite autofiltro
                        sort: true,
                        selectLockedCells: true,
                        selectUnlockedCells: true
                    });

                    // Salvar as mudanças
                    return workbook.xlsx.writeBuffer();
                })
                .then(buffer => {
                    const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                    const link = document.createElement('a');
                    link.style.display = 'none';
                    link.href = URL.createObjectURL(blob);
                    link.download = nomeCotacao + '.xlsx';
                    link.click();
                });
        
        }

        async function getSenhaXLSX() {
            const url = "php/senha_xlsx.php";
            const response = await fetch(url);
            const json = await response.json();

            return json.senha;
        }

        async function importaXlsx() {
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = '.xlsx';
            input.style.display = 'none';

            const colCodigo = 1;
            const colCusto = 7;
            const colObservacao = 8;

            input.addEventListener('change', async (event) => {
                try {
                    const file = event.target.files[0];

                    if (!file) return;

                    const extensao = file.name.split('.').pop().toLowerCase();

                    //Valida extensão do arquivo importado
                    if (extensao !== "xlsx") {
                        fancyMsgErro('Apenas arquivos .xlsx são permitidos.');
                    }

                    const workbook = new ExcelJS.Workbook();
                    await workbook.xlsx.load(file);

                    const worksheet = workbook.getWorksheet(1);

                    const mapProdutoValores = new Map();

                    //Consolida em um map os valores de custo e observação
                    worksheet.eachRow((row, rowNumber) => {
                        if (rowNumber >= 11) {
                            const codigo = row.getCell(colCodigo).value;
                            const custo = row.getCell(colCusto).value;
                            const observacao = row.getCell(colObservacao).value;

                            mapProdutoValores.set(codigo, {
                                custo: custo,
                                observacao: observacao
                            });
                        }
                    });

                    const formata = (valor) => {
                        if (valor === null
                            || valor === '') {
                            return "";
                        }

                        valor = String(valor).split('').reverse().join('')
                            .replace(',', '.')
                            .split('').reverse().join('');

                        if (isNaN(valor)) {
                            return "";
                        }

                        if (!valor.includes('.')){
                            valor += '.00'
                        }

                        return formataCusto(valor);
                    }

                    let i = 0;

                    while (document.getElementById("codigoexp[" + i + "]")) {
                        const codigo = document.getElementById("codigoexp[" + i + "]").innerHTML;

                        if (mapProdutoValores.has(codigo)) {
                            const valores = mapProdutoValores.get(codigo);

                            let custo = formata(valores.custo);

                            const observacao = valores.observacao == null ? "" : valores.observacao.toUpperCase();

                            const inputCusto = document.getElementById("custo[" + i + "]");
                            inputCusto.value = custo;
                            //Chama os eventos de alteração, para validar o custo, caso necessário.
                            inputCusto.focus();
                            inputCusto.blur();

                            const inputObservacao = document.getElementById("observacao[" + i + "]");
                            inputObservacao.value = observacao;
                        }

                        i++;
                    }

                    fancyMsg("Importado com sucesso!");
                } catch (error) {
                    fancyMsgErro(error);
                }

            });        

            input.click();
    }
    </script>
</body>

</html>