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
 * \file /consogazoil/class/consogazoilvehiculeservice.class.php
 * \ingroup consogazoil
 */

// Put here all includes required by your class file
require_once (DOL_DOCUMENT_ROOT . "/core/class/commonobject.class.php");
// require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
// require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");

/**
 * Put here description of your class
 */
class ConsogazoilVehiculeService extends CommonObject {
	var $db; // !< To store db handler
	var $error; // !< To return error code (or message)
	var $errors = array (); // !< To return several error codes (or messages)
	var $element = 'consogazoilvehiculeservice'; // !< Id that identify managed objects
	var $table_element = 'consogazoil_vehiculeservice'; // !< Name of table without prefix where object is stored
	protected $ismultientitymanaged = 1; // 0=No test on entity, 1=Test with field entity, 2=Test with link by societe
	var $id;
	var $entity;
	var $fk_vehicule;
	var $fk_service;
	var $date_start = '';
	var $date_end = '';
	var $datec = '';
	var $tms = '';
	var $fk_user_creat;
	var $fk_user_modif;
	var $import_key;
	var $lines = array ();
	
	/**
	 * Constructor
	 *
	 * @param DoliDb $db Database handler
	 */
	function __construct($db) {
		$this->db = $db;
		return 1;
	}
	
	/**
	 * Create object into database
	 *
	 * @param User $user User that creates
	 * @param int $notrigger 0=launch triggers after, 1=disable triggers
	 * @return int <0 if KO, Id of created object if OK
	 */
	function create($user, $notrigger = 0) {
		global $conf, $langs;
		$error = 0;
		
		// Clean parameters
		
		if (isset($this->entity))
			$this->entity = trim($this->entity);
		if (isset($this->fk_vehicule))
			$this->fk_vehicule = trim($this->fk_vehicule);
		if (isset($this->fk_service))
			$this->fk_service = trim($this->fk_service);
		if (isset($this->fk_user_creat))
			$this->fk_user_creat = trim($this->fk_user_creat);
		if (isset($this->fk_user_modif))
			$this->fk_user_modif = trim($this->fk_user_modif);
		if (isset($this->import_key))
			$this->import_key = trim($this->import_key);
			
			// Check parameters
			// Put here code to add control on parameters values
			
		// Insert request
		$sql = "INSERT INTO " . MAIN_DB_PREFIX . "consogazoil_vehiculeservice(";
		
		$sql .= "entity,";
		$sql .= "fk_vehicule,";
		$sql .= "fk_service,";
		$sql .= "date_start,";
		$sql .= "date_end,";
		$sql .= "datec,";
		$sql .= "fk_user_creat,";
		$sql .= "fk_user_modif,";
		$sql .= "import_key";
		
		$sql .= ") VALUES (";
		
		$sql .= " " . $conf->entity . ",";
		$sql .= " " . (! isset($this->fk_vehicule) ? 'NULL' : "'" . $this->fk_vehicule . "'") . ",";
		$sql .= " " . (! isset($this->fk_service) ? 'NULL' : "'" . $this->fk_service . "'") . ",";
		$sql .= " " . (! isset($this->date_start) || dol_strlen($this->date_start) == 0 ? 'NULL' : $this->db->idate($this->date_start)) . ",";
		$sql .= " " . (! isset($this->date_end) || dol_strlen($this->date_end) == 0 ? 'NULL' : $this->db->idate($this->date_end)) . ",";
		$sql .= "'" . $this->db->idate(dol_now()) . "',";
		$sql .= " " . $user->id . ",";
		$sql .= " " . $user->id . ",";
		$sql .= " " . (! isset($this->import_key) ? 'NULL' : "'" . $this->db->escape($this->import_key) . "'") . "";
		
		$sql .= ")";
		
		$this->db->begin();
		
		dol_syslog(get_class($this) . "::create sql=" . $sql, LOG_DEBUG);
		$resql = $this->db->query($sql);
		if (! $resql) {
			$error ++;
			$this->errors[] = "Error " . $this->db->lasterror();
		}
		
		if (! $error) {
			$this->id = $this->db->last_insert_id(MAIN_DB_PREFIX . "consogazoil_vehiculeservice");
			
			if (empty($conf->global->MAIN_EXTRAFIELDS_DISABLED)) {
				$result = $this->insertExtraFields();
				if ($result < 0) {
					$error ++;
				}
			}
			
			if (! $notrigger) {
				// Uncomment this and change MYOBJECT to your own tag if you
				// want this action calls a trigger.
				
				// // Call triggers
				// include_once DOL_DOCUMENT_ROOT . '/core/class/interfaces.class.php';
				// $interface=new Interfaces($this->db);
				// $result=$interface->run_triggers('MYOBJECT_CREATE',$this,$user,$langs,$conf);
				// if ($result < 0) { $error++; $this->errors=$interface->errors; }
				// // End call triggers
			}
		}
		
		// Commit or rollback
		if ($error) {
			foreach ( $this->errors as $errmsg ) {
				dol_syslog(get_class($this) . "::create " . $errmsg, LOG_ERR);
				$this->error .= ($this->error ? ', ' . $errmsg : $errmsg);
			}
			$this->db->rollback();
			return - 1 * $error;
		} else {
			$this->db->commit();
			return $this->id;
		}
	}
	
