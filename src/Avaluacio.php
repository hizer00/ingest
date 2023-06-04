﻿<?php

/** 
 * Avaluacio.php
 *
 * Administrador d'avaluacions.
 *
 * GET:
 * - CursId: Id del curs a administrar.
 *
 * @author Josep Ciberta
 * @license https://opensource.org/licenses/GPL-3.0 GNU General Public License version 3
 */

require_once('Config.php');
require_once(ROOT.'/lib/LibURL.php');
require_once(ROOT.'/lib/LibAvaluacio.php');

session_start();
if (!isset($_SESSION['usuari_id'])) 
	header("Location: Surt.php");
$Usuari = unserialize($_SESSION['USUARI']);
$Sistema = unserialize($_SESSION['SISTEMA']);

if (!$Usuari->es_admin && !$Usuari->es_direccio && !$Usuari->es_cap_estudis)
	header("Location: Surt.php");

$conn = new mysqli($CFG->Host, $CFG->Usuari, $CFG->Password, $CFG->BaseDades);
if ($conn->connect_error)
	die("ERROR: No ha estat possible connectar amb la base de dades: " . $conn->connect_error);

RecuperaGET($_GET);

// Paràmetres de la URL (si n'hi ha).
$CursId = (isset($_GET) && array_key_exists('CursId', $_GET)) ? $_GET['CursId'] : -1;
if ($CursId == -1)
	header("Location: Surt.php");

CreaIniciHTML($Usuari, "Avaluació");
echo '<script language="javascript" src="js/Avaluacio.js?v1.1" type="text/javascript"></script>';

$Avaluacio = new Avaluacio($conn, $Usuari, $Sistema);
$Avaluacio->Carrega($CursId);
echo $Avaluacio->CreaMissatges();

echo "<h2>Avaluació actual</h2>";
$Avaluacio->EscriuTaula();

echo "<h2>Accions</h2>";
$Avaluacio->EscriuBotons();

echo "<DIV id=debug></DIV>";
echo "<DIV id=debug2></DIV>";

$conn->close(); 
 
?>