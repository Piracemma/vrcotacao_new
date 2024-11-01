function exibirMensagem(msg, img) {
    return "<div style=\"background: url(img/" + img + ") no-repeat; height: 16px; padding-left: 25px; font-size: 14px; font-weight: bold\">" + msg + "</div>";
}

function maiusculo(fld) {
    fld.value = fld.value.toUpperCase();
}

function validarCampo(obj, tipo) {
    var msg = '';

    if (obj.value == '') {
        msg = 'Campo deve ser preenchido';
        erro = true;
    } else {
        switch (tipo) {
            case 'string':
                for (var i=0; i<obj.value.length; i++) {
                    if (obj.value.substr(i, 1) == '\'') {
                        msg = 'Caracter inválido \'';
                        erro = true;
                    } else if (obj.value.substr(i, 1) == '&') {
                        msg = 'Caracter inválido &';
                        erro = true;
                    }
                }
                break;
            case 'numero':
                if (!isNumber(obj.value)) {
                    msg = 'Campo deve ser numérico';
                    erro = true;
                }
                break;
            case 'hora':
                x = false;
                if (!isNumber(obj.value.substr(0,2))) {
                    x = true;
                } else if ((obj.value.substr(0,2) > 23) || (obj.value.substr(0,2) < 0)) {
                    x = true;
                }
                if (!isNumber(obj.value.substr(3,2))) {
                    x = true;
                } else if ((obj.value.substr(3,2) > 59) || (obj.value.substr(3,2) < 0)) {
                    x = true;
                }
                if (x) {
                    msg = 'Hora inválida';
                    erro = true;
                }
                break;
            case 'telefone':
                if ((obj.value.substr(0,1) != '(') || (!isNumber(obj.value.substr(1,2))) || (obj.value.substr(3,2) != ') ')
                    || (!isNumber(obj.value.substr(5,4))) || (!isNumber(obj.value.substr(10,4)))) {
                    msg = 'Telefone inválido';
                    erro = true;
                }
                break;
            case 'cpf':
                x = false;
                if (!isNumber(obj.value.substr(0,3))) {
                    x = true;
                }
                if (!isNumber(obj.value.substr(4,3))) {
                    x = true;
                }
                if (!isNumber(obj.value.substr(8,3))) {
                    x = true;
                }
                if (!isNumber(obj.value.substr(12,2))) {
                    x = true;
                }
                if (x) {
                    msg = 'CPF inválido';
                    erro = true;
                }
                break;
            case 'cnpj':
                if ((!isNumber(obj.value.substr(0,2))) || (obj.value.substr(2,1) != '.') || (!isNumber(obj.value.substr(3,3)))
                    || (obj.value.substr(6,1) != '.') || (!isNumber(obj.value.substr(7,3))) || (obj.value.substr(10,1) != '/')
                    || (!isNumber(obj.value.substr(11,4))) || (obj.value.substr(15,1) != '-') || (!isNumber(obj.value.substr(16,2)))) {
                    msg = 'CNPJ inválido';
                    erro = true;
                }
                break;
            case 'cep':
                if (!isNumber(obj.value.substr(0,5)) || (obj.value.substr(5,1) != '-') || (!isNumber(obj.value.substr(6,3)))) {
                    msg = 'CEP inválido';
                    erro = true;
                }
                break;
            case 'decimal2':
                real = obj.value.substr(0,obj.value.length-3).replace('.','');
                virgula = obj.value.substr(obj.value.length-3,1);
                centavo = obj.value.substr(obj.value.length-2,2);
                if (virgula != ',') {
                    real = obj.value.substr(0,obj.value.length-4).replace('.','');
                    virgula = obj.value.substr(obj.value.length-4,1);    
                    centavo = obj.value.substr(obj.value.length-3,3);
                }
                if ((!isNumber(real)) || (virgula != ",") || (!isNumber(centavo))) {
                    msg = 'Valor inválido';
                    erro = true;
                }
                break;
            case 'data':
                x = false;
                dia = obj.value.substr(0,2);
                mes = obj.value.substr(3,2);
                ano = obj.value.substr(6,4);
                if (!isNumber(dia)) {
                    x = true;
                } else {
                    switch (mes * 1) {
                        case 1:
                        case 3:
                        case 5:
                        case 7:
                        case 8:
                        case 10:
                        case 12:
                            vdia = 31;
                            break;
                        case 4:
                        case 6:
                        case 9:
                        case 11:
                            vdia = 30;
                            break;
                        case 2:
                            vdia = 28;
                            break;
                        default:
                            vdia = 0;
                            break;
                    }
                    if ((dia < 1) || (dia > vdia)) {
                        x = true;
                    }
                }
                if (!isNumber(mes)) {
                    x = true;
                } else {
                    if ((mes < 1) || (mes > 12)) {
                        x = true;
                    }
                }
                if (!isNumber(ano)) {
                    x = true;
                } else {
                    if (ano < 1900) {
                        x = true;
                    }
                }
                if (x) {
                    msg = 'Data inválida';
                    erro = true;
                }
                break;
            case 'datames':
                x = false;
                mes = obj.value.substr(0,2);
                ano = obj.value.substr(3,4);
                if (!isNumber(mes)) {
                    x = true;
                } else {
                    if ((mes < 1) || (mes > 12)) {
                        x = true;
                    }
                }
                if (!isNumber(ano)) {
                    x = true;
                } else {
                    if (ano < 1900) {
                        x = true;
                    }
                }
                if (x) {
                    msg = 'Data inválida';
                    erro = true;
                }
                break;
            default:
                msg = 'Tipo desconhecido';
                erro = true;
        }
    }

    obj_msg = document.getElementById("mensagem_" + obj.name);

    if (msg == '') {
        obj_msg.innerHTML = "";
    } else {
        obj_msg.innerHTML = "<img src=\"img/alerta.png\" title=\"" + msg + "\">";
    }
}

