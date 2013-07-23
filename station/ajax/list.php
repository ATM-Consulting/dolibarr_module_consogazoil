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
 * \file /consogazoil/station/ajax/list.php
 * \brief File to return datables output
 */
if (! defined ( 'NOTOKENRENEWAL' )) define ( 'NOTOKENRENEWAL', '1' ); // Disables token renewal
if (! defined ( 'NOREQUIREMENU' )) define ( 'NOREQUIREMENU', '1' );
// if (! defined('NOREQUIREHTML')) define('NOREQUIREHTML','1');
if (! defined ( 'NOREQUIREAJAX' )) define ( 'NOREQUIREAJAX', '1' );
// if (! defined('NOREQUIRESOC')) define('NOREQUIRESOC','1');
// if (! defined('NOREQUIRETRAN')) define('NOREQUIRETRAN','1');

$res = @include ("../../../main.inc.php"); // For root directory
if (! $res) $res = @include ("../../../../main.inc.php"); // For "custom" directory

dol_include_once ( '/consogazoil/class/consogazoilstation.class.php' );

$langs->load ( "consogazoil@consogazoil" );

top_httphead ();

// print '<!-- Ajax page called with url '.$_SERVER["PHP_SELF"].'?'.$_SERVER["QUERY_STRING"].' -->'."\n";

// print_r($_GET);

$object = new ConsogazoilStation ( $db );

/* Array of database columns which should be read and sent back to DataTables. Use a space where
	 * you want to insert a non-database field (for example a counter or static image)
	 */
$aColumns = array (
	'rowid',
	'ref',
	'name',
	'is_pref' 
);

$numColumns = count ( $aColumns );

/* Indexed column (used for fast and accurate table cardinality) */
$sIndexColumn = "rowid";

/* DB table to use */
$sTable = MAIN_DB_PREFIX . "consogazoil_station";

/*
	 * Paging
	 */
$sLimit = "";
if (isset ( $_GET ['iDisplayStart'] ) && $_GET ['iDisplayLength'] != '-1') {
	
	$sLimit = $db->plimit ( $db->escape ( $_GET ['iDisplayLength'] ), $db->escape ( $_GET ['iDisplayStart'] ) );
}

/*
	 * Ordering
	 */
if (isset ( $_GET ['iSortCol_0'] )) {
	$sOrder = "ORDER BY  ";
	for($i = 0; $i < intval ( $_GET ['iSortingCols'] ); $i ++) {
		if ($_GET ['bSortable_' . intval ( $_GET ['iSortCol_' . $i] )] == "true") {
			$sOrder .= $aColumns [intval ( $_GET ['iSortCol_' . $i] )] . " " . $db->escape ( $_GET ['sSortDir_' . $i] ) . ", ";
		}
	}
	
	$sOrder = substr_replace ( $sOrder, "", - 2 );
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
if ($_GET ['sSearch'] != "") {
	$sWhere = "WHERE (";
	$numColumns = count ( $aColumns );
	for($i = 0; $i < $numColumns; $i ++) {
		
		if (($aColumns [$i] == "ref") || ($aColumns [$i] == "name")) {
			$sWhere .= $aColumns [$i] . " LIKE '%" . $db->escape ( $_GET ['sSearch'] ) . "%' OR ";
		}
		if (($aColumns [$i] == "is_pref") && (is_numeric ( $_GET ['sSearch'] ))) {
			$sWhere .= $aColumns [$i] . " = " . $db->escape ( $_GET ['sSearch'] ) . " OR ";
		}
	}
	$sWhere = substr_replace ( $sWhere, "", - 3 );
	$sWhere .= ')';
}

/*
	 * SQL queries
	 * Get data to display
	 */
$sQuery = "
		SELECT " . str_replace ( " , ", " ", implode ( ", ", $aColumns ) ) . "
			FROM   $sTable
			$sWhere
			$sOrder
			$sLimit
			";
dol_syslog ( "station-list:: sQuery=" . $sQuery, LOG_DEBUG );
$rResult = $db->query ( $sQuery );
// echo $sQuery;

/* Data set length after filtering */
$sQuery = "
		SELECT count(rowid) FROM   $sTable
			$sWhere
			$sLimit
			";
$rResultFilterTotal = $db->query ( $sQuery );
$aResultFilterTotal = $db->fetch_array ( $rResultFilterTotal );
$iFilteredTotal = $aResultFilterTotal [0];

/* Total data set length */
$sQuery = "
		SELECT COUNT(" . $sIndexColumn . ")
		FROM   $sTable
	";
$rResultTotal = $db->query ( $sQuery );
$aResultTotal = $db->fetch_array ( $rResultTotal );
$iTotal = $aResultTotal [0];

/*
 * Output
 */
$output = array (
	"sEcho" => intval ( $_GET ['sEcho'] ),
	"iTotalRecords" => $iTotal,
	"iTotalDisplayRecords" => $iFilteredTotal,
	"aaData" => array () 
);

while ( $aRow = $db->fetch_array ( $rResult ) ) {
	$row = array ();
	for($i = 0; $i < $numColumns; $i ++) {
		if ($aColumns [$i] == "ref") {
			$object->fetch ( $aRow [$aColumns [0]] );
			$row [] = $object->getNomUrl ();
		} else if ($aColumns [$i] == "is_pref") {
			if (! empty ( $aRow [$aColumns [$i]] )) {
				$row [] = img_picto ( $langs->trans ( 'ConsoGazIsPrefYes' ), dol_buildpath('/consogazoil/img/flaggreen.png',1),'',1 );
			} else {
				$row [] = img_picto ( $langs->trans ( 'ConsoGazIsPrefNo' ), dol_buildpath('/consogazoil/img/flagred.png',1),'',1 );
			}
		} else if ($aColumns [$i] != "rowid") {
			$row [] = $aRow [$aColumns [$i]];
		}
	}
	$output ['aaData'] [] = $row;
}

echo json_encode ( $output );