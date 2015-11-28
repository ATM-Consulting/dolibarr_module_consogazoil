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
 * \file consogazoil/report/km.php
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

llxHeader('', $langs->trans("ConsoGazReportKM"));

$object = new ConsogazoilVehTake($db);

$formconsogaz = new FormConsoGazoil($db);

$style = Array (
		'bleu1' => array (
				'0' => 'bgcolor="#8DB4E3" align="center"',
				'1' => 'bgcolor="#DBE5F1" align="center"' 
		),
		'bleu2' => array (
				'0' => 'bgcolor="#538ED5" align="center"',
				'1' => 'bgcolor="#C5D9F1" align="center"' 
		),
		'turquoise1' => array (
				'0' => 'bgcolor="#76C0D4" align="center"',
				'1' => 'bgcolor="#DBEEF3" align="center"' 
		),
		'turquoise2' => array (
				'0' => 'bgcolor="#47AAC5" align="center"',
				'1' => 'bgcolor="#B6DDE8" align="center"' 
		),
		'vert1' => array (
				'0' => 'bgcolor="#B0CA7C" align="center"',
				'1' => 'bgcolor="#EAF1DD" align="center"' 
		),
		'vert2' => array (
				'0' => 'bgcolor="#8CAE48" align="center"',
				'1' => 'bgcolor="#C2D69A" align="center"' 
		),
		'violet1' => array (
				'0' => 'bgcolor="#BEB0D0" align="center"',
				'1' => 'bgcolor="#E5E0EC" align="center"' 
		),
		'violet2' => array (
				'0' => 'bgcolor="#9B84B6" align="center"',
				'1' => 'bgcolor="#C0B2D2" align="center"' 
		),
		'orange' => array (
				'0' => 'bgcolor="#FAC090" align="center"',
				'1' => 'bgcolor="#FDE9D9" align="center"' 
		),
		'marron' => array (
				'0' => 'bgcolor="#C5BE97" align="center"',
				'1' => 'bgcolor="#DDD9C3" align="center"' 
		) 
);

// Build array t display
if (empty($year_filter))
	$year_filter = strftime("%Y", dol_now());

print_fiche_titre($langs->trans('ConsoGazReportKM'), '', dol_buildpath('/consogazoil/img/object_consogazoil.png', 1), 1);

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

print '<table class="border" width="100%">';
print '<tr class="liste_titre">';
print '<td>' . $langs->trans('ConsoGazImmat') . '</td>';

for($month = 1; $month <= 12; $month ++) {
	
	print '<td align="center">' . dol_print_date(dol_mktime(12, 0, 0, $month, 1, $year_filter), "%B") . '</td>';
}

print '<td align="center">' . $langs->trans('ConsoGazAvgMonth') . '</td>';
print '<td align="center">' . $langs->trans('ConsoGazLstKmKnown') . '</td>';
print '</tr>';

$result = $object->fetch_immat($year_filter);
if ($result < 0)
	setEventMessage($object->error, 'errors');

foreach ( $object->lines_immat as $lineimat ) {
	$var = ! $var;
	print '<tr>';
	print '<td ' . $bc[$var] . '>' . $lineimat . '</td>';
	
	$result = $object->fetch_report_km($year_filter, $lineimat);
	if ($result < 0)
		setEventMessage($object->error, 'errors');
	
	print '<td ' . $style['bleu1'][$var] . ' align="center">';
	print $object->lines_report[1];
	print '</td>';
	print '<td ' . $style['bleu1'][$var] . ' align="center">';
	print $object->lines_report[2];
	print '</td>';
	print '<td ' . $style['bleu1'][$var] . ' align="center">';
	print $object->lines_report[3];
	print '</td>';
	print '<td ' . $style['turquoise1'][$var] . ' align="center">';
	print $object->lines_report[4];
	print '</td>';
	print '<td ' . $style['turquoise1'][$var] . ' align="center">';
	print $object->lines_report[5];
	print '</td>';
	print '<td ' . $style['turquoise1'][$var] . ' align="center">';
	print $object->lines_report[6];
	print '</td>';
	print '<td ' . $style['vert1'][$var] . ' align="center">';
	print $object->lines_report[7];
	print '</td>';
	print '<td ' . $style['vert1'][$var] . ' align="center">';
	print $object->lines_report[8];
	print '</td>';
	print '<td ' . $style['vert1'][$var] . ' align="center">';
	print $object->lines_report[9];
	print '</td>';
	print '<td ' . $style['violet1'][$var] . ' align="center">';
	print $object->lines_report[10];
	print '</td>';
	print '<td ' . $style['violet1'][$var] . ' align="center">';
	print $object->lines_report[11];
	print '</td>';
	print '<td ' . $style['violet1'][$var] . ' align="center">';
	print $object->lines_report[12];
	print '</td>';
	print '<td ' . $style['orange'][$var] . ' align="center">';
	print $object->lines_report[13];
	print '</td>';
	print '<td ' . $style['marron'][$var] . ' align="center">';
	print $object->lines_report[14];
	print '</td>';
	print '</tr>';
}

