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
 *      \file       consogazoil/report/conso.php
 *      \ingroup    consogazoil
 *      \brief      Pages of report
 */


$res=@include("../../main.inc.php");					// For root directory
if (! $res) $res=@include("../../../main.inc.php");	// For "custom" directory
if (! $res) die("Include of main fails");

require_once '../class/consogazoilvehtake.class.php';
require_once '../class/html.formconsogazoil.class.php';

// Security check
if (empty ( $user->rights->consogazoil->lire )) accessforbidden ();

$langs->load('consogazoil@consogazoil');

$year_filter=GETPOST('yearfilter','int');

llxHeader('',$langs->trans("ConsoGazReportConso"));

$object = new ConsogazoilVehTake ( $db );

$formconsogaz = new FormConsoGazoil($db);

$style=Array(
'bleu1'=>array(
'0'=>'bgcolor="#8DB4E3" align="center"',
 '1'=>'bgcolor="#DBE5F1" align="center"'
 ),
'bleu2'=>array(
 '0'=>'bgcolor="#538ED5" align="center"',
 '1'=>'bgcolor="#C5D9F1" align="center"'
 ),
'turquoise1'=>array(
 '0'=>'bgcolor="#76C0D4" align="center"',
 '1'=>'bgcolor="#DBEEF3" align="center"'
 ),
'turquoise2'=>array(
 '0'=>'bgcolor="#47AAC5" align="center"',
 '1'=>'bgcolor="#B6DDE8" align="center"'
 ),
'vert1'=>array(
 '0'=>'bgcolor="#B0CA7C" align="center"',
 '1'=>'bgcolor="#EAF1DD" align="center"'
 ),
'vert2'=>array(
 '0'=>'bgcolor="#8CAE48" align="center"',
 '1'=>'bgcolor="#C2D69A" align="center"'
 ),
'violet1'=>array(
 '0'=>'bgcolor="#BEB0D0" align="center"',
 '1'=>'bgcolor="#E5E0EC" align="center"'
 ),
'violet2'=>array(
 '0'=>'bgcolor="#9B84B6" align="center"',
 '1'=>'bgcolor="#C0B2D2" align="center"'
 ),
'orange'=>array(
 '0'=>'bgcolor="#FAC090" align="center"',
 '1'=>'bgcolor="#FDE9D9" align="center"'
 ),
'marron'=>array(
 '0'=>'bgcolor="#C5BE97" align="center"',
 '1'=>'bgcolor="#DDD9C3" align="center"'
 ),
);

//Build array t display
if (empty($year_filter))
	$year_filter = strftime("%Y",dol_now());


print_fiche_titre ( $langs->trans ( 'ConsoGazReportConso' ), '', dol_buildpath ( '/consogazoil/img/object_consogazoil.png', 1 ), 1 );

print '<form action="'.$_SERVER["PHP_SELF"].'" method="POST" name="filterdate">'."\n";
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'" / >';
print '<table><tr><td>';
$selectdate=$formconsogaz->select_year_report('yearfilter',$year_filter);
if ($selectdate!=-1) {
	print $selectdate;
} else {
	setEventMessage($formconsogaz->error,'errors');
}
print '</td><td><input type="submit" value="'.$langs->trans('ConsoGazFilterDate').'"/></td></tr></table>';
print '</form>';


print '<table class="border">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans('ConsoGazImmat').'</td>';

for ($month=1;$month<=12;$month++) {
	
	print '<td colspan="2" align="center">'.dol_print_date(dol_mktime(12,0,0,$month,1,$year_filter),"%B").'</td>';
	}
	
for ($month=1;$month<=4;$month++) {
	print '<td colspan="2" align="center">'.$langs->trans('ConsoGazTrimestre').' '.$month.'</td>';
	}
for ($month=1;$month<=2;$month++) {
	print '<td colspan="2" align="center">'.$langs->trans('ConsoGazSemestre').' '.$month.'</td>';
}

print '<td colspan="2"align="center">'.$langs->trans('Total').'</td>';
print '</tr>';


$result=$object->fetch_immat($year_filter);
if ($result < 0) setEventMessage ( $object->error, 'errors' );

