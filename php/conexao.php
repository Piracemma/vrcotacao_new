<?php

// $url_connection = "host=" . getenv("DB_SERVERNAME") .
//    " port=" . getenv("DB_PORT") .
//    " dbname=" . getenv("DB_DATABASENAME") .
//    " user=" . getenv("DB_USER") .
//    " password=" . getenv("DB_PASSWORD");

// $con = pg_connect($url_connection);
// $LOJA = getenv("ID_LOJA");

// Para ambiente Apache+PHP ou Glassfish Starter
//    Remover o comentário e editar as duas próximas linhas (15 e 16)
//    e adicionar um comentário 2 linhas anteriores (9 e 10)
$con = pg_connect("host=26.10.115.73 port=8745 dbname=vr user=postgres password=VrPost@Server");
// $con = pg_connect("host=192.168.0.250 port=8745 dbname=vr user=postgres password=VrPost@Server");
$LOJA = 1;

$timezone = 'America/Sao_Paulo';

pg_query($con, "SET TIMEZONE TO '$timezone';");

?>