Print '<tr><td colspan="15"></br></td></tr>';
print '<tr class="liste_titre">';
print '<td>' . $langs->trans('ConsoGazServ') . '</td>';
for($month = 1; $month <= 12; $month ++) {
	
	print '<td align="center">' . dol_print_date(dol_mktime(12, 0, 0, $month, 1, $year_filter), "%B") . '</td>';
}

print '<td colspan="2" align="center">' . $langs->trans('ConsoGazAvgMonth') . '</td>';
print '</tr>';

$result = $object->fetch_service($year_filter);
if ($result < 0)
	setEventMessage($object->error, 'errors');

foreach ( $object->lines_service as $keyserv => $lineserv ) {
	$var = ! $var;
	print '<tr>';
	print '<td ' . $bc[$var] . '>' . $lineserv . '</td>';
	
	$result = $object->fetch_report_km_service($year_filter, $keyserv);
	if ($result < 0)
		setEventMessage($object->error, 'errors');
	
	print '<td ' . $style['bleu1'][$var] . ' align="center">';
	print $object->lines_report[1];
	print '</td>';
	print '<td ' . $style['bleu1'][$var] . ' align="center">';
	print $object->lines_report[2];
	print '</td>';
	print '<td ' . $style['bleu1'][$var] . ' align="center">';
	print $object->lines_report[3];
	print '</td>';
	print '<td ' . $style['turquoise1'][$var] . ' align="center">';
	print $object->lines_report[4];
	print '</td>';
	print '<td ' . $style['turquoise1'][$var] . ' align="center">';
	print $object->lines_report[5];
	print '</td>';
	print '<td ' . $style['turquoise1'][$var] . ' align="center">';
	print $object->lines_report[6];
	print '</td>';
	print '<td ' . $style['vert1'][$var] . ' align="center">';
	print $object->lines_report[7];
	print '</td>';
	print '<td ' . $style['vert1'][$var] . ' align="center">';
	print $object->lines_report[8];
	print '</td>';
	print '<td ' . $style['vert1'][$var] . ' align="center">';
	print $object->lines_report[9];
	print '</td>';
	print '<td ' . $style['violet1'][$var] . ' align="center">';
	print $object->lines_report[10];
	print '</td>';
	print '<td ' . $style['violet1'][$var] . ' align="center">';
	print $object->lines_report[11];
	print '</td>';
	print '<td ' . $style['violet1'][$var] . ' align="center">';
	print $object->lines_report[12];
	print '</td>';
	print '<td ' . $style['orange'][$var] . ' align="center" colspan="2">';
	print $object->lines_report[13];
	print '</td>';
	print '</tr>';
	
	print '</tr>';
}

// Total soc
$var = ! $var;
print '<tr ' . $bc[$var] . '>';
print '<td>' . $langs->trans('Total') . '</td>';
$result = $object->fetch_report_km_service($year_filter, 0);
if ($result < 0)
	setEventMessage($object->error, 'errors');

print '<td ' . $style['bleu1'][$var] . ' align="center">';
print $object->lines_report[1];
print '</td>';
print '<td ' . $style['bleu1'][$var] . ' align="center">';
print $object->lines_report[2];
print '</td>';
print '<td ' . $style['bleu1'][$var] . ' align="center">';
print $object->lines_report[3];
print '</td>';
print '<td ' . $style['turquoise1'][$var] . ' align="center">';
print $object->lines_report[4];
print '</td>';
print '<td ' . $style['turquoise1'][$var] . ' align="center">';
print $object->lines_report[5];
print '</td>';
print '<td ' . $style['turquoise1'][$var] . ' align="center">';
print $object->lines_report[6];
print '</td>';
print '<td ' . $style['vert1'][$var] . ' align="center">';
print $object->lines_report[7];
print '</td>';
print '<td ' . $style['vert1'][$var] . ' align="center">';
print $object->lines_report[8];
print '</td>';
print '<td ' . $style['vert1'][$var] . ' align="center">';
print $object->lines_report[9];
print '</td>';
print '<td ' . $style['violet1'][$var] . ' align="center">';
print $object->lines_report[10];
print '</td>';
print '<td ' . $style['violet1'][$var] . ' align="center">';
print $object->lines_report[11];
print '</td>';
print '<td ' . $style['violet1'][$var] . ' align="center">';
print $object->lines_report[12];
print '</td>';
print '<td ' . $style['orange'][$var] . ' align="center" colspan="2">';
print $object->lines_report[13];
print '</td>';
print '</tr>';

print '</table>';

llxFooter();
$db->close();