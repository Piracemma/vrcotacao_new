<?php

function formatNumber($valor, $n) {
    $zero = "";

    for ($i = 1; $i <= ($n - strlen($valor)); $i++) {
        $zero .= "0";
    }

    return $zero . $valor;
}

function formatCNPJ($cnpj) {
    $cnpj = formatNumber($cnpj, 14);
    return substr($cnpj, 0, 2) . "." . substr($cnpj, 2, 3) . "." . substr($cnpj, 5, 3) . "/" . substr($cnpj, 8, 4) . "-" . substr($cnpj, 12, 2);
}

function formatDataGUI($data) {
    return substr($data, 8, 2) . "/" . substr($data, 5, 2) . "/" . substr($data, 0, 4);
}

function formatDataHoraGUI($data) {
    return substr($data, 8, 2) . "/" . substr($data, 5, 2) . "/" . substr($data, 0, 4) . " " . substr($data, 11, 8);
}

function formatDataBanco($data) {
    return substr($data, 6, 4) . "-" . substr($data, 3, 2) . "-" . substr($data, 0, 2);
}

function formatCusto($valor) {
    // $casaDecimal = isset(strlen(explode('.',(float)$valor)[1])) && strlen(explode('.',(float)$valor)[1]) <= 2 ? 2 : strlen(explode('.',(float)$valor)[1]);
    $casaDecimal = 2;

    if(isset(explode('.',(float)$valor)[1])) {

        if(strlen(explode('.',(float)$valor)[1]) <= 2) {
            $casaDecimal = 2;
        } else {
            $casaDecimal = strlen(explode('.',(float)$valor)[1]);
        }

    }

    return number_format($valor,$casaDecimal,",",".");
}

function formatDouble($valor) {
    return str_replace(",", ".", str_replace(".", "", $valor));
}

function formatDecimal2($valor) {
    return number_format($valor, 2, ",", ".");
}

function formatDecimal3($valor) {
    return number_format($valor, 3, ",", ".");
}

function formatDecimal0($valor) {
    return number_format($valor, 0, ",", ".");
}

function getDataAtual() {
    global $con, $LOJA;

    $sql = "SELECT data FROM dataprocessamento WHERE id_loja = $LOJA";

    $rst = pg_query($con, $sql);
    $row = pg_fetch_array($rst);

    return formatDataGUI($row["data"]);
}

function exibirMensagem($msg, $img) {
    return "<div style=\"background: url(img/$img) no-repeat; height: 16px; padding-left: 25px\">$msg</div>";
}

function calcularCustoFinal($codigo, $custo) {
    global $con;

    $sql = "SELECT (((100 - ac.reduzido) / 100) * ac.porcentagem) AS aliquotacredito, (((100 - ad.reduzido) / 100) * ad.porcentagem) AS aliquotadebito,";
    $sql .= " (((100 - afec.reduzido) / 100) * afec.porcentagem) AS aliquotacreditoforaestado, (((100 - afed.reduzido) / 100) * afed.porcentagem) AS aliquotadebitoforaestado";
    $sql .= " FROM produtoaliquota AS pa";
    $sql .= " INNER JOIN aliquota ac ON ac.id = pa.id_aliquotacredito";
    $sql .= " INNER JOIN aliquota ad ON ad.id = pa.id_aliquotadebito";
    $sql .= " INNER JOIN aliquota afec ON afec.id = pa.id_aliquotacreditoforaestado";
    $sql .= " INNER JOIN aliquota afed ON afed.id = pa.id_aliquotadebitoforaestado";
    $sql .= " WHERE  pa.id_produto = $codigo AND pa.id_estado = $_SESSION[estado]";

    $rst = pg_query($con, $sql);
    $row = pg_fetch_array($rst);

    if (!$row) {
        return $custo;
    }

    $icmscredito = round($row["aliquotacredito"], 2);
    //$icmsdebito = round($row[aliquotadebito], 2);

    $custosicms = round($custo - ($custo * $icmscredito / 100), 2);

    //$icmscreditoforaestado = round($row[aliquotacreditoforaestado], 2);
    $custofinal = round($custosicms + ($custosicms * $icmscredito / 100), 2);

    return $custofinal;
}

function validarCotacao() {
    global $con;

    $sql = "SELECT DISTINCT cotacao.id,";
    $sql .= " cotacao.data,";
    $sql .= " cotacao.descricao";
    $sql .= " FROM   precotacaofornecedor AS cotacao";
    $sql .= " INNER JOIN precotacaofornecedoritemfornecedor AS pfif";
    $sql .= " ON pfif.id_precotacaofornecedor = cotacao.id ";
    $sql .= " WHERE  cotacao.id_situacaoprecotacaofornecedor = 1 ";
    $sql .= " AND NOW() BETWEEN cotacao.datahorainicio AND cotacao.datahoratermino";
    $sql .= " AND pfif.id_fornecedor = $_SESSION[fornecedor]";
    $sql .= " ORDER  BY cotacao.id DESC ";

    $rst = pg_query($con, $sql);
    $row = pg_fetch_array($rst);

    if ($row) {
        return 1;
    } else {
        return 0;
    }

    return 1;
}

