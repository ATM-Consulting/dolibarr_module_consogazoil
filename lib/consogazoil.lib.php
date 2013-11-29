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
 *	\file		lib/consogazoil.lib.php
 *	\ingroup	consogazoil
 *	\brief		This file is an example module library
 *				Put some comments here
 */

function consogazoilAdminPrepareHead()
{
    global $langs, $conf;

    $langs->load("consogazoil@consogazoil");

    $h = 0;
    $head = array();

    $head[$h][0] = dol_buildpath("/consogazoil/admin/admin_consogazoil.php", 1);
    $head[$h][1] = $langs->trans("Settings");
    $head[$h][2] = 'settings';
    $h++;
    $head[$h][0] = dol_buildpath("/consogazoil/admin/about.php", 1);
    $head[$h][1] = $langs->trans("About");
    $head[$h][2] = 'about';
    $h++;

    // Show more tabs from modules
    // Entries must be declared in modules descriptor with line
    //$this->tabs = array(
    //	'entity:+tabname:Title:@consogazoil:/consogazoil/mypage.php?id=__ID__'
    //); // to add new tab
    //$this->tabs = array(
    //	'entity:-tabname:Title:@consogazoil:/consogazoil/mypage.php?id=__ID__'
    //); // to remove a tab
    complete_head_from_modules($conf, $langs, $object, $head, $h, 'consogazoiladmin');

    return $head;
}

function vehicule_prepare_head($object) {
	
	global $langs, $conf;
	
	$langs->load("consogazoil@consogazoil");
	
	$h = 0;
	$head = array();
	
	$head[$h][0] = dol_buildpath("/consogazoil/vehicule/card.php", 1).'?id='.$object->id;
	$head[$h][1] = $langs->trans("ConsoGazManageVeh");
	$head[$h][2] = 'card';
	$h++;
	
	$head[$h][0] = dol_buildpath('/consogazoil/vehicule/info.php',1).'?id='.$object->id;
	$head[$h][1] = $langs->trans("Info");
	$head[$h][2] = 'info';
	$hselected = $h;
	$h++;
	
	complete_head_from_modules($conf, $langs, $object, $head, $h, 'consogazoilvehicule');
	
	return $head;
}

function service_prepare_head($object) {

	global $langs, $conf;

	$langs->load("consogazoil@consogazoil");

	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/consogazoil/service/card.php", 1).'?id='.$object->id;
	$head[$h][1] = $langs->trans("ConsoGazManageServ");
	$head[$h][2] = 'card';
	$h++;

	$head[$h][0] = dol_buildpath('/consogazoil/service/info.php',1).'?id='.$object->id;
	$head[$h][1] = $langs->trans("Info");
	$head[$h][2] = 'info';
	$hselected = $h;
	$h++;

	complete_head_from_modules($conf, $langs, $object, $head, $h, 'consogazoilservice');

	return $head;
}

function station_prepare_head($object) {

	global $langs, $conf;

	$langs->load("consogazoil@consogazoil");

	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/consogazoil/station/card.php", 1).'?id='.$object->id;
	$head[$h][1] = $langs->trans("ConsoGazManageSta");
	$head[$h][2] = 'card';
	$h++;

	$head[$h][0] = dol_buildpath('/consogazoil/station/info.php',1).'?id='.$object->id;
	$head[$h][1] = $langs->trans("Info");
	$head[$h][2] = 'info';
	$hselected = $h;
	$h++;

	complete_head_from_modules($conf, $langs, $object, $head, $h, 'consogazoilstation');

	return $head;
}

function driver_prepare_head($object) {

	global $langs, $conf;

	$langs->load("consogazoil@consogazoil");

	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/consogazoil/driver/card.php", 1).'?id='.$object->id;
	$head[$h][1] = $langs->trans("ConsoGazManageDriv");
	$head[$h][2] = 'card';
	$h++;

	$head[$h][0] = dol_buildpath('/consogazoil/driver/info.php',1).'?id='.$object->id;
	$head[$h][1] = $langs->trans("Info");
	$head[$h][2] = 'info';
	$hselected = $h;
	$h++;

	complete_head_from_modules($conf, $langs, $object, $head, $h, 'consogazoildriver');

	return $head;
}

function take_prepare_head($object) {

	global $langs, $conf;

	$langs->load("consogazoil@consogazoil");

	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/consogazoil/take/card.php", 1).'?id='.$object->id;
	$head[$h][1] = $langs->trans("ConsoGazManageTake");
	$head[$h][2] = 'card';
	$h++;

	$head[$h][0] = dol_buildpath('/consogazoil/take/info.php',1).'?id='.$object->id;
	$head[$h][1] = $langs->trans("Info");
	$head[$h][2] = 'info';
	$hselected = $h;
	$h++;

	complete_head_from_modules($conf, $langs, $object, $head, $h, 'consogazoiltake');

	return $head;
}

