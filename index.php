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
 * \file consogazoil/index.php
 * \ingroup consogazoil
 * \brief This file about page
 */

// Dolibarr environment
$res = @include ("../main.inc.php"); // From htdocs directory
if (! $res) {
	$res = @include ("../../main.inc.php"); // From "custom" directory
}

// require_once "../class/myclass.class.php";
// Translations
$langs->load("consogazoil@consogazoil");

// Access control
if (! $user->rights->consogazoil->lire) {
	accessforbidden();
}

/*
 * Actions
 */

/*
 * View
 */
$page_name = "Module103040Name";
llxHeader('', $langs->trans($page_name));

print $langs->trans('Module103040Desc');

llxFooter();
$db->close();
