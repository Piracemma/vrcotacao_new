import {
  AgendamentoRecebimento,
  DiaAgendamento,
  ParametroAgendamentoRecebimento,
  PedidoCompra,
} from './classes.js';

// Variables
let currentYear = new Date().getFullYear();
let currentMonthIndex = new Date().getMonth();
let pedidosCompra;
let diasAgendamento;

const horaInicioInput = document.getElementById('horaInicio');
const horaTerminoInput = document.getElementById('horaTermino');

const selectPedidoCompraModal = document.getElementById(
  'modal-select-pedido-compra',
);
const agendamentoModal = document.getElementById('modal-agendamento');
const horariosAgendadosModal = document.getElementById(
  'modal-horarios-agendados',
);
const removerAgendamentoModal = document.getElementById(
  'modal-remover-agendamento',
);

const mensagemModal = document.getElementById('modal-mensagem');

const agendamentoTempoMinimoInfoP = document.getElementById(
  'agendamento-tempo-minimo-info',
);

const horariosAgendamentoForm = agendamentoModal.querySelector(
  '#horarios-agendamento',
);
const horariosAgendamentoErroSpan = agendamentoModal.querySelector(
  '#horarios-agendamento-erro',
);

const closePedidoCompraBtn = document.getElementById('close-pedido-compra');
const closeAgendamentoBtn = document.getElementById('close-agendamento');
const closeHorariosAgendadosBtn = document.getElementById(
  'close-horarios-agendados',
);
const closeRemoverAgendamentoBtn = document.getElementById(
  'close-remover-agendamento',
);

const sairPedidoCompraBtn = document.getElementById('sair-pedido-compra');
const sairAgendamentoBtn = document.getElementById('sair-agendamento');
const sairRemoverAgendamentoBtn = document.getElementById(
  'sair-remover-agendamento',
);
const sairHorariosAgendadosBtn = document.getElementById(
  'sair-horarios-agendados',
);

const removerAgendamentoBtn = document.getElementById('remover-agendamento');

const salvarAgendamentoBtn = document.getElementById('salvar-agendamento');
const agendarNovoHorarioBtn = document.getElementById('agendar-novo-horario');
const agendarPedidoBtn = document.getElementById('agendar-pedido');
const nextMonthBtn = document.getElementById('next-month-button');
const previousMonthBtn = document.getElementById('previous-month-button');

const lojaSelect = document.getElementById('loja');
const selectAllPedidoCompraCheckbox = document.getElementById(
  'select-all-pedido-compra',
);

// Event Listeners

nextMonthBtn.onclick = () => {
  nextMonth();
};

previousMonthBtn.onclick = () => {
  prevMonth();
};

horaInicioInput.onkeydown = skipNonNumericalInput;
horaTerminoInput.onkeydown = skipNonNumericalInput;
horaInicioInput.oninput = formatTimeWhenInput;
horaTerminoInput.oninput = formatTimeWhenInput;

salvarAgendamentoBtn.onclick = () => {
  salvarAgendamento();
};

closePedidoCompraBtn.onclick = () => {
  closeModalSelectPedidoCompra();
};

sairPedidoCompraBtn.onclick = () => {
  closeModalSelectPedidoCompra();
};

agendarPedidoBtn.onclick = () => {
  const diaAberto = selectPedidoCompraModal.dataset['diaAberto'];
  closeModalSelectPedidoCompra();

  const selectedIdPedidos = Array.from(
    selectPedidoCompraModal.querySelectorAll(
      'tbody tr input[type="checkbox"]:checked',
    ),
  ).map((checkbox) =>
    Number(checkbox.parentElement.parentElement.dataset['idPedido']),
  );
  openModalAgendamento(diaAberto, selectedIdPedidos);
};

closeAgendamentoBtn.onclick = () => {
  closeModalAgendamento();
};

sairAgendamentoBtn.onclick = () => {
  closeModalAgendamento();
};

closeHorariosAgendadosBtn.onclick = () => {
  closeModalHorariosAgendados();
};

sairHorariosAgendadosBtn.onclick = () => {
  closeModalHorariosAgendados();
};

