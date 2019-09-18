<?php

/** 
 * LibNotes.ajax.php
 *
 * Accions AJAX per a la llibreria de notes.
 *
 * @author Josep Ciberta
 * @license https://opensource.org/licenses/GPL-3.0 GNU General Public License version 3
 */

require_once('../Config.php');
require_once(ROOT.'/lib/LibNotes.php');

session_start();
if (!isset($_SESSION['usuari_id'])) 
	header("Location: ../Surt.php");
$Festiu = unserialize($_SESSION['FESTIU']);

$conn = new mysqli($CFG->Host, $CFG->Usuari, $CFG->Password, $CFG->BaseDades);
if ($conn->connect_error) 
	die("ERROR: No ha estat possible connectar amb la base de dades: " . $conn->connect_error);

if (($_SERVER['REQUEST_METHOD'] === 'POST') && (isset($_REQUEST['accio']))) {
	if ($_REQUEST['accio'] == 'MarcaComNotaAnterior') {
		$nom = $_REQUEST['nom'];
		$data = explode("_", $nom);
		$SQL = 'UPDATE NOTES SET convocatoria=0 WHERE notes_id='.$data[1];	

		try {
			if (!$conn->query($SQL))
				throw new Exception($this->Connexio->error.'. SQL: '.$SQL);
			print $SQL;
		} 
		catch (Exception $e) {
			print "ERROR MarcaComNotaAnterior. Causa: ".$e->getMessage();
		}	
	}
	else if ($_REQUEST['accio'] == 'Convalida') {
		// Convalida una UF: Posa el camp convalidat de NOTES a cert, posa una nota de 5 i el camp convocatòria a 0.
		$nom = $_REQUEST['nom'];
		$data = explode("_", $nom); // Nom_Id_Convocatòria
		$SQL = 'UPDATE NOTES SET convalidat=1, convocatoria=0, nota'.$data[2].'=5 WHERE notes_id='.$data[1];	
		try {
			if (!$conn->query($SQL))
				throw new Exception($this->Connexio->error.'. SQL: '.$SQL);
			print $SQL;
		} 
		catch (Exception $e) {
			print "ERROR Convalida. Causa: ".$e->getMessage();
		}	
	}
	else {
		if ($CFG->Debug)
			print "Acció no suportada. Valor de $_POST: ".json_encode($_POST);
		else
			print "Acció no suportada.";
	}
}
else 
    print "ERROR. No hi ha POST o no hi ha acció.";

?>