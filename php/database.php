<?php

function isColumnType($table, $column, $type){
    global $con;

    $sql = "SELECT format_type(atttypid, atttypmod) AS tipo";
    $sql .= " FROM pg_attribute AS campo";
    $sql .= " INNER JOIN pg_class AS tabela ON tabela.oid = campo.attrelid";
    $sql .= " INNER JOIN pg_namespace AS schema ON schema.oid = tabela.relnamespace";
    $sql .= " INNER JOIN pg_type AS tipo ON tipo.oid = campo.atttypid";
    $sql .= " WHERE tabela.relkind = 'r'";

    if (strpos($table,".") === true) {
        $sql .= " AND schema.nspname || '.' ||   tabela.relname = '" . $table . "'";
    } else {
        $sql .= " AND schema.nspname || '.' ||   tabela.relname = 'public." . $table . "'";
    }

    $sql .= " AND campo.attname = '" . $column . "'";

    $rst = pg_query($con, $sql);

    $row = pg_fetch_array($rst);

    return $row["tipo"] === $type;
}

function isColumnExist($table, $column) {
    global $con;
    
    $sql = "SELECT pg_attribute.attname as campo";
    $sql .= " FROM pg_index, pg_attribute";
    $sql .= " JOIN pg_class ON (pg_attribute.attrelid = pg_class.oid AND pg_class.relkind = 'r')";
    $sql .= " JOIN pg_namespace ON (pg_namespace.oid = pg_class.relnamespace)";
    $sql .= " WHERE pg_class.relkind = 'r' AND pg_attribute.attname = '" . $column . "'";

    if (strpos($table, ".") === true) {
        $sql .= " AND pg_namespace.nspname || '.' ||   pg_class.relname = '" . $table . "'";
    } else {
        $sql .= " AND pg_namespace.nspname || '.' ||   pg_class.relname = 'public." . $table . "'";
    }

    $sql .= " LIMIT 1";

    $rst = pg_query($con, $sql);

    $row = pg_fetch_array($rst);

    return $row["campo"] === $column;
}

?>