	/**
	 * Load object in memory from the database
	 *
	 * @param int $id Id object
	 * @return int <0 if KO, >0 if OK
	 */
	function fetch($id) {
		global $langs;
		$sql = "SELECT";
		$sql .= " t.rowid,";
		
		$sql .= " t.entity,";
		$sql .= " t.fk_vehicule,";
		$sql .= " t.fk_service,";
		$sql .= " t.date_start,";
		$sql .= " t.date_end,";
		$sql .= " t.datec,";
		$sql .= " t.tms,";
		$sql .= " t.fk_user_creat,";
		$sql .= " t.fk_user_modif,";
		$sql .= " t.import_key";
		
		$sql .= " FROM " . MAIN_DB_PREFIX . "consogazoil_vehiculeservice as t";
		$sql .= " WHERE t.rowid = " . $id;
		
		dol_syslog(get_class($this) . "::fetch sql=" . $sql, LOG_DEBUG);
		$resql = $this->db->query($sql);
		if ($resql) {
			if ($this->db->num_rows($resql)) {
				$obj = $this->db->fetch_object($resql);
				
				$this->id = $obj->rowid;
				
				$this->entity = $obj->entity;
				$this->fk_vehicule = $obj->fk_vehicule;
				$this->fk_service = $obj->fk_service;
				$this->date_start = $this->db->jdate($obj->date_start);
				$this->date_end = $this->db->jdate($obj->date_end);
				$this->datec = $this->db->jdate($obj->datec);
				$this->tms = $this->db->jdate($obj->tms);
				$this->fk_user_creat = $obj->fk_user_creat;
				$this->fk_user_modif = $obj->fk_user_modif;
				$this->import_key = $obj->import_key;
				
				$extrafields = new ExtraFields($this->db);
				$extralabels = $extrafields->fetch_name_optionals_label($this->table_element, true);
				if (count($extralabels) > 0) {
					$this->fetch_optionals($this->id, $extralabels);
				}
			}
			$this->db->free($resql);
			
			return 1;
		} else {
			$this->error = "Error " . $this->db->lasterror();
			dol_syslog(get_class($this) . "::fetch " . $this->error, LOG_ERR);
			return - 1;
		}
	}
	
