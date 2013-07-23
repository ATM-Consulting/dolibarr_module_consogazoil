<?php
/* Copyright (C) 2007-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2013   Florian HENRY <florian.henry@open-concept.pro>
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
 * \file /consogazoil/class/consogazoilvehtake.class.php
 * \ingroup consogazoil
 */

// Put here all includes required by your class file
require_once 'commonobjectconsogazoil.class.php';

/**
 * Put here description of your class
 */
class ConsogazoilVehTake extends CommonObjectConsoGazoil {
	var $db; // !< To store db handler
	var $error; // !< To return error code (or message)
	var $errors = array (); // !< To return several error codes (or messages)
	var $element = 'consogazoilvehtake'; // !< Id that identify managed objects
	var $table_element = 'consogazoil_vehtake'; // !< Name of table without prefix where object is stored
	protected $ismultientitymanaged = 1; // 0=No test on entity, 1=Test with field entity, 2=Test with link by societe
	
	var $id;
	var $entity;
	var $fk_vehicule;
	var $fk_station;
	var $fk_driver;
	var $dt_hr_take;
	var $volume;
	var $km_declare;
	var $km_controle;
	var $datec = '';
	var $tms = '';
	var $fk_user_creat;
	var $fk_user_modif;
	var $import_key;
	var $conso_calc;
	var $station_ref;
	var $station_name;
	var $veh_ref;
	var $veh_immat;
	var $driv_ref;
	var $driv_name;
	var $lines = array ();
	
	var $lines_immat = array ();
	var $lines_report=array();
	var $lines_service=array();
		
	/**
	 * Constructor
	 * 
	 * @param DoliDb $db handler
	 */
	function __construct($db) {
		$this->db = $db;
		return 1;
	}
	
	/**
	 * Create object into database
	 * 
	 * @param User $user that creates
	 * @param int $notrigger triggers after, 1=disable triggers
	 * @return int <0 if KO, Id of created object if OK
	 */
	function create($user, $notrigger = 0) {
		global $conf, $langs;
		$error = 0;
		
		// Clean parameters
		
		if (isset ( $this->entity )) $this->entity = trim ( $this->entity );
		if (isset ( $this->fk_vehicule )) $this->fk_vehicule = trim ( $this->fk_vehicule );
		if (isset ( $this->fk_station )) $this->fk_station = trim ( $this->fk_station );
		if (isset ( $this->fk_driver )) $this->fk_driver = trim ( $this->fk_driver );
		if (isset ( $this->volume )) $this->volume = trim ( $this->volume );
		if (isset ( $this->km_declare )) $this->km_declare = trim ( $this->km_declare );
		if (isset ( $this->km_controle )) $this->km_controle = trim ( $this->km_controle );
		if (isset ( $this->fk_user_creat )) $this->fk_user_creat = trim ( $this->fk_user_creat );
		if (isset ( $this->fk_user_modif )) $this->fk_user_modif = trim ( $this->fk_user_modif );
		if (isset ( $this->import_key )) $this->import_key = trim ( $this->import_key );
		if (isset ( $this->dt_hr_take )) $this->dt_hr_take = trim ( $this->dt_hr_take );
		if (isset ( $this->conso_calc )) $this->dt_hr_take = trim ( $this->conso_calc );
		
		// Check parameters
		// Put here code to add control on parameters values
		
		// Insert request
		$sql = "INSERT INTO " . MAIN_DB_PREFIX . "consogazoil_vehtake(";
		
		$sql .= "entity,";
		$sql .= "fk_vehicule,";
		$sql .= "fk_station,";
		$sql .= "fk_driver,";
		$sql .= "dt_hr_take,";
		$sql .= "volume,";
		$sql .= "km_declare,";
		$sql .= "km_controle,";
		$sql .= "conso_calc,";
		$sql .= "datec,";
		$sql .= "fk_user_creat,";
		$sql .= "fk_user_modif,";
		$sql .= "import_key";
		
		$sql .= ") VALUES (";
		
		$sql .= " " . $conf->entity . ",";
		$sql .= " " . (! isset ( $this->fk_vehicule ) ? 'NULL' : "'" . $this->fk_vehicule . "'") . ",";
		$sql .= " " . (! isset ( $this->fk_station ) ? 'NULL' : "'" . $this->fk_station . "'") . ",";
		$sql .= " " . (! isset ( $this->fk_driver ) ? 'NULL' : "'" . $this->fk_driver . "'") . ",";
		$sql .= " " . (! isset ( $this->dt_hr_take ) ? 'NULL' : "'" . $this->db->idate ( $this->dt_hr_take ) . "'") . ",";
		$sql .= " " . (empty ( $this->volume ) ? '0' : "'" . $this->volume . "'") . ",";
		$sql .= " " . (empty ( $this->km_declare ) ? '0' :  $this->km_declare ) . ",";
		$sql .= " " . (empty ( $this->km_controle ) ? 'NULL' :   $this->km_controle ) . ",";
		$sql .= " " . (empty ( $this->conso_calc ) ? 'NULL' : "'" . $this->conso_calc . "'") . ",";
		$sql .= "'" . $this->db->idate ( dol_now () ) . "',";
		$sql .= " " . $user->id . ",";
		$sql .= " " . $user->id . ",";
		$sql .= " " . (! isset ( $this->import_key ) ? 'NULL' : "'" . $this->db->escape ( $this->import_key ) . "'") . "";
		
		$sql .= ")";
		
		$this->db->begin ();
		
		dol_syslog ( get_class ( $this ) . "::create sql=" . $sql, LOG_DEBUG );
		$resql = $this->db->query ( $sql );
		if (! $resql) {
			$error ++;
			$this->errors [] = "Error " . $this->db->lasterror ();
		}
		
		if (! $error) {
			$this->id = $this->db->last_insert_id ( MAIN_DB_PREFIX . "consogazoil_vehtake" );
			
			if (! $notrigger) {
				// // Call triggers
				// include_once DOL_DOCUMENT_ROOT . '/core/class/interfaces.class.php';
				// $interface=new Interfaces($this->db);
				// $result=$interface->run_triggers('CONSOGAZ_TAKE_CREATE',$this,$user,$langs,$conf);
				// if ($result < 0) { $error++; $this->errors=$interface->errors; }
				// End call triggers
			}
		}
		
		if (! $error) {
			$this->calc_conso ( $user );
		}
		
		// Commit or rollback
		if ($error) {
			foreach ( $this->errors as $errmsg ) {
				dol_syslog ( get_class ( $this ) . "::create " . $errmsg, LOG_ERR );
				$this->error .= ($this->error ? ', ' . $errmsg : $errmsg);
			}
			$this->db->rollback ();
			return - 1 * $error;
		} else {
			$this->db->commit ();
			return $this->id;
		}
	}
	
	/**
	 * Load object in memory from the database
	 * 
	 * @param int $id object
	 * @return int <0 if KO, >0 if OK
	 */
	function fetch($id) {
		global $langs;
		$sql = "SELECT";
		$sql .= " t.rowid,";
		
		$sql .= " t.entity,";
		$sql .= " t.fk_vehicule,";
		$sql .= " t.fk_station,";
		$sql .= " t.fk_driver,";
		$sql .= " t.dt_hr_take,";
		$sql .= " t.volume,";
		$sql .= " t.km_declare,";
		$sql .= " t.km_controle,";
		$sql .= " t.conso_calc,";
		$sql .= " t.datec,";
		$sql .= " t.tms,";
		$sql .= " t.fk_user_creat,";
		$sql .= " t.fk_user_modif,";
		$sql .= " t.import_key,";
		
		$sql .= " station.ref as station_ref,";
		$sql .= " station.name as station_name,";
		$sql .= " veh.ref as veh_ref,";
		$sql .= " veh.immat_veh as veh_immat,";
		$sql .= " driv.ref as driv_ref,";
		$sql .= " driv.name as driv_name";
		
		$sql .= " FROM " . MAIN_DB_PREFIX . "consogazoil_vehtake as t";
		$sql .= "		LEFT JOIN " . MAIN_DB_PREFIX . "consogazoil_station as station ON t.fk_station=station.rowid ";
		$sql .= "		LEFT JOIN " . MAIN_DB_PREFIX . "consogazoil_vehicule as veh ON t.fk_vehicule=veh.rowid ";
		$sql .= "		LEFT JOIN " . MAIN_DB_PREFIX . "consogazoil_driver as driv ON t.fk_driver=driv.rowid ";
		$sql .= " WHERE t.rowid = " . $id;
		
		dol_syslog ( get_class ( $this ) . "::fetch sql=" . $sql, LOG_DEBUG );
		$resql = $this->db->query ( $sql );
		if ($resql) {
			if ($this->db->num_rows ( $resql )) {
				$obj = $this->db->fetch_object ( $resql );
				
				$this->id = $obj->rowid;
				
				$this->entity = $obj->entity;
				$this->fk_vehicule = $obj->fk_vehicule;
				$this->fk_station = $obj->fk_station;
				$this->fk_driver = $obj->fk_driver;
				$this->dt_hr_take = $obj->dt_hr_take;
				$this->volume = $obj->volume;
				$this->km_declare = $obj->km_declare;
				$this->km_controle = $obj->km_controle;
				$this->datec = $this->db->jdate ( $obj->datec );
				$this->tms = $this->db->jdate ( $obj->tms );
				$this->fk_user_creat = $obj->fk_user_creat;
				$this->fk_user_modif = $obj->fk_user_modif;
				$this->import_key = $obj->import_key;
				$this->conso_calc = $obj->conso_calc;
				$this->station_ref = $obj->station_ref;
				$this->station_name = $obj->station_name;
				$this->veh_ref = $obj->veh_ref;
				$this->veh_immat = $obj->veh_immat;
				$this->driv_ref = $obj->driv_ref;
				$this->driv_name = $obj->driv_name;
			}
			$this->db->free ( $resql );
			
			return 1;
		} else {
			$this->error = "Error " . $this->db->lasterror ();
			dol_syslog ( get_class ( $this ) . "::fetch " . $this->error, LOG_ERR );
			return - 1;
		}
	}
	