lojaSelect.onchange = () => {
  updateCalendar();
};

selectAllPedidoCompraCheckbox.onclick = () => {
  const isChecked = selectAllPedidoCompraCheckbox.checked;
  const checkboxes = selectPedidoCompraModal.querySelectorAll(
    'td input[type="checkbox"]',
  );
  for (const checkbox of checkboxes) {
    checkbox.checked = isChecked;
    checkbox.dispatchEvent(new Event('change'));
  }
};

selectPedidoCompraModal.onclose = () => {
  selectAllPedidoCompraCheckbox.checked = false;
};

agendamentoModal.onclose = () => {
  horariosAgendamentoForm.reset();
};

agendarNovoHorarioBtn.onclick = () => {
  const dia = horariosAgendadosModal.dataset['diaAberto'];
  closeModalHorariosAgendados();
  openModalSelectPedidoCompra(dia);
};

closeRemoverAgendamentoBtn.onclick = () => {
  closeModalRemoverAgendamento();
};

sairRemoverAgendamentoBtn.onclick = () => {
  closeModalRemoverAgendamento();
};

function formatTimeWhenInput(event) {
  const input = event.target.value.replace(/\D/g, '');
  const hours = input.slice(0, 2);
  const minutes = input.slice(2, 4);

  if (input.length >= 3) {
    event.target.value = `${hours}:${minutes}`;
  }
}

function skipNonNumericalInput(event) {
  if (
    !event.key.match(/[0-9]/) &&
    event.key !== 'Backspace' &&
    event.key !== 'Delete' &&
    event.key !== 'ArrowLeft' &&
    event.key !== 'ArrowRight' &&
    event.key !== 'Tab' &&
    event.key !== 'Enter'
  ) {
    event.preventDefault();
  }
}

function setAgendamentoInputsError(message) {
  horariosAgendamentoErroSpan.textContent = message;
  horariosAgendamentoErroSpan.style.display = 'block';
  horaInicioInput.classList.add('form-group__input--error');
  horaTerminoInput.classList.add('form-group__input--error');
}

function resetAgendamentoInputsError() {
  horariosAgendamentoErroSpan.textContent = '';
  horariosAgendamentoErroSpan.style.display = 'none';
  horaInicioInput.classList.remove('form-group__input--error');
  horaTerminoInput.classList.remove('form-group__input--error');
}

function isAgendamentoValido(dataHoraInicio, dataHoraTermino) {
  const dia = dataHoraInicio.getDate();

  const dataHoraInicioDia = new Date(
    dataHoraInicio.getFullYear(),
    dataHoraInicio.getMonth(),
    dataHoraInicio.getDate(),
    ...diasAgendamento[dia].parametro.horarioInicio.split(':').map(Number),
  );

  const dataHoraTerminoDia = new Date(
    dataHoraInicio.getFullYear(),
    dataHoraInicio.getMonth(),
    dataHoraInicio.getDate(),
    ...diasAgendamento[dia].parametro.horarioTermino.split(':').map(Number),
  );

  if (
    dataHoraInicio < dataHoraInicioDia ||
    dataHoraTermino > dataHoraTerminoDia
  ) {
    setAgendamentoInputsError('Agendamento fora do horário parametrizado.');
    return false;
  }

  if (dataHoraInicio >= dataHoraTermino) {
    setAgendamentoInputsError(
      'Horário de chegada tem que ser anterior ao de partida.',
    );
    return false;
  }

  const diff = dataHoraTermino.getTime() - dataHoraInicio.getTime();
  const tempoMinimoRecebimentoMilissegundos =
    diasAgendamento[dia].parametro.tempoRecebimentoSegundos * 1000;
  if (diff < tempoMinimoRecebimentoMilissegundos) {
    setAgendamentoInputsError(
      'O agendamento precisa cumprir o mínimo de tempo de recebimento.',
    );
    return false;
  }

  return true;
}

