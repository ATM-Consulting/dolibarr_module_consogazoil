<?php
/*
* Copyright (C) 2012-2013   Florian Henry   <florian.henry@open-concept.pro>
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
* along with this program; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
*/

/**
 *	\file       consogazoil/driver/info.php
 *	\ingroup    consogazoil
 *	\brief      info of consogazoil
 */

$res=@include("../../main.inc.php");				// For root directory
if (! $res) $res=@include("../../../main.inc.php");	// For "custom" directory
if (! $res) die("Include of main fails");

require_once '../class/consogazoildriver.class.php';
require_once '../lib/consogazoil.lib.php';
require_once(DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php');


// Security check
if (empty($user->rights->consogazoil->lire)) accessforbidden();


$langs->load("consogazoil@consogazoil");

$id=GETPOST('id','int');

$object= new ConsogazoilDriver($db);

$error=0;


// Load object
if (!empty($id))
{
	$ret=$object->info($id);
	if ($ret < 0) setEventMessage($object->error,'errors');
}

/*
 * View
*/
$title = $langs->trans('Module103040Name').'-'.$langs->trans('ConsoGazManageServ');

llxHeader('',$title);

$head = driver_prepare_head($object);
dol_fiche_head($head, 'info', $title, 0, dol_buildpath('/consogazoil/img/object_consogazoil.png',1),1);

print '<table width="100%"><tr><td>';
dol_print_object_info($object);
print '</td></tr></table>';
print '</div>';


llxFooter();
$db->close();