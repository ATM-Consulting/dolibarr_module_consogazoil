<?php
/* Copyright (C) 2012-2013	Regis Houssin	<regis.houssin@capnetworks.com>
 * Copyright (C) 2013	Florian HENRY 		<florian.henry@open-concept.pro>
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
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 */

/**
 * \file /consogazoil/service/ajax/list.php
 * \brief File to return datables output
 */
if (! defined('NOTOKENRENEWAL'))
	define('NOTOKENRENEWAL', '1'); // Disables token renewal
if (! defined('NOREQUIREMENU'))
	define('NOREQUIREMENU', '1');
	// if (! defined('NOREQUIREHTML')) define('NOREQUIREHTML','1');
if (! defined('NOREQUIREAJAX'))
	define('NOREQUIREAJAX', '1');
	// if (! defined('NOREQUIRESOC')) define('NOREQUIRESOC','1');
	// if (! defined('NOREQUIRETRAN')) define('NOREQUIRETRAN','1');

$res = @include ("../../../main.inc.php"); // For root directory
if (! $res)
	$res = @include ("../../../../main.inc.php"); // For "custom" directory

dol_include_once('/consogazoil/class/consogazoilservice.class.php');
require_once DOL_DOCUMENT_ROOT . '/core/class/extrafields.class.php';

$langs->load("consogazoil@consogazoil");

top_httphead();

// print '<!-- Ajax page called with url '.$_SERVER["PHP_SELF"].'?'.$_SERVER["QUERY_STRING"].' -->'."\n";

// print_r($_GET);

$object = new ConsogazoilService($db);

/* Array of database columns which should be read and sent back to DataTables. Use a space where
 * you want to insert a non-database field (for example a counter or static image)
 */
$aColumns = array (
		't.rowid',
		't.ref',
		't.label' 
);

$extrafields = new ExtraFields($db);
$extralabels = $extrafields->fetch_name_optionals_label($object->table_element);

if (count($extrafields->attribute_label) > 0)
{
	foreach($extrafields->attribute_label as $key=>$label) {
		$aColumns[]='extra.'.$key;
	}
}

$numColumns = count($aColumns);

/* Indexed column (used for fast and accurate table cardinality) */
$sIndexColumn = "rowid";

/* DB table to use */
$sTable = MAIN_DB_PREFIX . "consogazoil_service";

/*
 * Paging
 */
$sLimit = "";
if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
	
	$sLimit = $db->plimit($db->escape($_GET['iDisplayLength']), $db->escape($_GET['iDisplayStart']));
}

/*
 * Ordering
 */
if (isset($_GET['iSortCol_0'])) {
	$sOrder = "ORDER BY  ";
	for($i = 0; $i < intval($_GET['iSortingCols']); $i ++) {
		if ($_GET['bSortable_' . intval($_GET['iSortCol_' . $i])] == "true") {
			$sOrder .= $aColumns[intval($_GET['iSortCol_' . $i])] . " " . $db->escape($_GET['sSortDir_' . $i]) . ", ";
		}
	}
	
	$sOrder = substr_replace($sOrder, "", - 2);
	if ($sOrder == "ORDER BY") {
		$sOrder = "";
	}
}

/*
 * Filtering
 * NOTE this does not match the built-in DataTables filtering which does it
 * word by word on any field. It's possible to do here, but concerned about efficiency
 * on very large tables, and MySQL's regex functionality is very limited
 */
$sWhere = "";
if ($_GET['sSearch'] != "") {
	$sWhere = "WHERE (";
	$numColumns = count($aColumns);
	for($i = 0; $i < $numColumns; $i ++) {
		
		if (($aColumns[$i] == "ref") || ($aColumns[$i] == "label")) {
			$sWhere .= $aColumns[$i] . " LIKE '%" . $db->escape($_GET['sSearch']) . "%' OR ";
		}
	}
	$sWhere = substr_replace($sWhere, "", - 3);
	$sWhere .= ')';
}

/*
 * SQL queries
 * Get data to display
 */
$sQuery = "
		SELECT " . str_replace(" , ", " ", implode(", ", $aColumns)) . "
			FROM   $sTable as t";
$sQuery .= " LEFT OUTER JOIN ".$sTable."_extrafields as extra ON extra.fk_object=t.rowid";
$sQuery .= " $sWhere
			$sOrder
			$sLimit
			";
dol_syslog("service-list:: sQuery=" . $sQuery, LOG_DEBUG);
$rResult = $db->query($sQuery);
// echo $sQuery;

/* Data set length after filtering */
$sQuery = "
		SELECT count(t.rowid) FROM   $sTable as t";
$sQuery .= " LEFT OUTER JOIN ".$sTable."_extrafields as extra ON extra.fk_object=t.rowid";
$sQuery .= " $sWhere
			$sLimit
			";
$rResultFilterTotal = $db->query($sQuery);
$aResultFilterTotal = $db->fetch_array($rResultFilterTotal);
$iFilteredTotal = $aResultFilterTotal[0];

/* Total data set length */
$sQuery = "
		SELECT COUNT(" . $sIndexColumn . ")
		FROM   $sTable
	";
$rResultTotal = $db->query($sQuery);
$aResultTotal = $db->fetch_array($rResultTotal);
$iTotal = $aResultTotal[0];

/*
 * Output
 */
$output = array (
		"sEcho" => intval($_GET['sEcho']),
		"iTotalRecords" => $iTotal,
		"iTotalDisplayRecords" => $iFilteredTotal,
		"aaData" => array () 
);

while ( $aRow = $db->fetch_array($rResult) ) {
	$row = array ();
	for($i = 0; $i < $numColumns; $i ++) {
		if ($aColumns[$i] == "t.ref") {
			$object->fetch($aRow[$aColumns[0]]);
			$row[] = $object->getNomUrl();
		} elseif (strpos($aColumns[$i],'extra.')!==false) {
			$extrafields_name=str_replace('extra.', '', $aColumns[$i]);
			$row[]=$extrafields->showOutputField($extrafields_name,$aRow[$i]);
		} else if ($aColumns[$i] != "t.rowid") {
			$row[] = $aRow[$i];
		}
	}
	$output['aaData'][] = $row;
}

echo json_encode($output);