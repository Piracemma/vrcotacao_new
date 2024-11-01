<?php
class IntervaloRecebimento
{
  public $dataHoraInicio;
  public $dataHoraTermino;

  public function __construct($dataHoraInicio, $dataHoraTermino)
  {
    $this->dataHoraInicio = new DateTime($dataHoraInicio);
    $this->dataHoraTermino = new DateTime($dataHoraTermino);
  }

  public function isSubIntervaloRecebimentoOf(IntervaloRecebimento $otherIntervaloRecebimento)
  {
    $isSubIntervaloRecebimento = $this->dataHoraInicio >= $otherIntervaloRecebimento->dataHoraInicio
      && $this->dataHoraTermino <= $otherIntervaloRecebimento->dataHoraTermino;

    return $isSubIntervaloRecebimento;
  }


  private function naoCompartilhaIntervalo(IntervaloRecebimento $otherIntervaloRecebimento)
  {
    $intervaloRecebimentoA = $this->dataHoraInicio < $otherIntervaloRecebimento->dataHoraInicio ? $this : $otherIntervaloRecebimento;
    $intervaloRecebimentoB = $this->dataHoraInicio < $otherIntervaloRecebimento->dataHoraInicio ? $otherIntervaloRecebimento : $this;

    return $intervaloRecebimentoA->dataHoraTermino <= $intervaloRecebimentoB->dataHoraInicio;
  }

  public function compartilhaIntervalo(IntervaloRecebimento $otherIntervaloRecebimento)
  {
    return !$this->naoCompartilhaIntervalo($otherIntervaloRecebimento);
  }


  public function getDataHoraInicio()
  {
    return $this->dataHoraInicio;
  }

  public function getDataHoraTermino()
  {
    return $this->dataHoraTermino;
  }
}
?>