	/**
	 * Update object into database
	 *
	 * @param User $user User that modifies
	 * @param int $notrigger 0=launch triggers after, 1=disable triggers
	 * @return int <0 if KO, >0 if OK
	 */
	function update($user = 0, $notrigger = 0) {
		global $conf, $langs;
		$error = 0;
		
		// Clean parameters
		
		if (isset($this->entity))
			$this->entity = trim($this->entity);
		if (isset($this->fk_vehicule))
			$this->fk_vehicule = trim($this->fk_vehicule);
		if (isset($this->fk_service))
			$this->fk_service = trim($this->fk_service);
		if (isset($this->fk_user_creat))
			$this->fk_user_creat = trim($this->fk_user_creat);
		if (isset($this->fk_user_modif))
			$this->fk_user_modif = trim($this->fk_user_modif);
		if (isset($this->import_key))
			$this->import_key = trim($this->import_key);
			
			// Check parameters
			// Put here code to add a control on parameters values
			
		// Update request
		$sql = "UPDATE " . MAIN_DB_PREFIX . "consogazoil_vehiculeservice SET";
		
		$sql .= " entity=" . $conf->entity . ",";
		$sql .= " fk_vehicule=" . (isset($this->fk_vehicule) ? $this->fk_vehicule : "null") . ",";
		$sql .= " fk_service=" . (isset($this->fk_service) ? $this->fk_service : "null") . ",";
		$sql .= " date_start=" . (dol_strlen($this->date_start) != 0 ? "'" . $this->db->idate($this->date_start) . "'" : 'null') . ",";
		$sql .= " date_end=" . (dol_strlen($this->date_end) != 0 ? "'" . $this->db->idate($this->date_end) . "'" : 'null') . ",";
		$sql .= " fk_user_modif=" . $user->id . ",";
		$sql .= " import_key=" . (isset($this->import_key) ? "'" . $this->db->escape($this->import_key) . "'" : "null") . "";
		
		$sql .= " WHERE rowid=" . $this->id;
		
		$this->db->begin();
		
		dol_syslog(get_class($this) . "::update sql=" . $sql, LOG_DEBUG);
		$resql = $this->db->query($sql);
		if (! $resql) {
			$error ++;
			$this->errors[] = "Error " . $this->db->lasterror();
		}
		
		if (! $error) {
			
			if (empty($conf->global->MAIN_EXTRAFIELDS_DISABLED)) {
				$result = $this->insertExtraFields();
				if ($result < 0) {
					$error ++;
				}
			}
		}
		
		if (! $error) {
			if (! $notrigger) {
				// Uncomment this and change MYOBJECT to your own tag if you
				// want this action calls a trigger.
				
				// // Call triggers
				// include_once DOL_DOCUMENT_ROOT . '/core/class/interfaces.class.php';
				// $interface=new Interfaces($this->db);
				// $result=$interface->run_triggers('MYOBJECT_MODIFY',$this,$user,$langs,$conf);
				// if ($result < 0) { $error++; $this->errors=$interface->errors; }
				// // End call triggers
			}
		}
		
		// Commit or rollback
		if ($error) {
			foreach ( $this->errors as $errmsg ) {
				dol_syslog(get_class($this) . "::update " . $errmsg, LOG_ERR);
				$this->error .= ($this->error ? ', ' . $errmsg : $errmsg);
			}
			$this->db->rollback();
			return - 1 * $error;
		} else {
			$this->db->commit();
			return 1;
		}
	}
	
	/**
	 * Delete object in database
	 *
	 * @param User $user User that deletes
	 * @param int $notrigger 0=launch triggers after, 1=disable triggers
	 * @return int <0 if KO, >0 if OK
	 */
	function delete($user, $notrigger = 0) {
		global $conf, $langs;
		$error = 0;
		
		$this->db->begin();
		
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
			$sql = "DELETE FROM " . MAIN_DB_PREFIX . "consogazoil_vehiculeservice";
			$sql .= " WHERE rowid=" . $this->id;
			
			dol_syslog(get_class($this) . "::delete sql=" . $sql);
			$resql = $this->db->query($sql);
			if (! $resql) {
				$error ++;
				$this->errors[] = "Error " . $this->db->lasterror();
			}
		}
		if (! $error) {
			$sql = "DELETE FROM " . MAIN_DB_PREFIX . "consogazoil_vehiculeservice_extrafields";
			$sql .= " WHERE fk_object=" . $this->id;
				
			dol_syslog(get_class($this) . "::delete sql=" . $sql);
			$resql = $this->db->query($sql);
			if (! $resql) {
				$error ++;
				$this->errors[] = "Error " . $this->db->lasterror();
			}
		}
		
