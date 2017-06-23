<?php
/* Consomation Gazoil 
 * Copyright (C) 2013	Florian HENRY 		<florian.henry@open-concept.pro>
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
 * \file consogazoil/vehicule/list.php
 * \ingroup consogazoil
 */
$res = @include '../../main.inc.php'; // For root directory
if (! $res)
	$res = @include '../../../main.inc.php'; // For "custom" directory

require_once DOL_DOCUMENT_ROOT . '/core/class/extrafields.class.php';
require_once '../class/consogazoilvehicule.class.php';
require_once '../class/consogazoilvehiculeservice.class.php';

// Load translation files required by the page
$langs->load("consogazoil@consogazoil");

// Security check
if (! $user->rights->consogazoil->lire)
	accessforbidden();

$optioncss = GETPOST('optioncss', 'alpha');

/*
 * VIEW
 *
 * Put here all code to build page
 */
$TJs = array(
	dol_buildpath('/consogazoil/lib/datatables/js/jquery.dataTables.min.js', 1)
	,dol_buildpath('/consogazoil/lib/datatables/media/js/jquery.dataTables.min.js', 1)
	,dol_buildpath('/consogazoil/lib/datatables/extensions/ColReorder/js/dataTables.colReorder.min.js', 1)
	,dol_buildpath('/consogazoil/lib/datatables/extensions/ColVis/js/dataTables.colVis.min.js', 1)
	,dol_buildpath('/consogazoil/lib/datatables/extensions/TableTools/js/dataTables.tableTools.min.js', 1)
);
$TCss = array(
	dol_buildpath('/consogazoil/lib/datatables/css/datatables.min.css', 1)
	,dol_buildpath('/consogazoil/lib/datatables/media/css/jquery.dataTables.min.css', 1)
	,dol_buildpath('/consogazoil/lib/datatables/extensions/ColReorder/css/dataTables.colReorder.min.css', 1)
	,dol_buildpath('/consogazoil/lib/datatables/extensions/ColVis/css/dataTables.colVis.min.css', 1)
	,dol_buildpath('/consogazoil/lib/datatables/extensions/TableTools/css/dataTables.tableTools.min.css', 1)
);
llxHeader('', $langs->trans('ConsoGazManageVeh') . '-' . $langs->trans('ConsoGazList'), '', '', 0, 0, $TJs, $TCss);

$form = new Form($db);

echo load_fiche_titre($langs->trans('ConsoGazManageVeh') . '-' . $langs->trans('ConsoGazList'), '', 'consogazoil@consogazoil');

include 'tpl/list.tpl.php';

// End of page
llxFooter();
$db->close();