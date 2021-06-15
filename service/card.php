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
 * \file consogazoil/service/card.php
 * \ingroup consogazoil
 * \brief card of consogazoil
 */

// Dolibarr environment
$res = @include ("../../main.inc.php"); // From htdocs directory
if (! $res) {
	$res = @include ("../../../main.inc.php"); // From "custom" directory
}
require_once '../class/consogazoilservice.class.php';
require_once '../lib/consogazoil.lib.php';
require_once DOL_DOCUMENT_ROOT . "/core/class/extrafields.class.php";

$langs->load("consogazoil@consogazoil");

$id = GETPOST('id', 'int');
$ref = GETPOST('ref', 'alpha');
$label = GETPOST('label', 'alpha');
$action = GETPOST('action', 'alpha');
$confirm = GETPOST('confirm', 'alpha');
$newToken = function_exists('newToken')?newToken():$_SESSION['newtoken'];

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

$object = new ConsogazoilService($db);
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label($object->table_element);

// Initialize technical object to manage hooks of thirdparties. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array (
		'consogazoilservicecard'
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
	if (empty($error)) {
		$object->ref = $ref;
		$object->label = $label;

		$extrafields->setOptionalsFromPost($extralabels, $object);

		$result = $object->create($user);
		if ($result < 0) {
			setEventMessage($object->errors, 'errors');
		} else {
			header('Location:' . dol_buildpath('/consogazoil/service/card.php', 1) . '?id=' . $result);
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
} else if ($action == "setlabel") {

	$object->label = $label;

	$result = $object->update($user);
	if ($result < 0)
		setEventMessage($object->errors, 'errors');
} else if ($action == 'confirm_delete' && $confirm == 'yes' && $user->rights->consogazoil->supprimer) {
	$result = $object->delete($user);
	if ($result < 0) {
		setEventMessage($object->errors, 'errors');
	} else {
		header('Location:' . dol_buildpath('/consogazoil/service/list.php', 1));
	}
} else if ($action == "update") {

	$extrafields->setOptionalsFromPost($extralabels, $object);

	$result = $object->update($user);
	if ($result < 0) {
		setEventMessage($object->errors, 'errors');
	} else {
		header('Location:' . dol_buildpath('/consogazoil/service/card.php', 1) . '?id=' . $object->id);
	}
}

/*
 * View
 */
$title = $langs->trans('Module103040Name') . '-' . $langs->trans('ConsoGazManageServ');
if (! empty($object->ref))
	$title .= '-' . $object->ref;

llxHeader('', $title);

$form = new Form($db);

// Add new
if ($action == 'create' && $user->rights->consogazoil->creer) {
	print_fiche_titre($title . '-' . $langs->trans('ConsoGazNew'), '', dol_buildpath('/consogazoil/img/object_consogazoil.png', 1), 1);

	print '<form name="add" action="' . $_SERVER["PHP_SELF"] . '" method="POST">';
	print '<input type="hidden" name="token" value="'.$newToken.'">';
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
	print $langs->trans('Label');
	print '</td>';
	print '<td>';
	print '<input type="text" value="' . $label . '" size="10" name="label"/>';
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
	$head = service_prepare_head($object);
	dol_fiche_head($head, 'card', $title, 0, 'bill');

	// Confirm form
	$formconfirm = '';
	if ($action == 'delete') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('ConsoGazDelete'), $langs->trans('ConsoGazConfirmDelete'), 'confirm_delete', '', 0, 1);
	}
	print $formconfirm;

	$linkback = '<a href="' . dol_buildpath('/consogazoil/service/list.php', 1) . '">' . $langs->trans("BackToList") . '</a>';

	if ($action == 'edit') {
		print '<form name="update" action="' . $_SERVER["PHP_SELF"] . '?id=' . $object->id . '" method="POST">';
		print '<input type="hidden" name="token" value="'.$newToken.'">';
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

	print '<tr><td>' . $form->editfieldkey("Label", 'label', $object->label, $object, $user->rights->consogazoil->modifier, 'string') . '</td><td>';
	print $form->editfieldval("Label", 'label', $object->label, $object, $user->rights->consogazoil->modifier, 'string');
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
}

// End of page
llxFooter();
$db->close();
