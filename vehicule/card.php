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
 * \file consogazoil/vehicule/card.php
 * \ingroup consogazoil
 * \brief card of consogazoil
 */

// Dolibarr environment
$res = @include ("../../main.inc.php"); // From htdocs directory
if (! $res) {
	$res = @include ("../../../main.inc.php"); // From "custom" directory
}
require_once '../class/consogazoilvehicule.class.php';
require_once '../class/consogazoilvehiculeservice.class.php';
require_once '../class/html.formconsogazoil.class.php';
require_once '../lib/consogazoil.lib.php';
require_once DOL_DOCUMENT_ROOT . "/core/class/extrafields.class.php";

$langs->load("consogazoil@consogazoil");

$id = GETPOST('id', 'int');
$ref = GETPOST('ref', 'alpha');
$immat_veh = GETPOST('immat_veh', 'alpha');
$avg_conso = GETPOST('avg_conso', 'alpha');
$action = GETPOST('action', 'alpha');
$confirm = GETPOST('confirm', 'alpha');
$active = GETPOST('activ', 'int');

// Security check
if (empty($user->rights->consogazoil->lire))
	accessforbidden();

if ((($action == 'create') || ($action == 'create_confirm')) && (empty($user->rights->consogazoil->creer))) {
	accessforbidden();
}
if ((($action == 'delete') || ($action == 'delete_confirm')) && (empty($user->rights->consogazoil->supprimer))) {
	accessforbidden();
}

/*
 * Fetch
 */

$object = new ConsogazoilVehicule($db);
$object_link = new ConsogazoilVehiculeService($db);
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label($object->table_element);

// Initialize technical object to manage hooks of thirdparties. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array (
		'consogazoilvehiculecard' 
));

$error = 0;

// Load object
if (! empty($id) || (! empty($ref))) {
	$ret = $object->fetch($id, $ref);
	if ($ret < 0)
		setEventMessage($object->error, 'errors');
}

/*
 * Action
 */
if ($action == "create_confirm") {
	if (empty($ref)) {
		setEventMessage($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("Ref")), 'errors');
		$action = 'create';
		$error ++;
	}
	if (! is_numeric($avg_conso)) {
		setEventMessage($langs->trans("ConsoGazMustBeNumeric", $langs->transnoentitiesnoconv("ConsoGazConsoAvg")), 'errors');
		$action = 'create';
		$error ++;
	}
	if (empty($error)) {
		$object->ref = $ref;
		$object->immat_veh = $immat_veh;
		$object->avg_conso = $avg_conso;
		
		$extrafields->setOptionalsFromPost($extralabels, $object);
		
		$result = $object->create($user);
		if ($result < 0) {
			setEventMessage($object->errors, 'errors');
		} else {
			header('Location:' . dol_buildpath('/consogazoil/vehicule/card.php', 1) . '?id=' . $result);
		}
	}
} else if ($action == "create_link_serv_confirm") {
	$dtstart = dol_mktime(0, 0, 0, GETPOST('dtstmonth', 'int'), GETPOST('dtstday', 'int'), GETPOST('dtstyear', 'int'));
	$dtend = dol_mktime(0, 0, 0, GETPOST('dtendmonth', 'int'), GETPOST('dtendday', 'int'), GETPOST('dtendyear', 'int'));
	
	if (empty($dtstart) || empty($dtend)) {
		setEventMessage($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("ConsoGazDtSt") . '/' . $langs->transnoentitiesnoconv("ConsoGazDtEnd"), 'errors'));
	} else {
		$object_link->fk_service = GETPOST('service', 'int');
		$object_link->fk_vehicule = $object->id;
		$object_link->date_start = $dtstart;
		$object_link->date_end = $dtend;
		$result = $object_link->create($user);
		if ($result < 0) {
			setEventMessage($object_link->errors, 'errors');
		} else {
			header('Location:' . dol_buildpath('/consogazoil/vehicule/card.php', 1) . '?id=' . $object->id);
		}
	}
} else if ($action == 'confirm_link_delete' && $confirm == 'yes' && $user->rights->consogazoil->supprimer) {
	$result = $object_link->fetch(GETPOST('id_link', 'int'));
	if ($result < 0) {
		setEventMessage($object->errors, 'errors');
	} else {
		$result = $object_link->delete($user);
		if ($result < 0) {
			setEventMessage($object->errors, 'errors');
		} else {
			header('Location:' . dol_buildpath('/consogazoil/vehicule/card.php', 1) . '?id=' . $object->id);
		}
	}
} else if ($action == "setref") {
	if (empty($ref)) {
		setEventMessage($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("Ref")), 'errors');
		$action = 'edit';
	} else {
		$object->ref = $ref;
		
		$result = $object->update($user);
		if ($result < 0)
			setEventMessage($object->errors, 'errors');
	}
} else if ($action == "setavg_conso") {
	if (! is_numeric($avg_conso)) {
		setEventMessage($langs->trans("ConsoGazMustBeNumeric", $langs->transnoentitiesnoconv("ConsoGazConsoAvg")), 'errors');
	} else {
		$object->avg_conso = $avg_conso;
		
		$result = $object->update($user);
		if ($result < 0)
			setEventMessage($object->errors, 'errors');
	}
} else if ($action == "setimmat_veh") {
	
	$object->immat_veh = $immat_veh;
	
	$result = $object->update($user);
	if ($result < 0)
		setEventMessage($object->errors, 'errors');
} else if ($action == "setactiv") {
	
	$object->activ = $active;
	
	$result = $object->update($user);
	if ($result < 0)
		setEventMessage($object->errors, 'errors');
} else if ($action == 'confirm_delete' && $confirm == 'yes' && $user->rights->consogazoil->supprimer) {
	$result = $object->delete($user);
	if ($result < 0) {
		setEventMessage($object->errors, 'errors');
	} else {
		header('Location:' . dol_buildpath('/consogazoil/vehicule/list.php', 1));
	}
} else if ($action == "update") {
	
	$extrafields->setOptionalsFromPost($extralabels, $object);
	
	$result = $object->update($user);
	if ($result < 0) {
		setEventMessage($object->errors, 'errors');
	} else {
		header('Location:' . dol_buildpath('/consogazoil/vehicule/card.php', 1) . '?id=' . $object->id);
	}
}

