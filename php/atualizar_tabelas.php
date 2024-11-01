<?php

include("database.php");

function atualizar(){
    global $con;

    if(isColumnType("cotacaofornecedoritem","custo","numeric(12,4)") !== true){
        $sql = "ALTER TABLE cotacaofornecedoritem ALTER COLUMN custo TYPE numeric(12,4) USING custo::numeric";
        pg_query($con, $sql);
    }

    if(isColumnExist("precotacaofornecedor","enviaprodutozerado") !== true){
        $sql = "ALTER TABLE precotacaofornecedor ADD COLUMN enviaprodutozerado BOOLEAN DEFAULT true";
        pg_query($con, $sql);
    }
}

?>