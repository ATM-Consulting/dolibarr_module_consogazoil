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
 * \file /consogazoil/take/ajax/list.php
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

dol_include_once ( '/consogazoil/class/consogazoilvehtake.class.php' );

$langs->load ( "consogazoil@consogazoil" );

$filterdate = GETPOST ( 'filterdate', 'alpha' );

top_httphead ();

// print '<!-- Ajax page called with url '.$_SERVER["PHP_SELF"].'?'.$_SERVER["QUERY_STRING"].' -->'."\n";

// print_r($_GET);

$object = new ConsogazoilVehTake ( $db );

/* Array of database columns which should be read and sent back to DataTables. Use a space where
	 * you want to insert a non-database field (for example a counter or static image)
	 */
$aColumns = array (
	'station.ref',
	'station.name',
	'veh.ref',
	'veh.immat_veh',
	'driv.ref',
	'driv.name',
	't.volume',
	't.km_declare',
	't.km_controle',
	't.dt_hr_take',
	't.rowid',
	't.fk_vehicule',
	't.fk_station',
	't.fk_driver',
	't.conso_calc',
	'station.is_pref' 
);

$numColumns = count ( $aColumns );

/* Indexed column (used for fast and accurate table cardinality) */
$sIndexColumn = "t.rowid";

/* DB table to use */
$sTable = MAIN_DB_PREFIX .
		 "consogazoil_vehtake as t ";
$sTable .= "		INNER JOIN " .
		 MAIN_DB_PREFIX . "consogazoil_station as station ON t.fk_station=station.rowid ";
$sTable .= "		INNER JOIN " .
		 MAIN_DB_PREFIX . "consogazoil_vehicule as veh ON t.fk_vehicule=veh.rowid ";
$sTable .= "		INNER JOIN " .
		 MAIN_DB_PREFIX . "consogazoil_driver as driv ON t.fk_driver=driv.rowid ";

/*
	 * Paging
	 */
$sLimit = "";
if (isset ( $_GET ['iDisplayStart'] ) &&
		 $_GET ['iDisplayLength'] != '-1') {
	
	$sLimit = $db->plimit ( $db->escape ( $_GET ['iDisplayLength'] ), $db->escape ( $_GET ['iDisplayStart'] ) );
}

/*
	 * Ordering
	 */