	/**
	 * Update object into database
	 * 
	 * @param User $user that modifies
	 * @param int $notrigger triggers after, 1=disable triggers
	 * @return int <0 if KO, >0 if OK
	 */
	function update($user = 0, $notrigger = 0) {
		global $conf, $langs;
		$error = 0;
		
		// Clean parameters
		
		if (isset ( $this->entity )) $this->entity = trim ( $this->entity );
		if (isset ( $this->fk_vehicule )) $this->fk_vehicule = trim ( $this->fk_vehicule );
		if (isset ( $this->fk_station )) $this->fk_station = trim ( $this->fk_station );
		if (isset ( $this->fk_driver )) $this->fk_driver = trim ( $this->fk_driver );
		if (isset ( $this->volume )) $this->volume = trim ( $this->volume );
		if (isset ( $this->km_declare )) $this->km_declare = trim ( $this->km_declare );
		if (isset ( $this->km_controle )) $this->km_controle = trim ( $this->km_controle );
		if (isset ( $this->fk_user_creat )) $this->fk_user_creat = trim ( $this->fk_user_creat );
		if (isset ( $this->fk_user_modif )) $this->fk_user_modif = trim ( $this->fk_user_modif );
		if (isset ( $this->import_key )) $this->import_key = trim ( $this->import_key );
		if (isset ( $this->dt_hr_take )) $this->dt_hr_take = trim ( $this->dt_hr_take );
		if (isset ( $this->conso_calc )) $this->conso_calc = trim ( $this->conso_calc );
		
		// Check parameters
		// Put here code to add a control on parameters values
		
		// Update request
		$sql = "UPDATE " . MAIN_DB_PREFIX . "consogazoil_vehtake SET";
		
		$sql .= " entity=" . $conf->entity . ",";
		$sql .= " fk_vehicule=" . (isset ( $this->fk_vehicule ) ? $this->fk_vehicule : "null") . ",";
		$sql .= " fk_station=" . (isset ( $this->fk_station ) ? $this->fk_station : "null") . ",";
		$sql .= " fk_driver=" . (isset ( $this->fk_driver ) ? $this->fk_driver : "null") . ",";
		$sql .= " dt_hr_take=" . (isset ( $this->dt_hr_take ) ? "'" . $this->db->idate ( $this->dt_hr_take ) . "'" : "null") . ",";
		$sql .= " volume=" . (!empty ( $this->volume ) ? "'" . $this->volume . "'" : "0") . ",";
		$sql .= " km_declare=" . (!empty ( $this->km_declare ) ? $this->km_declare : "0") . ",";
		$sql .= " km_controle=" . (!empty ( $this->km_controle ) ? $this->km_controle : "null") . ",";
		$sql .= " conso_calc=" . (!empty ( $this->conso_calc ) ? "'" . $this->conso_calc . "'" : "null") . ",";
		$sql .= " fk_user_modif=" . $user->id . ",";
		$sql .= " import_key=" . (isset ( $this->import_key ) ? "'" . $this->db->escape ( $this->import_key ) . "'" : "null") . "";
		
		$sql .= " WHERE rowid=" . $this->id;
		
		$this->db->begin ();
		
		dol_syslog ( get_class ( $this ) . "::update sql=" . $sql, LOG_DEBUG );
		$resql = $this->db->query ( $sql );
		if (! $resql) {
			$error ++;
			$this->errors [] = "Error " . $this->db->lasterror ();
		}
		
		if (! $error) {
			if (! $notrigger) {
				
				
				$this->calc_conso ( $user );
				
				
				// // Call triggers
				// include_once DOL_DOCUMENT_ROOT . '/core/class/interfaces.class.php';
				// $interface=new Interfaces($this->db);
				// $result=$interface->run_triggers('CONSOGAZ_TAKE_MODIFY',$this,$user,$langs,$conf);
				// if ($result < 0) { $error++; $this->errors=$interface->errors; }
				// // End call triggers
			}
		}
		
		// Commit or rollback
		if ($error) {
			foreach ( $this->errors as $errmsg ) {
				dol_syslog ( get_class ( $this ) . "::update " . $errmsg, LOG_ERR );
				$this->error .= ($this->error ? ', ' . $errmsg : $errmsg);
			}
			$this->db->rollback ();
			return - 1 * $error;
		} else {
			$this->db->commit ();
			return 1;
		}
	}
	
	/**
	 * Delete object in database
	 * 
	 * @param User $user that deletes
	 * @param int $notrigger triggers after, 1=disable triggers
	 * @return int <0 if KO, >0 if OK
	 */
	function delete($user, $notrigger = 0) {
		global $conf, $langs;
		$error = 0;
		
		$this->db->begin ();
		
		if (! $error) {
			if (! $notrigger) {
				// Uncomment this and change MYOBJECT to your own tag if you
				// want this action calls a trigger.
				
				// // Call triggers
				// include_once DOL_DOCUMENT_ROOT . '/core/class/interfaces.class.php';
				// $interface=new Interfaces($this->db);
				// $result=$interface->run_triggers('MYOBJECT_DELETE',$this,$user,$langs,$conf);
				// if ($result < 0) { $error++; $this->errors=$interface->errors; }
				// // End call triggers
			}
		}
		
		if (! $error) {
			$sql = "DELETE FROM " . MAIN_DB_PREFIX . "consogazoil_vehtake";
			$sql .= " WHERE rowid=" . $this->id;
			
			dol_syslog ( get_class ( $this ) . "::delete sql=" . $sql );
			$resql = $this->db->query ( $sql );
			if (! $resql) {
				$error ++;
				$this->errors [] = "Error " . $this->db->lasterror ();
			}
		}
		
		// Commit or rollback
		if ($error) {
			foreach ( $this->errors as $errmsg ) {
				dol_syslog ( get_class ( $this ) . "::delete " . $errmsg, LOG_ERR );
				$this->error .= ($this->error ? ', ' . $errmsg : $errmsg);
			}
			$this->db->rollback ();
			return - 1 * $error;
		} else {
			$this->db->commit ();
			return 1;
		}
	}
	
	/**
	 * Load an object from its id and create a new one in database
	 * 
	 * @param int $fromid of object to clone
	 * @return int id of clone
	 */
	function createFromClone($fromid) {
		global $user, $langs;
		
		$error = 0;
		
		$object = new Consogazoilvehtake ( $this->db );
		
		$this->db->begin ();
		
		// Load source object
		$object->fetch ( $fromid );
		$object->id = 0;
		$object->statut = 0;
		
		// Clear fields
		// ...
		
		// Create clone
		$result = $object->create ( $user );
		
		// Other options
		if ($result < 0) {
			$this->error = $object->error;
			$error ++;
		}
		
		if (! $error) {
		}
		
		// End
		if (! $error) {
			$this->db->commit ();
			return $object->id;
		} else {
			$this->db->rollback ();
			return - 1;
		}
	}
	
	/**
	 * Initialise object with example values
	 * Id must be 0 if object instance is a specimen
	 * 
	 * @return void
	 */
	function initAsSpecimen() {
		$this->id = 0;
		
		$this->entity = '';
		$this->fk_vehicule = '';
		$this->fk_station = '';
		$this->fk_driver = '';
		$this->dt_hr_take = '';
		$this->volume = '';
		$this->km_declare = '';
		$this->km_controle = '';
		$this->datec = '';
		$this->tms = '';
		$this->fk_user_creat = '';
		$this->fk_user_modif = '';
		$this->import_key = '';
	}
	
	/**
	 * Load object in memory from the database
	 * 
	 * @param string $sortorder order
	 * @param string $sortfield field
	 * @param int $limit page
	 * @param int $offset
	 * @param int $arch archive or not
	 * @param array $filter output
	 * @return int <0 if KO, >0 if OK
	 */
	function fetch_all($sortorder = 'DESC', $sortfield = 't.ref', $limit, $offset, $filter = '') {
		global $langs;
		
		$sql = "SELECT";
		$sql .= " t.rowid,";
		
		$sql .= " t.entity,";
		$sql .= " t.fk_vehicule,";
		$sql .= " t.fk_station,";
		$sql .= " t.fk_driver,";
		$sql .= " t.dt_hr_take,";
		$sql .= " t.volume,";
		$sql .= " t.km_declare,";
		$sql .= " t.km_controle,";
		$sql .= " t.conso_calc,";
		$sql .= " t.datec,";
		$sql .= " t.tms,";
		$sql .= " t.fk_user_creat,";
		$sql .= " t.fk_user_modif,";
		$sql .= " t.import_key";
		;
		$sql .= " WHERE t.entity IN (" . getEntity ( $this->element, 1 ) . ")";
		// Manage filter
		if (is_array ( $filter ) && count ( $filter ) > 0) {
			foreach ( $filter as $key => $value ) {
				if (strpos ( $key, 'date' )) {
					$sql .= ' AND ' . $key . ' = \'' . $this->db->idate ( $value ) . '\'';
				} else if (strpos ( $key, 'fk_' ) || strpos ( $key, 'km_' )) {
					$sql .= ' AND ' . $key . ' = ' . $value;
				} else if (strpos ( $key, 'volume' )) {
					$sql .= ' AND ' . $key . ' = \'' . $value . '\'';
				} else {
					$sql .= ' AND ' . $key . ' LIKE \'%' . $value . '%\'';
				}
			}
		}
		$sql .= " ORDER BY " . $sortfield . " " . $sortorder;
		if (! empty ( $limit )) $sql .= $this->db->plimit ( $limit + 1, $offset );
		
		dol_syslog ( get_class ( $this ) . "::fetch_all sql=" . $sql, LOG_DEBUG );
		$resql = $this->db->query ( $sql );
		if ($resql) {
			$this->lines = array ();
			
			$num = $this->db->num_rows ( $resql );
			while ( $obj = $this->db->fetch_object ( $resql ) ) {
				$line = new ConsogazoilVehTakeLine ();
				
				$line->id = $obj->rowid;
				
				$line->entity = $obj->entity;
				$line->fk_vehicule = $obj->fk_vehicule;
				$line->fk_station = $obj->fk_station;
				$line->fk_driver = $obj->fk_driver;
				$line->dt_hr_take = $obj->dt_hr_take;
				$line->volume = $obj->volume;
				$line->km_declare = $obj->km_declare;
				$line->km_controle = $obj->km_controle;
				$line->conso_calc = $obj->conso_calc;
				$line->datec = $this->db->jdate ( $obj->datec );
				$line->tms = $this->db->jdate ( $obj->tms );
				$line->fk_user_creat = $obj->fk_user_creat;
				$line->fk_user_modif = $obj->fk_user_modif;
				$line->import_key = $obj->import_key;
				
				$this->lines [] = $line;
			}
			$this->db->free ( $resql );
			
			return $num;
		} else {
			$this->error = "Error " . $this->db->lasterror ();
			dol_syslog ( get_class ( $this ) . "::fetch_all " . $this->error, LOG_ERR );
			return - 1;
		}
	}
	
