<?php
/* Consomation Gazoil 
 * Copyright (C) 2013 florian Henry <florian.henry@open-concept.pro>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * 	\file		admin/consogazoil.php
 * 	\ingroup	consogazoil
 * 	\brief		This file is setup page
 * 
 */
// Dolibarr environment
$res = @include("../../main.inc.php"); // From htdocs directory
if (! $res) {
    $res = @include("../../../main.inc.php"); // From "custom" directory
}


// Libraries
require_once DOL_DOCUMENT_ROOT . "/core/lib/admin.lib.php";
require_once '../lib/consogazoil.lib.php';
//require_once "../class/myclass.class.php";
// Translations
$langs->load("consogazoil@consogazoil");

// Access control
if (! $user->admin) {
    accessforbidden();
}

// Parameters
$action = GETPOST('action', 'alpha');

/*
 * Actions
 */

if ($action == 'setvar')
{
	require_once(DOL_DOCUMENT_ROOT."/core/lib/files.lib.php");
	
	$val=GETPOST('GAZOIL_THRESOLD_CONSO','alpha');
	$res = dolibarr_set_const($db, 'GAZOIL_THRESOLD_CONSO', $val,'chaine',0,'',$conf->entity);
	if (! $res > 0) $error++;
	
	$val=GETPOST('GAZOIL_THRESOLD_KM','alpha');
	$res = dolibarr_set_const($db, 'GAZOIL_THRESOLD_KM', $val,'chaine',0,'',$conf->entity);
	if (! $res > 0) $error++;
	
	$val=GETPOST('GAZOIL_EMAIL_EXPLOIT','alpha');
	$res = dolibarr_set_const($db, 'GAZOIL_EMAIL_EXPLOIT', $val,'chaine',0,'',$conf->entity);
	if (! $res > 0) $error++;
	
	$val=GETPOST('GAZOIL_ID_VEH_NO_IMPORT','alpha');
	$res = dolibarr_set_const($db, 'GAZOIL_ID_VEH_NO_IMPORT', $val,'chaine',0,'',$conf->entity);
	if (! $res > 0) $error++;
	
	$val=GETPOST('GAZOIL_PROD_CODE_REPORT','alpha');
	$res = dolibarr_set_const($db, 'GAZOIL_PROD_CODE_REPORT', $val,'chaine',0,'',$conf->entity);
	if (! $res > 0) $error++;
	
	
	if (! $error)
	{
		setEventMessage($langs->trans("SetupSaved"),'mesgs');
	}
	else
	{
		setEventMessage($langs->trans("Error"),'errors');
	}
}

/*
 * View
 */
$page_name = "ConsoGazoilSetup";
llxHeader('', $langs->trans($page_name));

$form=new Form($db);

// Subheader
$linkback = '<a href="' . DOL_URL_ROOT . '/admin/modules.php">'
    . $langs->trans("BackToModuleList") . '</a>';
print_fiche_titre($langs->trans($page_name), $linkback);

// Configuration header
$head = consogazoilAdminPrepareHead();
dol_fiche_head(
    $head,
    'settings',
    $langs->trans("Module103040Name"),
    0,
    "consogazoil@consogazoil"
);

// Setup page goes here
echo $langs->trans("ConsoGazoilSetupPage");


print '<table class="noborder" width="100%">';

print '<form method="post" action="'.$_SERVER['PHP_SELF'].'" >';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="setvar">';

print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Name").'</td>';
print '<td width="400px">'.$langs->trans("Valeur").'</td>';
print '<td></td>';
print "</tr>\n";

//GAZOIL_THRESOLD_CONSO
print '<tr class="pair"><td>'.$langs->trans("ConsoGazThresoldConso").'</td>';
print '<td align="left">';
print '<input type="text"   name="GAZOIL_THRESOLD_CONSO" value="'.$conf->global->GAZOIL_THRESOLD_CONSO.'" size="5" >%</td>';
print '<td align="center">';
print '</td>';
print '</tr>';


//GAZOIL_THRESOLD_KM
print '<tr class="pair"><td>'.$langs->trans("ConsoGazThresoldKM").'</td>';
print '<td align="left">';
print '<input type="text"   name="GAZOIL_THRESOLD_KM" value="'.$conf->global->GAZOIL_THRESOLD_KM.'" size="5" >Km</td>';
print '<td align="center">';
print '</td>';
print '</tr>';


//GAZOIL_EMAIL_EXPLOIT
print '<tr class="pair"><td>'.$langs->trans("ConsoGazMailExploit").'</td>';
print '<td align="left">';
print '<textarea wrap="soft" cols="70" rows="2"  name="GAZOIL_EMAIL_EXPLOIT">'.$conf->global->GAZOIL_EMAIL_EXPLOIT.'</textarea></td>';
print '<td align="center">';
print $form->textwithpicto('',$langs->trans("ConsoGazMailExploitHelp"),1,'help');
print '</td>';
print '</tr>';


//GAZOIL_ID_VEH_NO_IMPORT
print '<tr class="pair"><td>'.$langs->trans("ConsoGazVehNoImport").'</td>';
print '<td align="left">';
print '<textarea wrap="soft" cols="70" rows="2"  name="GAZOIL_ID_VEH_NO_IMPORT">'.$conf->global->GAZOIL_ID_VEH_NO_IMPORT.'</textarea></td>';
print '<td align="center">';
print $form->textwithpicto('',$langs->trans("ConsoGazVehNoImportHelp"),1,'help');
print '</td>';
print '</tr>';

//GAZOIL_ID_VEH_NO_IMPORT
print '<tr class="pair"><td>'.$langs->trans("ConsoProductCodeUseIntoReport").'</td>';
print '<td align="left">';
print '<input type="text"  name="GAZOIL_PROD_CODE_REPORT"  size="10" value="'.$conf->global->GAZOIL_PROD_CODE_REPORT.'"></td>';
print '<td align="center">';
print $form->textwithpicto('',$langs->trans("ConsoProductCodeUseIntoReportHelp"),1,'help');
print '</td>';
print '</tr>';


print '<tr class="impair"><td colspan="3" align="right"><input type="submit" class="button" value="'.$langs->trans("Save").'"></td>';
print '</tr>';

print '</table><br>';
print '</form>';


llxFooter();

$db->close();