/*
 * View
 */
$title = $langs->trans('Module103040Name') . '-' . $langs->trans('ConsoGazManageVeh');
if (! empty($object->ref))
	$title .= '-' . $object->ref;

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
	print $langs->trans('Ref');
	print '</td>';
	print '<td>';
	print '<input type="text" value="' . $ref . '" size="10" name="ref"/>';
	print '</td>';
	print '</tr>';
	print '<tr>';
	print '<td>';
	print $langs->trans('ConsoGazImmat');
	print '</td>';
	print '<td>';
	print '<input type="text" value="' . $immat_veh . '" size="10" name="immat_veh"/>';
	print '</td>';
	print '</tr>';
	print '<tr>';
	print '<td>';
	print $langs->trans('ConsoGazConsoAvg');
	print '</td>';
	print '<td>';
	print '<input type="text" value="' . $avg_conso . '" size="10" name="avg_conso"/>';
	print '</td>';
	print '</tr>';
	print '<tr>';
	print '<td>';
	print $langs->trans('active');
	print '</td>';
	print '<td>';
	$arrval = array (
			'0' => $langs->trans("No"),
			'1' => $langs->trans("Yes") 
	);
	print $form->selectarray("activ", $arrval, $active);
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
} else {
	/*
	 * Show object in view mode
	 */
	$head = vehicule_prepare_head($object);
	dol_fiche_head($head, 'card', $title, 0, 'bill');
	
	// Confirm form
	$formconfirm = '';
	if ($action == 'delete') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('ConsoGazDelete'), $langs->trans('ConsoGazConfirmDelete'), 'confirm_delete', '', 0, 1);
	} else if ($action == "delete_link") {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id . '&id_link=' . GETPOST('id_link', 'int'), $langs->trans('ConsoGazDelete'), $langs->trans('ConsoGazConfirmDelete'), 'confirm_link_delete', '', 0, 1);
	}
	print $formconfirm;
	
	$linkback = '<a href="' . dol_buildpath('/consogazoil/vehicule/list.php', 1) . '">' . $langs->trans("BackToList") . '</a>';
	
	if ($action == 'edit') {
		print '<form name="update" action="' . $_SERVER["PHP_SELF"] . '?id=' . $object->id . '" method="POST">';
		print '<input type="hidden" name="token" value="' . $_SESSION['newtoken'] . '">';
		print '<input type="hidden" name="action" value="update">';
	}
	
	print '<table class="border" width="100%">';
	print '<tr>';
	print '<td width="20%">';
	print $langs->trans('Id');
	print '</td>';
	print '<td>';
	print $form->showrefnav($object, 'id', $linkback, 1, 'rowid', 'id', '');
	print '</td>';
	print '</tr>';
	
	print '<tr><td>' . $form->editfieldkey("Ref", 'ref', $object->ref, $object, $user->rights->consogazoil->modifier, 'string') . '</td><td>';
	print $form->editfieldval("Ref", 'ref', $object->ref, $object, $user->rights->consogazoil->modifier, 'string');
	
	print '</td></tr>';
	
	print '<tr><td>' . $form->editfieldkey("ConsoGazImmat", 'immat_veh', $object->immat_veh, $object, $user->rights->consogazoil->modifier, 'string') . '</td><td>';
	print $form->editfieldval("ConsoGazImmat", 'immat_veh', $object->immat_veh, $object, $user->rights->consogazoil->modifier, 'string');
	print '</td></tr>';
	
	print '<tr><td>' . $form->editfieldkey("ConsoGazConsoAvg", 'avg_conso', $object->avg_conso, $object, $user->rights->consogazoil->modifier, 'string') . '</td><td>';
	print $form->editfieldval("ConsoGazConsoAvg", 'avg_conso', $object->avg_conso, $object, $user->rights->consogazoil->modifier, 'string');
	print '</td></tr>';
	
	print '<tr><td>' . $form->editfieldkey("activ", 'activ', $object->activ, $object, $user->rights->consogazoil->modifier, 'select;1:' . $langs->trans("Yes") . ',0:' . $langs->trans("No")) . '</td><td>';
	print $form->editfieldval("activ", 'activ', $object->activ, $object, $user->rights->consogazoil->modifier, 'select;1:' . $langs->trans("Yes") . ',0:' . $langs->trans("No"));
	print '</td></tr>';
	
	// Other attributes
	$reshook = $hookmanager->executeHooks('formObjectOptions', $parameters, $object, $action); // Note that $action and $object may have been modified by hook
	
	if (empty($reshook) && ! empty($extrafields->attribute_label)) {
		if ($action == 'edit') {
			print $object->showOptionals($extrafields, 'edit');
		} else {
			print $object->showOptionals($extrafields);
		}
	}
	
	print '</table>';
	
	if ($action == 'edit') {
		
		print '<center>';
		print '<input type="submit" class="button" value="' . $langs->trans("Modify") . '">';
		print '&nbsp;<input type="button" class="button" value="' . $langs->trans("Cancel") . '" onClick="javascript:history.go(-1)">';
		print '</center>';
		
		print '</form>';
	}
	
	print "</div>\n";
	
	/*
	 * Barre d'actions
	 *
	 */
	if ($action != 'edit') {
		print '<div class="tabsAction">';
		
		if (! empty($extrafields->attribute_label)) {
			// Edit
			if ($user->rights->consogazoil->modifier) {
				print '<div class="inline-block divButAction"><a class="butAction" href="' . $_SERVER['PHP_SELF'] . '?id=' . $object->id . '&action=edit">' . $langs->trans("Edit") . "</a></div>\n";
			} else {
				print '<div class="inline-block divButAction"><font class="butActionRefused" href="#" title="' . dol_escape_htmltag($langs->trans("NotEnoughPermissions")) . '">' . $langs->trans("Edit") . "</font></div>";
			}
		}
		// Delete
		if ($user->rights->consogazoil->supprimer) {
			print '<div class="inline-block divButAction"><a class="butActionDelete" href="' . $_SERVER['PHP_SELF'] . '?id=' . $object->id . '&action=delete">' . $langs->trans("Delete") . "</a></div>\n";
		} else {
			print '<div class="inline-block divButAction"><font class="butActionRefused" href="#" title="' . dol_escape_htmltag($langs->trans("NotEnoughPermissions")) . '">' . $langs->trans("Delete") . "</font></div>";
		}
		
		print '</div>';
	}
	
	if ($action != 'edit') {
		print_fiche_titre($langs->trans('ConsoGazManageServ'), '', dol_buildpath('/consogazoil/img/object_consogazoil.png', 1), 1);
		
		print '<div class="fiche">';
		print '<form name="add" action="' . $_SERVER["PHP_SELF"] . '?id=' . $object->id . '" method="POST">';
		print '<input type="hidden" name="token" value="' . $_SESSION['newtoken'] . '">';
		print '<input type="hidden" name="action" value="create_link_serv_confirm">';
		
		print '<table class="border" width="100%">';
		print '<tr>';
		print '<td class="fieldrequired"  width="20%">';
		print $langs->trans('Label');
		print '</td>';
		print '<td>';
		print $formconsogaz->select_service(GETPOST('service'));
		print '</td>';
		print '</tr>';
		
		print '<tr>';
		print '<td class="fieldrequired"  width="20%">';
		print $langs->trans('ConsoGazDtSt');
		print '</td>';
		print '<td>';
		$form->select_date("", 'dtst', '', '', '', 'create_link_serv_confirm');
		print '</td>';
		print '</tr>';
		
		print '<tr>';
		print '<td class="fieldrequired"  width="20%">';
		print $langs->trans('ConsoGazDtEnd');
		print '</td>';
		print '<td>';
		$form->select_date("", 'dtend', '', '', '', 'create_link_serv_confirm');
		print '</td>';
		print '</tr>';
		print '<table>';
		
		print '<center>';
		print '<input type="submit" class="button" value="' . $langs->trans("ConsoGazAssociate") . '">';
		print '</center>';
		
		print '</form>';
		print '</div>';
		
		include 'tpl/list_service.tpl.php';
	}
}

// End of page
llxFooter();
$db->close();
