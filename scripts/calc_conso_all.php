<?php
/* Copyright (C) 2013 Florian Henry  <florian.henry@open-concept.pro>
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
 * \file /consogazoil/scripts/calc_conso_all.php
 * \brief Generate script
 */
if (! defined('NOTOKENRENEWAL'))
	define('NOTOKENRENEWAL', '1'); // Disables token renewal
if (! defined('NOREQUIREMENU'))
	define('NOREQUIREMENU', '1');
if (! defined('NOREQUIREHTML'))
	define('NOREQUIREHTML', '1');
if (! defined('NOREQUIREAJAX'))
	define('NOREQUIREAJAX', '1');
if (! defined('NOLOGIN'))
	define('NOLOGIN', '1');

$res = @include ("../../main.inc.php"); // For root directory
if (! $res)
	$res = @include ("../../../main.inc.php"); // For "custom" directory
if (! $res)
	die("Include of main fails");

dol_include_once('/consogazoil/class/consogazoilvehtake.class.php');
dol_include_once('/user/class/user.class.php');

$userlogin = GETPOST('login');
$key = GETPOST('key');

$user = new User($db);
$result = $user->fetch('', $userlogin);
if (empty($user->id)) {
	print 'user do not exists!';
	exit();
}
if ($key != $conf->global->GAZOIL_KEY_SCRIPT) {
	print 'key is not valid!';
	exit();
}

$sql = "SELECT t.rowid";
$sql .= " FROM " . MAIN_DB_PREFIX . "consogazoil_vehtake as t";
$resql = $db->query($sql);
if ($resql) {
	
	while ( $obj = $db->fetch_object($resql) ) {
		$gazoil = new ConsogazoilVehTake($db);
		$gazoil->fetch($obj->rowid);
		if (! empty($gazoil->id)) {
			
			$result = $gazoil->calc_conso($user);
			if ($result < 0) {
				print - 1;
				print ' $gazoil->error=' . $gazoil->error . '<BR>';
			} else {
				print 1;
			}
		}
	}
}

			