	/**
	 * Calculate the average consomation per line
	 * 
	 * @param user $user
	 * @return int <0 if KO, >0 if OK
	 */
	function calc_conso($user) {
		global $conf;
		
		// Find immat of current vehicule
		$sql = "SELECT immat_veh FROM " . MAIN_DB_PREFIX . "consogazoil_vehicule WHERE rowid=" . $this->fk_vehicule;
		$resql = $this->db->query ( $sql );
		if ($resql) {
			if ($this->db->num_rows ( $resql )) {
				$obj = $this->db->fetch_object ( $resql );
				
				$immat_veh = $obj->immat_veh;
			}
			
			$this->db->free ( $resql );
		} else {
			$this->error = "Error " . $this->db->lasterror ();
			dol_syslog ( get_class ( $this ) . "::calc_conso " . $this->error, LOG_ERR );
			return - 1;
		}
		
		if (! empty ( $immat_veh )) {
			// Find the previous km_declare for the same vehicule find by immat
			$sql = "SELECT";
			$sql .= " t.rowid,";
			
			$sql .= " t.volume,";
			$sql .= " t.km_declare";
			
			$sql .= " FROM " . MAIN_DB_PREFIX . "consogazoil_vehtake as t";
			$sql .= " WHERE t.fk_vehicule IN (SELECT rowid FROM " . MAIN_DB_PREFIX . "consogazoil_vehicule WHERE immat_veh='" . $immat_veh . "')";
			$sql .= " AND t.dt_hr_take < '" . $this->db->idate ($this->dt_hr_take) . "'";
			$sql .= " ORDER BY t.dt_hr_take DESC";
			
			dol_syslog ( get_class ( $this ) . "::calc_conso sql=" . $sql, LOG_DEBUG );
			$resql = $this->db->query ( $sql );
			if ($resql) {
				if ($this->db->num_rows ( $resql )) {
					$obj = $this->db->fetch_object ( $resql );
					
					if (! empty ( $obj->volume ) && !empty($this->km_declare) && !empty($obj->km_declare)) {
						$this->conso_calc = price2num(($this->km_declare - $obj->km_declare) / $obj->volume,2,1);
						$this->update ( $user, 1 );
					}
				}
				$this->db->free ( $resql );
				
			} else {
				$this->error = "Error " . $this->db->lasterror ();
				dol_syslog ( get_class ( $this ) . "::calc_conso " . $this->error, LOG_ERR );
				return - 1;
			}
		}
		
		return 1;
	}
	
	
	/**
	 * Populate lines_immat with immat
	 *
	 * @param int	$year	Year filter
	 * @return int <0 if KO, >0 if OK
	 */
	function fetch_immat($year) {
		
		$this->lines_immat=array();
		
		
		$sql = "SELECT";
		$sql .= " DISTINCT veh.immat_veh";
		
		$sql .= " FROM " . MAIN_DB_PREFIX . "consogazoil_vehtake as t";
		$sql .= " INNER JOIN " . MAIN_DB_PREFIX . "consogazoil_vehicule as veh ON veh.rowid=t.fk_vehicule";
		$sql .= " WHERE date_format(t.dt_hr_take,'%Y') = '" . $year . "'";
		$sql .= " ORDER BY veh.immat_veh DESC";
		
		dol_syslog ( get_class ( $this ) . "::fetch_immat sql=" . $sql, LOG_DEBUG );
		$resql = $this->db->query ( $sql );
		if ($resql) {
			$num = $this->db->num_rows ( $resql );
			while ( $obj = $this->db->fetch_object ( $resql ) ) {		
				$this->lines_immat[]=$obj->immat_veh;
			}
				
			$this->db->free ( $resql );
			
			return $num;
		} else {
			$this->error = "Error " . $this->db->lasterror ();
			dol_syslog ( get_class ( $this ) . "::fetch_immat " . $this->error, LOG_ERR );
			return - 1;
		}
		
		//$this->lines_immat[]='RD7013';
	}
	
