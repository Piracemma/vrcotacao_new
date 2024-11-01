<?php

session_start();

if (!$_SESSION['logado']) {
    header("Location: ../login.php");
    exit;
}

include ("conexao.php");
include ("global.php");


//verifica agenda fornecedor
$sql = "SELECT DISTINCT cotacao.id";
$sql .= " FROM   precotacaofornecedor AS cotacao";
$sql .= " INNER JOIN precotacaofornecedoritemfornecedor AS pfif";
$sql .= " ON pfif.id_precotacaofornecedor = cotacao.id ";
$sql .= " WHERE  cotacao.id_situacaoprecotacaofornecedor = 1 ";
$sql .= " AND NOW() BETWEEN cotacao.datahorainicio AND cotacao.datahoratermino";
$sql .= " AND pfif.id_fornecedor = $_SESSION[fornecedor]";
$sql .= " ORDER  BY cotacao.id DESC ";

$rst = pg_query($con, $sql);

$row = pg_fetch_array($rst);

if (!$row) {
    echo "<div id='content' style='margin-right: -15px;'>";
    echo "<div class='alert alert-error'>";
    echo "<h4>ATENÇÃO</h4>";
    echo "Sua empresa não possui cotações disponíveis neste momento";
    echo "</div>";
    echo "</div>";

    exit();
}

//verifica ultima cotacao
if (!$_POST["cotacao"]) {
    $sql = "SELECT precotacaofornecedor.id, precotacaofornecedor.data, precotacaofornecedor.observacao,";
    $sql .= " precotacaofornecedor.datahorainicio, precotacaofornecedor.datahoratermino, precotacaofornecedor.enviaprodutozerado from precotacaofornecedor";
    $sql .= " INNER JOIN precotacaofornecedoritemfornecedor AS pfif ON  pfif.id_precotacaofornecedor = precotacaofornecedor.id";
    $sql .= " WHERE id_situacaoprecotacaofornecedor = 1 ";
    $sql .= " AND NOW() BETWEEN datahorainicio AND datahoratermino";
    $sql .= " AND pfif.id_fornecedor =  $_SESSION[fornecedor]";
    $sql .= " ORDER BY id desc";

} else {
    $sql = "select id, data, observacao, datahorainicio, datahoratermino, enviaprodutozerado from precotacaofornecedor where id = $_POST[cotacao]";
}

$rst = pg_query($con, $sql);

$row = pg_fetch_array($rst);

if (!$row) {
    echo "<div id = 'content'>";
    echo "<div class = 'alert alert-error'>";
    echo "<h4>ATENÇÃO</h4>";
    echo "Não há cotações disponíveis";
    echo "</div>";
    echo "</div>";

    exit();
}

$cotacao = $row["id"];
$datacotacao = formatDataGUI($row["data"]);
$dataHoraInicio = formatDataHoraGUI($row["datahorainicio"]);
$dataHoraTermino = formatDataHoraGUI($row["datahoratermino"]);
$observacaocotacao = $row["observacao"];
$enviaProdutoZerado = $row["enviaprodutozerado"];


echo "<div style = 'position: fixed; background-color: #FFFFFF; border-bottom: 1px solid; border-color: #B1B1B1;'>";
echo "<div class = 'legenda'>";
echo "<div style = 'vertical-align: top; margin-left: 15px; margin-right: 15px;'>";
echo "<span style = 'width: 50px; height: 43px; padding-top: 10px; color: #00d7ac; font-weight: bold; font-size:18px;'>Cotação:&nbsp</span>";
echo "<span style = 'padding-left: 0px; padding-top: 19px'><select id = 'cboCotacao' onchange = 'consultar(this.value)' style = 'height:23px; padding-top: 2px; padding-right: 6px; width:auto; margin-bottom: 0px;'>" . carregarCotacaoAberta($cotacao) . "</select>";
echo "<input type = 'hidden' name = 'cotacao' id = 'cotacao' value = '$cotacao'>";
echo "<input type = 'hidden' name = 'datacotacao' id = 'datacotacao' value = '$datacotacao'></span>";
echo "&nbsp&nbsp";
echo "<span><span style= 'font-size: 80%'><span style='font-weight: bold'>Data Inclusão:</span> $datacotacao</span></span>";
echo "&nbsp&nbsp";
echo "<span><span style= 'font-size: 80%'><span style='font-weight: bold'>Data/Hora Início:</span> $dataHoraInicio</span></span>";
echo "&nbsp&nbsp";
echo "<span><span style= 'font-size: 80%'><span style='font-weight: bold'>Data/Hora Término:</span> $dataHoraTermino</span></span>";
echo "&nbsp&nbsp";
echo "<span style = 'font-size: 80%'><i class = 'icon-arrow-down' style = 'margin-top: -5px;'></i><i class = 'icon-arrow-up' style = 'margin-top: -5px;'></i>&nbsp;
Custo abaixo ou acima da margem permitida</span>";
echo "</div>";
echo "</div>";
echo "</div>";
echo "<div style='height: 70px;'></div>";