		// Commit or rollback
		if ($error) {
			foreach ( $this->errors as $errmsg ) {
				dol_syslog(get_class($this) . "::delete " . $errmsg, LOG_ERR);
				$this->error .= ($this->error ? ', ' . $errmsg : $errmsg);
			}
			$this->db->rollback();
			return - 1 * $error;
		} else {
			$this->db->commit();
			return 1;
		}
	}
	
	/**
	 * Load an object from its id and create a new one in database
	 *
	 * @param int $fromid Id of object to clone
	 * @return int New id of clone
	 */
	function createFromClone($fromid) {
		global $user, $langs;
		
		$error = 0;
		
		$object = new Consogazoilvehiculeservice($this->db);
		
		$this->db->begin();
		
		// Load source object
		$object->fetch($fromid);
		$object->id = 0;
		$object->statut = 0;
		
		// Clear fields
		// ...
		
		// Create clone
		$result = $object->create($user);
		
		// Other options
		if ($result < 0) {
			$this->error = $object->error;
			$error ++;
		}
		
		if (! $error) {
		}
		
		// End
		if (! $error) {
			$this->db->commit();
			return $object->id;
		} else {
			$this->db->rollback();
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
		$this->fk_service = '';
		$this->date_start = '';
		$this->date_end = '';
		$this->datec = '';
		$this->tms = '';
		$this->fk_user_creat = '';
		$this->fk_user_modif = '';
		$this->import_key = '';
	}
	
	/**
	 * Load object in memory from the database
	 *
	 * @param string $sortorder sort order
	 * @param string $sortfield sort field
	 * @param int $limit limit page
	 * @param int $offset page
	 * @param int $arch display archive or not
	 * @param array $filter filter output
	 * @return int <0 if KO, >0 if OK
	 */
	function fetch_all($sortorder = 'DESC', $sortfield = 't.ref', $limit, $offset, $filter = '') {
		global $langs;
		
		$sql = "SELECT";
		$sql .= " t.rowid,";
		
		$sql .= " t.entity,";
		$sql .= " t.fk_vehicule,";
		$sql .= " t.fk_service,";
		$sql .= " t.date_start,";
		$sql .= " t.date_end,";
		$sql .= " t.datec,";
		$sql .= " t.tms,";
		$sql .= " t.fk_user_creat,";
		$sql .= " t.fk_user_modif,";
		$sql .= " t.import_key";
		
		$sql .= " FROM " . MAIN_DB_PREFIX . "consogazoil_vehiculeservice as t";
		$sql .= " WHERE t.entity IN (" . getEntity($this->element, 1) . ")";
		// Manage filter
		if (is_array($filter) && count($filter) > 0) {
			foreach ( $filter as $key => $value ) {
				if (strpos($key, 'date')) {
					$sql .= ' AND ' . $key . ' = \'' . $this->db->idate($value) . '\'';
				} else if (strpos($key, 'fk_')) {
					$sql .= ' AND ' . $key . ' = ' . $value;
				} else {
					$sql .= ' AND ' . $key . ' LIKE \'%' . $value . '%\'';
				}
			}
		}
		$sql .= " ORDER BY " . $sortfield . " " . $sortorder;
		if (! empty($limit))
			$sql .= $this->db->plimit($limit + 1, $offset);
		
		dol_syslog(get_class($this) . "::fetch_all sql=" . $sql, LOG_DEBUG);
		$resql = $this->db->query($sql);
		if ($resql) {
			$this->lines = array ();
			
			$num = $this->db->num_rows($resql);
			while ( $obj = $this->db->fetch_object($resql) ) {
				$line = new ConsogazoilVehiculeServiceLine();
				
				$line->id = $obj->rowid;
				
				$line->entity = $obj->entity;
				$line->fk_vehicule = $obj->fk_vehicule;
				$line->fk_service = $obj->fk_service;
				$line->date_start = $this->db->jdate($obj->date_start);
				$line->date_end = $this->db->jdate($obj->date_end);
				$line->datec = $this->db->jdate($obj->datec);
				$line->tms = $this->db->jdate($obj->tms);
				$line->fk_user_creat = $obj->fk_user_creat;
				$line->fk_user_modif = $obj->fk_user_modif;
				$line->import_key = $obj->import_key;
				
				$this->lines[] = $line;
			}
			$this->db->free($resql);
			
			return $num;
		} else {
			$this->error = "Error " . $this->db->lasterror();
			dol_syslog(get_class($this) . "::fetch_all " . $this->error, LOG_ERR);
			return - 1;
		}
	}
}
class ConsogazoilVehiculeServiceLine {
	var $id;
	var $entity;
	var $fk_vehicule;
	var $fk_service;
	var $date_start = '';
	var $date_end = '';
	var $datec = '';
	var $tms = '';
	var $fk_user_creat;
	var $fk_user_modif;
	var $import_key;
	function __construct() {
		return 1;
	}
}
