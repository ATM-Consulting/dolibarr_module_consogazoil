<?php
/* Consomation Gazoil 
 * Copyright (C) 2013	Florian HENRY 		<florian.henry@open-concept.pro>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
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
 * \file consogazoil/take/card.php
 * \ingroup consogazoil
 * \brief card of consogazoil
 */

// Dolibarr environment
$res = @include ("../../main.inc.php"); // From htdocs directory
if (! $res) {
	$res = @include ("../../../main.inc.php"); // From "custom" directory
}
require_once '../class/consogazoilvehtake.class.php';
require_once '../class/html.formconsogazoil.class.php';
require_once '../lib/consogazoil.lib.php';
require_once DOL_DOCUMENT_ROOT . "/core/class/extrafields.class.php";

$langs->load("consogazoil@consogazoil");

$id = GETPOST('id', 'int');
$id_veh = GETPOST('id_veh', 'int');
$id_sta = GETPOST('id_sta', 'int');
$id_driv = GETPOST('id_driv', 'int');
$vol = GETPOST('vol', 'alpha');
$prod = GETPOST('prod', 'alpha');
$km_declare = GETPOST('km_declare', 'int');
$km_controle = GETPOST('km_controle', 'int');
$dt_take = dol_mktime(GETPOST('dttakehour', 'int'), GETPOST('dttakemin', 'int'), 0, GETPOST('dttakemonth', 'int'), GETPOST('dttakeday', 'int'), GETPOST('dttakeyear', 'int'));
$action = GETPOST('action', 'alpha');
$confirm = GETPOST('confirm', 'alpha');

// Security check
if (empty($user->rights->consogazoil->lire))
	accessforbidden();

if ((($action == 'create') || ($action == 'create_confirm')) && (empty($user->rights->consogazoil->creer))) {
	accessforbidden();
}
if ((($action == 'delete') || ($action == 'delete_confirm')) && (empty($user->rights->consogazoil->supprimer))) {
	accessforbidden();
}
if ((($action == 'edit') || ($action == 'edit_confirm')) && (empty($user->rights->consogazoil->modifier))) {
	accessforbidden();
}

/*
 * Fetch
 */

$object = new ConsogazoilVehTake($db);
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label($object->table_element);

// Initialize technical object to manage hooks of thirdparties. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array (
		'consogazoilvehtakecard' 
));

$error = 0;

// Load object
if (! empty($id) || (! empty($ref))) {
	$ret = $object->fetch($id);
	if ($ret < 0)
		setEventMessage($object->error, 'errors');
}

/*
 * Action
 */