if ($observacaocotacao != "") {
    echo "<div class = 'alert alert-block' style = 'margin-left: 15px;'>";
    echo $observacaocotacao;
    echo "</div>";
}

//consulta cotacao
$sql = "SELECT  item.id_produto as id, coalesce(cotacaofornecedoritem.qtdembalagem,item.qtdembalagem) as qtdembalagem, produto.descricaocompleta, produto.id_tipoembalagem, SUM(item.quantidade) AS quantidade, ";
$sql .= " pre.data, tipoembalagem.descricao AS descricaoembalagem, produto.id_familiaproduto, ";
$sql .= " coalesce(cotacaofornecedoritem.custo, 0.000) as custo, merc.descricao, coalesce(cotacaofornecedoritem.observacao, '') as observacao, ";
$sql .= " (SELECT ARRAY_TO_STRING(ARRAY_AGG(codigobarras), '<br>')  FROM produtoautomacao WHERE id_produto = produto.id) AS codigoautomacao";
$sql .= " FROM precotacaofornecedor AS pre";
$sql .= " INNER JOIN precotacaofornecedoritem AS item ON pre.id = item.id_precotacaofornecedor";
$sql .= " LEFT JOIN cotacaofornecedor ON cotacaofornecedor.id_precotacaofornecedor = pre.id AND cotacaofornecedor.id_fornecedor = $_SESSION[fornecedor]";
$sql .= " LEFT JOIN cotacaofornecedoritem ON cotacaofornecedor.id = cotacaofornecedoritem.id_cotacaofornecedor AND cotacaofornecedoritem.id_produto = item.id_produto";
$sql .= " INNER JOIN produto ON produto.id = item.id_produto";
$sql .= " INNER JOIN tipoembalagem AS tipoembalagem ON tipoembalagem.id = produto.id_tipoembalagem";
$sql .= " INNER JOIN mercadologico AS merc ON produto.mercadologico1 = merc.mercadologico1 AND merc.nivel = 1";
$sql .= " LEFT JOIN fornecedor ON fornecedor.id = cotacaofornecedor.id_fornecedor";
$sql .= " WHERE pre.id_situacaoprecotacaofornecedor = 1";
$sql .= " AND pre.id = $cotacao";

if ($enviaProdutoZerado === "f") {
    $sql .= " AND item.quantidade > 0";
}

$sql .= " GROUP BY item.id_produto, coalesce(cotacaofornecedoritem.qtdembalagem,item.qtdembalagem), produto.descricaocompleta, produto.id_tipoembalagem, produto.id_familiaproduto, ";
$sql .= " pre.data, tipoembalagem.descricao, coalesce(cotacaofornecedoritem.custo, 0.000), merc.descricao, coalesce(cotacaofornecedoritem.observacao, ''), ";
$sql .= " (SELECT ARRAY_TO_STRING(ARRAY_AGG(codigobarras), '<br>')  FROM produtoautomacao WHERE id_produto = produto.id)";
$sql .= " ORDER BY merc.descricao, produto.descricaocompleta ASC";

$rst = pg_query($con, $sql);

$row = pg_fetch_array($rst);

if (!$row) {
    echo exibirMensagem("Cotação não encontrada", "erro.png");
    exit();
}

//exibe produto
$mercadologico = "";
$i = 0;