foreach($object->lines_immat as $lineimat) {
	$var=!$var;
	print '<tr>';
	print '<td '.$bc[$var].' width="100">'.$lineimat.'</td>';
	
	$result=$object->fetch_report_conso($year_filter,$lineimat);
	if ($result < 0) setEventMessage ( $object->error, 'errors' );
	
	//janvier fevier mars
	For($indice=1;$indice<=6;$indice++){
		print '<td '.$style['bleu1'][$var].'>';
		print $object->lines_report[$indice];
		print '</td>';
	}
	
	//avril mai Juin
	For($indice=7;$indice<=12;$indice++){
		print '<td '.$style['turquoise1'][$var].'>';
		print $object->lines_report[$indice];
		print '</td>';
	}
	
	//Juillet Aout Septembre
	For($indice=13;$indice<=18;$indice++){
		print '<td '.$style['vert1'][$var].'>';
		print $object->lines_report[$indice];
		print '</td>';
}

	//Octobre Novembre decembre
	For($indice=19;$indice<=24;$indice++){
		print '<td '.$style['violet1'][$var].'>';
		print $object->lines_report[$indice];
		print '</td>';
	}

	//trimestre 1
	For($indice=25;$indice<=26;$indice++){
		print '<td '.$style['bleu2'][$var].'>';
		print $object->lines_report[$indice];
		print '</td>';
	}

	//trimestre 2
	For($indice=27;$indice<=28;$indice++){
		print '<td '.$style['turquoise2'][$var].'>';
		print $object->lines_report[$indice];
		print '</td>';
	}
	
	//trimestre 3
	For($indice=29;$indice<=30;$indice++){
		print '<td '.$style['vert2'][$var].'>';
		print $object->lines_report[$indice];
		print '</td>';
	}
	
	//trimestre 4
	For($indice=31;$indice<=32;$indice++){
		print '<td '.$style['violet2'][$var].'>';
		print $object->lines_report[$indice];
		print '</td>';
	}
	
	//Semestre 1 et 2
	For($indice=33;$indice<=36;$indice++){
		print '<td '.$style['orange'][$var].'>';
		print $object->lines_report[$indice];
		print '</td>';
	}
	
	//Total annuel
	For($indice=37;$indice<=38;$indice++){
		print '<td '.$style['marron'][$var].'>';
		print $object->lines_report[$indice];
		print '</td>';
	}
		
}

Print '<tr><td colspan="21"></br></td></tr>';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans('ConsoGazImmat').'</td>';

for ($month=1;$month<=12;$month++) {

	print '<td colspan="2" align="center">'.dol_print_date(dol_mktime(12,0,0,$month,1,$year_filter),"%B").'</td>';
	}
	
for ($month=1;$month<=4;$month++) {
	print '<td colspan="2" align="center">'.$langs->trans('ConsoGazTrimestre').' '.$month.'</td>';
	}
for ($month=1;$month<=2;$month++) {
	print '<td colspan="2" align="center">'.$langs->trans('ConsoGazSemestre').' '.$month.'</td>';
}

print '<td colspan="2"align="center">'.$langs->trans('Total').'</td>';
print '</tr>';


$result=$object->fetch_service($year_filter);
if ($result < 0) setEventMessage ( $object->error, 'errors' );

