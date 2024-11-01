export class PedidoCompra {
  constructor({
    id,
    idLoja,
    descricaoLoja,
    dataCompra,
    dataEntrega,
    valorTotal,
    agendado,
  }) {
    this.id = Number(id);
    this.idLoja = Number(idLoja);
    this.descricaoLoja = descricaoLoja;
    this.dataCompra = new Date(dataCompra);
    this.dataEntrega = new Date(dataEntrega);
    this.valorTotal = Number(valorTotal);
    this.agendado = agendado;
  }
}

export class ParametroAgendamentoRecebimento {
  constructor({
    horarioInicio,
    horarioTermino,
    tempoRecebimentoSegundos,
    quantidadeDocas,
  }) {
    this.horarioInicio = horarioInicio;
    this.horarioTermino = horarioTermino;
    this.tempoRecebimentoSegundos = Number(tempoRecebimentoSegundos);
    this.quantidadeDocas = Number(quantidadeDocas);
  }

  get tempoRecebimento() {
    const tempoRecebimentoSegundos = this.tempoRecebimentoSegundos;
    const horas = Math.floor(tempoRecebimentoSegundos / 3600);
    const minutos = Math.floor((tempoRecebimentoSegundos % 3600) / 60);
    return `${horas.toString().padStart(2, '0')}:${minutos
      .toString()
      .padStart(2, '0')}`;
  }

  get configurado() {
    return (
      this.horarioInicio != '00:00' &&
      this.horarioInicio != '' &&
      this.horarioTermino != '00:00' &&
      this.horarioTermino != '' &&
      this.tempoRecebimento != '00:00' &&
      this.quantidadeDocas > 0
    );
  }

  get naoConfigurado() {
    return !this.configurado;
  }
}

export class AgendamentoRecebimento {
  constructor({ id, dataHoraInicio, dataHoraTermino, fromLoggedFornecedor }) {
    this.id = id;
    this.horarioInicio = new Date(dataHoraInicio);
    this.horarioTermino = new Date(dataHoraTermino);
    this.fromLoggedFornecedor = fromLoggedFornecedor;
  }
}

export class DiaAgendamento {
  constructor(data, parametro, agendamentos) {
    this.data = data;
    this.parametro = parametro;
    this.agendamentos = agendamentos;
  }

  possuiAgendamentoFornecedorLogado() {
    if (!this.agendamentos) {
      return false;
    }
    return this.agendamentos.some((a) => a.fromLoggedFornecedor);
  }

  getAgendamentosFornecedorLogado() {
    if (!this.agendamentos) {
      return [];
    }
    return this.agendamentos.filter((a) => a.fromLoggedFornecedor);
  }

  intervaloValido(horarioInicio, horarioTermino) {
    const diff = horarioTermino.getTime() - horarioInicio.getTime();
    const tempoMinimoAgendamentoMilisegundos =
      this.parametro.tempoRecebimentoSegundos * 1000;
    return (
      diff >= tempoMinimoAgendamentoMilisegundos &&
      this.temDocaDisponivel({ horarioInicio, horarioTermino })
    );
  }

  temDocaDisponivel(intervalo) {
    const agendamentosQueCompartilhamDocaNoIntervalo = this.agendamentos.filter(
      (a) =>
        !(
          intervalo.horarioTermino <= a.horarioInicio ||
          intervalo.horarioInicio >= a.horarioTermino
        ),
    );
    const quantidadeDeDocasCompartilhadas =
      agendamentosQueCompartilhamDocaNoIntervalo.length;

    return quantidadeDeDocasCompartilhadas < this.parametro.quantidadeDocas;
  }

  isDiaDisponivel() {
    if (!this.agendamentos) {
      return true;
    }

    const tempoMinimoAgendamentoSegundos =
      this.parametro.tempoRecebimentoSegundos;
    const tempoMinimoAgendamentoMilisegundos =
      tempoMinimoAgendamentoSegundos * 1000;

    const diaAtual = this.data.getDate();


    const inicioDoDia = new Date(
      this.data.getFullYear(),
      this.data.getMonth(),
      diaAtual,
      ...this.parametro.horarioInicio.split(':').map(Number),
    );
    const terminoDoDia = new Date(
      this.data.getFullYear(),
      this.data.getMonth(),
      diaAtual,
      ...this.parametro.horarioTermino.split(':').map(Number),
    );

    if (this.intervaloValido(inicioDoDia, this.agendamentos[0].horarioInicio)) {
      return true;
    }

    if (
      this.intervaloValido(
        this.agendamentos[this.agendamentos.length - 1].horarioTermino,
        terminoDoDia,
      )
    ) {
      return true;
    }

    for (let i = 0; i < this.agendamentos.length; i++) {
      const agendamentoAtual = this.agendamentos[i];

      if (
        this.temDocaDisponivel(
          agendamentoAtual,
          this.agendamentos,
          this.parametro.quantidadeDocas,
        )
      ) {
        return true;
      }

      const proximoIntervaloPossivel = {
        horarioInicio: agendamentoAtual.horarioTermino,
        horarioTermino: new Date(
          agendamentoAtual.horarioTermino.getTime() +
            tempoMinimoAgendamentoMilisegundos,
        ),
      };

      if (
        proximoIntervaloPossivel.horarioTermino <= terminoDoDia &&
        this.intervaloValido(
          proximoIntervaloPossivel.horarioInicio,
          proximoIntervaloPossivel.horarioTermino,
        )
      ) {
        return true;
      }
    }

    return false;
  }
}