if ($action == "create_confirm") {
	if (empty($id_veh)) {
		setEventMessage($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("ConsoGazVeh")), 'errors');
		$action = 'create';
		$error ++;
	}
	if (empty($id_sta)) {
		setEventMessage($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("ConsoGazStation")), 'errors');
		$action = 'create';
		$error ++;
	}
	if (empty($id_driv)) {
		setEventMessage($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("ConsoGazDriver")), 'errors');
		$action = 'create';
		$error ++;
	}
	if (empty($vol)) {
		setEventMessage($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("ConsoGazColVol")), 'errors');
		$action = 'create';
		$error ++;
	}
	if (empty($prod)) {
		setEventMessage($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("ConsoGazColProd")), 'errors');
		$action = 'create';
		$error ++;
	} else {
		$prod_label_code = array ();
		$prod_label_code = explode('&', $prod);
		$prod_code = $prod_label_code[0];
		$prod_label = $prod_label_code[1];
	}
	if (empty($km_declare)) {
		setEventMessage($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("ConsoGazColKM")), 'errors');
		$action = 'create';
		$error ++;
	}
	if (empty($dt_take)) {
		setEventMessage($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("Date")), 'errors');
		$action = 'create';
		$error ++;
	}
	if (empty($error)) {
		$object->fk_driver = $id_driv;
		$object->fk_station = $id_sta;
		$object->fk_vehicule = $id_veh;
		$object->volume = $vol;
		$object->code_produit = $prod_code;
		$object->produit = $prod_label;
		$object->km_declare = $km_declare;
		$object->km_controle = $km_controle;
		$object->dt_hr_take = $dt_take;
		
		$extrafields->setOptionalsFromPost($extralabels, $object);
		
		$result = $object->create($user);
		if ($result < 0) {
			setEventMessage($object->errors, 'errors');
		} else {
			header('Location:' . dol_buildpath('/consogazoil/take/card.php', 1) . '?id=' . $result);
		}
	}
} else if ($action == "edit_confirm") {
	if (empty($id_veh)) {
		setEventMessage($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("ConsoGazVeh")), 'errors');
		$action = 'edit';
		$error ++;
	}
	if (empty($id_sta)) {
		setEventMessage($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("ConsoGazStation")), 'errors');
		$action = 'edit';
		$error ++;
	}
	if (empty($id_driv)) {
		setEventMessage($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("ConsoGazDriver")), 'errors');
		$action = 'edit';
		$error ++;
	}
	if (empty($vol)) {
		setEventMessage($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("ConsoGazColVol")), 'errors');
		$action = 'edit';
		$error ++;
	}
	if (empty($prod)) {
		setEventMessage($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("ConsoGazColProd")), 'errors');
		$action = 'edit';
		$error ++;
	} else {
		$prod_label_code = array ();
		$prod_label_code = explode('&', $prod);
		$prod_code = $prod_label_code[0];
		$prod_label = $prod_label_code[1];
	}
	if (empty($km_declare)) {
		setEventMessage($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("ConsoGazColKM")), 'errors');
		$action = 'edit';
		$error ++;
	}
	if (empty($dt_take)) {
		setEventMessage($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("Date")), 'errors');
		$action = 'edit';
		$error ++;
	}
	if (empty($error)) {
		$object->fk_driver = $id_driv;
		$object->fk_station = $id_sta;
		$object->fk_vehicule = $id_veh;
		$object->volume = $vol;
		$object->code_produit = $prod_code;
		$object->produit = $prod_label;
		$object->km_declare = $km_declare;
		$object->km_controle = $km_controle;
		$object->dt_hr_take = $dt_take;
		
		$extrafields->setOptionalsFromPost($extralabels, $object);
		
		$result = $object->update($user);
		if ($result < 0) {
			setEventMessage($object->errors, 'errors');
		} else {
			header('Location:' . dol_buildpath('/consogazoil/take/card.php', 1) . '?id=' . $object->id);
		}
	}
} else if ($action == 'confirm_delete' && $confirm == 'yes' && $user->rights->consogazoil->supprimer) {
	$result = $object->delete($user);
	if ($result < 0) {
		setEventMessage($object->errors, 'errors');
	} else {
		header('Location:' . dol_buildpath('/consogazoil/take/list.php', 1));
	}
}

/*
 * View
 */
$title = $langs->trans('Module103040Name') . '-' . $langs->trans('ConsoGazManageTake');

llxHeader('', $title);

$form = new Form($db);
$formconsogaz = new FormConsoGazoil($db);

