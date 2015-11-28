<?php
/* Copyright (C) 2013 Florian Henry  		<florian.henry@open-concept.pro>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * \file consogazoil/report/conso.php
 * \ingroup consogazoil
 * \brief Pages of report
 */
$res = @include ("../../main.inc.php"); // For root directory
if (! $res)
	$res = @include ("../../../main.inc.php"); // For "custom" directory
if (! $res)
	die("Include of main fails");

require_once '../class/consogazoilvehtake.class.php';
require_once '../class/html.formconsogazoil.class.php';

// Security check
if (empty($user->rights->consogazoil->lire))
	accessforbidden();

$langs->load('consogazoil@consogazoil');

$year_filter = GETPOST('yearfilter', 'int');

llxHeader('', $langs->trans("ConsoGazReportConso"), '', '', '', '', array (), array (
		'/consogazoil/css/gazoil.css' 
));

$object = new ConsogazoilVehTake($db);

$formconsogaz = new FormConsoGazoil($db);

// Build array t display
if (empty($year_filter))
	$year_filter = strftime("%Y", dol_now());

print_fiche_titre($langs->trans('ConsoGazReportConso'), '', dol_buildpath('/consogazoil/img/object_consogazoil.png', 1), 1);

print '<form action="' . $_SERVER["PHP_SELF"] . '" method="POST" name="filterdate">' . "\n";
print '<input type="hidden" name="token" value="' . $_SESSION['newtoken'] . '" / >';
print '<table><tr><td>';
$selectdate = $formconsogaz->select_year_report('yearfilter', $year_filter);
if ($selectdate != - 1) {
	print $selectdate;
} else {
	setEventMessage($formconsogaz->error, 'errors');
}
print '</td><td><input type="submit" value="' . $langs->trans('ConsoGazFilterDate') . '"/></td></tr></table>';
print '</form>';

print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td>' . $langs->trans('ConsoGazImmat') . '</td>';

for($month = 1; $month <= 12; $month ++) {
	
	print '<td>' . dol_print_date(dol_mktime(12, 0, 0, $month, 1, $year_filter), "%B") . '</td>';
	print '<td>' . $langs->trans('ConsoGazState') . '</td>';
	if ((($month % 3) == 0)) {
		print '<td>' . $langs->trans('ConsoGazTrimestre') . '</td>';
	}
	if ((($month % 6) == 0)) {
		print '<td>' . $langs->trans('ConsoGazSemestre') . '</td>';
	}
}

print '<td>' . $langs->trans('Total') . '</td>';
print '</tr>';

$result = $object->fetch_immat($year_filter);
if ($result < 0)
	setEventMessage($object->error, 'errors');

foreach ( $object->lines_immat as $lineimat ) {
	$var = ! $var;
	if ($var)
		$ligne_style = '';
	else
		$ligne_style = 'bis';
	
	print '<tr ' . $bc[$var] . '>';
	print '<td>' . $lineimat . '</td>';
	
	$result = $object->fetch_report_conso_original($year_filter, $lineimat);
	if ($result < 0)
		setEventMessage($object->error, 'errors');
	
	foreach ( $object->lines_report as $key => $linereport ) {
		if ($key >= 1 && $key <= 7) {
			$columnstyle = 'trim1' . $ligne_style;
		} elseif ($key >= 8 && $key <= 14) {
			$columnstyle = 'trim2' . $ligne_style;
		} elseif ($key >= 16 && $key <= 22) {
			$columnstyle = 'trim3' . $ligne_style;
		} elseif ($key >= 23 && $key <= 29) {
			$columnstyle = 'trim4' . $ligne_style;
		} elseif ($key == 15 || $key == 30) {
			$columnstyle = 'semestre' . $ligne_style;
		} elseif ($key == 31) {
			$columnstyle = 'total' . $ligne_style;
		}
		print '<td class="' . $columnstyle . '">' . $linereport . '</td>' . "\n";
	}
	
	print '</tr>';
}

print '</table>';

print '<br>';

print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td>' . $langs->trans('ConsoGazServ') . '</td>';

for($month = 1; $month <= 12; $month ++) {
	
	print '<td>' . dol_print_date(dol_mktime(12, 0, 0, $month, 1, $year_filter), "%B") . '</td>';
	print '<td></td>';
	if ((($month % 3) == 0)) {
		print '<td>' . $langs->trans('ConsoGazTrimestre') . '</td>';
	}
	if ((($month % 6) == 0)) {
		print '<td>' . $langs->trans('ConsoGazSemestre') . '</td>';
	}
}

print '<td>' . $langs->trans('Total') . '</td>';
print '</tr>';

$result = $object->fetch_service($year_filter);
if ($result < 0)
	setEventMessage($object->error, 'errors');

foreach ( $object->lines_service as $keyserv => $lineserv ) {
	$var = ! $var;
	if ($var)
		$ligne_style = '';
	else
		$ligne_style = 'bis';
	
	print '<tr ' . $bc[$var] . '>';
	print '<td>' . $lineserv . '</td>';
	
	$result = $object->fetch_report_conso_service_original($year_filter, $keyserv);
	if ($result < 0)
		setEventMessage($object->error, 'errors');
	
	foreach ( $object->lines_report as $key => $linereport ) {
		if ($key >= 1 && $key <= 7) {
			$columnstyle = 'trim1' . $ligne_style;
		} elseif ($key >= 8 && $key <= 14) {
			$columnstyle = 'trim2' . $ligne_style;
		} elseif ($key >= 16 && $key <= 22) {
			$columnstyle = 'trim3' . $ligne_style;
		} elseif ($key >= 23 && $key <= 29) {
			$columnstyle = 'trim4' . $ligne_style;
		} elseif ($key == 15 || $key == 30) {
			$columnstyle = 'semestre' . $ligne_style;
		} elseif ($key == 31) {
			$columnstyle = 'total' . $ligne_style;
		}
		print '<td class="' . $columnstyle . '">' . $linereport . '</td>' . "\n";
	}
	
	print '</tr>';
}

// Total soc
$var = ! $var;
if ($var)
	$ligne_style = '';
else
	$ligne_style = 'bis';
print '<tr ' . $bc[$var] . '>';
print '<td>' . $langs->trans('Total') . '</td>';
$result = $object->fetch_report_conso_service_original($year_filter, 0);
if ($result < 0)
	setEventMessage($object->error, 'errors');

foreach ( $object->lines_report as $key => $linereport ) {
	if ($key >= 1 && $key <= 7) {
		$columnstyle = 'trim1' . $ligne_style;
	} elseif ($key >= 8 && $key <= 14) {
		$columnstyle = 'trim2' . $ligne_style;
	} elseif ($key >= 16 && $key <= 22) {
		$columnstyle = 'trim3' . $ligne_style;
	} elseif ($key >= 23 && $key <= 29) {
		$columnstyle = 'trim4' . $ligne_style;
	} elseif ($key == 15 || $key == 30) {
		$columnstyle = 'semestre' . $ligne_style;
	} elseif ($key == 31) {
		$columnstyle = 'total' . $ligne_style;
	}
	print '<td class="' . $columnstyle . '">' . $linereport . '</td>' . "\n";
}

print '</table>';

llxFooter();
$db->close();