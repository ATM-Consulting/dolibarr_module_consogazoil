<?php
/* Copyright (C) 2013 Florian Henry  <florian.henry@open-concept.pro>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
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
 *       \file       consogazoil/import/ajax/upddate_temp_table.php
 *       \brief      File to set action on temp tables
 */

//if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL','1'); // Disables token renewal
//if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');
//if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');
if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
//if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
//if (! defined('NOREQUIRETRAN'))  define('NOREQUIRETRAN','1');

$res=@include("../../main.inc.php");					// For root directory
if (! $res) $res=@include("../../../main.inc.php");	// For "custom" directory
if (! $res) die("Include of main fails");

$typesource=GETPOST('typesource','alpha');
$id=GETPOST('rowid','int');
$action=GETPOST('action','alpha');

// Ajout directives pour resoudre bug IE
//header('Cache-Control: Public, must-revalidate');
//header('Pragma: public');

top_httphead();



//print '<!-- Ajax page called with url '.$_SERVER["PHP_SELF"].'?'.$_SERVER["QUERY_STRING"].' -->'."\n";

if (! empty($action) && ! empty($id) && ! empty($typesource))
{
	$error=0;
	
	$sql = "UPDATE ".MAIN_DB_PREFIX."consogazoil_tmp SET";
	
	if ($typesource=='veh') {
		$sql.= " veh_conflit_action='".$action."'";
	}
	if ($typesource=='station') {
		$sql.= " station_conflit_action='".$action."'";
	}
	
	$sql.= " WHERE rowid=".$id;
	
	$db->begin();
	
	dol_syslog("ajax:update_temp_table:update sql=".$sql, LOG_DEBUG);
	$resql = $db->query($sql);
	if (! $resql) { $error++; dol_syslog("ajax:update_temp_table:ERROR".$db->lasterror(), LOG_ERR); }
	
	// Commit or rollback
	if ($error)
	{
		$db->rollback();
	}
	else
	{
		$db->commit();
	}
}