if (isset ( $_GET ['iSortCol_0'] )) {
	$sOrder = "ORDER BY  ";
	for($i = 0; $i <
			 intval ( $_GET ['iSortingCols'] ); $i ++) {
		if ($_GET ['bSortable_' . intval ( $_GET ['iSortCol_' . $i] )] == "true") {
			$sOrder .= $aColumns [intval ( $_GET ['iSortCol_' . $i] )] . " " . $db->escape ( $_GET ['sSortDir_' . $i] ) . ", ";
		}
	}
	
	$sOrder = substr_replace ( $sOrder, "", - 2 );
	if ($sOrder ==
			 "ORDER BY") {
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
if ($_GET ['sSearch'] !=
		 "") {
	$sWhere = "WHERE (";
	$numColumns = count ( $aColumns );
	for($i = 0; $i <
			 $numColumns; $i ++) {
		
		if (($aColumns [$i] == "t.km_controle") || ($aColumns [$i] == "t.km_declare")) {
			if (is_numeric($db->escape ( $_GET ['sSearch'] ))) {
				$sWhere .= $aColumns [$i] . " = " . $db->escape ( $_GET ['sSearch'] ) . " OR ";
			}
		} else if (($aColumns [$i] ==
				 "t.volume") || ($aColumns [$i] == "t.conso_calc")) {
				 if (is_numeric($db->escape ( $_GET ['sSearch'] ))) {
					$sWhere .= $aColumns [$i] . " = '" . $db->escape ( $_GET ['sSearch'] ) . "' OR ";
				 }
		} else if (($aColumns [$i] !=
				 "t.dt_hr_take") && ($aColumns [$i] != "t.rowid") && ($aColumns [$i] != "t.fk_vehicule") && ($aColumns [$i] != "t.fk_station") && ($aColumns [$i] != "t.fk_driver") && ($aColumns [$i] != "station.is_pref")) {
			
			$sWhere .= $aColumns [$i] . " LIKE '%" . $db->escape ( $_GET ['sSearch'] ) . "%' OR ";
		}
	}
	$sWhere = substr_replace ( $sWhere, "", - 3 );
	$sWhere .= ')';
}

// Date filter
if (! empty ( $filterdate )) {
	// calculate the ma day in the search month
	$maxday = date ( 't', strtotime ( $filterdate .
			 '-01' ) );
	
	if ($sWhere ==
			 "") {
		$sWhere = "WHERE (t.dt_hr_take<='" . $filterdate . "-" . $maxday . "') AND (t.dt_hr_take>'" . $filterdate . "-01')";
	} else {
		$sWhere .= " AND (t.dt_hr_take<='" .
				 $filterdate . "-" . $maxday . "') AND (t.dt_hr_take>'" . $filterdate . "-01')";
	}
}

/*
	 * SQL queries
	 * Get data to display
	 */
$sQuery = "
		SELECT " .
		 str_replace ( " , ", " ", implode ( ", ", $aColumns ) ) . "
			FROM   $sTable
			$sWhere
			$sOrder
			$sLimit
			";
dol_syslog ( "take-list:: sQuery=" .
		 $sQuery, LOG_DEBUG );
$rResult = $db->query ( $sQuery );
// echo $sQuery;

/* Data set length after filtering */
$sQuery = "
		SELECT count(t.rowid) FROM   $sTable
			$sWhere
			$sLimit
			";
$rResultFilterTotal = $db->query ( $sQuery );
$aResultFilterTotal = $db->fetch_array ( $rResultFilterTotal );
$iFilteredTotal = $aResultFilterTotal [0];

/* Total data set length */
$sQuery = "
		SELECT COUNT(" .
		 $sIndexColumn . ")
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
	
	// datetake
	$row [] = dol_print_date ( $db->jdate ( $aRow [9] ), 'dayhourtext' );
	// Station Ref
	$row [] = $aRow [0];
	
	// Station is pref or not
	$station_pref_picto = '';
	if (empty ( $aRow [15] )) {
		$station_pref_picto = img_picto ( $langs->trans ( 'ConsoGazIsPrefNo' ), dol_buildpath ( '/consogazoil/img/flagred.png', 1 ), '', 1 );
	} else {
		$station_pref_picto = img_picto ( $langs->trans ( 'ConsoGazIsPrefYes' ), dol_buildpath ( '/consogazoil/img/flaggreen.png', 1 ), '', 1 );
	}
	// Station Name
	$row [] = $aRow [1] .
			 ' ' . $station_pref_picto;
	// Vehicule ref
	$row [] = $aRow [2];
	// Vehicule immat
	$row [] = $aRow [3];
	// Driver ref
	$row [] = $aRow [4];
	// Driver name
	$row [] = $aRow [5];
	// Avg Conso
	$row [] = $aRow [14];
	// Volume
	$row [] = $aRow [6];
	// Km declare
	$row [] = $aRow [7];
	// Picto km control status
	$km_ctrl_picto = '';
	if (! empty ( $aRow [8] ) &&
			 ! empty ( $aRow [7] )) {
		if (($aRow [7] - $aRow [8]) < $conf->global->GAZOIL_THRESOLD_KM) {
			$km_ctrl_picto = img_picto ( 'OK', dol_buildpath ( '/consogazoil/img/flaggreen.png', 1 ), '', 1 );
		} else {
			$km_ctrl_picto = img_picto ( 'KO', dol_buildpath ( '/consogazoil/img/flagred.png', 1 ), '', 1 );
		}
	}
	// km controle
	$row [] = $aRow [8] .
			 ' ' . $km_ctrl_picto;
	if ($user->rights->consogazoil->modifier) {
		$row [] = '<a href="' .
				 dol_buildpath ( '/consogazoil/take/card.php', 1 ) . '?id=' . $aRow [10] . '">' . $langs->trans ( "Show" ) . "</a>\n";
	}
	
	$output ['aaData'] [] = $row;
}

echo json_encode ( $output );