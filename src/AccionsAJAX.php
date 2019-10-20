<?php

/** 
 * AccionsAJAX.php
 *
 * Accions AJAX diverses.
 *
 * @author Josep Ciberta
 * @license https://opensource.org/licenses/GPL-3.0 GNU General Public License version 3
 */
 
require_once('Config.php');
require_once(ROOT.'lib/LibNotes.php');
require_once(ROOT.'lib/LibMatricula.php');
require_once(ROOT.'lib/LibExpedient.php');

print "AccionsAJAX<hr>";
exit;

session_start();
if (!isset($_SESSION['usuari_id'])) 
	header("Location: Surt.php");
$Usuari = unserialize($_SESSION['USUARI']);

$conn = new mysqli($CFG->Host, $CFG->Usuari, $CFG->Password, $CFG->BaseDades);
if ($conn->connect_error) 
	die("ERROR: No ha estat possible connectar amb la base de dades: " . $conn->connect_error);

print "AccionsAJAX<hr>";
exit;

if (($_SERVER['REQUEST_METHOD'] === 'POST') && (isset($_REQUEST['accio']))) {
	if ($_REQUEST['accio'] == 'MatriculaUF') {
		$nom = $_REQUEST['nom'];
		$check = $_REQUEST['check'];
		$Baixa = ($check == 'true') ? 0 : 1; // Si estava actiu, ara el donem de baixa
		$NotaId = str_replace('chbNotaId_', '', $nom);	
		$SQL = 'UPDATE NOTES SET baixa='.$Baixa.' WHERE notes_id='.$NotaId;	
		$conn->query($SQL);
		print $SQL;
	}
	else if ($_REQUEST['accio'] == 'ConvalidaUF') {
		$nom = $_REQUEST['nom'];
		$AlumneId = $_REQUEST['alumne'];
		//$check = $_REQUEST['check'];
		//$Baixa = ($check == 'true') ? 0 : 1; // Si estava actiu, ara el donem de baixa
		$NotaId = str_replace('chbConvalidaUFNotaId_', '', $nom);	

		$Matricula = new Matricula($conn, $Usuari);
		$Matricula->ConvalidaUF($NotaId);
		
		//header("Location: MatriculaAlumne.php?AlumneId=".$AlumneId); -> No funciona!

		print 'Id nota convalidada: '.$NotaId;
	}
	else if ($_REQUEST['accio'] == 'CanviPassword') {
		$UsuariId = $_REQUEST['usuari_id'];
		$Password = $_REQUEST['password'];
		$SQL = "UPDATE USUARI SET password='".password_hash($Password, PASSWORD_DEFAULT)."', imposa_canvi_password=1 WHERE usuari_id=". $UsuariId;
		$conn->query($SQL);
		print 'Contrasenya canviada correctament.';
	}
	else if ($_REQUEST['accio'] == 'ActualitzaNota') {
		$nom = $_REQUEST['nom'];
		$data = explode("_", $nom);
		$valor = $_REQUEST['valor'];
		if (EsNotaValida($valor)) {
			$NotaNumerica = NotaANumero($valor);
			$SQL = 'UPDATE NOTES SET nota'.$data[2].'='.$NotaNumerica.' WHERE notes_id='.$data[1];	
			$conn->query($SQL);
			print $SQL;
		} 
		else
			print "Valor no v�lid: ".$valor;
	}
	else if ($_REQUEST['accio'] == 'ActualitzaNotaRecuperacio') {
		$nom = $_REQUEST['nom'];
//print $nom;
		$data = explode("_", $nom);
		$valor = $_REQUEST['valor'];
		if (EsNotaValida($valor)) {
			$NotaNumerica = NotaANumero($valor);
			$SQL = 'UPDATE NOTES SET nota'.($data[2]+1).'='.$NotaNumerica.' WHERE notes_id='.$data[1];	
			$conn->query($SQL);
			print $SQL;
		} 
		else
			print "Valor no v�lid: ".$valor;
	}
	else if ($_REQUEST['accio'] == 'AssignaUF') {
		$nom = $_REQUEST['nom'];
		$check = ($_REQUEST['check']=='true');
		$data = explode("_", $nom);
		if ($check) {
			// Assignem UF
			$SQL = 'INSERT INTO PROFESSOR_UF (professor_id, uf_id) VALUES ('.$data[2].', '.$data[1].')';	
			$conn->query($SQL);
			print $SQL;
		}
		else {
			// Desassignem UF
			$SQL = 'DELETE FROM PROFESSOR_UF WHERE professor_id='.$data[2].' AND uf_id='.$data[1];	
			$conn->query($SQL);
			print $SQL;
		}
	}
	else if ($_REQUEST['accio'] == 'ActualitzaTaulaNotes') {
		$CicleId = $_REQUEST['CicleId'];
		$Nivell = $_REQUEST['Nivell'];
		print ObteTaulaNotesJSON($conn, $CicleId, $Nivell);
	}
	else
        print "Acci� no suportada.";
}
else 
    print "ERROR. No hi ha POST o no hi ha acci�.";

?>