	/**
	 * Populate lines_immat with immat
	 *
	 * @param int	$year	Year filter
	 * @return int <0 if KO, >0 if OK
	 */
	function fetch_service($year) {
	
		$this->lines_service=array();
	
	
		$sql = "SELECT";
		$sql .= " DISTINCT serv.label,serv.rowid";
	
		$sql .= " FROM " . MAIN_DB_PREFIX . "consogazoil_vehtake as t";
		$sql .= " INNER JOIN " . MAIN_DB_PREFIX . "consogazoil_vehicule as veh ON veh.rowid=t.fk_vehicule";
		$sql .= " INNER JOIN " . MAIN_DB_PREFIX . "consogazoil_vehiculeservice as servveh ON servveh.fk_vehicule=t.fk_vehicule";
		$sql .= " INNER JOIN " . MAIN_DB_PREFIX . "consogazoil_service as serv ON serv.rowid=servveh.fk_service";
		$sql .= " WHERE date_format(t.dt_hr_take,'%Y') = '" . $year . "'";
		$sql .= " ORDER BY serv.label DESC";
	
		dol_syslog ( get_class ( $this ) . "::fetch_service sql=" . $sql, LOG_DEBUG );
		$resql = $this->db->query ( $sql );
		if ($resql) {
			$num = $this->db->num_rows ( $resql );
			while ( $obj = $this->db->fetch_object ( $resql ) ) {
				$this->lines_service[$obj->rowid]=$obj->label;
			}
	
			$this->db->free ( $resql );
				
			return $num;
		} else {
			$this->error = "Error " . $this->db->lasterror ();
			dol_syslog ( get_class ( $this ) . "::fetch_service " . $this->error, LOG_ERR );
			return - 1;
		}
	}
	
	
	/**
	 * Load array to display in reports
	 *
	 * @param int		$year		Year filter
	 * @param string	$idservice	Idservice
	 * @return int <0 if KO, >0 if OK
	 */
	function fetch_report_conso_service($year,$idservice) {
		
		global $conf,$langs;
		
		require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
		
		//This array will be populated as report 
		//$this->lines_report[1]=Avg Conso January
		//$this->lines_report[2]=Avg Conso January flag
		//...
		$this->lines_report=array();
		
		$arry_sum_vol_month=array();
		$arry_last_vol_month=array();
		$arry_last_vol_prevmonth=array();
		$arry_last_km_month=array();
		$arry_last_km_prevmonth=array();
		
		$array_consoavg_month=array();
		
		$avg_conso_veh=array();
		
		//Populate with 0 if for each month all array
		for ($month=1;$month<=12;$month++) {
				$arry_sum_vol_month[$month]=0;
				$arry_last_vol_month[$month]=0;
				$arry_last_vol_prevmonth[$month]=0;
				$arry_last_km_month[$month]=0;
				$arry_last_km_prevmonth[$month]=0;
				$array_consoavg_month[$month]=0;
		}
		
		
		//formula to calculate avg conso per month
		//(sum volume per month - volume last take+Volume last take on prev month)
		//divided by
		//(Last km declare on month - last km declare on prev month) / 100
		
		
		//Get sum volume on a periode
		$sql = "SELECT";
		$sql .= " sum(t.volume) as sumvol,";
		$sql .= " date_format(t.dt_hr_take,'%m') as dtmonth";
		$sql .= " FROM " . MAIN_DB_PREFIX . "consogazoil_vehtake as t";
		$sql .= " INNER JOIN " . MAIN_DB_PREFIX . "consogazoil_vehicule as veh ON veh.rowid=t.fk_vehicule";
		if (!empty($idservice)) $sql .= " INNER JOIN " . MAIN_DB_PREFIX . "consogazoil_vehiculeservice as servveh ON servveh.fk_vehicule=t.fk_vehicule";
		if (!empty($idservice))$sql .= " INNER JOIN " . MAIN_DB_PREFIX . "consogazoil_service as serv ON serv.rowid=servveh.fk_service AND serv.rowid=".$idservice;
		$sql .= " WHERE date_format(t.dt_hr_take,'%Y') = '" . $year . "'";
		if (!empty($idservice))$sql .= " AND t.dt_hr_take BETWEEN servveh.date_start AND servveh.date_end";
		$sql .= " GROUP BY date_format(t.dt_hr_take,'%m') ";

		dol_syslog ( get_class ( $this ) . "::fetch_report_conso_service sql=" . $sql, LOG_DEBUG );
		$resql = $this->db->query ( $sql );
		if ($resql) {
			while($obj = $this->db->fetch_object ( $resql )) {
				$arry_sum_vol_month[intval($obj->dtmonth)]=$obj->sumvol;
			}
		} else {
			$this->error = "Error " . $this->db->lasterror ();
			dol_syslog ( get_class ( $this ) . "::fetch_report_conso_service " . $this->error, LOG_ERR );
			return - 1;
		}
		
		//Get volume last take on this period
		foreach($arry_sum_vol_month as $key=>$val) {
			$firstday_month=dol_mktime(0, 0, 0, $key, 1, $year);
			$sql = "SELECT";
			$sql .= " t.volume as vollasttake";
			$sql .= " FROM " . MAIN_DB_PREFIX . "consogazoil_vehtake as t";
			$sql .= " INNER JOIN " . MAIN_DB_PREFIX . "consogazoil_vehicule as veh ON veh.rowid=t.fk_vehicule";
			if (!empty($idservice))$sql .= " INNER JOIN " . MAIN_DB_PREFIX . "consogazoil_vehiculeservice as servveh ON servveh.fk_vehicule=t.fk_vehicule";
			if (!empty($idservice))$sql .= " INNER JOIN " . MAIN_DB_PREFIX . "consogazoil_service as serv ON serv.rowid=servveh.fk_service AND serv.rowid=".$idservice;
			$sql .= " WHERE date_format(t.dt_hr_take,'%Y-%m') = '" . dol_print_date($firstday_month,'%Y-%m') . "'";
			if (!empty($idservice))$sql .= " AND t.dt_hr_take BETWEEN servveh.date_start AND servveh.date_end";
			$sql .= " ORDER BY t.dt_hr_take desc ";
			$sql .= "LIMIT 1 ";
			
			dol_syslog ( get_class ( $this ) . "::fetch_report_conso_service sql=" . $sql, LOG_DEBUG );
			$resql = $this->db->query ( $sql );
			if ($resql) {
				$obj = $this->db->fetch_object ( $resql );
				$arry_last_vol_month[$key]=$obj->vollasttake;
				
			} else {
				$this->error = "Error " . $this->db->lasterror ();
				dol_syslog ( get_class ( $this ) . "::fetch_report_conso_service " . $this->error, LOG_ERR );
				return - 1;
			}
		}
		
		//Get Volume last take on prev month
		foreach($arry_sum_vol_month as $key=>$val) {
			$firstday_month=dol_mktime(0, 0, 0, $key, 1, $year);
			$firstday_prevmonth=dol_time_plus_duree($firstday_month,-1,m);
			
			$sql = "SELECT";
			$sql .= " t.volume as vollasttakeprevmonth ";
			$sql .= " FROM " . MAIN_DB_PREFIX . "consogazoil_vehtake as t";
			$sql .= " INNER JOIN " . MAIN_DB_PREFIX . "consogazoil_vehicule as veh ON veh.rowid=t.fk_vehicule";
			if (!empty($idservice))$sql .= " INNER JOIN " . MAIN_DB_PREFIX . "consogazoil_vehiculeservice as servveh ON servveh.fk_vehicule=t.fk_vehicule";
			if (!empty($idservice))$sql .= " INNER JOIN " . MAIN_DB_PREFIX . "consogazoil_service as serv ON serv.rowid=servveh.fk_service AND serv.rowid=".$idservice;
			$sql .= " WHERE date_format(t.dt_hr_take,'%Y-%m') = '" . dol_print_date($firstday_prevmonth,'%Y-%m') . "'";
			if (!empty($idservice))$sql .= " AND t.dt_hr_take BETWEEN servveh.date_start AND servveh.date_end";
			$sql .= " ORDER BY t.dt_hr_take desc ";
			$sql .= "LIMIT 1 ";
			
			dol_syslog ( get_class ( $this ) . "::fetch_report_conso_service sql=" . $sql, LOG_DEBUG );
			$resql = $this->db->query ( $sql );
			if ($resql) {
				$obj = $this->db->fetch_object ( $resql );
				$arry_last_vol_prevmonth[$key]=$obj->vollasttakeprevmonth;
			} else {
				$this->error = "Error " . $this->db->lasterror ();
				dol_syslog ( get_class ( $this ) . "::fetch_report_conso_service " . $this->error, LOG_ERR );
				return - 1;
			}	
		}
		
		//Get last KM declare on month
		foreach($arry_sum_vol_month as $key=>$val) {
			$firstday_month=dol_mktime(0, 0, 0, $key, 1, $year);
			$sql = "SELECT";
			$sql .= " t.km_declare as kmlasttake,";
			$sql .= " date_format(t.dt_hr_take,'%m') as dtmonth";
			$sql .= " FROM " . MAIN_DB_PREFIX . "consogazoil_vehtake as t";
			$sql .= " INNER JOIN " . MAIN_DB_PREFIX . "consogazoil_vehicule as veh ON veh.rowid=t.fk_vehicule";
			if (!empty($idservice))$sql .= " INNER JOIN " . MAIN_DB_PREFIX . "consogazoil_vehiculeservice as servveh ON servveh.fk_vehicule=t.fk_vehicule";
			if (!empty($idservice))$sql .= " INNER JOIN " . MAIN_DB_PREFIX . "consogazoil_service as serv ON serv.rowid=servveh.fk_service AND serv.rowid=".$idservice;
			$sql .= " WHERE date_format(t.dt_hr_take,'%Y-%m') = '" . dol_print_date($firstday_month,'%Y-%m') . "'";
			if (!empty($idservice))$sql .= " AND t.dt_hr_take BETWEEN servveh.date_start AND servveh.date_end";
			$sql .= " ORDER BY t.dt_hr_take desc ";
			$sql .= "LIMIT 1 ";
			
			dol_syslog ( get_class ( $this ) . "::fetch_report_conso_service sql=" . $sql, LOG_DEBUG );
			$resql = $this->db->query ( $sql );
			if ($resql) {
				$obj = $this->db->fetch_object ( $resql );
				$arry_last_km_month[intval($obj->dtmonth)]=$obj->kmlasttake;
			} else {
				$this->error = "Error " . $this->db->lasterror ();
				dol_syslog ( get_class ( $this ) . "::fetch_report_conso_service " . $this->error, LOG_ERR );
				return - 1;
			}
		}
		
		//Get km last take on prev month
		foreach($arry_sum_vol_month as $key=>$val) {
			$firstday_month=dol_mktime(0, 0, 0, $key, 1, $year);
			$firstday_prevmonth=dol_time_plus_duree($firstday_month,-1,m);
				
			$sql = "SELECT";
			$sql .= " t.km_declare as kmlasttakeprevmonth ";
			$sql .= " FROM " . MAIN_DB_PREFIX . "consogazoil_vehtake as t";
			$sql .= " INNER JOIN " . MAIN_DB_PREFIX . "consogazoil_vehicule as veh ON veh.rowid=t.fk_vehicule";
			if (!empty($idservice))$sql .= " INNER JOIN " . MAIN_DB_PREFIX . "consogazoil_vehiculeservice as servveh ON servveh.fk_vehicule=t.fk_vehicule";
			if (!empty($idservice))$sql .= " INNER JOIN " . MAIN_DB_PREFIX . "consogazoil_service as serv ON serv.rowid=servveh.fk_service AND serv.rowid=".$idservice;
			$sql .= " WHERE date_format(t.dt_hr_take,'%Y-%m') = '" . dol_print_date($firstday_prevmonth,'%Y-%m') . "'";
			if (!empty($idservice))$sql .= " AND t.dt_hr_take BETWEEN servveh.date_start AND servveh.date_end";
			$sql .= " ORDER BY t.dt_hr_take desc ";
			$sql .= "LIMIT 1 ";
				
			dol_syslog ( get_class ( $this ) . "::fetch_report_conso_service sql=" . $sql, LOG_DEBUG );
			$resql = $this->db->query ( $sql );
			if ($resql) {
				$obj = $this->db->fetch_object ( $resql );
				$arry_last_km_prevmonth[$key]=$obj->kmlasttakeprevmonth;
			} else {
				$this->error = "Error " . $this->db->lasterror ();
				dol_syslog ( get_class ( $this ) . "::fetch_report_conso_service " . $this->error, LOG_ERR );
				return - 1;
			}
		}
		
		//Populate with 0 if for each month all array
		for ($month=1;$month<=12;$month++) {
			if (empty($arry_sum_vol_month[$month])) $arry_sum_vol_month[$month]=0;
			if (empty($arry_last_vol_month[$month])) $arry_last_vol_month[$month]=0;
			if (empty($arry_last_vol_prevmonth[$month])) $arry_last_vol_prevmonth[$month]=0;
			if (empty($arry_last_km_month[$month])) $arry_last_km_month[$month]=0;
			if (empty($arry_last_km_prevmonth[$month])) $arry_last_km_prevmonth[$month]=0;
			if (empty($array_consoavg_month[$month])) $array_consoavg_month[$month]=0;
		}
		
		
		
		for ($month=1;$month<=12;$month++) {
			
			$debug_string=' $idservice='.$idservice;
			$debug_string.=' $arry_sum_vol_month['.$month.']=' . $arry_sum_vol_month[$month];
			$debug_string.=' $arry_last_vol_month['.$month.']=' . $arry_last_vol_month[$month];
			$debug_string.=' $arry_last_vol_prevmonth['.$month.']=' . $arry_last_vol_prevmonth[$month];
			$debug_string.=' $arry_last_km_month['.$month.']=' . $arry_last_km_month[$month];
			$debug_string.=' $arry_last_km_prevmonth['.$month.']=' . $arry_last_km_prevmonth[$month];
			
			if (($arry_last_km_month[$month]-$arry_last_km_prevmonth[$month]) != 0) {
		
				
		
				$array_consoavg_month[$month]=($arry_sum_vol_month[$month]-$arry_last_vol_month[$month]+$arry_last_vol_prevmonth[$month])/(($arry_last_km_month[$month]-$arry_last_km_prevmonth[$month])/100);
			}else {
				$array_consoavg_month[$month]='0';
			}
			$array_consoavg_month[$month]=price2num($array_consoavg_month[$month],2,1);
			
			$debug_string.=' $array_consoavg_month[$month]['.$month.']=' . $array_consoavg_month[$month];
			dol_syslog ( get_class ( $this ) . '::fetch_report_conso_service '.$debug_string, LOG_DEBUG );
		}
		
		
		//January
		$month=1;
		$this->lines_report[1]=$array_consoavg_month[$month];
		$this->lines_report[2]='';

		
		//Febuary
		$month=2;
		$this->lines_report[3]=$array_consoavg_month[$month];
		$this->lines_report[4]='';

		
		//March
		$month=3;
		$this->lines_report[5]=$array_consoavg_month[$month];
		$this->lines_report[6]='';

		
		//Trimestre
		for ($month=1;$month<=3;$month++) {
			$this->lines_report[7]+=$array_consoavg_month[$month];
		}
		
		//April
		$month=4;
		$this->lines_report[8]=$array_consoavg_month[$month];
		$this->lines_report[9]='';
		
		//May
		$month=5;
		$this->lines_report[10]=$array_consoavg_month[$month];
		$this->lines_report[11]='';
		
		//Jun
		$month=6;
		$this->lines_report[12]=$array_consoavg_month[$month];
		$this->lines_report[13]='';
		
		//Trimestre
		for ($month=4;$month<=6;$month++) {
			$this->lines_report[14]+=$array_consoavg_month[$month];
		}
		
		//Semestre
		for ($month=1;$month<=6;$month++) {
			$this->lines_report[15]+=$array_consoavg_month[$month];
		}
		
		//Jully
		$month=7;
		$this->lines_report[16]=$array_consoavg_month[$month];
		$this->lines_report[17]='';
		
		//August
		$month=8;
		$this->lines_report[18]=$array_consoavg_month[$month];
		$this->lines_report[19]='';
		
		//Septembre
		$month=9;
		$this->lines_report[20]=$array_consoavg_month[$month];
		$this->lines_report[21]='';
		
		//Trimestre
		for ($month=7;$month<=9;$month++) {
			$this->lines_report[22]+=$array_consoavg_month[$month];
		}
		
		//Octobre
		$month=10;
		$this->lines_report[23]=$array_consoavg_month[$month];
		$this->lines_report[24]='';
		
		//Novembre
		$month=11;
		$this->lines_report[25]=$array_consoavg_month[$month];
		$this->lines_report[26]='';
		
		//Decembre
		$month=12;
		$this->lines_report[27]=$array_consoavg_month[$month];
		$this->lines_report[28]='';
		
		//Trimestre
		for ($month=10;$month<=12;$month++) {
			$this->lines_report[29]+=$array_consoavg_month[$month];
		}
		
		//Semestre
		for ($month=7;$month<=12;$month++) {
			$this->lines_report[30]+=$array_consoavg_month[$month];
		}
		
		//Total
		for ($month=0;$month<=12;$month++) {
			$this->lines_report[31]+=$array_consoavg_month[$month];
		}
		return 1;
	}
		
	
	