foreach($object->lines_service as $keyserv=>$lineserv) {
	$var=!$var;
	print '<tr>';
	print '<td '.$bc[$var].'>'.$lineserv.'</td>';

	$result=$object->fetch_report_conso_service($year_filter,$keyserv);
	if ($result < 0) setEventMessage ( $object->error, 'errors' );

	//janvier fevier mars
	For($indice=1;$indice<=3;$indice++){
		print '<td '.$style['bleu1'][$var].' colspan ="2">';
		print round($object->lines_report[$indice],2);
		print '</td>';
	}

	//avril mai Juin
	For($indice=4;$indice<=6;$indice++){
		print '<td '.$style['turquoise1'][$var].' colspan ="2">';
		print round($object->lines_report[$indice],2);
		print '</td>';
	}
	
	//Juillet Aout Septembre
	For($indice=7;$indice<=9;$indice++){
		print '<td '.$style['vert1'][$var].' colspan ="2">';
		print round($object->lines_report[$indice],2);
		print '</td>';
	}
	
	//Octobre Novembre decembre
	For($indice=10;$indice<=12;$indice++){
		print '<td '.$style['violet1'][$var].' colspan ="2">';
		print round($object->lines_report[$indice],2);
		print '</td>';
	}
	
	//trimestre 1
	$indice = 13;
	print '<td '.$style['bleu2'][$var].' colspan ="2">';
	print round($object->lines_report[$indice],2);
	print '</td>';
	
	
	//trimestre 2
	$indice = 14;
	print '<td '.$style['turquoise2'][$var].' colspan ="2">';
	print round($object->lines_report[$indice],2);
	print '</td>';
	
	//trimestre 3
	$indice = 15;
	print '<td '.$style['vert2'][$var].' colspan ="2">';
	print round($object->lines_report[$indice],2);
	print '</td>';
	
	//trimestre 4
	$indice = 16;
	print '<td '.$style['violet2'][$var].' colspan ="2">';
	print round($object->lines_report[$indice],2);
	print '</td>';
	
	//Semestre 1 et 2
	For($indice=17;$indice<=18;$indice++){
		print '<td '.$style['orange'][$var].' colspan ="2">';
		print round($object->lines_report[$indice],2);
		print '</td>';
	}
	
	//Total annuel
	$indice = 19;
		print '<td '.$style['marron'][$var].' colspan ="2">';
		print round($object->lines_report[$indice],2);
		print '</td>';

	print '</tr>';
}

//Total soc
$var=!$var;
print '<tr '.$bc[$var].'>';
print '<td>'.$langs->trans('Total').'</td>';
$result=$object->fetch_report_conso_service($year_filter,0);
if ($result < 0) setEventMessage ( $object->error, 'errors' );

//janvier fevier mars
	For($indice=1;$indice<=3;$indice++){
		print '<td '.$style['bleu1'][$var].' colspan ="2">';
		print round($object->lines_report[$indice],2);
		print '</td>';
}
	
	//avril mai Juin
	For($indice=4;$indice<=6;$indice++){
		print '<td '.$style['turquoise1'][$var].' colspan ="2">';
		print round($object->lines_report[$indice],2);
		print '</td>';
	}
	
	//Juillet Aout Septembre
	For($indice=7;$indice<=9;$indice++){
		print '<td '.$style['vert1'][$var].' colspan ="2">';
		print round($object->lines_report[$indice],2);
		print '</td>';
	}
	
	//Octobre Novembre decembre
	For($indice=10;$indice<=12;$indice++){
		print '<td '.$style['violet1'][$var].' colspan ="2">';
		print round($object->lines_report[$indice],2);
		print '</td>';
	}
	
	//trimestre 1
	$indice = 13;
	print '<td '.$style['bleu2'][$var].' colspan ="2">';
	print round($object->lines_report[$indice],2);
	print '</td>';
	
	
	//trimestre 2
	$indice = 14;
	print '<td '.$style['turquoise2'][$var].' colspan ="2">';
	print round($object->lines_report[$indice],2);
	print '</td>';
	
	//trimestre 3
	$indice = 15;
	print '<td '.$style['vert2'][$var].' colspan ="2">';
	print round($object->lines_report[$indice],2);
	print '</td>';
	
	//trimestre 4
	$indice = 16;
	print '<td '.$style['violet2'][$var].' colspan ="2">';
	print round($object->lines_report[$indice],2);
	print '</td>';
	
	//Semestre 1 et 2
	For($indice=17;$indice<=18;$indice++){
		print '<td '.$style['orange'][$var].' colspan ="2">';
		print round($object->lines_report[$indice],2);
		print '</td>';
	}
	
	//Total annuel
	$indice = 19;
		print '<td '.$style['marron'][$var].' colspan ="2">';
		print round($object->lines_report[$indice],2);
		print '</td>';

print '</table>';


llxFooter();
$db->close();