function validarCusto($codigo, $tipoembalagem, $custo) {
    global $con, $LOJA;

    if (getParametro(8) == 1 && !isTipoEmbalagemFracionado($tipoembalagem)) {
        $sql = "SELECT (complemento.custocomimposto * produtos.qtdembalagem) AS custo, complemento.custocomimposto, produtos.qtdembalagem";
        $sql .= " FROM produtocomplemento AS complemento";
        $sql .= " INNER JOIN produto AS produtos ON complemento.id_produto = produtos.id AND complemento.id_loja = $LOJA";
        $sql .= " WHERE complemento.id_produto = $codigo";
    } else {
        $sql = "SELECT complemento.custocomimposto AS custo, produtos.qtdembalagem";
        $sql .= " FROM produtocomplemento AS complemento";
        $sql .= " INNER JOIN produto AS produtos ON complemento.id_produto = produtos.id AND complemento.id_loja = $LOJA";
        $sql .= " WHERE complemento.id_produto = $codigo";
    }

    $rst = pg_query($con, $sql);
    $row = pg_fetch_array($rst);

    $custoatual = 0;

    if ($row) {
        $custoatual = $row["custo"];
    } else {
        $custoatual = 0;
    }

    if ($custoatual == 0) {
        return "";
    }

    //verifica percentual
    $percentualabaixo = getParametro(138);
    $percentualacima = getParametro(139);
    $utilizapercentual = getParametro(224) == "false" ? false : true;

    if ($percentualabaixo == "") {
        $percentualabaixo = $percentualacima;
    }

    //Valida se utiliza o percentual antes de rodar qualquer outro codigo
    if($utilizapercentual) {

        $maiorcusto = round($custoatual + ($custoatual * $percentualacima / 100), 2);
        $menorcusto = round($custoatual - ($custoatual * $percentualabaixo / 100), 2);

        if ($custo > $maiorcusto && $percentualacima != "") {
            return ">";
        } else if ($custo < $menorcusto && $percentualabaixo != "") {
            return "<";
        } else {
            return "";
        }

    } else {

        return "";

    }
    
}

function getParametro($_id) {
    global $con, $LOJA;

    $sql = "SELECT p.id, p.descricao, COALESCE(pv.valor, '') AS valor";
    $sql .= " FROM parametro AS p";
    $sql .= " LEFT JOIN parametrovalor AS pv ON pv.id_parametro = p.id AND pv.id_loja = $LOJA";
    $sql .= " WHERE p.id =  $_id";

    $rst = pg_query($con, $sql);

    $row = pg_fetch_array($rst);

    return $row["valor"];
}

function excluirItem($codigo, $cotacao) {
    global $con, $LOJA;

    $id_cotacaofornecedor = 0;

    $sql = "SELECT id FROM cotacaofornecedor";
    $sql .= " WHERE id_precotacaofornecedor = $cotacao";
    $sql .= " AND id_fornecedor = $_SESSION[fornecedor]";

    $rst = pg_query($con, $sql);
    $row = pg_fetch_array($rst);

    $id_cotacaofornecedor = $row["id"];

    if ($id_cotacaofornecedor > 0) {
        $sql = "delete from cotacaofornecedoritem where id_cotacaofornecedor = $id_cotacaofornecedor AND id_produto = $codigo";
        $rst = pg_query($con, $sql);

        $sql = "select id from cotacaofornecedoritem where id_cotacaofornecedor = $id_cotacaofornecedor";
        $rst = pg_query($con, $sql);
        $row = pg_fetch_array($rst);

        if (!$row) {
            $sql = "delete from cotacaofornecedor where id = $id_cotacaofornecedor";
            $rst = pg_query($con, $sql);
        }
    }
}

function consultarFinalizado($cotacao) {
    global $con, $LOJA;

    $sql = "SELECT id_situacaoprecotacaofornecedor from precotacaofornecedor WHERE id = $cotacao";

    $rst = pg_query($con, $sql);
    $row = pg_fetch_array($rst);

    if ($row) {
        if ($row["id_situacaoprecotacaofornecedor"] == 2) {
            return true;
        }
    }

    return false;
}

