<?php

/**
 * Documents.php
 * 
 * Pàgina principal dels documents (Qualitat).
 * Es permet l'accés a aquesta pàgina sense estar identificat.
 * 
 * @author: Josep Ciberta
 * @license: https://opensource.org/licenses/GPL-3.0 GNU General Public License version 3
 */

require_once('Config.php');
require_once(ROOT.'/lib/LibDocument.php');

session_start();
if (isset($_SESSION['usuari_id'])) {
    // Usuari identificat
    $Usuari = unserialize($_SESSION['USUARI']);
    $Sistema = unserialize($_SESSION['SISTEMA']);
}
else {
    // Usuari sense identificar
    $Usuari = null;
    $Sistema = null;
}

$conn = new mysqli($CFG->Host, $CFG->Usuari, $CFG->Password, $CFG->BaseDades);
if ($conn->connect_error)
	die("ERROR: No ha estat possible connectar amb la base de dades: " . $conn->connect_error);

if ($Usuari === null) {
    // Usuari no identificat
	$doc = new Document($conn, $Usuari, $Sistema);
	$doc->EscriuFormulariRecerca();
}

?>