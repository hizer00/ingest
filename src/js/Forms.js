/** 
 * Forms.js
 *
 * Accions JavaScript de suport a la llibreria de formularis.
 *
 * @author Josep Ciberta
 * @license https://opensource.org/licenses/GPL-3.0 GNU General Public License version 3
 * @version 1.0
 */

/**
 * ActualitzaTaula
 *
 * @param element Botó que ha fet la crida.
 */
function ActualitzaTaula(element) { 
	var sCerca = $('input[name="edtRecerca"]').val();	
	var sSQL = $('input[name="edtSQL"]').val();	
	var sCamps = $('input[name="edtCamps"]').val();	
	var sDescripcions = $('input[name="edtDescripcions"]').val();	
//console.dir(sCerca);
//console.dir(sSQL);
//console.dir(sCamps);
console.dir(sDescripcions);

	$.ajax( {
		type: 'POST',
		url: 'lib/LibFormsAJAX.php',
		data:{
			'accio': 'ActualitzaTaula',
			'cerca': sCerca,
			'sql': sSQL,
			'camps': sCamps,
			'descripcions': sDescripcions
		},
        success: function(data) {
            $('#taula').html(data);
            //$('#debug').html('<textarea disabled>'+data+'</textarea>');
        }, 
		error: function(data) {
			$('#debug').html('Hi ha hagut un error. Dades rebudes: '+ JSON.stringify(data));
		}
    } );
}

/**
 * RecercaKeyPress
 *
 * Funció que comprova si s'ha premut la tecla ENTER per apretar el botó de cercar.
 *
 * @param event Event que ha provocat la crida.
 */
function RecercaKeyPress(event) {
	if (event.keyCode === 13) {
		// Cancel the default action, if needed
		event.preventDefault();
		$('#btnRecerca').click();
	}
}

/**
 * GetFormDataJSON
 *
 * Funció que retorna els elements d'un formulari en format JSON.
 *
 * @param oForm Formulari del que es vol obtenir les dades.
 * @return string Elements del formulari en format JSON.
 */
function GetFormDataJSON(oForm) {
//console.dir('oForm: ' + oForm);
//console.dir('elements: ' + oForm.elements);
	var controls = oForm.elements;
	var msg = "[";
	for (var i=0, iLen=controls.length; i<iLen; i++) {
		msg += '{"name":"' + controls[i].name + '","value":"' + controls[i].value + '"},';
    }
	msg = msg.slice(0, -1); // Treiem la darrera coma
	msg += ']';
	return msg;
}

/**
 * DesaFitxa
 *
 * @param element Botó que desa la fitxa.
 */
function DesaFitxa(element) { 
//console.dir('element: ' + element);
	var frm = document.getElementById('frmFitxa');
console.dir('frm: ' + frm);
	var jsonForm = GetFormDataJSON(frm);
console.dir('jsonForm: ' + jsonForm);
//	var jsonForm2 = JSON.stringify($('#frmFitxa').serializeArray());
//console.dir('jsonForm2: ' + jsonForm2);

	$.ajax( {
		type: 'POST',
		url: 'lib/LibFormsAJAX.php',
		data:{
			'accio': 'DesaFitxa',
			'form': jsonForm
		},
        success: function(data) {
			$('#btnDesa').hide();
			$('#MissatgeCorrecte').show();
            $('#debug').html(data);
        }, 
		error: function(data) {
			$('#MissatgeError').show();
			$('#debug').html('Hi ha hagut un error. Dades rebudes: '+ JSON.stringify(data));
		}
    } );
}

// http://ecmanaut.blogspot.com/2006/07/encoding-decoding-utf8-in-javascript.html
function encode_utf8(s) {
  return unescape(encodeURIComponent(s));
}

function decode_utf8(s) {
  return decodeURIComponent(escape(s));
}

/**
 * CercaLookup
 *
 * @param codi Nom del component lookup que conté el codi.
 * @param valor Nom del component lookup que conté la descripció.
 * @param url Pàgina per fer la recerca.
 * @param camps Camps a mostrar al lookup.
 */
function CercaLookup(codi, valor, url, camps) { 
	targetFieldCodi = document.getElementsByName(codi)[0];
	targetFieldValor = document.getElementsByName(valor)[0];
	targetCamps = camps;

	w = screen.width - 100;
	h = screen.height - 100;
	var left = (screen.width/2)-(w/2);
	var top = (screen.height/2)-(h/2);

	var w = window.open(url + '?Modalitat=mfBusca','_blank','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);
	// pass the targetField to the pop up window
	w.targetFieldCodi = targetFieldCodi;
	w.targetFieldValor = targetFieldValor;
	w.targetCamps = targetCamps;
	w.focus();
}

// this function is called by the pop up window
function setSearchResult(targetFieldCodi, targetFieldValor, returnValue) {
	//alert("returnValue:" + returnValue);
console.dir(targetFieldValor);

	// Obtenim els camps que s'han de mostrar al lookup.
	var objCamps = document.getElementsByName(targetFieldCodi.name + '_camps')[0];
	var sCamps = (objCamps.value).replace(/ /g, '');;
console.log("sCamps: " + sCamps);
	var aCamps = sCamps.split(',');
console.dir("aCamps: " + aCamps);

	// El replace no substitueix totes les ocurrències de forma "normal". S'ha de fer amb una expressió regular.
	// https://stackoverflow.com/questions/1144783/how-to-replace-all-occurrences-of-a-string-in-javascript
	var jsonValorRetorn = returnValue.replace(/~/g, '"');
console.dir("jsonValorRetorn: " + jsonValorRetorn);
//alert("jsonValorRetorn: " + jsonValorRetorn);
	var obj = JSON.parse(jsonValorRetorn);

	// Recorrem les propietats (del 1r nivell) de l'objecte.
	// La primera propietat és l'identificador.
	// https://stackoverflow.com/questions/684672/how-do-i-loop-through-or-enumerate-a-javascript-object 
	var bPrimer = true;
	var sText = '';
	for (var key in obj) {
		if (obj.hasOwnProperty(key)) {
			if (bPrimer) {
				targetFieldCodi.value = obj[key]; 
				bPrimer = false;
			}
			else {
				if (aCamps.indexOf(key) > -1)
					sText += obj[key] + ' ';
			}
		}
	}
	targetFieldValor.value = sText.trim();

	window.focus();
}

// return the value to the parent window
function returnYourChoice(choice) {
	opener.setSearchResult(targetFieldCodi, targetFieldValor, choice);
	close();
}