function salvarCotacao($cotacao, $data, $codigo, $custo, $observacao, $qtdembalagem) {
    global $con, $LOJA;


    $id_cotacaofornecedor = 0;

    $sql = "SELECT id FROM cotacaofornecedor";
    $sql .= " WHERE id_precotacaofornecedor = $cotacao";
    $sql .= " AND id_fornecedor = $_SESSION[fornecedor]";

    $rst = pg_query($con, $sql);
    $row = pg_fetch_array($rst);

    if (!$row) {
        $sql = "INSERT INTO cotacaofornecedor (id_precotacaofornecedor, id_fornecedor) VALUES (";
        $sql .= "$cotacao, ";
        $sql .= "$_SESSION[fornecedor])";

        $rst = pg_query($con, $sql);

        $sql = "SELECT CURRVAL('cotacaofornecedor_id_seq') AS id";
        $rst = pg_query($con, $sql);
        $row = pg_fetch_array($rst);
    }

    $id_cotacaofornecedor = $row["id"];

    $sql = "SELECT id FROM cotacaofornecedoritem where id_cotacaofornecedor =  $id_cotacaofornecedor AND id_produto = $codigo AND qtdembalagem = $qtdembalagem";
    $rst = pg_query($con, $sql);
    $row = pg_fetch_array($rst);

    if (!$row) {
        $sql = "INSERT INTO cotacaofornecedoritem (id_cotacaofornecedor, id_produto, custo, selecionado, qtdembalagem, observacao) VALUES (";
        $sql .= "$id_cotacaofornecedor, ";
        $sql .= "$codigo, ";
        $sql .= "$custo,";
        $sql .= "false,";
        $sql .= "$qtdembalagem, ";
        $sql .= "'$observacao')";
    } else {
        $sql = "UPDATE cotacaofornecedoritem SET";
        $sql .= " custo = $custo,";
        $sql .= " qtdembalagem = $qtdembalagem,";
        $sql .= " observacao = '$observacao'";
        $sql .= " WHERE id = $row[id]";
    }

    // echo $sql;

    $rst = pg_query($con, $sql);

    if (!$rst) {
        throw new Exception("Um erro ocorreu. Tente novamente!");
    }
}

function carregarCotacaoAberta($_id) {
    global $con;

    $sql = "SELECT DISTINCT cotacao.id,";
    $sql .= " cotacao.descricao";
    $sql .= " FROM precotacaofornecedor AS cotacao";
    $sql .= " INNER JOIN precotacaofornecedoritemfornecedor AS pfif";
    $sql .= " ON pfif.id_precotacaofornecedor = cotacao.id ";
    $sql .= " WHERE cotacao.id_situacaoprecotacaofornecedor = 1 ";
    $sql .= " AND NOW() BETWEEN cotacao.datahorainicio AND cotacao.datahoratermino";
    $sql .= " AND pfif.id_fornecedor = $_SESSION[fornecedor]";
    $sql .= " ORDER  BY cotacao.id DESC ";

    $rst = pg_query($con, $sql);

    $combo = "";

    while ($row = pg_fetch_array($rst)) {
        $cotacao = formatNumber($row["id"], 3);
        $descricao = $row["descricao"];
        $selected = $_id == $row["id"] ? "selected" : "";

        $combo .= "<option value=\"$row[id]\" $selected>$cotacao - $descricao</option>";
    }

    return $combo;
}

function listarCotacaoAberta() {
    global $con;

    $sql = "SELECT DISTINCT cotacao.id, cotacao.descricao
            FROM precotacaofornecedor AS cotacao
            INNER JOIN precotacaofornecedoritemfornecedor AS pfif ON pfif.id_precotacaofornecedor = cotacao.id
            WHERE cotacao.id_situacaoprecotacaofornecedor = 1
            AND NOW() BETWEEN cotacao.datahorainicio AND cotacao.datahoratermino
            AND pfif.id_fornecedor = $_SESSION[fornecedor]
            ORDER  BY cotacao.id DESC";

    $rst = pg_query($con, $sql);

    $return = pg_fetch_all($rst);

    return $return;
}

function validarCotacaoParaFornecedor($id_contacao) {
    global $con;

    $consulta = htmlspecialchars($id_contacao, ENT_QUOTES, 'UTF-8');

    if (!is_numeric($consulta)) {
        return false;
    }

    $fornecedor = $_SESSION['fornecedor'];

    $sql = "SELECT pcf.id
            FROM precotacaofornecedor AS pcf
            INNER JOIN precotacaofornecedoritemfornecedor AS pcfif ON  pcfif.id_precotacaofornecedor = pcf.id
            WHERE pcf.id = $consulta AND pcfif.id_fornecedor = $fornecedor AND id_situacaoprecotacaofornecedor = 1 AND NOW() BETWEEN datahorainicio AND datahoratermino";
    
    $rst = pg_query($con,$sql);

    $resultado = pg_fetch_row($rst);

    if(!empty($resultado)) {

        return true;

    } else {

        return false;

    }

}

function isTipoEmbalagemFracionado($idTipoEmbalagem){
    if (($idTipoEmbalagem == 4) || ($idTipoEmbalagem == 6) || ($idTipoEmbalagem == 9)) {
        return true;
    } else {
        return false;
    }
}

function getLojaDescricaoAtivas() {
  global $con;

    $sql = "SELECT id, descricao FROM loja";
    $sql .= " WHERE id_situacaocadastro = 1";
    $sql .= " ORDER BY id";

    $rst = pg_query($con, $sql);

    $lojas = array();

    while ($row = pg_fetch_array($rst)) {
      $loja = array('id' => $row['id'], 'descricao' => $row['descricao']);
      $lojas[] = $loja;
    }

    return $lojas;
}

?>
