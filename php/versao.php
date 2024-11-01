<?php

$jsonFile = file_get_contents(__DIR__ . "/../vrcotacao.json");
$json = json_decode($jsonFile);

function getVersao(){
    global $json;

    $build = intval($json->{'versao.build'}) > 0 ? '-' . $json->{'versao.build'} : '';
    $beta = intval($json->{'versao.beta'}) > 0 ? '-' . 'b' . $json->{'versao.beta'} : '';
    $versao = $json->{'versao.major'} . '.' . $json->{'versao.minor'} . '.' . $json->{'versao.release'} . $build . $beta;

    return $versao;
}

function getNome(){
    global $json;

    return $json->{'nome'};
}

function getData(){
    global $json;

    return $json->{'data'};
}

?>
