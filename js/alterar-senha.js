// Variables
const form = document.getElementById('form');
const senhaInput = document.getElementById('senha');
const confirmacaoSenhaInput = document.getElementById('confirmacao-senha');
const formContainer = document.getElementById('form-container');
const sucessContainer = document.getElementById('success-container');
const errorContainer = document.getElementById('error-container');
const enviarBtn = document.getElementById('enviar');
const avancarBtn = document.getElementById('avancar');
const tentarNovamenteBtn = document.getElementById('tentar-novamente');
const cancelarBtn = document.getElementById('cancelar');
const errorMessageDiv = document.getElementById('error-message');
const errorMessageForm = document.getElementById('form__error');

//Event Listeners
enviarBtn.onclick = async () => {
  try {
    cursorLoading();
    const response = await fetchAlterarSenha();
    if (response.error) {
      showFormError(response.error);
      return;
    }

    showSucessContainer();
  } catch (error) {
    showFormError(error);
    console.error(error);
  } finally {
    defaultCursor();
  }
};

senhaInput.oninput = (event) => formatNumericInput(event.target);
senhaInput.onpaste = handlePasteNumeric;
confirmacaoSenhaInput.oninput = (event) => formatNumericInput(event.target);
confirmacaoSenhaInput.onpaste = handlePasteNumeric;


avancarBtn.onclick = () => {
  window.location.href = 'login.php';
};

tentarNovamenteBtn.onclick = () => {
  window.location.href = 'esqueci_senha.php';
}

cancelarBtn.onclick = () => {
  window.location.href = 'login.php';
}


// Overall functions

function getNumericValue(value) {
  return value.replace(/\D/g, '');
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


async function fetchAlterarSenha() {
  const url = 'php/alterar_senha.php';
  const data = new FormData(form);
  const urlParams = new URLSearchParams(window.location.search);
  const token = urlParams.get('token');
  data.append('token', token);
  const options = {
    method: 'POST',
    body: data
  };

  const response = await fetch(url, options);
  const json = await response.json();
  return json;
}


function hideAllContainers() {
  hideFormContainer();
  hideErrorContainer();
  hideSuccessContainer();
}

function showFormContainer() {
  hideAllContainers();
  formContainer.classList.remove('hidden');
}

function hideFormContainer() {
  formContainer.classList.add('hidden');
}

function showErrorContainer() {
  hideAllContainers();
  errorContainer.classList.remove('hidden');
}

function hideErrorContainer(message) {
  errorContainer.classList.add('hidden');
  errorMessageDiv.innerHTML = '';

  const p = document.createElement('p');
  p.textContent = message;

  errorMessageDiv.append(p);
}

function showSucessContainer() {
  hideAllContainers();
  sucessContainer.classList.remove('hidden');
}

function hideSuccessContainer() {
  sucessContainer.classList.add('hidden');
}

function showFormError(message) {
  errorMessageForm.classList.remove('hidden');
  errorMessageForm.innerHTML = '';
  errorMessageForm.textContent = message;
}


function cursorLoading() {
  document.body.classList.add('loading-cursor');
}

function defaultCursor() {
  document.body.classList.remove('loading-cursor');
}