async function salvarAgendamento() {
  const formData = new FormData(horariosAgendamentoForm);
  const horaInicioString = formData.get('horaInicio');
  const horaTerminoString = formData.get('horaTermino');

  const ano = currentYear;
  const mes = (currentMonthIndex + 1).toString().padStart(2, '0');
  const dia = agendamentoModal.dataset['diaAberto'].padStart(2, '0');

  const dataHoraInicioString = `${ano}-${mes}-${dia}T${horaInicioString}:00`;
  const dataHoraTerminoString = `${ano}-${mes}-${dia}T${horaTerminoString}:00`;

  const dataHoraInicio = new Date(dataHoraInicioString);
  const dataHoraTermino = new Date(dataHoraTerminoString);

  if (!isAgendamentoValido(dataHoraInicio, dataHoraTermino)) {
    return;
  }

  const idPedidosParaAgendamento = agendamentoModal.dataset['idPedidos']
    .split(',')
    .map((id) => Number(id));
  const idLoja = getIdSelectedLoja();

  const jsonData = {
    dataHoraInicio: dataHoraInicio.getTime() / 1000,
    dataHoraTermino: dataHoraTermino.getTime() / 1000,
    idPedidos: idPedidosParaAgendamento,
    idLoja,
  };
  const jsonString = JSON.stringify(jsonData);

  try {
    showLoading();
    const response = await fetchSalvarAgendamento(jsonString);
    const jsonResponse = await response.json();

    if (!jsonResponse.error) {
      showMsg('Agendamento salvo com sucesso!');
      closeModalAgendamento();
      updateCalendar();
      return;
    }
    showErrorMsg(`Erro ao salvar agendamento: ${jsonResponse.error}`);
  } catch (error) {
    showErrorMsg(`Erro ao salvar agendamento: ${error}`);
    console.error('Erro ao salvar agendamento: ', error);
  } finally {
    hideLoading();
  }
}

async function fetchSalvarAgendamento(jsonString) {
  const url = 'php/agendamento.php';

  try {
    const response = await fetch(url, {
      method: 'POST',
      body: jsonString,
      headers: {
        'Content-Type': 'application/json',
      },
    });

    return response;
  } catch (error) {
    throw new Error('Erro ao salvar agendamento: ', error);
  }
}

function hasAnyPedidoCompraSelected() {
  return (
    selectPedidoCompraModal.querySelectorAll(
      'tbody tr input[type="checkbox"]:checked',
    ).length >= 1
  );
}

function closeModalSelectPedidoCompra() {
  selectPedidoCompraModal.close();
  delete selectPedidoCompraModal.dataset['diaAberto'];
}

function closeModalHorariosAgendados() {
  horariosAgendadosModal.close();
  delete horariosAgendadosModal.dataset['diaAberto'];
}

function closeModalAgendamento() {
  agendamentoModal.close();
  delete agendamentoModal.dataset['diaAberto'];
  delete agendamentoModal.dataset['idPedidos'];
}

function openModalSelectPedidoCompra(dia) {
  agendarPedidoBtn.disabled = true;
  selectPedidoCompraModal.showModal();
  selectPedidoCompraModal.dataset['diaAberto'] = dia;
  loadPedidoCompra();
}

function openModalHorariosAgendados(dia) {
  horariosAgendamentoForm.reset();
  horariosAgendadosModal.showModal();
  horariosAgendadosModal.dataset['diaAberto'] = dia;
  loadHorariosAgendados(dia);
}

