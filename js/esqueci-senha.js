//Variables

const cnpjInput = document.getElementById('cnpj');
const codigoInput = document.getElementById('codigo');
const erroSpan = document.getElementById('erro');
const enviarBtn = document.getElementById('enviar');
const sairBtn = document.getElementById('sair');
const esqueciSenhaContainerDiv = document.querySelector('.esqueci-senha-container');
const formContainerDiv = document.getElementById('form-container');
const form = document.getElementById('form');
const infoContainerDiv = document.getElementById('info-container');
const voltarBtn = document.getElementById('voltar');
const emailNaoRecebidoDiv = document.getElementById('email-nao-recebido');
const reenviarBtn = document.getElementById('reenviar');


//Event Listeners
cnpjInput.oninput = (event) => formatCNPJInput(event.target);
cnpjInput.onpaste = handlePasteCNPJ;
codigoInput.oninput = (event) => formatNumericInput(event.target);
codigoInput.onpaste = handlePasteNumeric;

enviarBtn.onclick = () => {
  if (!formValido()) {
    return;
  }
  solicitarNovaSenha();
};

reenviarBtn.onclick = () => {
  solicitarNovaSenha();
}

sairBtn.onclick = () => {
  window.location.href = 'login.php';
};

voltarBtn.onclick = () => {
  window.location.href = 'login.php';
};


window.onload = () => {
  cnpjInput.focus();
};


//Overall functions

function getNumericValue(value) {
  return value.replace(/\D/g, '');
}

function formatCNPJInput(input) {
  input.value = formatCNPJ(input.value);
}

function formatNumericInput(input) {
  input.value = getNumericValue(input.value);
};

function handlePasteNumeric(event) {
  event.preventDefault();

  const pastedData = event.clipboardData.getData("text");

  const numericOnly = getNumericValue(pastedData);

  const input = event.target;
  const start = input.selectionStart;
  const end = input.selectionEnd;
  const value = input.value;

  let newValue = value.substring(0, start) + numericOnly + value.substring(end);
  const maxLength = input.maxLength;
  newValue = newValue.slice(0, maxLength);

  input.value = newValue;
  input.setSelectionRange(newValue.length, newValue.length);
};

function handlePasteCNPJ(event) {
  event.preventDefault();

  const pastedData = event.clipboardData.getData("text");

  const numericOnly = getNumericValue(pastedData);

  const input = event.target;
  const start = input.selectionStart;
  const end = input.selectionEnd;
  const value = input.value;

  let newValue = formatCNPJ(value.substring(0, start) + numericOnly + value.substring(end));
  const maxLength = input.maxLength;
  newValue = newValue.slice(0, maxLength);

  input.value = newValue;
  input.setSelectionRange(newValue.length, newValue.length);
}

function formatCNPJ(cnpj) {
  const numericValue = getNumericValue(cnpj);
  const maskedValue = numericValue.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/, '$1.$2.$3/$4-$5');
  return maskedValue;
}


function cursorLoading() {
  document.body.classList.add('loading-cursor');
}

function defaultCursor() {
  document.body.classList.remove('loading-cursor');
}

async function solicitarNovaSenha() {
  hideError();

  let response;
  let retries = 0;
  const maxRetries = 3;
  const retryDelay = 4000; //4 segundos

  try {
    cursorLoading();

    do {
      response = await fetchEsqueciSenha();

      if (response.exception && response.error) {
        console.error(`Erro na ${retries + 1}ª tentativa ao solicitar nova senha: ${response.error}. Exceção: ${response.exception}`);
        retries++;
        await new Promise(resolve => setTimeout(resolve, retryDelay));
        continue;
      } else if (response.error) {
        showError(response.error);
        return;
      }

      showAguardeEmail();
      return;

    } while (retries < maxRetries);

    showError(`${response.error}. ${response.exception} ?  ${`Exceção: ${response.exception}`} : ''`);
  } catch (error) {
    showError('Erro ao solicitar nova senha. Tente novamente mais tarde.');
    console.error(error);
  } finally {
    defaultCursor();
  }

}

function formValido() {
  const data = new FormData(form);
  const cnpjPuro = data.get('cnpj').replace(/\D/g, '');
  const codigoPuro = data.get('codigo').replace(/\D/g, '');

  if (cnpjPuro.length !== 14) {
    showError('CNPJ inválido.');
    return false;
  }

  if (cnpjPuro.length == 0) {
    showError('Insira um CNPJ.');
    return false;
  }

  if (codigoPuro.length == 0) {
    showError('Insira um código.');
    return false;
  }

  return true;
}

async function fetchEsqueciSenha() {
  const data = new FormData(form);
  const cnpjPuro = data.get('cnpj').replace(/\D/g, '');
  data.set('cnpj', cnpjPuro);
  const response = await fetch('php/esqueci_senha.php', {
    method: 'POST',
    body: data
  });
  const result = await response.json();
  return result;
}

function showError(message) {
  if (formContainerDiv.style.display === 'none') {
    return;
  }

  esqueciSenhaContainerDiv.style.height = 'calc(300px + 3rem)';
  erroSpan.innerText = message;
  erroSpan.classList.remove('hidden');
}

function hideError() {
  if (formContainerDiv.style.display === 'none') {
    return;
  }

  esqueciSenhaContainerDiv.style.height = '300px';
  erroSpan.innerText = '';
  erroSpan.classList.add('hidden');
}


function showAguardeEmail() {

  emailNaoRecebidoDiv.style.cursor = 'wait';
  formContainerDiv.classList.add('hidden');
  infoContainerDiv.classList.remove('hidden');
  document.getElementById('email-nao-recebido__info').classList.remove('hidden');
  document.getElementById('email-nao-recebido__resend').classList.add('hidden');


  setTimeout(() => {
    document.getElementById('email-nao-recebido__info').classList.add('hidden');
    document.getElementById('email-nao-recebido__resend').classList.remove('hidden');
    emailNaoRecebidoDiv.style.cursor = 'default';
  }, 60000);
}
