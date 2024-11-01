<?php

include __DIR__ . "/../conexao.php";
include __DIR__ . "/../classes/parametro_agendamento_recebimento.php";

function getTempoRecebimentoSegundos($tempoRecebimentoString)
{
  list($hours, $minutes) = explode(":", $tempoRecebimentoString);

  $seconds = $hours * 3600 + $minutes * 60;

  return $seconds;
}


function getParametroDiaSemanaByIdLoja($idLoja) {
  global $con;

  $sql = "SELECT dia_semana, horario_inicio, horario_termino, tempo_recebimento, quantidade_docas";
  $sql .= " FROM parametroagendarecebimento";
  $sql .= " WHERE id_loja = $idLoja";
  $sql .= " ORDER BY dia_semana";

  $rst = pg_query($con, $sql);

  $parametroAgendaRecebimento = array();

  while ($row = pg_fetch_array($rst)) {
    $diaSemana = $row['dia_semana'];
    $tempoRecebimentoSegundos = getTempoRecebimentoSegundos($row['tempo_recebimento']);
    $parametroAgendaRecebimento[$diaSemana] = new ParametroAgendamentoRecebimento();
    $parametroAgendaRecebimento[$diaSemana]->horarioInicio = $row['horario_inicio'];
    $parametroAgendaRecebimento[$diaSemana]->horarioTermino = $row['horario_termino'];
    $parametroAgendaRecebimento[$diaSemana]->quantidadeDocas = $row['quantidade_docas'];
    $parametroAgendaRecebimento[$diaSemana]->tempoRecebimentoSegundos = $tempoRecebimentoSegundos;
  }
  return $parametroAgendaRecebimento;
}