	/**
	 * Load array to display in reports
	 *
	 * @param int		$year	Year filter
	 * @param string	$immat	Immat
	 * @return int <0 if KO, >0 if OK
	 */
	function fetch_report_conso($year,$immat) {
		
		global $conf,$langs;
		
		require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
		
		//This array will be populated as report 
		//$this->lines_report[1]=Avg Conso January
		//$this->lines_report[2]=Avg Conso January flag
		//...
		$this->lines_report=array();
		
		$arry_sum_vol_month=array();
		$arry_last_vol_month=array();
		$arry_last_vol_prevmonth=array();
		$arry_last_km_month=array();
		$arry_last_km_prevmonth=array();
		
		$array_consoavg_month=array();
		
		$avg_conso_veh=array();
		
		//Populate with 0 if for each month all array
		for ($month=1;$month<=12;$month++) {
				$arry_sum_vol_month[$month]=0;
				$arry_last_vol_month[$month]=0;
				$arry_last_vol_prevmonth[$month]=0;
				$arry_last_km_month[$month]=0;
				$arry_last_km_prevmonth[$month]=0;
				$array_consoavg_month[$month]=0;
		}
		
		
		//formula to calculate avg conso per month
		//(sum volume per month - volume last take+Volume last take on prev month)
		//divided by
		//(Last km declare on month - last km declare on prev month) / 100
		
		
		//Get sum volume on a periode
		$sql = "SELECT";
		$sql .= " sum(t.volume) as sumvol,";
		$sql .= " date_format(t.dt_hr_take,'%m') as dtmonth";
		$sql .= " FROM " . MAIN_DB_PREFIX . "consogazoil_vehtake as t";
		$sql .= " INNER JOIN " . MAIN_DB_PREFIX . "consogazoil_vehicule as veh ON veh.rowid=t.fk_vehicule AND veh.immat_veh='".$immat."'";
		$sql .= " WHERE date_format(t.dt_hr_take,'%Y') = '" . $year . "'";
		$sql .= " GROUP BY date_format(t.dt_hr_take,'%m') ";

		dol_syslog ( get_class ( $this ) . "::fetch_report_conso sql=" . $sql, LOG_DEBUG );
		$resql = $this->db->query ( $sql );
		if ($resql) {
			while($obj = $this->db->fetch_object ( $resql )) {
				$arry_sum_vol_month[intval($obj->dtmonth)]=$obj->sumvol;
			}
		} else {
			$this->error = "Error " . $this->db->lasterror ();
			dol_syslog ( get_class ( $this ) . "::fetch_report_conso " . $this->error, LOG_ERR );
			return - 1;
		}
		
		//Get volume last take on this period
		foreach($arry_sum_vol_month as $key=>$val) {
			$firstday_month=dol_mktime(0, 0, 0, $key, 1, $year);
			$sql = "SELECT";
			$sql .= " t.volume as vollasttake";
			$sql .= " FROM " . MAIN_DB_PREFIX . "consogazoil_vehtake as t";
			$sql .= " INNER JOIN " . MAIN_DB_PREFIX . "consogazoil_vehicule as veh ON veh.rowid=t.fk_vehicule AND veh.immat_veh='".$immat."'";
			$sql .= " WHERE date_format(t.dt_hr_take,'%Y-%m') = '" . dol_print_date($firstday_month,'%Y-%m') . "'";
			$sql .= " ORDER BY t.dt_hr_take desc ";
			$sql .= "LIMIT 1 ";
			
			dol_syslog ( get_class ( $this ) . "::fetch_report_conso sql=" . $sql, LOG_DEBUG );
			$resql = $this->db->query ( $sql );
			if ($resql) {
				$obj = $this->db->fetch_object ( $resql );
				$arry_last_vol_month[$key]=$obj->vollasttake;
			} else {
				$this->error = "Error " . $this->db->lasterror ();
				dol_syslog ( get_class ( $this ) . "::fetch_report_conso " . $this->error, LOG_ERR );
				return - 1;
			}
		}
		
		//Get Volume last take on prev month
		foreach($arry_sum_vol_month as $key=>$val) {
			$firstday_month=dol_mktime(0, 0, 0, $key, 1, $year);
			$firstday_prevmonth=dol_time_plus_duree($firstday_month,-1,m);
			
			$sql = "SELECT";
			$sql .= " t.volume as vollasttakeprevmonth ";
			$sql .= " FROM " . MAIN_DB_PREFIX . "consogazoil_vehtake as t";
			$sql .= " INNER JOIN " . MAIN_DB_PREFIX . "consogazoil_vehicule as veh ON veh.rowid=t.fk_vehicule AND veh.immat_veh='".$immat."'";
			$sql .= " WHERE date_format(t.dt_hr_take,'%Y-%m') = '" . dol_print_date($firstday_prevmonth,'%Y-%m') . "'";
			$sql .= " ORDER BY t.dt_hr_take desc ";
			$sql .= "LIMIT 1 ";
			
			dol_syslog ( get_class ( $this ) . "::fetch_report_conso sql=" . $sql, LOG_DEBUG );
			$resql = $this->db->query ( $sql );
			if ($resql) {
				$obj = $this->db->fetch_object ( $resql );
				$arry_last_vol_prevmonth[$key]=$obj->vollasttakeprevmonth;
			} else {
				$this->error = "Error " . $this->db->lasterror ();
				dol_syslog ( get_class ( $this ) . "::fetch_report_conso " . $this->error, LOG_ERR );
				return - 1;
			}	
		}
		
		//Get last KM declare on month
		foreach($arry_sum_vol_month as $key=>$val) {
			$firstday_month=dol_mktime(0, 0, 0, $key, 1, $year);
			$sql = "SELECT";
			$sql .= " t.km_declare as kmlasttake";
			$sql .= " FROM " . MAIN_DB_PREFIX . "consogazoil_vehtake as t";
			$sql .= " INNER JOIN " . MAIN_DB_PREFIX . "consogazoil_vehicule as veh ON veh.rowid=t.fk_vehicule AND veh.immat_veh='".$immat."'";
			$sql .= " WHERE date_format(t.dt_hr_take,'%Y-%m') = '" . dol_print_date($firstday_month,'%Y-%m') . "'";
			$sql .= " ORDER BY t.dt_hr_take desc ";
			$sql .= "LIMIT 1 ";
			
			dol_syslog ( get_class ( $this ) . "::fetch_report_conso sql=" . $sql, LOG_DEBUG );
			$resql = $this->db->query ( $sql );
			if ($resql) {
				$obj = $this->db->fetch_object ( $resql );
				$arry_last_km_month[$key]=$obj->kmlasttake;
				
			} else {
				$this->error = "Error " . $this->db->lasterror ();
				dol_syslog ( get_class ( $this ) . "::fetch_report_conso " . $this->error, LOG_ERR );
				return - 1;
			}
		}
		
		//Get km last take on prev month
		foreach($arry_sum_vol_month as $key=>$val) {
			$firstday_month=dol_mktime(0, 0, 0, $key, 1, $year);
			$firstday_prevmonth=dol_time_plus_duree($firstday_month,-1,m);
				
			$sql = "SELECT";
			$sql .= " t.km_declare as kmlasttakeprevmonth ";
			$sql .= " FROM " . MAIN_DB_PREFIX . "consogazoil_vehtake as t";
			$sql .= " INNER JOIN " . MAIN_DB_PREFIX . "consogazoil_vehicule as veh ON veh.rowid=t.fk_vehicule AND veh.immat_veh='".$immat."'";
			$sql .= " WHERE date_format(t.dt_hr_take,'%Y-%m') = '" . dol_print_date($firstday_prevmonth,'%Y-%m') . "'";
			$sql .= " ORDER BY t.dt_hr_take desc ";
			$sql .= "LIMIT 1 ";
				
			dol_syslog ( get_class ( $this ) . "::fetch_report_conso sql=" . $sql, LOG_DEBUG );
			$resql = $this->db->query ( $sql );
			if ($resql) {
				$obj = $this->db->fetch_object ( $resql );
				$arry_last_km_prevmonth[$key]=$obj->kmlasttakeprevmonth;
			} else {
				$this->error = "Error " . $this->db->lasterror ();
				dol_syslog ( get_class ( $this ) . "::fetch_report_conso " . $this->error, LOG_ERR );
				return - 1;
			}
		}
		
		//Populate with 0 if for each month all array
		for ($month=1;$month<=12;$month++) {
			if (empty($arry_sum_vol_month[$month])) $arry_sum_vol_month[$month]=0;
			if (empty($arry_last_vol_month[$month])) $arry_last_vol_month[$month]=0;
			if (empty($arry_last_vol_prevmonth[$month])) $arry_last_vol_prevmonth[$month]=0;
			if (empty($arry_last_km_month[$month])) $arry_last_km_month[$month]=0;
			if (empty($arry_last_km_prevmonth[$month])) $arry_last_km_prevmonth[$month]=0;
			if (empty($array_consoavg_month[$month])) $array_consoavg_month[$month]=0;
		}
		
		
		
		for ($month=1;$month<=12;$month++) {
			if (($arry_last_km_month[$month]-$arry_last_km_prevmonth[$month]) != 0) {
				
				$debug_string=' $immat='.$immat;
				$debug_string.=' $arry_sum_vol_month['.$month.']=' . $arry_sum_vol_month[$month];
				$debug_string.=' $arry_last_vol_month['.$month.']=' . $arry_last_vol_month[$month];
				$debug_string.=' $arry_last_vol_prevmonth['.$month.']=' . $arry_last_vol_prevmonth[$month];
				$debug_string.=' $arry_last_km_month['.$month.']=' . $arry_last_km_month[$month];
				$debug_string.=' $arry_last_km_prevmonth['.$month.']=' . $arry_last_km_prevmonth[$month];
				
				dol_syslog ( get_class ( $this ) . '::fetch_report_conso '.$debug_string, LOG_DEBUG );
				$array_consoavg_month[$month]=($arry_sum_vol_month[$month]-$arry_last_vol_month[$month]+$arry_last_vol_prevmonth[$month])/(($arry_last_km_month[$month]-$arry_last_km_prevmonth[$month])/100);
			}else {
				$array_consoavg_month[$month]='0';
			}
			$array_consoavg_month[$month]=price2num($array_consoavg_month[$month],2,1);
		}
		
		//get avg conso vehicule
		if (!array_key_exists($immat,$avg_conso_veh)){
			$sql = "SELECT";
			$sql .= " avg_conso ";
			$sql .= " FROM " . MAIN_DB_PREFIX . "consogazoil_vehicule WHERE immat_veh='".$immat."'";
			$sql .= "LIMIT 1 ";
			dol_syslog ( get_class ( $this ) . "::fetch_report_conso sql=" . $sql, LOG_DEBUG );
			$resql = $this->db->query ( $sql );
			if ($resql) {
				$obj = $this->db->fetch_object ( $resql );
				$avg_conso_veh[$immat]=$obj->avg_conso;
			} else {
				$this->error = "Error " . $this->db->lasterror ();
				dol_syslog ( get_class ( $this ) . "::fetch_report_conso " . $this->error, LOG_ERR );
				return - 1;
			}
		}
		$avg_alert_percent = $avg_conso_veh[$immat] + (($avg_conso_veh[$immat] / 100) * $conf->global->GAZOIL_THRESOLD_CONSO);
		
		//January
		$month=1;
		$this->lines_report[1]=$array_consoavg_month[$month];
		//January sate
		if ($array_consoavg_month[$month]<=$avg_conso_veh[$immat]) {
			$this->lines_report[2]=img_picto ( 'OK', dol_buildpath ( '/consogazoil/img/flaggreen.png', 1 ), '', 1 );
		}else if (($avg_conso_veh[$immat] < $array_consoavg_month[$month]) &&  ($array_consoavg_month[1] < $avg_alert_percent)) {
			$this->lines_report[2]=img_picto ( 'OK', dol_buildpath ( '/consogazoil/img/flagyellow.png', 1 ), '', 1 );
		} else if ($array_consoavg_month[$month]>$avg_alert_percent) {
			$this->lines_report[2]=img_picto ( 'OK', dol_buildpath ( '/consogazoil/img/flagred.png', 1 ), '', 1 );
		} else {
			$this->lines_report[2]='';
		}
		
		//Febuary
		$month=2;
		$this->lines_report[3]=$array_consoavg_month[$month];
		//Febuary sate
		if ($array_consoavg_month[$month]<=$avg_conso_veh[$immat]) {
			$this->lines_report[4]=img_picto ( 'OK', dol_buildpath ( '/consogazoil/img/flaggreen.png', 1 ), '', 1 );
		}else if (($avg_conso_veh[$immat] < $array_consoavg_month[$month]) &&  ($array_consoavg_month[1] < $avg_alert_percent)) {
			$this->lines_report[4]=img_picto ( 'OK', dol_buildpath ( '/consogazoil/img/flagyellow.png', 1 ), '', 1 );
		} else if ($array_consoavg_month[$month]>$avg_alert_percent) {
			$this->lines_report[4]=img_picto ( 'OK', dol_buildpath ( '/consogazoil/img/flagred.png', 1 ), '', 1 );
		}else {
			$this->lines_report[4]='';
		}
		
		//March
		$month=3;
		$this->lines_report[5]=$array_consoavg_month[$month];
		//March sate
		if ($array_consoavg_month[$month]<=$avg_conso_veh[$immat]) {
			$this->lines_report[6]=img_picto ( 'OK', dol_buildpath ( '/consogazoil/img/flaggreen.png', 1 ), '', 1 );
		}else if (($avg_conso_veh[$immat] < $array_consoavg_month[$month]) &&  ($array_consoavg_month[1] < $avg_alert_percent)) {
			$this->lines_report[6]=img_picto ( 'OK', dol_buildpath ( '/consogazoil/img/flagyellow.png', 1 ), '', 1 );
		} else if ($array_consoavg_month[$month]>$avg_alert_percent) {
			$this->lines_report[6]=img_picto ( 'OK', dol_buildpath ( '/consogazoil/img/flagred.png', 1 ), '', 1 );
		}else {
			$this->lines_report[6]='';
		}
		
		//Trimestre
		for ($month=1;$month<=3;$month++) {
			$this->lines_report[7]+=$array_consoavg_month[$month];
		}
		
		//April
		$month=4;
		$this->lines_report[8]=$array_consoavg_month[$month];
		//April sate
		if ($array_consoavg_month[$month]<=$avg_conso_veh[$immat]) {
			$this->lines_report[9]=img_picto ( 'OK', dol_buildpath ( '/consogazoil/img/flaggreen.png', 1 ), '', 1 );
		}else if (($avg_conso_veh[$immat] < $array_consoavg_month[$month]) &&  ($array_consoavg_month[1] < $avg_alert_percent)) {
			$this->lines_report[9]=img_picto ( 'OK', dol_buildpath ( '/consogazoil/img/flagyellow.png', 1 ), '', 1 );
		} else if ($array_consoavg_month[$month]>$avg_alert_percent) {
			$this->lines_report[9]=img_picto ( 'OK', dol_buildpath ( '/consogazoil/img/flagred.png', 1 ), '', 1 );
		}else {
			$this->lines_report[9]='';
		}
		
		//May
		$month=5;
		$this->lines_report[10]=$array_consoavg_month[$month];
		//May sate
		if ($array_consoavg_month[$month]<=$avg_conso_veh[$immat]) {
			$this->lines_report[11]=img_picto ( 'OK', dol_buildpath ( '/consogazoil/img/flaggreen.png', 1 ), '', 1 );
		}else if (($avg_conso_veh[$immat] < $array_consoavg_month[$month]) &&  ($array_consoavg_month[1] < $avg_alert_percent)) {
			$this->lines_report[11]=img_picto ( 'OK', dol_buildpath ( '/consogazoil/img/flagyellow.png', 1 ), '', 1 );
		} else if ($array_consoavg_month[$month]>$avg_alert_percent) {
			$this->lines_report[11]=img_picto ( 'OK', dol_buildpath ( '/consogazoil/img/flagred.png', 1 ), '', 1 );
		}else {
			$this->lines_report[11]='';
		}
		
		//Jun
		$month=6;
		$this->lines_report[12]=$array_consoavg_month[$month];
		//Jun sate
		if ($array_consoavg_month[$month]<=$avg_conso_veh[$immat]) {
			$this->lines_report[13]=img_picto ( 'OK', dol_buildpath ( '/consogazoil/img/flaggreen.png', 1 ), '', 1 );
		}else if (($avg_conso_veh[$immat] < $array_consoavg_month[$month]) &&  ($array_consoavg_month[1] < $avg_alert_percent)) {
			$this->lines_report[13]=img_picto ( 'OK', dol_buildpath ( '/consogazoil/img/flagyellow.png', 1 ), '', 1 );
		} else if ($array_consoavg_month[$month]>$avg_alert_percent) {
			$this->lines_report[13]=img_picto ( 'OK', dol_buildpath ( '/consogazoil/img/flagred.png', 1 ), '', 1 );
		}else {
			$this->lines_report[13]='';
		}
		
		//Trimestre
		for ($month=4;$month<=6;$month++) {
			$this->lines_report[14]+=$array_consoavg_month[$month];
		}
		
		//Semestre
		for ($month=1;$month<=6;$month++) {
			$this->lines_report[15]+=$array_consoavg_month[$month];
		}
		
		//Jully
		$month=7;
		$this->lines_report[16]=$array_consoavg_month[$month];
		//Jully sate
		if ($array_consoavg_month[$month]<=$avg_conso_veh[$immat]) {
			$this->lines_report[17]=img_picto ( 'OK', dol_buildpath ( '/consogazoil/img/flaggreen.png', 1 ), '', 1 );
		}else if (($avg_conso_veh[$immat] < $array_consoavg_month[$month]) &&  ($array_consoavg_month[1] < $avg_alert_percent)) {
			$this->lines_report[17]=img_picto ( 'OK', dol_buildpath ( '/consogazoil/img/flagyellow.png', 1 ), '', 1 );
		} else if ($array_consoavg_month[$month]>$avg_alert_percent) {
			$this->lines_report[17]=img_picto ( 'OK', dol_buildpath ( '/consogazoil/img/flagred.png', 1 ), '', 1 );
		}else {
			$this->lines_report[17]='';
		}
		
		//August
		$month=8;
		$this->lines_report[18]=$array_consoavg_month[$month];
		//August sate
		if ($array_consoavg_month[$month]<=$avg_conso_veh[$immat]) {
			$this->lines_report[19]=img_picto ( 'OK', dol_buildpath ( '/consogazoil/img/flaggreen.png', 1 ), '', 1 );
		}else if (($avg_conso_veh[$immat] < $array_consoavg_month[$month]) &&  ($array_consoavg_month[1] < $avg_alert_percent)) {
			$this->lines_report[19]=img_picto ( 'OK', dol_buildpath ( '/consogazoil/img/flagyellow.png', 1 ), '', 1 );
		} else if ($array_consoavg_month[$month]>$avg_alert_percent) {
			$this->lines_report[19]=img_picto ( 'OK', dol_buildpath ( '/consogazoil/img/flagred.png', 1 ), '', 1 );
		}else {
			$this->lines_report[19]='';
		}
		
		//Septembre
		$month=9;
		$this->lines_report[20]=$array_consoavg_month[$month];
		//Septembre sate
		if ($array_consoavg_month[$month]<=$avg_conso_veh[$immat]) {
			$this->lines_report[21]=img_picto ( 'OK', dol_buildpath ( '/consogazoil/img/flaggreen.png', 1 ), '', 1 );
		}else if (($avg_conso_veh[$immat] < $array_consoavg_month[$month]) &&  ($array_consoavg_month[1] < $avg_alert_percent)) {
			$this->lines_report[21]=img_picto ( 'OK', dol_buildpath ( '/consogazoil/img/flagyellow.png', 1 ), '', 1 );
		} else if ($array_consoavg_month[$month]>$avg_alert_percent) {
			$this->lines_report[21]=img_picto ( 'OK', dol_buildpath ( '/consogazoil/img/flagred.png', 1 ), '', 1 );
		}else {
			$this->lines_report[21]='';
		}
		
		//Trimestre
		for ($month=7;$month<=9;$month++) {
			$this->lines_report[22]+=$array_consoavg_month[$month];
		}
		
		//Octobre
		$month=10;
		$this->lines_report[23]=$array_consoavg_month[$month];
		//Octobre sate
		if ($array_consoavg_month[$month]<=$avg_conso_veh[$immat]) {
			$this->lines_report[24]=img_picto ( 'OK', dol_buildpath ( '/consogazoil/img/flaggreen.png', 1 ), '', 1 );
		}else if (($avg_conso_veh[$immat] < $array_consoavg_month[$month]) &&  ($array_consoavg_month[1] < $avg_alert_percent)) {
			$this->lines_report[24]=img_picto ( 'OK', dol_buildpath ( '/consogazoil/img/flagyellow.png', 1 ), '', 1 );
		} else if ($array_consoavg_month[$month]>$avg_alert_percent) {
			$this->lines_report[24]=img_picto ( 'OK', dol_buildpath ( '/consogazoil/img/flagred.png', 1 ), '', 1 );
		}else {
			$this->lines_report[24]='';
		}
		
		//Novembre
		$month=11;
		$this->lines_report[25]=$array_consoavg_month[$month];
		//Novembre sate
		if ($array_consoavg_month[$month]<=$avg_conso_veh[$immat]) {
			$this->lines_report[26]=img_picto ( 'OK', dol_buildpath ( '/consogazoil/img/flaggreen.png', 1 ), '', 1 );
		}else if (($avg_conso_veh[$immat] < $array_consoavg_month[$month]) &&  ($array_consoavg_month[1] < $avg_alert_percent)) {
			$this->lines_report[26]=img_picto ( 'OK', dol_buildpath ( '/consogazoil/img/flagyellow.png', 1 ), '', 1 );
		} else if ($array_consoavg_month[$month]>$avg_alert_percent) {
			$this->lines_report[26]=img_picto ( 'OK', dol_buildpath ( '/consogazoil/img/flagred.png', 1 ), '', 1 );
		}else {
			$this->lines_report[26]='';
		}
		
		//Decembre
		$month=12;
		$this->lines_report[27]=$array_consoavg_month[$month];
		//Decembre sate
		if ($array_consoavg_month[$month]<=$avg_conso_veh[$immat]) {
			$this->lines_report[28]=img_picto ( 'OK', dol_buildpath ( '/consogazoil/img/flaggreen.png', 1 ), '', 1 );
		}else if (($avg_conso_veh[$immat] < $array_consoavg_month[$month]) &&  ($array_consoavg_month[1] < $avg_alert_percent)) {
			$this->lines_report[28]=img_picto ( 'OK', dol_buildpath ( '/consogazoil/img/flagyellow.png', 1 ), '', 1 );
		} else if ($array_consoavg_month[$month]>$avg_alert_percent) {
			$this->lines_report[28]=img_picto ( 'OK', dol_buildpath ( '/consogazoil/img/flagred.png', 1 ), '', 1 );
		}else {
			$this->lines_report[28]='';
		}
		
		//Trimestre
		for ($month=10;$month<=12;$month++) {
			$this->lines_report[29]+=$array_consoavg_month[$month];
		}
		
		//Semestre
		for ($month=7;$month<=12;$month++) {
			$this->lines_report[30]+=$array_consoavg_month[$month];
		}
		
		//Total
		for ($month=0;$month<=12;$month++) {
			$this->lines_report[31]+=$array_consoavg_month[$month];
		}
		
		
		return 1;
	}
	
	
	/**
	 * Load array to display in reports
	 *
	 * @param int		$year	Year filter
	 * @param string	$immat	Immat
	 * @return int <0 if KO, >0 if OK
	 */
	function fetch_report_km($year,$immat) {
		
		global $conf,$langs;
		
		//This array will be populated as report 
		//$this->lines_report[1]=km January
		//$this->lines_report[2]=km Febuary
		//...
		$this->lines_report=array();
		
		$arry_first_km_month=array();
		$arry_last_km_month=array();
		
		
		//Populate with 0 if for each month all array
		for ($month=1;$month<=12;$month++) {
			$arry_first_km_month[$month]=0;
			$arry_last_km_month[$month]=0;
		}
		
		
		//formula to calculate km per month
		//(last km month - first km month)
		
		
		//Get first km declare on this period
		foreach($arry_first_km_month as $key=>$val) {
			$firstday_month=dol_mktime(0, 0, 0, $key, 1, $year);
			$sql = "SELECT";
			$sql .= " t.km_declare";
			$sql .= " FROM " . MAIN_DB_PREFIX . "consogazoil_vehtake as t";
			$sql .= " INNER JOIN " . MAIN_DB_PREFIX . "consogazoil_vehicule as veh ON veh.rowid=t.fk_vehicule AND veh.immat_veh='".$immat."'";
			$sql .= " WHERE date_format(t.dt_hr_take,'%Y-%m') = '" . dol_print_date($firstday_month,'%Y-%m') . "'";
			$sql .= " AND  t.km_declare IS NOT NULL AND t.km_declare<>0";
			$sql .= " ORDER BY t.dt_hr_take asc ";
			$sql .= "LIMIT 1 ";
			
			dol_syslog ( get_class ( $this ) . "::fetch_report_km sql=" . $sql, LOG_DEBUG );
			$resql = $this->db->query ( $sql );
			if ($resql) {
				$obj = $this->db->fetch_object ( $resql );
				$arry_first_km_month[$key]=$obj->km_declare;
			} else {
				$this->error = "Error " . $this->db->lasterror ();
				dol_syslog ( get_class ( $this ) . "::fetch_report_km " . $this->error, LOG_ERR );
				return - 1;
			}
		}
		
		//Get last km declare on this period
		foreach($arry_first_km_month as $key=>$val) {
			$firstday_month=dol_mktime(0, 0, 0, $key, 1, $year);
			$sql = "SELECT";
			$sql .= " t.km_declare";
			$sql .= " FROM " . MAIN_DB_PREFIX . "consogazoil_vehtake as t";
			$sql .= " INNER JOIN " . MAIN_DB_PREFIX . "consogazoil_vehicule as veh ON veh.rowid=t.fk_vehicule AND veh.immat_veh='".$immat."'";
			$sql .= " WHERE date_format(t.dt_hr_take,'%Y-%m') = '" . dol_print_date($firstday_month,'%Y-%m') . "'";
			$sql .= " AND  t.km_declare IS NOT NULL AND t.km_declare<>0";
			$sql .= " ORDER BY t.dt_hr_take desc ";
			$sql .= "LIMIT 1 ";
				
			dol_syslog ( get_class ( $this ) . "::fetch_report_km sql=" . $sql, LOG_DEBUG );
			$resql = $this->db->query ( $sql );
			if ($resql) {
				$obj = $this->db->fetch_object ( $resql );
				$arry_last_km_month[$key]=$obj->km_declare;
			} else {
				$this->error = "Error " . $this->db->lasterror ();
				dol_syslog ( get_class ( $this ) . "::fetch_report_km " . $this->error, LOG_ERR );
				return - 1;
			}
		}
		
		//Populate with 0 if for each month all array
		for ($month=1;$month<=12;$month++) {
			if (empty($arry_first_km_month[$month])) $arry_first_km_month[$month]=0;
			if (empty($arry_last_km_month[$month])) $arry_last_km_month[$month]=0;
		}
		
		
		$km=0;
		for ($month=1;$month<=12;$month++) {
				
			$debug_string=' $immat='.$immat;
			$debug_string.=' $arry_first_km_month['.$month.']=' . $arry_first_km_month[$month];
			$debug_string.=' $arry_last_km_month['.$month.']=' . $arry_last_km_month[$month];
			dol_syslog ( get_class ( $this ) . '::fetch_report_km '.$debug_string, LOG_DEBUG );
			
			
			$km+=$arry_last_km_month[$month]-$arry_first_km_month[$month];
			$this->lines_report[$month]=$arry_last_km_month[$month]-$arry_first_km_month[$month];
			
			if (!empty($arry_last_km_month[$month])) {
				$lastkmknow=$arry_last_km_month[$month];
			}
			
		}
		
		//Km Avg		
		$this->lines_report[13]=price2num($km/12,2,1);
		
		//Last km know
		$this->lines_report[14]=$lastkmknow;
		
		return 1;
	}
	
