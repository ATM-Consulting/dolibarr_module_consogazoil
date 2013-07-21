<?php
/* Consomation Gazoil 
* Copyright (C) 2013       Florian Henry		<florian.henry@open-concept.pro>
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
* or see http://www.gnu.org/
*/

/**
 *		\file       consogazoil/class/consogazoilimport.class.php
*		\ingroup    consogazoil
*		\brief      File to load import files with CSV format
*/


/**
 *	Class to import consogazoil CSV specific files
*/
class ConsogazoilImport 
{
	
	var $handle;
	
	var $lines=array();
	

	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)

	var $file;
	
	var $id;
	
	var $contrat; 
	var $carte_vehicule;  
	var $carte_driver; 
	var $code_produit; 
	var $produit; 
	var $volume_gaz; 
	var $dt_take; 
	var $hour_take; 
	var $country; 
	var $id_station; 
	var $label_station; 
	var $dt_invoice; 
	var $num_invoice; 
	var $tva_tx_invoice; 
	var $currency_take; 
	var $amount_ht_take; 
	var $amount_tva_take; 
	var $amount_ttc_take; 
	var $currency_payment; 
	var $amount_ht_payment; 
	var $amount_tva_payment; 
	var $amount_ttc_payment; 
	var $km_take;
	var $immat_veh;
	var $gear_type;
	
	var $veh_conflit;
	var $veh_conflit_action;
	var $station_conflit;
	var $station_conflit_action;
	var $driver_conflit;
	var $driver_conflit_action;
	
	var $pb_quality;
	
	/**
	 *	Constructor
	 *
	 *	@param	DoliDB		$db				Database handler
	 */
	function __construct($db)
	{
		global $conf,$langs;
		$this->db = $db;
	}
	
	/**
	 *	Open input file
	 *
	 *	@param	string	$file		Path of filename
	 *	@return	int					<0 if KO, >=0 if OK
	 */
	function import_open_file($file)
	{
		global $langs;
		$ret=1;
	
		dol_syslog(get_class($this)."::open_file file=".$file);
	
		ini_set('auto_detect_line_endings',1);	// For MAC compatibility
	
		$this->handle = fopen(dol_osencode($file), "r");
		if (! $this->handle)
		{
			$langs->load("errors");
			$this->error=$langs->trans("ErrorFailToOpenFile",$file);
			$ret=-1;
		}
		else
		{
			$this->file=$file;
		}
	
		return $ret;
	}
	
	/**
	 * 	Return array of next record in input file.
	 *
	 * 	@return		Array		Array of field values. Data are UTF8 encoded. [fieldpos] => (['val']=>val, ['type']=>-1=null,0=blank,1=string)
	 */
	function import_read_record()
	{
		global $conf;
	
		$arrayres=array();
		if (version_compare(phpversion(), '5.2') < 0)
		{
			$arrayres=fgetcsv($this->handle,100000,',','"');
		}
		else
		{
			$arrayres=fgetcsv($this->handle,100000,',','"','"');
		}
	
		//var_dump($this->handle);
		//var_dump($arrayres);exit;
		$newarrayres=array();
		if ($arrayres && is_array($arrayres) && count($arrayres)>0)
		{
			foreach($arrayres as $key => $val)
			{
				if (! empty($conf->global->IMPORT_CSV_FORCE_CHARSET))	// Forced charset
				{
					if (strtolower($conf->global->IMPORT_CSV_FORCE_CHARSET) == 'utf8')
					{
						$newarrayres[$key]['val']=$val;
						$newarrayres[$key]['type']=(dol_strlen($val)?1:-1);	// If empty we considere it's null
					}
					else
					{
						$newarrayres[$key]['val']=utf8_encode($val);
						$newarrayres[$key]['type']=(dol_strlen($val)?1:-1);	// If empty we considere it's null
					}
				}
				else	// Autodetect format (UTF8 or ISO)
				{
					if (utf8_check($val))
					{
						$newarrayres[$key]['val']=$val;
						$newarrayres[$key]['type']=(dol_strlen($val)?1:-1);	// If empty we considere it's null
					}
					else
					{
						$newarrayres[$key]['val']=utf8_encode($val);
						$newarrayres[$key]['type']=(dol_strlen($val)?1:-1);	// If empty we considere it's null
					}
				}
			}
	
			$this->col=count($newarrayres);
		}
	
		return $newarrayres;
	}
	
	/**
	 * 	Close file handle
	 *
	 *  @return	void
	 */
	function import_close_file()
	{
		fclose($this->handle);
		return 0;
	}
	
	/**
	 * 	Truncate temporary tables
	 *
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function truncate_temp_table()
	{
		global $conf, $langs;
		$error=0;
		
		$sql="TRUNCATE TABLE ".MAIN_DB_PREFIX."consogazoil_tmp";
		
		dol_syslog(get_class($this)."::truncate_temp_table sql=".$sql);
		$resql = $this->db->query($sql);
		if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }
		
		
		// Commit or rollback
		if ($error)
		{
			foreach($this->errors as $errmsg)
			{
				dol_syslog(get_class($this)."::truncate_temp_table ".$errmsg, LOG_ERR);
				$this->error.=($this->error?', '.$errmsg:$errmsg);
			}
			$this->db->rollback();
			return -1*$error;
		}
		else
		{
			$this->db->commit();
			return 1;
		}		
	}
	
	/**
	 * 	Import lines into temporary table
	 *
	 *  @param	array	$linearray	Line array to import into tables
	 *  @return int          		<0 if KO, >0 if OK
	 */
	function import_file_in_temp_table($linearray)
	{
		global $conf, $langs;
		$error=0;
		
		// Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."consogazoil_tmp(";
		
		$sql.= "contrat,";
		$sql.= "carte_vehicule,";
		$sql.= "carte_driver,";
		$sql.= "code_produit,";
		$sql.= "produit,";
		$sql.= "volume_gaz,";
		$sql.= "dt_take,";
		$sql.= "hour_take,";
		$sql.= "country,";
		$sql.= "id_station,";
		$sql.= "label_station,";
		$sql.= "dt_invoice,";
		$sql.= "num_invoice,";
		$sql.= "tva_tx_invoice,";
		$sql.= "currency_take,";
		$sql.= "amount_ht_take,";
		$sql.= "amount_tva_take,";
		$sql.= "amount_ttc_take,";
		$sql.= "currency_payment,";
		$sql.= "amount_ht_payment,";
		$sql.= "amount_tva_payment,";
		$sql.= "amount_ttc_payment,";
		$sql.= "km_take,";
		$sql.= "immat_veh,";
		$sql.= "gear_type";
		
		$sql.= ") VALUES (";
		
		foreach ($linearray as $val) {
			$sql.= " ".(($val['type']==-1)?'NULL':"'".$this->db->escape($val['val'])."'").",";
		}
		//Remove last coma
		$sql=substr($sql, 0, -1);
		
		$sql.= ")";
		
		$this->db->begin();
		
		dol_syslog(get_class($this)."::import_file_in_temp_table sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }
		
		if (! $error)
		{
			$this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."importb2s_tmp");
		}
		
		// Commit or rollback
		if ($error)
		{
			foreach($this->errors as $errmsg)
			{
				dol_syslog(get_class($this)."::import_file_in_temp_table ".$errmsg, LOG_ERR);
				$this->error.=($this->error?', '.$errmsg:$errmsg);
			}
			$this->db->rollback();
			return -1*$error;
		}
		else
		{
			$this->db->commit();
			return $this->id;
		}
		
		
	}
	
	/**
	 * 	Check data consistancy
	 *
	 *  @return int          	<0 if KO, >0 if OK 
	 */
	function import_check_data() {
		
		global $langs;
		
		$error=0;
		
		$sql = "SELECT";
		$sql.= "t.contrat,";
		$sql.= "t.carte_vehicule,";
		$sql.= "t.carte_driver,";
		$sql.= "t.code_produit,";
		$sql.= "t.produit,";
		$sql.= "t.volume_gaz,";
		$sql.= "t.dt_take,";
		$sql.= "t.hour_take,";
		$sql.= "t.country,";
		$sql.= "t.id_station,";
		$sql.= "t.label_station,";
		$sql.= "t.dt_invoice,";
		$sql.= "t.num_invoice,";
		$sql.= "t.tva_tx_invoice,";
		$sql.= "t.currency_take,";
		$sql.= "t.amount_ht_take,";
		$sql.= "t.amount_tva_take,";
		$sql.= "t.amount_ttc_take,";
		$sql.= "t.currency_payment,";
		$sql.= "t.amount_ht_payment,";
		$sql.= "t.amount_tva_payment,";
		$sql.= "t.amount_ttc_payment,";
		$sql.= "t.km_take,";
		$sql.= "t.immat_veh,";
		$sql.= "t.gear_type";

        $sql.= " FROM ".MAIN_DB_PREFIX."consogazoil_tmp as t";
		
		dol_syslog(get_class($this)."::import_check_data sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			
			if ($num)
			{
				while (($i < $num) && $error==0)
				{
					$arrayres = $this->db->fetch_array($resql);
					$id=$arrayres['rowid'];
					
					$conflitveh=false;
					$conflitvehrowid=0;
					
					$conflitstation=false;
					$conflitstationrowid=0;
					
					$conflitdriver=false;
					$conflitdriverrowid=0;
					
					$addline=false;

					//Check if vehicule exists with same immat but different ref
					$sqlveh="SELECT rowid,ref,immat_veh FROM ".MAIN_DB_PREFIX."consogazoil_vehicule ";
					$sqlveh.=" WHERE immat_veh='".trim($arrayres['immat_veh'])."'";
					$sqlveh.=" AND ref <> '".$this->db->escape(trim($arrayres['carte_vehicule']))."'";
					
					dol_syslog(get_class($this)."::import_check_data vehicule sql=".$sqlsoc, LOG_DEBUG);
					$resqlveh=$this->db->query($sqlveh);
					if ($resqlveh){
						$numveh = $this->db->num_rows($resqlveh);
						if ($numveh>0) {
							$addline=true;
							$conflitveh=true;
							$objveh = $this->db->fetch_object($resqlveh);
							$conflitvehrowid=$objveh->rowid;
						}
						$this->db->free($resqlveh);
					}else {
						$this->error="Error ".$this->db->lasterror();
						dol_syslog(get_class($this)."::import_check_data soc ".$this->error, LOG_ERR);
						return -1;
					}
					
					//Check if station exists with same name but different ref
					$sqlstation="SELECT rowid,ref,name FROM ".MAIN_DB_PREFIX."consogazoil_station ";
					$sqlstation.=" WHERE ref='".trim($arrayres['id_station'])."'";
					$sqlstation.=" AND name <> '".$this->db->escape(trim($arrayres['label_station']))."'";
						
					dol_syslog(get_class($this)."::import_check_data station sql=".$sqlsoc, LOG_DEBUG);
					$resqlstation=$this->db->query($sqlstation);
					if ($resqlstation){
						$numveh = $this->db->num_rows($resqlstation);
						if ($numveh>0) {
							$addline=true;
							$conflitstation=true;
							$objstation = $this->db->fetch_object($resqlstation);
							$conflitstationrowid=$objstation->rowid;
						}
						$this->db->free($resqlstation);
					}else {
						$this->error="Error ".$this->db->lasterror();
						dol_syslog(get_class($this)."::import_check_data station ".$this->error, LOG_ERR);
						return -1;
					}
					
					
					//Test product code
					$msg_product_code='';
					if (!empty($arrayres['code_produit'])) {
						if ($arrayres['code_produit']!='03') {
							$addline=false;
							$msg_product_code=$langs->trans("ConsoGazImportQual",$langs->trans("ConsoGazImportCdProd"));
						}
					}
					
					//Test quality volume
					$msg_vol='';
					if (!empty($arrayres['volume_gaz'])) {
						if (!is_numeric(trim($arrayres['volume_gaz']))) {
							$msg_vol=$langs->trans("ConsoGazImportQual",$langs->transnoentities("ConsoGazImportMustBeNumeric"),$arrayres['volume_gaz']);
						}
						
					}
					
					//Test date take
					$msg_datetake='';
					if (!empty($arrayres['dt_take'])) {
						if (!is_numeric(trim($arrayres['dt_take']))) {
							$msg_datetake=$langs->trans("ConsoGazImportQual",$langs->transnoentities("ConsoGazImportQualDtFormat"),$arrayres['dt_take']);
						}else {
							$year_dt=substr($arrayres['dt_take'],0,4);
							$month_dt=substr($arrayres['dt_take'],3,2);
							$day_dt=substr($arrayres['dt_take'],-2);
							if(dol_mktime(0, 0,0, $day_dt, $month_dt, $year_dt)=='') {
								$msg_datetake=$langs->trans("ConsoGazImportQual",$langs->trans("ConsoGazImportQualDtFormat",$arrayres['dt_take']));
							}
						}
					}
					
					//Test hour take
					$msg_hour='';
					if (!empty($arrayres['hour_take'])) {
						if (!is_numeric(trim($arrayres['hour_take']))) {
							$msg_hour=$langs->trans("ConsoGazImportQual",$langs->transnoentities("ConsoGazImportQualHourFormat"),$arrayres['hour_take']);
							$addline=false;
						}else {
							if (count($arrayres['hour_take'])!=4) {
								$msg_hour=$langs->trans("ConsoGazImportQual",$langs->trans("ConsoGazImportQualHourFormat",$arrayres['hour_take']));
							} else {
								$hour_dt=substr($arrayres['hour_take'],0,2);
								$min_dt=substr($arrayres['hour_take'],-2);
								if ($hour_dt>23) {
									$msg_hour=$langs->trans("ConsoGazImportQual",$langs->trans("ConsoGazImportQualHourFormat",$arrayres['hour_take']));
								}
								if ($min_dt>59) {
									$msg_hour=$langs->trans("ConsoGazImportQual",$langs->trans("ConsoGazImportQualHourFormat",$arrayres['hour_take']));
								}
							}
						}
					}
					
					
					if ($addline) {

						$this->lines[$id] = new ConsogazoilImportLine();
						$this->lines[$id]->id=$id;
						
						$this->lines[$id]->conflitveh=$conflitveh;
						$this->lines[$id]->conflitvehrowid=$conflitvehrowid;
						
						$this->lines[$id]->conflitstation=$conflitcontact;
						$this->lines[$id]->conflitstationrowid=$conflitstationrowid;
						
						$msg_qualtity='';
						if (!empty($msg_product_code)) {$msg_qualtity.='- '.$msg_product_code."\n";}
						if (!empty($msg_vol)) {$msg_qualtity.='- '.$msg_vol."\n";}
						if (!empty($msg_datetake)) {$msg_qualtity.='- '.$msg_datetake."\n";}
						if (!empty($msg_hour)) {$msg_qualtity.='- '.$msg_hour."\n";}
						
						
						if (!empty($msg_qualtity)) {$this->lines[$id]->pb_quality=$msg_qualtity;}
						
						$this->lines[$id]->record=$arrayres;
					}
					
					$i++;
				}
			}
			
			$this->db->free($resql);
			
			$ret=$this->update_tmp_table_quality();
			if($ret<0) {
				dol_syslog(get_class($this)."::import_check_data ".$this->error, LOG_ERR);
				return -1;
			}
			
			return 1;
				
		}else {
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::import_check_data ".$this->error, LOG_ERR);
			return -1;
		}
	}
	
	/**
	 * 	Update temp table with quality test result
	 *
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function update_tmp_table_quality() {

		foreach ($this->lines as $line) {
			$sql = "UPDATE ".MAIN_DB_PREFIX."consogazoil_tmp SET";

			$sql.= " veh_conflit=".(isset($line->conflitvehrowid)?$this->db->escape($line->conflitvehrowid):"null").",";
			$sql.= " station_conflit=".(isset($line->conflitstationrowid)?$this->db->escape($line->conflitstationrowid):"null").",";

			
			if (isset($line->conflitvehaction)) {
				$sql.= " veh_conflit_action='".$line->conflitvehaction."',";
			}
			if (isset($line->conflitstationrowid)) {
				$sql.= " station_conflit_action='".$line->conflitstationaction."',";
			}
			
			$sql.= " pb_quality=".(isset($line->pb_quality)?"'".$this->db->escape($this->pb_quality)."'":"null")." ";
			
			$sql.= " WHERE rowid=".$line->id;
			
			$this->db->begin();
			
			dol_syslog(get_class($this)."::update_tmp_table_quality sql=".$sql, LOG_DEBUG);
			$resql = $this->db->query($sql);
			if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }
			// Commit or rollback
			if ($error)
			{
				foreach($this->errors as $errmsg)
				{
					dol_syslog(get_class($this)."::update_tmp_table_quality ".$errmsg, LOG_ERR);
					$this->error.=($this->error?', '.$errmsg:$errmsg);
				}
				$this->db->rollback();
			}
			else
			{
				$this->db->commit();
			}
		}
		
		if ($error) {
			return -1*$error;
		}else {
			return 1;
		}

	}
	
	/**
	 * 	Import Data
	 *
	 *  @param	object	$user	user tha do import data
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function import_data($user) {
		
		$nowyearmonth=dol_print_date(dol_now(),'%Y%m%d%H%M%S');
		
		require_once 'consogazoildriver.class.php';
		require_once 'consogazoilstation.class.php';
		require_once 'consogazoilvehicule.class.php';
		require_once 'consogazoilvehtake.class.php';
		
		$error=0;
		
		$sql = "SELECT";
		$sql.= "t.rowid,";
		$sql.= "t.contrat,";
		$sql.= "t.carte_vehicule,";
		$sql.= "t.carte_driver,";
		$sql.= "t.code_produit,";
		$sql.= "t.produit,";
		$sql.= "t.volume_gaz,";
		$sql.= "t.dt_take,";
		$sql.= "t.hour_take,";
		$sql.= "t.country,";
		$sql.= "t.id_station,";
		$sql.= "t.label_station,";
		$sql.= "t.dt_invoice,";
		$sql.= "t.num_invoice,";
		$sql.= "t.tva_tx_invoice,";
		$sql.= "t.currency_take,";
		$sql.= "t.amount_ht_take,";
		$sql.= "t.amount_tva_take,";
		$sql.= "t.amount_ttc_take,";
		$sql.= "t.currency_payment,";
		$sql.= "t.amount_ht_payment,";
		$sql.= "t.amount_tva_payment,";
		$sql.= "t.amount_ttc_payment,";
		$sql.= "t.km_take,";
		$sql.= "t.immat_veh,";
		$sql.= "t.gear_type";
		
		$sql.= " t.veh_conflit,";
		$sql.= " t.veh_conflit_action,";
		$sql.= " t.station_conflit,";
		$sql.= " t.station_conflit_action,";

		$sql.= " t.pb_quality";
		
		$sql.= " FROM ".MAIN_DB_PREFIX."consogazoil_tmp as t";
		
		dol_syslog(get_class($this)."::import_prepare_import_data sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
				
			if ($num)
			{	
				$i=0;
				while (($i < $num) && $error==0)
				{
					$arrayres = $this->db->fetch_array($resql);
					
					
					
					$i++;
				}
			}
			
			$this->db->free($resql);
			
			return 1;
				
		}else {
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::import_prepare_import_data ".$this->error, LOG_ERR);
			return -1;
		}
	}
}

/**
 *	Class to store lines treatement
 */
class ConsogazoilImportLine
{
	var $conflitveh;
	var $conflitvehaction;
	var $conflitvehrowid;
	
	var $conflitstation;
	var $conflitstationaction;
	var $conflitstationrowid;
	
	var $pb_quality;
	
	var $record;
}