function criaXMLHttpRequest() {
    var XMLHTTPREQUEST_IE = new Array(
        "Msxml2.XMLHTTP.6.0",
        "Msxml2.XMLHTTP.5.0",
        "Msxml2.XMLHTTP.4.0",
        "Msxml2.XMLHTTP.3.0",
        "Msxml2.XMLHTTP",
        "Microsoft.XMLHTTP"
        );
    var oXMLhttp = null;

    // Cria o HttpRequest para o respectivo navegador.
    if (window.XMLHttpRequest != null) {
        oXMLhttp = new window.XMLHttpRequest();
    } else if (window.ActiveXObject != null) {

        // Percorre no IE a procura do objeto ActiveX na biblioteca mais recente
        var bCriado = false;
        for (var ind = 0;ind < XMLHTTPREQUEST_IE.length && ! bCriado; ind++){
            try {
                oXMLhttp = new ActiveXObject(XMLHTTPREQUEST_IE[ind]);
                bCriado = true;
            } catch (ex) {}
        }
    }

    // Tratamento de erro caso n�o encontre nenhum.
    if (oXMLhttp == null) {
        alert("Falha no HttpRequest():\n\n" + "Objeto XMLHttpRequest n�o foi criado.");
    }

    // Retorna o objeto instanciado ou n�o
    return oXMLhttp;

}

function criaXMLDocument(){
    var XMLDOCUMENT_IE = new Array (
        "Msxml2.DOMDocument.6.0",
        "Msxml2.DOMDocument.5.0",
        "Msxml2.DOMDocument.4.0",
        "Msxml2.DOMDocument.3.0",
        "Msxml2.DOMDocument",
        "Microsoft.XmlDom");

    var oXMLdoc = null;
    
    //Criar o objeto DOMDocument para o respectivo navegador.
    if (window.ActiveXObject != null){
        
        for (var i = 0; i < XMLDOCUMENT_IE.length; i++) {
            try {                
                var oXMLdoc = new ActiveXObject(XMLDOCUMENT_IE[i]);
                return oXMLdoc;
            } catch (oErro) {
            //N�o deve fazer nada
            }
        }
         
        //Mostrar mensagem de erro.
        throw new Error("A pacote MSXML n�o est� instalado.");
    }            
    // Cria��o do XMLDocument no Mozilla
    else if (document.implementation && 
        document.implementation.createDocument){
        try {
            oXMLdoc = document.implementation.createDocument("","",
                null);
            return oXMLdoc;
        } catch(oErro){
            oXMLdoc = null;
        }
        throw new Error("Erro ao criar XMLDocument no Mozilla.");
    }      
}

//Vari�veis para identificar o navegador
var sUserAgent = navigator.userAgent;
var fAppVersion = parseFloat(navigator.appVersion);
var ehOpera = sUserAgent.indexOf("Opera") > -1;
var ehIE = !ehOpera && sUserAgent.indexOf("compatible") > -1 && sUserAgent.indexOf("MSIE") > -1;