function caracterIsNumber(valor) {
    var i = 0;
    for (i=0; i<10 ; i++) {
        if (valor == i) {
            return true;
        }
    }
    return false;
}

function isNumber(valor) {
    for (var i=0; i<valor.length; i++) {
        if (!caracterIsNumber(valor.substr(i,1))) {
            return false;
        }
    }
    return true;
}

function maskCep(obj, evt) {
    formataCampo(obj, "?????-???", evt)
}

function maskTelefone(obj, evt) {
    formataCampo(obj, "(??) ????-????", evt)
}

function maskCNPJ(obj, evt) {
    formataCampo(obj, "??.???.???/????-??", evt)
}

function maskData(obj, evt) {
    formataCampo(obj, "??/??/????", evt)
}

function formataCampo(campo, mask, evt) {
    if(document.all) { // Internet Explorer
        key = evt.keyCode;
    } else{ // Nestcape
        key = evt.which;
    }

    if (key == 8) {
        return true;
    }

    string = campo.value;
    i = string.length;

    if (i < mask.length) {
        if (mask.charAt(i) == '?') {
            return (key > 47 && key < 58);
        } else {
            if (mask.charAt(i) == '!') {
                return true;
            }
            for (c = i; c < mask.length; c++) {
                if (mask.charAt(c) != '?' && mask.charAt(c) != '!')
                    campo.value = campo.value + mask.charAt(c);
                else if (mask.charAt(c) == '!'){
                    return true;
                } else {
                    return (key > 47 && key < 58);
                }
            }
        }
    }

    return false;
}

function maskMoeda(fld) {
    if (fld.value != "") {
        fld.value = formataCusto(demaskValue(fld.value));
    }
}

const demaskValue = (valor) => {
    if (String(valor).includes(',') || String(valor).includes('.')){
        return String(valor).replace(/\./g,'').replace(/,/g,'.');
    } 
    let temp = String(parseFloat(valor) / 100);
    if (!temp.includes('.')){
        temp += '.00'
    }
    return temp;
}

const formataCusto = (valor) => {
    const casaDecimal = Math.min(Number(valor.split(".")[1].length), 4);
    return Number(valor).toLocaleString('pt-BR', { minimumFractionDigits: casaDecimal, maximumFractionDigits: casaDecimal });
}

function unmaskMoeda(fld) {
    fld.value = fld.value.replace(",", "").replace(".", "");
}
