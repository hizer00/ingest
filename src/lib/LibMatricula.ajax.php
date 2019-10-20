<?php

/** 
 * LibMatricula.ajax.php
 *
 * Accions AJAX per a la llibreria d'usuaris.
 *
 * @author Josep Ciberta
 * @license https://opensource.org/licenses/GPL-3.0 GNU General Public License version 3
 */

require_once('../Config.php');
require_once(ROOT.'/lib/LibForms.php');
require_once(ROOT.'/lib/LibCripto.php');
require_once(ROOT.'/lib/LibUsuari.php');

session_start();
if (!isset($_SESSION['usuari_id'])) 
	header("Location: ../Surt.php");

$conn = new mysqli($CFG->Host, $CFG->Usuari, $CFG->Password, $CFG->BaseDades);
if ($conn->connect_error) 
	die("ERROR: No ha estat possible connectar amb la base de dades: " . $conn->connect_error);

if (($_SERVER['REQUEST_METHOD'] === 'POST') && (isset($_REQUEST['accio']))) {
	if ($_REQUEST['accio'] == 'EliminaMatriculaCurs') {

		print "EliminaMatriculaCurs";
		
		$CursId = $_REQUEST['id'];
		
		// S'ha d'executar de forma atòmica
		$conn->query('START TRANSACTION');
		try {
			// https://stackoverflow.com/questions/4429319/you-cant-specify-target-table-for-update-in-from-clause
			$SQL1 = 'DELETE FROM NOTES WHERE matricula_id IN ( '.
				' 	SELECT Temp.matricula_id FROM ( '.
				' 		SELECT DISTINCT N.matricula_id '.
				' 		FROM NOTES N '.
				' 		LEFT JOIN MATRICULA M ON (M.matricula_id=N.matricula_id) '.
				' 		WHERE curs_id='.$CursId.	
				' 	) AS Temp '.
				' ) ';
			if (!$conn->query($SQL1))
				throw new Exception($conn->error.'. SQL: '.$SQL1);

			$SQL2 = 'DELETE FROM MATRICULA WHERE curs_id='.$CursId;	
			if (!$conn->query($SQL2))
				throw new Exception($conn->error.'. SQL: '.$SQL2);

			$conn->query('COMMIT');

		} catch (Exception $e) {
			$conn->query('ROLLBACK');
			die("ERROR EliminaMatriculaCurs. Causa: ".$e->getMessage());
		}
		
		print '<P>'.$SQL1.'<P>'.$SQL2;
		
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