	/**
	 * Load array to display in reports
	 *
	 * @param int		$year		Year filter
	 * @param string	$idservice	Service
	 * @return int <0 if KO, >0 if OK
	 */
	function fetch_report_km_service($year,$idservice) {
	
		global $conf,$langs;
	
		//This array will be populated as report
		//$this->lines_report[1]=km January
		//$this->lines_report[2]=km Febuary
		//...
		$this->lines_report=array();
	
		$arry_first_km_month=array();
		$arry_last_km_month=array();
	
	
		//Populate with 0 if for each month all array
		for ($month=1;$month<=12;$month++) {
			$arry_first_km_month[$month]=0;
			$arry_last_km_month[$month]=0;
		}
	
	
		//formula to calculate km per month
		//(last km month - first km month)
	
	
		//Get first km declare on this period
		foreach($arry_first_km_month as $key=>$val) {
			$firstday_month=dol_mktime(0, 0, 0, $key, 1, $year);
			$sql = "SELECT";
			$sql .= " t.km_declare";
			$sql .= " FROM " . MAIN_DB_PREFIX . "consogazoil_vehtake as t";
			$sql .= " INNER JOIN " . MAIN_DB_PREFIX . "consogazoil_vehicule as veh ON veh.rowid=t.fk_vehicule";
			if (!empty($idservice))$sql .= " INNER JOIN " . MAIN_DB_PREFIX . "consogazoil_vehiculeservice as servveh ON servveh.fk_vehicule=t.fk_vehicule";
			if (!empty($idservice))$sql .= " INNER JOIN " . MAIN_DB_PREFIX . "consogazoil_service as serv ON serv.rowid=servveh.fk_service AND serv.rowid=".$idservice;
			$sql .= " WHERE date_format(t.dt_hr_take,'%Y-%m') = '" . dol_print_date($firstday_month,'%Y-%m') . "'";
			if (!empty($idservice))$sql .= " AND t.dt_hr_take BETWEEN servveh.date_start AND servveh.date_end";
			$sql .= " AND  t.km_declare IS NOT NULL AND t.km_declare<>0";
			$sql .= " ORDER BY t.dt_hr_take asc ";
			$sql .= "LIMIT 1 ";
				
			dol_syslog ( get_class ( $this ) . "::fetch_report_km_service sql=" . $sql, LOG_DEBUG );
			$resql = $this->db->query ( $sql );
			if ($resql) {
				$obj = $this->db->fetch_object ( $resql );
				$arry_first_km_month[$key]=$obj->km_declare;
			} else {
				$this->error = "Error " . $this->db->lasterror ();
				dol_syslog ( get_class ( $this ) . "::fetch_report_km_service " . $this->error, LOG_ERR );
				return - 1;
			}
		}
	
		//Get last km declare on this period
		foreach($arry_first_km_month as $key=>$val) {
			$firstday_month=dol_mktime(0, 0, 0, $key, 1, $year);
			$sql = "SELECT";
			$sql .= " t.km_declare";
			$sql .= " FROM " . MAIN_DB_PREFIX . "consogazoil_vehtake as t";
			$sql .= " INNER JOIN " . MAIN_DB_PREFIX . "consogazoil_vehicule as veh ON veh.rowid=t.fk_vehicule";
			if (!empty($idservice))$sql .= " INNER JOIN " . MAIN_DB_PREFIX . "consogazoil_vehiculeservice as servveh ON servveh.fk_vehicule=t.fk_vehicule";
			if (!empty($idservice))$sql .= " INNER JOIN " . MAIN_DB_PREFIX . "consogazoil_service as serv ON serv.rowid=servveh.fk_service AND serv.rowid=".$idservice;
			$sql .= " WHERE date_format(t.dt_hr_take,'%Y-%m') = '" . dol_print_date($firstday_month,'%Y-%m') . "'";
			if (!empty($idservice))$sql .= " AND t.dt_hr_take BETWEEN servveh.date_start AND servveh.date_end";
			$sql .= " AND  t.km_declare IS NOT NULL AND t.km_declare<>0";
			$sql .= " ORDER BY t.dt_hr_take desc ";
			$sql .= "LIMIT 1 ";
	
			dol_syslog ( get_class ( $this ) . "::fetch_report_km_service sql=" . $sql, LOG_DEBUG );
			$resql = $this->db->query ( $sql );
			if ($resql) {
				$obj = $this->db->fetch_object ( $resql );
				$arry_last_km_month[$key]=$obj->km_declare;
			} else {
				$this->error = "Error " . $this->db->lasterror ();
				dol_syslog ( get_class ( $this ) . "::fetch_report_km_service " . $this->error, LOG_ERR );
				return - 1;
			}
		}
	
		//Populate with 0 if for each month all array
		for ($month=1;$month<=12;$month++) {
			if (empty($arry_first_km_month[$month])) $arry_first_km_month[$month]=0;
			if (empty($arry_last_km_month[$month])) $arry_last_km_month[$month]=0;
		}
	
	
		$km=0;
		for ($month=1;$month<=12;$month++) {
	
			$debug_string=' $immat='.$immat;
			$debug_string.=' $arry_first_km_month['.$month.']=' . $arry_first_km_month[$month];
			$debug_string.=' $arry_last_km_month['.$month.']=' . $arry_last_km_month[$month];
			dol_syslog ( get_class ( $this ) . '::fetch_report_km_service '.$debug_string, LOG_DEBUG );
				
				
			$km+=$arry_last_km_month[$month]-$arry_first_km_month[$month];
			$this->lines_report[$month]=$arry_last_km_month[$month]-$arry_first_km_month[$month];
				
			if (!empty($arry_last_km_month[$month])) {
				$lastkmknow=$arry_last_km_month[$month];
			}
				
		}
	
		//Km Avg
		$this->lines_report[13]=price2num($km/12,2,1);
	
		return 1;
	}
	
}


class ConsogazoilVehTakeLine {
	var $id;
	var $entity;
	var $fk_vehicule;
	var $fk_station;
	var $fk_driver;
	var $volume;
	var $dt_hr_take;
	var $km_declare;
	var $km_controle;
	var $datec = '';
	var $tms = '';
	var $fk_user_creat;
	var $fk_user_modif;
	var $import_key;
	var $conso_calc;
	function __construct() {
		return 1;
	}
}