function loadHorariosAgendados(dia) {
  const tbodyElement = horariosAgendadosModal.querySelector('tbody');
  tbodyElement.innerHTML = '';

  const agendamentosDoDia =
    diasAgendamento[dia].getAgendamentosFornecedorLogado();

  if (!agendamentosDoDia) {
    return;
  }

  for (const agendamento of agendamentosDoDia) {
    const tdHorarioInicio = document.createElement('td');
    tdHorarioInicio.textContent = agendamento.horarioInicio.toLocaleTimeString(
      [],
      { hour: '2-digit', minute: '2-digit' },
    );

    const tdHorarioTermino = document.createElement('td');
    tdHorarioTermino.textContent =
      agendamento.horarioTermino.toLocaleTimeString([], {
        hour: '2-digit',
        minute: '2-digit',
      });

    const tdRemover = document.createElement('td');
    const removerBtn = document.createElement('button');
    removerBtn.classList.add('remover-agendamento-button');
    removerBtn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
    <rect x="2" y="1.99985" width="13.9985" height="13.9985" rx="6.99925" fill="#E86800" stroke="#E86800"/>
    <path d="M6 5.99941L8.97319 8.97259C8.98783 8.98723 9.01157 8.98723 9.02621 8.97259L11.9994 5.9994M11.9994 11.9988L9.02621 9.02562C9.01157 9.01098 8.98783 9.01098 8.97319 9.02562L6 11.9988" stroke="white" stroke-width="1.02083" stroke-linecap="round"/>
  </svg>`;
    removerBtn.onclick = () => {
      openModalRemoverAgendamento(agendamento);
    };
    tdRemover.appendChild(removerBtn);

    const trElement = document.createElement('tr');
    trElement.appendChild(tdHorarioInicio);
    trElement.appendChild(tdHorarioTermino);
    trElement.appendChild(tdRemover);

    tbodyElement.appendChild(trElement);
  }
}

function getTempoMinimoInfoText(tempoRecebimentoSegundos) {
  const tempoRecebimentoMinutos = Math.floor(tempoRecebimentoSegundos / 60);
  const tempoRecebimentoHoras = Math.floor(tempoRecebimentoMinutos / 60);

  let text = 'O horário escolhido, deve respeitar o tempo mínimo de <b>';

  if (tempoRecebimentoHoras > 0) {
    text += ` ${tempoRecebimentoHoras} hora(s)`;
  }

  if (tempoRecebimentoMinutos % 60 > 0) {
    if (tempoRecebimentoHoras > 0) {
      text += ' e';
    }

    text += ` ${tempoRecebimentoMinutos % 60} minuto(s)`;
  }

  text += '</b> entre a chegada e a partida.';

  return text;
}

function openModalAgendamento(dia, idPedidos) {
  const infoSpan = agendamentoModal.querySelector('#modal-agendamento__info');
  resetAgendamentoInputsError();

  const data = new Date(currentYear, currentMonthIndex, dia);

  const dataAsString = data.toLocaleDateString([], {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
  });

  const parametro = diasAgendamento[dia].parametro;

  infoSpan.innerHTML = `<b>Data:</b> ${dataAsString} <b>Horário de Atendimento:</b> das ${parametro.horarioInicio} às ${parametro.horarioTermino}.`;

  agendamentoTempoMinimoInfoP.innerHTML = getTempoMinimoInfoText(
    parametro.tempoRecebimentoSegundos,
  );

  agendamentoModal.showModal();
  agendamentoModal.dataset['diaAberto'] = dia;
  agendamentoModal.dataset['idPedidos'] = idPedidos.join(',');
  loadAgendamentosDoDia(dia);
}

function loadAgendamentosDoDia(dia) {
  const tbodyElement = agendamentoModal.querySelector('tbody');
  tbodyElement.innerHTML = '';

  const agendamentosDoDia = diasAgendamento[dia].agendamentos;

  if (!agendamentosDoDia) {
    return;
  }

  for (const agendamento of agendamentosDoDia) {
    const tdHorarioInicio = document.createElement('td');
    tdHorarioInicio.textContent = agendamento.horarioInicio.toLocaleTimeString(
      [],
      { hour: '2-digit', minute: '2-digit' },
    );

    const tdHorarioTermino = document.createElement('td');

    tdHorarioTermino.textContent =
      agendamento.horarioTermino.toLocaleTimeString([], {
        hour: '2-digit',
        minute: '2-digit',
      });

    const tdSituacao = document.createElement('td');
    tdSituacao.style.display = 'flex';

    const divLegenda = document.createElement('div');
    divLegenda.classList.add('legenda-agendamento');
    if (agendamento.fromLoggedFornecedor) {
      divLegenda.classList.add('day--scheduled');
    } else {
      divLegenda.classList.add('day--unavailable');
    }

    const spanSituacao = document.createElement('span');
    spanSituacao.textContent = agendamento.fromLoggedFornecedor
      ? 'Agendado'
      : 'Não Disponível';

    tdSituacao.appendChild(divLegenda);
    tdSituacao.appendChild(spanSituacao);

    const trElement = document.createElement('tr');
    trElement.appendChild(tdHorarioInicio);
    trElement.appendChild(tdHorarioTermino);
    trElement.appendChild(tdSituacao);

    tbodyElement.appendChild(trElement);
  }
}

function openModalRemoverAgendamento(agendamento) {
  removerAgendamentoModal.showModal();
  loadRemoverAgendamento(agendamento);
}

function loadRemoverAgendamento(agendamento) {
  const tbodyElement = removerAgendamentoModal.querySelector('tbody');
  tbodyElement.innerHTML = '';

  if (!agendamento) {
    return;
  }

  const tdHorarioInicio = document.createElement('td');
  tdHorarioInicio.textContent = agendamento.horarioInicio.toLocaleTimeString(
    [],
    { hour: '2-digit', minute: '2-digit' },
  );

  const tdHorarioTermino = document.createElement('td');
  tdHorarioTermino.textContent = agendamento.horarioTermino.toLocaleTimeString(
    [],
    {
      hour: '2-digit',
      minute: '2-digit',
    },
  );

  const trElement = document.createElement('tr');
  trElement.appendChild(tdHorarioInicio);
  trElement.appendChild(tdHorarioTermino);

  tbodyElement.appendChild(trElement);

  removerAgendamentoBtn.onclick = () => {
    removerAgendamento(agendamento);
  };
}

async function removerAgendamento(agendamento) {
  try {
    const response = await fetchRemoverAgendamento(agendamento.id);

    if (response.status == 204) {
      showMsg('Agendamento removido com sucesso');
      closeModalRemoverAgendamento();
      closeModalHorariosAgendados();
      updateCalendar();
    }
  } catch (error) {
    showErrorMsg(`Erro ao deletar agendamento: ${error}`);
    console.error('Erro ao deletar agendamento', error);
  }
}

async function fetchRemoverAgendamento(idAgendamento) {
  const url = `php/agendamento.php/${idAgendamento}`;
  const options = {
    method: 'DELETE',
  };
  return await fetch(url, options);
}

function closeModalRemoverAgendamento() {
  removerAgendamentoModal.close();
  removerAgendamentoBtn.onclick = null;
}

async function updateParametrosAgendaRecebimento() {
  const parametrosDiarios =
    await fetchParametroAgendaRecebimentoLojaSelecionada();
  const parametros = {};
  for (const dia in parametrosDiarios) {
    Object.assign(parametros, {
      [dia]: new ParametroAgendamentoRecebimento(parametrosDiarios[dia]),
    });
  }
  return parametros;
}

async function updateAgendamentos() {
  const agendamentosPorDiaResponse = await fetchAgendamentos();
  const agendamentos = {};
  for (const dia in agendamentosPorDiaResponse) {
    const agendamentosDoDia = agendamentosPorDiaResponse[dia];
    const agendamentosMapeados = agendamentosDoDia.map(
      (agendamento) => new AgendamentoRecebimento(agendamento),
    );
    Object.assign(agendamentos, { [dia]: agendamentosMapeados });
  }
  return agendamentos;
}

async function updatePedidosCompra() {
  const resultadoPedidoCompra = await fetchPedidoCompra();
  if (resultadoPedidoCompra) {
    const pedidosMapeados = resultadoPedidoCompra.map(
      (pedido) => new PedidoCompra(pedido),
    );
    pedidosCompra = pedidosMapeados;
  }
}

async function fetchAgendamentos() {
  const idLoja = getIdSelectedLoja();
  const ano = currentYear;
  const mes = currentMonthIndex + 1;

  const url = `php/agendamento.php?idLoja=${idLoja}&ano=${ano}&mes=${mes}`;
  const response = await fetch(url);
  const json = await response.json();

  return json.items;
}

async function fetchParametroAgendaRecebimentoLojaSelecionada() {
  const idLoja = getIdSelectedLoja();

  const url = `php/parametro_agendamento_recebimento.php?idLoja=${idLoja}`;
  const response = await fetch(url);
  const json = await response.json();

  return json.items;
}

async function updateCalendar() {
  try {
    showLoading();
    const parametros = await updateParametrosAgendaRecebimento();
    const agendamentos = await updateAgendamentos();
    await updatePedidosCompra();

    const date = new Date(currentYear, currentMonthIndex);
    date.setMonth(date.getMonth() + 1);
    date.setDate(0);
    diasAgendamento = {};
    for (let i = 1; i <= date.getDate(); i++) {
      const data = new Date(currentYear, currentMonthIndex, i);
      const diaDaSemana = data.getDay() + 1;
      const diaAgendamento = new DiaAgendamento(
        data,
        parametros[diaDaSemana],
        agendamentos[i],
      );
      diasAgendamento[i] = diaAgendamento;
    }

    const calendarDiv = document.getElementById('calendar');
    calendarDiv.innerHTML = '';

    const firstDay = new Date(currentYear, currentMonthIndex, 1).getDay();
    const lastDay = new Date(currentYear, currentMonthIndex + 1, 0).getDay();

    const weekDays = [
      'Domingo',
      'Segunda-feira',
      'Terça-feira',
      'Quarta-feira',
      'Quinta-feira',
      'Sexta-feira',
      'Sábado',
    ];

    for (const day of weekDays) {
      const dayNameDiv = document.createElement('div');
      dayNameDiv.textContent = day;
      dayNameDiv.classList.add('week-day');
      calendarDiv.appendChild(dayNameDiv);
    }

    const daysInMonth = new Date(
      currentYear,
      currentMonthIndex + 1,
      0,
    ).getDate();

    for (let i = 0; i < firstDay; i++) {
      calendarDiv.appendChild(createEmptyDayDiv());
    }

    for (let i = 1; i <= daysInMonth; i++) {
      calendarDiv.appendChild(createDayDiv(i));
    }

    for (let i = lastDay + 1; i < 7; i++) {
      calendarDiv.appendChild(createEmptyDayDiv());
    }

    const currentMonthDiv = document.getElementById('current-month');
    let currentMonthText = new Date(
      currentYear,
      currentMonthIndex,
    ).toLocaleString('default', { month: 'long' });
    currentMonthText =
      currentMonthText.charAt(0).toUpperCase() + currentMonthText.slice(1);
    currentMonthDiv.textContent = `${currentMonthText} ${currentYear}`;
  } catch (error) {
    showErrorMsg(`Erro ao atualizar calendário: ${error}`);
    console.error('Erro ao atualizar calendário: ', error);
  } finally {
    hideLoading();
  }
}

async function fetchPedidoCompra() {
  const idLoja = getIdSelectedLoja();

  const url = `php/pedido_compra.php?idLoja=${idLoja}`;
  const response = await fetch(url);
  const json = await response.json();

  return json.items;
}

function getIdSelectedLoja() {
  return Number(document.getElementById('loja').value);
}

function createEmptyDayDiv() {
  const emptyDayDiv = document.createElement('div');
  emptyDayDiv.classList.add('day');
  emptyDayDiv.classList.add('day--inactive');
  return emptyDayDiv;
}

function createDayDiv(dia) {
  const dayDiv = document.createElement('div');
  dayDiv.classList.add('day');

  const dayText = document.createElement('span');
  dayText.classList.add('day__text');

  dayText.textContent = dia;

  dayDiv.appendChild(dayText);

  const dayOfWeek = new Date(currentYear, currentMonthIndex, dia).getDay() + 1;

  const diaPossuiAgendamentoFornecedor =
    diasAgendamento[dia].possuiAgendamentoFornecedorLogado();

  if (diaPossuiAgendamentoFornecedor) {
    dayDiv.classList.add('day--scheduled');
    const agendamentoDoFornecedor =
      diasAgendamento[dia].getAgendamentosFornecedorLogado()[0];
    dayDiv.appendChild(createDayAppointmentCaption(agendamentoDoFornecedor));
    dayDiv.onclick = () => {
      openModalHorariosAgendados(dia);
    };
  } else if (
    diasAgendamento[dia].parametro &&
    diasAgendamento[dia].parametro.configurado &&
    diasAgendamento[dia].isDiaDisponivel()
  ) {
    dayDiv.classList.add('day--available');
    dayDiv.onclick = () => {
      openModalSelectPedidoCompra(dia);
    };
  } else {
    dayDiv.classList.add('day--unavailable');
    dayDiv.onclick = () => {
      showMsg('Dia não parametrizado e/ou disponível para agendamentos');
    };
  }

  return dayDiv;
}

function loadPedidoCompra() {
  const tbodyElement = selectPedidoCompraModal.querySelector('tbody');
  tbodyElement.innerHTML = '';

  const pedidoCompraSemAgendamento = pedidosCompra.filter(
    (pedido) => !pedido.agendado,
  );

  for (const pedido of pedidoCompraSemAgendamento) {
    const tdCheckbox = document.createElement('td');
    const checkbox = document.createElement('input');
    checkbox.type = 'checkbox';
    tdCheckbox.appendChild(checkbox);
    checkbox.onchange = () => {
      const hasAnyCheckboxSelected = hasAnyPedidoCompraSelected();
      if (hasAnyCheckboxSelected && agendarPedidoBtn.disabled) {
        agendarPedidoBtn.disabled = false;
      } else if (!hasAnyCheckboxSelected && !agendarPedidoBtn.disabled) {
        agendarPedidoBtn.disabled = true;
      }
    };
    tdCheckbox.classList.add('centered-column');

    const tdIdPedido = document.createElement('td');
    tdIdPedido.textContent = pedido.id;

    const tdDataCompra = document.createElement('td');
    tdDataCompra.textContent = pedido.dataCompra.toLocaleDateString();

    const tdDataEntrega = document.createElement('td');
    tdDataEntrega.textContent = pedido.dataEntrega.toLocaleDateString();

    const tdValorTotal = document.createElement('td');
    tdValorTotal.textContent = pedido.valorTotal.toLocaleString('pt-BR', {
      style: 'currency',
      currency: 'BRL',
    });

    const trElement = document.createElement('tr');
    trElement.dataset['idPedido'] = pedido.id;

    trElement.appendChild(tdCheckbox);
    trElement.appendChild(tdIdPedido);
    trElement.appendChild(tdDataCompra);
    trElement.appendChild(tdDataEntrega);
    trElement.appendChild(tdValorTotal);

    tbodyElement.appendChild(trElement);
  }
}

async function prevMonth() {
  currentMonthIndex--;
  if (currentMonthIndex < 0) {
    currentYear--;
    currentMonthIndex = 11;
  }
  await updateCalendar();
}

async function nextMonth() {
  currentMonthIndex++;
  if (currentMonthIndex > 11) {
    currentYear++;
    currentMonthIndex = 0;
  }
  await updateCalendar();
}

function createDayAppointmentCaption(agendamento) {
  const div = document.createElement('div');
  div.classList.add('day__appointment-caption');

  const horaInicio = String(agendamento.horarioInicio.getHours()).padStart(
    2,
    '0',
  );
  const minutoInicio = String(agendamento.horarioTermino.getMinutes()).padStart(
    2,
    '0',
  );
  const horarioInicio = `${horaInicio}:${minutoInicio}`;

  const horaTermino = String(agendamento.horarioTermino.getHours()).padStart(
    2,
    '0',
  );
  const minutoTermino = String(
    agendamento.horarioTermino.getMinutes(),
  ).padStart(2, '0');
  const horarioTermino = `${horaTermino}:${minutoTermino}`;

  const p = document.createElement('p');
  p.textContent = `${horarioInicio} - ${horarioTermino}`;
  div.appendChild(p);

  return div;
}

function showLoading() {
  const aguardeElement = document.getElementById('aguarde');
  const agendaRecebimentoElement = document.querySelector(
    '.agenda-recebimento',
  );

  aguardeElement.style.display = 'block';
  agendaRecebimentoElement.style.display = 'none';
}

function hideLoading() {
  const aguardeElement = document.getElementById('aguarde');
  const agendaRecebimentoElement = document.querySelector(
    '.agenda-recebimento',
  );
  aguardeElement.style.display = 'none';
  agendaRecebimentoElement.style.display = 'block';
}

function getMensagemModal(message, icon) {
  const messageElement = document.getElementById('modal-mensagem');
  messageElement.innerHTML = '';

  const div = document.createElement('div');
  div.style.margin = '1px';
  div.style.maxWidth = '60vw';
  div.style.minWidth = '10rem';
  div.style.display = 'flex';
  div.style.alignItems = 'center';

  const iconDiv = document.createElement('div');
  iconDiv.innerHTML = icon;

  const divBtn = document.createElement('div');
  divBtn.style.textAlign = 'right';
  divBtn.style.marginTop = '10px';

  const button = document.createElement('button');
  button.textContent = 'Ok';
  button.onclick = () => {
    messageElement.close();
  };

  div.appendChild(iconDiv);
  div.appendChild(document.createTextNode(message));

  divBtn.appendChild(button);

  messageElement.appendChild(div);
  messageElement.appendChild(divBtn);

  return messageElement;
}

function showMsg(message) {
  const messageElement = getMensagemModal(
    message,
    `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
  <mask id="mask0_6_2170" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="20" height="20">
    <rect width="20" height="20" fill="#D9D9D9"/>
  </mask>
  <g mask="url(#mask0_6_2170)">
    <path d="M8.65676 12.3148L6.34194 9.97686C6.24935 9.88426 6.14534 9.83797 6.0299 9.83797C5.91386 9.83797 5.80182 9.89198 5.69379 10C5.6012 10.0926 5.5549 10.2006 5.5549 10.3241C5.5549 10.4475 5.6012 10.5556 5.69379 10.6482L8.1475 13.1019C8.28639 13.2407 8.45984 13.3102 8.66787 13.3102C8.87651 13.3102 9.05028 13.2407 9.18916 13.1019L14.2818 8.00926C14.3743 7.91667 14.4206 7.81235 14.4206 7.6963C14.4206 7.58087 14.3666 7.46914 14.2586 7.36112C14.166 7.26852 14.058 7.22223 13.9345 7.22223C13.8111 7.22223 13.7031 7.26852 13.6105 7.36112L8.65676 12.3148ZM9.99935 18.3333C8.84194 18.3333 7.75799 18.1136 6.7475 17.6741C5.73639 17.234 4.85676 16.6395 4.10861 15.8907C3.35984 15.1426 2.7654 14.263 2.32527 13.2519C1.88577 12.2414 1.66602 11.1574 1.66602 10C1.66602 8.8426 1.88577 7.75834 2.32527 6.74723C2.7654 5.73673 3.35984 4.8571 4.10861 4.10834C4.85676 3.36019 5.73639 2.76605 6.7475 2.32593C7.75799 1.88642 8.84194 1.66667 9.99935 1.66667C11.1568 1.66667 12.241 1.88642 13.2521 2.32593C14.2626 2.76605 15.1423 3.36019 15.891 4.10834C16.6392 4.8571 17.2333 5.73673 17.6734 6.74723C18.1129 7.75834 18.3327 8.8426 18.3327 10C18.3327 11.1574 18.1129 12.2414 17.6734 13.2519C17.2333 14.263 16.6392 15.1426 15.891 15.8907C15.1423 16.6395 14.2626 17.234 13.2521 17.6741C12.241 18.1136 11.1568 18.3333 9.99935 18.3333Z" fill="#3D3D3D"/>
  </g>
</svg>`,
  );

  messageElement.showModal();
}

function showErrorMsg(message) {
  const messageElement = getMensagemModal(
    message,
    '<i class="icon-warning-sign" style="margin-top: -1px"></i>',
  );

  messageElement.showModal();
}

updateCalendar();
