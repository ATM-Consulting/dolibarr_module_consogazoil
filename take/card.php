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
require_once '../lib/consogazoil.lib.php';

$langs->load ( "consogazoil@consogazoil" );

$id = GETPOST ( 'id', 'int' );
$action = GETPOST ( 'action', 'alpha' );

// Security check
if (empty ( $user->rights->consogazoil->lire )) accessforbidden ();

if ((($action == 'edit') || ($action == 'edit_confirm')) && (empty ( $user->rights->consogazoil->modifier ))) {
	accessforbidden ();
}


/*
 * Fetch
*/

$object = new ConsogazoilVehTake ( $db );

$error = 0;

// Load object
if (! empty ( $id )) {
	$ret = $object->fetch ( $id);
	if ($ret < 0) setEventMessage ( $object->error, 'errors' );
}

/*
 * Action
*/
if ($action == "edit_confirm") {
	
	$object->km_controle=GETPOST('km_controle','int');
	$result = $object->update ( $user );
	if ($result < 0) {
		setEventMessage ( $object->errors, 'errors' );
	} else {
		header ( 'Location:' . dol_buildpath ( '/consogazoil/take/list.php', 1 ));
	}

} 

/*
 * View
*/
$title = $langs->trans ( 'Module103040Name' ) . '-' . $langs->trans ( 'ConsoGazManageTake' );

llxHeader ( '', $title );

$form = new Form ( $db );

// Add new
if ($action == 'edit' && $user->rights->consogazoil->modifier) {
	
	
	print '<form name="edit" action="' . $_SERVER ["PHP_SELF"] . '" method="POST">';
	print '<input type="hidden" name="token" value="' . $_SESSION ['newtoken'] . '">';
	print '<input type="hidden" name="action" value="edit_confirm">';
	print '<input type="hidden" name="id" value="'.$id.'">';
	
	print '<table class="border" width="100%">';
	print '<tr>';
	print '<td class="fieldrequired"  width="20%">';
	print $langs->trans ( 'ConsoGazColKMCtrole' );
	print '</td>';
	print '<td>';
	print '<input type="text" value="' . $object->km_controle . '" size="10" name="km_controle"/>';
	print '</td>';
	print '</tr>';
	
	print '<table>';
	
	print '<center>';
	print '<input type="submit" class="button" value="' . $langs->trans ( "Edit" ) . '">';
	print '&nbsp;<input type="button" class="button" value="' . $langs->trans ( "Cancel" ) . '" onClick="javascript:history.go(-1)">';
	print '</center>';
	
	print '</form>';
} 

// End of page
llxFooter ();
$db->close ();