// Add new
if ($action == 'create' && $user->rights->consogazoil->creer) {
	print_fiche_titre($title . '-' . $langs->trans('ConsoGazNew'), '', dol_buildpath('/consogazoil/img/object_consogazoil.png', 1), 1);
	
	print '<form name="add" action="' . $_SERVER["PHP_SELF"] . '" method="POST">';
	print '<input type="hidden" name="token" value="' . $_SESSION['newtoken'] . '">';
	print '<input type="hidden" name="action" value="create_confirm">';
	
	print '<table class="border" width="100%">';
	print '<tr>';
	print '<td class="fieldrequired"  width="20%">';
	print $langs->trans('ConsoGazVeh');
	print '</td>';
	print '<td>';
	print $formconsogaz->select_vehicule($id_veh, 'id_veh');
	print '</td>';
	print '</tr>';
	
	print '<td class="fieldrequired"  width="20%">';
	print $langs->trans('ConsoGazStation');
	print '</td>';
	print '<td>';
	print $formconsogaz->select_station($id_sta, 'id_sta');
	print '</td>';
	print '</tr>';
	
	print '<td class="fieldrequired"  width="20%">';
	print $langs->trans('ConsoGazDriver');
	print '</td>';
	print '<td>';
	print $formconsogaz->select_driver($id_driv, 'id_driv');
	print '</td>';
	print '</tr>';
	
	print '<td class="fieldrequired"  width="20%">';
	print $langs->trans('ConsoGazColVol');
	print '</td>';
	print '<td>';
	print '<input type="text" value="' . $vol . '" size="10" name="vol"/>';
	print '</td>';
	print '</tr>';
	
	print '<td class="fieldrequired"  width="20%">';
	print $langs->trans('ConsoGazProd');
	print '</td>';
	print '<td>';
	print $formconsogaz->select_prod($prod, 'prod');
	print '</td>';
	print '</tr>';
	
	print '<td class="fieldrequired"  width="20%">';
	print $langs->trans('ConsoGazColKM');
	print '</td>';
	print '<td>';
	print '<input type="text" value="' . $km_declare . '" size="10" name="km_declare"/>';
	print '</td>';
	print '</tr>';
	
	print '<td class="fieldrequired"  width="20%">';
	print $langs->trans('ConsoGazColKMCtrole');
	print '</td>';
	print '<td>';
	print '<input type="text" value="' . $km_controle . '" size="10" name="km_controle"/>';
	print '</td>';
	print '</tr>';
	
	print '<td class="fieldrequired"  width="20%">';
	print $langs->trans('Date');
	print '</td>';
	print '<td>';
	$form->select_date($dt_take, 'dttake', 1, 1, '', 'add');
	print '</td>';
	print '</tr>';
	
	// Other attributes
	$reshook = $hookmanager->executeHooks('formObjectOptions', $parameters, $object, $action); // Note that $action and $object may have been modified by hook
	
	if (empty($reshook) && ! empty($extrafields->attribute_label)) {
		print $object->showOptionals($extrafields, 'edit');
	}
	
	print '<table>';
	
	print '<center>';
	print '<input type="submit" class="button" value="' . $langs->trans("ConsoGazNew") . '">';
	print '&nbsp;<input type="button" class="button" value="' . $langs->trans("Cancel") . '" onClick="javascript:history.go(-1)">';
	print '</center>';
	
	print '</form>';
} else if ($action == 'edit') {
	// Show in edit mode
	print_fiche_titre($title . '-' . $langs->trans('ConsoGazEdit'), '', dol_buildpath('/consogazoil/img/object_consogazoil.png', 1), 1);
	
	print '<form name="add" action="' . $_SERVER["PHP_SELF"] . '?id=' . $object->id . '" method="POST">';
	print '<input type="hidden" name="token" value="' . $_SESSION['newtoken'] . '">';
	print '<input type="hidden" name="action" value="edit_confirm">';
	
	If ($user->rights->consogazoil->import) {
		print '<table class="border" width="100%">';
		print '<tr>';
		print '<td class="fieldrequired"  width="20%">';
		print $langs->trans('ConsoGazVeh');
		print '</td>';
		print '<td>';
		print $formconsogaz->select_vehicule($object->fk_vehicule, 'id_veh');
		print '</td>';
		print '</tr>';
	} else {
		print '<table class="border" width="100%">';
		print '<tr>';
		print '<td class="fieldrequired"  width="20%">';
		print $langs->trans('ConsoGazVeh');
		print '</td>';
		print '<td>';
		print $object->veh_ref . '-' . $object->veh_immat;
		print '<input type="hidden" name="id_veh" value="' . $object->fk_vehicule . '">';
		print '</td>';
		print '</tr>';
	}
	
	If ($user->rights->consogazoil->import) {
		print '<td class="fieldrequired"  width="20%">';
		print $langs->trans('ConsoGazStation');
		print '</td>';
		print '<td>';
		print $formconsogaz->select_station($object->fk_station, 'id_sta');
		print '</td>';
		print '</tr>';
	} else {
		print '<td class="fieldrequired"  width="20%">';
		print $langs->trans('ConsoGazStation');
		print '</td>';
		print '<td>';
		print $object->station_ref . '-' . $object->station_name;
		print '<input type="hidden" name="id_sta" value="' . $object->fk_station . '">';
		print '</td>';
		print '</tr>';
	}
	
	If ($user->rights->consogazoil->import) {
		print '<td class="fieldrequired"  width="20%">';
		print $langs->trans('ConsoGazDriver');
		print '</td>';
		print '<td>';
		print $formconsogaz->select_driver($object->fk_driver, 'id_driv');
		print '</td>';
		print '</tr>';
	} else {
		print '<td  width="20%">';
		print $langs->trans('ConsoGazDriver');
		print '</td>';
		print '<td>';
		print $object->driv_ref . '-' . $object->driv_name;
		print '<input type="hidden" name="id_driv" value="' . $object->fk_driver . '">';
		print '</td>';
		print '</tr>';
	}
	If ($user->rights->consogazoil->import) {
		print '<td class="fieldrequired"  width="20%">';
		print $langs->trans('ConsoGazColVol');
		print '</td>';
		print '<td>';
		print '<input type="text" value="' . $object->volume . '" size="10" name="vol"/>';
		print '</td>';
		print '</tr>';
	} else {
		print '<td   width="20%">';
		print $langs->trans('ConsoGazColVol');
		print '</td>';
		print '<td>';
		print $object->volume;
		print '<input type="hidden" name="vol" value="' . $object->volume . '">';
		print '</td>';
		print '</tr>';
	}
	
	If ($user->rights->consogazoil->import) {
		print '<td class="fieldrequired"  width="20%">';
		print $langs->trans('ConsoGazColProd');
		print '</td>';
		print '<td>';
		print $formconsogaz->select_prod($object->produit, 'prod');
		print '</td>';
		print '</tr>';
	} else {
		print '<td   width="20%">';
		print $langs->trans('ConsoGazColProd');
		print '</td>';
		print '<td>';
		print $object->produit;
		print '<input type="hidden" name="prod" value="' . $object->produit . '">';
		print '</td>';
		print '</tr>';
	}
	
	print '<td class="fieldrequired"  width="20%">';
	print $langs->trans('ConsoGazColKM');
	print '</td>';
	print '<td>';
	print '<input type="text" value="' . $object->km_declare . '" size="10" name="km_declare"/>';
	print '</td>';
	print '</tr>';
	
	print '<td class="fieldrequired"  width="20%">';
	print $langs->trans('ConsoGazColKMCtrole');
	print '</td>';
	print '<td>';
	print '<input type="text" value="' . $object->km_controle . '" size="10" name="km_controle"/>';
	print '</td>';
	print '</tr>';
	
	print '<td class="fieldrequired"  width="20%">';
	print $langs->trans('Date');
	print '</td>';
	print '<td>';
	$form->select_date($object->dt_hr_take, 'dttake', 1, 1, '', 'add');
	print '</td>';
	print '</tr>';
	
	// Other attributes
	$reshook = $hookmanager->executeHooks('formObjectOptions', $parameters, $object, $action); // Note that $action and $object may have been modified by hook
	
	if (empty($reshook) && ! empty($extrafields->attribute_label)) {
		print $object->showOptionals($extrafields, 'edit');
	}
	
	print '<table>';
	
	print '<center>';
	print '<input type="submit" class="button" value="' . $langs->trans("Save") . '">';
	print '&nbsp;<input type="button" class="button" value="' . $langs->trans("Cancel") . '" onClick="javascript:history.go(-1)">';
	print '</center>';
} else {
	/*
	 * Show object in view mode
	 */
	$head = take_prepare_head($object);
	dol_fiche_head($head, 'card', $title, 0, 'bill');
	
	// Confirm form
	$formconfirm = '';
	if ($action == 'delete') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('ConsoGazDelete'), $langs->trans('ConsoGazConfirmDelete'), 'confirm_delete', '', 0, 1);
	}
	print $formconfirm;
	
	$linkback = '<a href="' . dol_buildpath('/consogazoil/take/list.php', 1) . '">' . $langs->trans("BackToList") . '</a>';
	
	print '<table class="border" width="100%">';
	print '<tr>';
	print '<td width="20%">';
	print $langs->trans('Id');
	print '</td>';
	print '<td>';
	print $form->showrefnav($object, 'id', $linkback, 1, 'rowid', 'id', '');
	print '</td>';
	print '</tr>';
	
	print '<tr>';
	print '<td  width="20%">';
	print $langs->trans('ConsoGazVeh');
	print '</td>';
	print '<td>';
	print $object->veh_ref . '-' . $object->veh_immat;
	print '</td>';
	print '</tr>';
	
	print '<td  width="20%">';
	print $langs->trans('ConsoGazStation');
	print '</td>';
	print '<td>';
	print $object->station_ref . '-' . $object->station_name;
	print '</td>';
	print '</tr>';
	
	print '<td  width="20%">';
	print $langs->trans('ConsoGazDriver');
	print '</td>';
	print '<td>';
	print $object->driv_ref . '-' . $object->driv_name;
	print '</td>';
	print '</tr>';
	
	print '<td   width="20%">';
	print $langs->trans('ConsoGazColVol');
	print '</td>';
	print '<td>';
	print $object->volume;
	print '</td>';
	print '</tr>';
	
	print '<td   width="20%">';
	print $langs->trans('ConsoGazColProd');
	print '</td>';
	print '<td>';
	print $object->code_produit . '-' . $object->produit;
	print '</td>';
	print '</tr>';
	
	print '<td  width="20%">';
	print $langs->trans('ConsoGazColKM');
	print '</td>';
	print '<td>';
	print $object->km_declare;
	print '</td>';
	print '</tr>';
	
	print '<td  width="20%">';
	print $langs->trans('ConsoGazConsoAvg');
	print '</td>';
	print '<td>';
	print $object->conso_calc;
	print '</td>';
	print '</tr>';
	
	print '<td  width="20%">';
	print $langs->trans('ConsoGazColKMCtrole');
	print '</td>';
	print '<td>';
	print $object->km_controle;
	print '</td>';
	print '</tr>';
	
	print '<td  width="20%">';
	print $langs->trans('Date');
	print '</td>';
	print '<td>';
	print dol_print_date($object->dt_hr_take, 'dayhourtext');
	print '</td>';
	print '</tr>';
	
	// Other attributes
	$reshook = $hookmanager->executeHooks('formObjectOptions', $parameters, $object, $action); // Note that $action and $object may have been modified by hook
	
	if (empty($reshook) && ! empty($extrafields->attribute_label)) {
		print $object->showOptionals($extrafields);
	}
	
	print '</table>';
	print "</div>\n";
	
	/*
	 * Barre d'actions
	 *
	 */
	print '<div class="tabsAction">';
	
	// Edit
	if ($user->rights->consogazoil->modifier) {
		print '<a class="butAction" href="' . $_SERVER['PHP_SELF'] . '?id=' . $object->id . '&action=edit">' . $langs->trans("Edit") . "</a>\n";
	} else {
		print '<font class="butActionRefused" href="#" title="' . dol_escape_htmltag($langs->trans("NotEnoughPermissions")) . '">' . $langs->trans("Edit") . "</font>";
	}
	
	// Delete
	if ($user->rights->consogazoil->supprimer) {
		print '<a class="butActionDelete" href="' . $_SERVER['PHP_SELF'] . '?id=' . $object->id . '&action=delete">' . $langs->trans("Delete") . "</a>\n";
	} else {
		print '<font class="butActionRefused" href="#" title="' . dol_escape_htmltag($langs->trans("NotEnoughPermissions")) . '">' . $langs->trans("Delete") . "</font>";
	}
	print '</div>';
}

// End of page
llxFooter();
$db->close();