do {
    if ($i % 2 == 0) {
        $destaque = "gridNormal";
    } else {
        $destaque = "gridDestaque";
    }

    if ($mercadologico != $row["descricao"]) {
        if ($mercadologico != "") {
            echo "</tbody>";
            echo "</table>";
            echo "<br>";
        }

        echo "<table class = 'grid'>";
        echo "<thead>";
        echo "<tr>";
        echo    '<td class = "gridM" style = "width: 60px; background: #00d7ac; color: #FFF; border-radius: 10px 10px 0px 0px;box-shadow: 1px -3px 7px 0px rgba(0,0,0,0.1);">
                    <div style="display:flex; justify-content:start; align-items:center; padding: 3px 0px; margin-left: 3px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M9 8h10M9 12h10M9 16h10M4.99 8H5m-.02 4h.01m0 4H5"/>
                        </svg>
                        <span>'.$row["descricao"].'</span>
                    </div>
                </td>';
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";

        echo "<table class = 'grid' style='box-shadow: 1px 3px 7px 0px rgba(0,0,0,0.2);'>";
        echo "<thead>";
        echo "<tr>";
        echo "<td class = 'gridM' style = 'width: 60px'>Código</td>";
        echo "<td class = 'gridM' style = 'width: 90%'>Descrição</td>";
        echo "<td class = 'gridM' style = 'width: 100px'>Código Barra</td>";
        echo "<td class = 'gridM' style = 'width: 90px'>Quantidade</td>";
        echo "<td class = 'gridM' style = 'width: 80px'>Embalagem</td>";
        echo "<td class = 'gridM' style = 'width: 20px'></td>";
        echo "<td class = 'gridM' style = 'width: 120px'>Custo</td>";
        echo "<td class = 'gridM' width = '425'>Observação</td>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
    }

    $codigo = formatNumber($row["id"], 6);
    $codigoautomacao = formatNumber($row["codigoautomacao"], 13);
    $embalagem = $row["descricaoembalagem"] . "/" . formatNumber($row["qtdembalagem"], 4);
    $qtdembalagem = $row["qtdembalagem"];

    if (isTipoEmbalagemFracionado($row["id_tipoembalagem"])) {
        $quantidade = number_format($row["quantidade"], 3, ", ", ".");
    } else {
        $quantidade = number_format($row["quantidade"], 0, ", ", ".");
    }

    if ($row["custo"] == 0) {
        $custo = "";
    } else {
        $custo = formatCusto($row["custo"]);
    }

    $observacao = $row["observacao"];

    echo "<tr>";
    echo "<td class = '$destaque'><span id = 'codigoexp[$i]'>$codigo</span><input type = 'hidden' name = 'codigo[$i]' id = 'codigo[$i]' value = '$row[id]'></td>";
    echo "<td class = '$destaque'><span id = 'descricaoexp[$i]'>$row[descricaocompleta]</span><input type = 'hidden' name = 'tipoembalagem[$i]' id = 'tipoembalagem[$i]' value = '$row[id_tipoembalagem]'>";
    echo "<input type = 'hidden' name = 'familia[$i]' id = 'familia[$i]' value = '$row[id_familiaproduto]'>";
    echo "<input type = 'hidden' name = 'mercadologico[$i]' id = 'mercadologicoexp[$i]' value = '$row[descricao]'</td>";
    echo "<td class = '$destaque' id = 'codigobarrasexp[$i]'>$codigoautomacao</td>";
    echo "<td class = '$destaque' id = 'quantidadeexp[$i]'>$quantidade</td>";
    echo "<td class = '$destaque'><span id = 'embalagemexp[$i]'>$embalagem</span><input type = 'hidden' name = 'qtdembalagem[$i]' id = 'qtdembalagem[$i]' value = '$qtdembalagem'></td>";
    
    echo "<td class = '$destaque'> <div style = 'width: 15px;'> <i id = 'mensagem_custo[$i]' style = 'vertical-align: middle;'> </i></div></td>";
    echo "<td class = '$destaque'><input type = 'text' value = '$custo' name = 'custo[$i]' id = 'custo[$i]' maxlength='10' size = 10 style = 'text-align: right; height: 15px; margin-bottom: 3px; padding-bottom: 1px; width: 100px; margin-top: 2px;' maxlength = '60' onblur = 'maskMoeda(this); validarCusto($i);'>";
    echo "</td>";
    echo "<td class = '$destaque'><input type = 'text' value = '$observacao' name = 'observacao[$i]' id = 'observacao[$i]' size = 80 maxlength='1000' style = 'text-align: right; height: 15px; margin-bottom: 3px; padding-bottom: 1px; margin-top: 2px;'maxlength = '60' onblur = 'maiusculo(this)'></td>";
    echo "</tr>";

    $mercadologico = $row["descricao"];

    $row = pg_fetch_array($rst);
    $i++;
} while ($row);

echo "</tbody>";
echo "</table>";
echo "<br>";

echo "<div style = 'bottom:0 ; position: fixed; width: 100%; height: 75px; background-color: #FFFFFF; border-top: #E5E5E5 1px solid'>";
echo "<div style = 'margin-left: 0px; height: 100%;margin-top: 5px'>";
echo '<button class = "botaocotacao" href = "#" onclick = "salvar()" style = "position: relative; margin-left: 15px;">
        <div style="display: flex; align-items: center; justify-content:center;">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 11.917 9.724 16.5 19 7.5"/>
            </svg>
            <span>Salvar</span>
        </div>
    </button>';
echo '<button class = "botaocotacao" href = "#" onclick = "exportaXlsx()" style = "position: relative; margin-left: 15px;">
        <div style="display: flex; align-items: center; justify-content:center;">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 15v2a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-2m-8 1V4m0 12-4-4m4 4 4-4"/>
            </svg>
            <span>Exportar</span>
        </div>
    </button>';
echo '<button class = "botaocotacao" href = "#" onclick = "importaXlsx()" style = "position: relative; margin-left: 15px;">
        <div style="display: flex; align-items: center; justify-content:center;">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 3v4a1 1 0 0 1-1 1H5m4 8h6m-6-4h6m4-8v16a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V7.914a1 1 0 0 1 .293-.707l3.914-3.914A1 1 0 0 1 9.914 3H18a1 1 0 0 1 1 1Z"/>
            </svg>

            <span>Importar</span>
        </div>
    </button>';
echo "</div>";
echo "</div>";
?>