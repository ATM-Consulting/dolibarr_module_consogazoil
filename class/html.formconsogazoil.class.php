<?php
/*
 * Copyright (C) 2013  Florian Henry   <florian.henry@open-concept.pro>
 * 
*
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
 * \file consogazoil/class/html.formconsogazoil.class.php
 * \brief Class for HML form
 */
class FormConsoGazoil extends Form {
	var $db;
	var $error;
	
	/**
	 * Constructor
	 * 
	 * @param DoliDB $db handler
	 */
	function __construct($db) {
		$this->db = $db;
		return 1;
	}
	
	/**
	 * Display select with Service available
	 * 
	 * @param int $selectid à preselectionner
	 * @param string $htmlname select field
	 * @return string select field
	 */
	function select_service($selectid, $htmlname = 'service') {
		global $conf, $user, $langs;
		
		require_once 'consogazoilservice.class.php';
		
		$out = '';
		
		$object = new ConsogazoilService ( $this->db );
		
		$num = $object->fetch_all ();
		
		if ($num >= 0) {
			$out .= '<select id="' . $htmlname . '" class="flat" name="' . $htmlname . '">';
			
			$i = 0;

			foreach ( $object->lines as $line ) {
				
				if ($selectid > 0 && $selectid == $line->id) {
					$out .= '<option value="' . $line->id . '" selected="selected">' . $line->label . '</option>';
				} else {
					$out .= '<option value="' . $line->id . '">' . $line->label . '</option>';
				}
			}

			$out .= '</select>';
		} else {
			setEventMessage ( $object->error, 'errors' );
		}
		
		return $out;
	}
	
	/**
	 * Display select with data available
	 *
	 * @param int $selectid à preselectionner
	 * @param string $htmlname select field
	 * @return string select field
	 */
	function select_vehicule($selectid, $htmlname = 'vehicule') {
		global $conf, $user, $langs;
	
		require_once 'consogazoilvehicule.class.php';
	
		$out = '';
	
		$object = new ConsogazoilVehicule ( $this->db );
	
		$num = $object->fetch_all ();
	
		if ($num >= 0) {
			$out .= '<select id="' . $htmlname . '" class="flat" name="' . $htmlname . '">';
				
			$i = 0;
	
			foreach ( $object->lines as $line ) {
			 if($line->activ == 1){
					if ($selectid > 0 && $selectid == $line->id) {
						$out .= '<option value="' . $line->id . '" selected="selected">' . $line->ref.'-'.$line->immat_veh . '</option>';
					} else {
						$out .= '<option value="' . $line->id . '">' .$line->ref.'-'.$line->immat_veh . '</option>';
					}
				}
			}
	
			$out .= '</select>';
		} else {
			setEventMessage ( $object->error, 'errors' );
		}
	
		return $out;
	}
	
	/**
	 * Display select with data available
	 *
	 * @param int $selectid à preselectionner
	 * @param string $htmlname select field
	 * @return string select field
	 */
	function select_station($selectid, $htmlname = 'station') {
		global $conf, $user, $langs;
	
		require_once 'consogazoilstation.class.php';
	
		$out = '';
	
		$object = new ConsogazoilStation ( $this->db );
	
		$num = $object->fetch_all ();
	
		if ($num >= 0) {
			$out .= '<select id="' . $htmlname . '" class="flat" name="' . $htmlname . '">';
	
			$i = 0;
	
			foreach ( $object->lines as $line ) {
	
				if ($selectid > 0 && $selectid == $line->id) {
					$out .= '<option value="' . $line->id . '" selected="selected">' . $line->ref.'-'.$line->name . '</option>';
				} else {
					$out .= '<option value="' . $line->id . '">' .$line->ref.'-'.$line->name . '</option>';
				}
			}
	
			$out .= '</select>';
		} else {
			setEventMessage ( $object->error, 'errors' );
		}
	
		return $out;
	}
	
	/**
	 * Display select with data available
	 *
	 * @param int $selectid à preselectionner
	 * @param string $htmlname select field
	 * @return string select field
	 */
	function select_driver($selectid, $htmlname = 'driver') {
		global $conf, $user, $langs;
	
		require_once 'consogazoildriver.class.php';
	
		$out = '';
	
		$object = new ConsogazoilDriver ( $this->db );
	
		$num = $object->fetch_all ();
	
		if ($num >= 0) {
			$out .= '<select id="' . $htmlname . '" class="flat" name="' . $htmlname . '">';
	
			$i = 0;
	
			foreach ( $object->lines as $line ) {
	
				if ($selectid > 0 && $selectid == $line->id) {
					$out .= '<option value="' . $line->id . '" selected="selected">' . $line->ref.'-'.$line->name . '</option>';
				} else {
					$out .= '<option value="' . $line->id . '">' .$line->ref.'-'.$line->name . '</option>';
				}
			}
	
			$out .= '</select>';
		} else {
			setEventMessage ( $object->error, 'errors' );
		}
	
		return $out;
	}
	
	/**
	 *	Return select filer with date of transaction
	 *
	 *  @param	string	$htmlname 		name of input
	 *  @param	string	$selectedkey	selected default value
	 *  @param	int		$custid 		customerid
	 *  @param	int 	$shopid 		shopid
	 *  @param	string 	$type 			'histoshop' or 'histocust' or ''
	 *	@return	string					HTML select input
	 */
	function select_date_filter($htmlname,$selectedkey,$type='take') {
	
		global $langs;
	
		$date_array=array();
	
		$sql='SELECT DISTINCT dt_hr_take from '.MAIN_DB_PREFIX.'consogazoil_vehtake ';
		$sql.=' ORDER BY dt_hr_take';
	
		dol_syslog(get_class($this)."::select_date_filter sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$i=0;
			$num = $this->db->num_rows($resql);
				
			while ($i<$num)
			{
				$obj = $this->db->fetch_object($resql);
	
				$date=$this->db->jdate($obj->dt_hr_take);
				$keydate=dol_print_date($date,'%Y-%m');
				$valdate=dol_print_date($date,'%b %Y');
	
				if (!array_key_exists($keydate,$date_array)) {
					$date_array[$keydate]=$valdate;
				}
	
				$i++;
			}
	
		}else {
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::select_date_filter ".$this->error, LOG_ERR);
			return -1;
		}
	
		if (count($date_array)>0) {
			$out='<SELECT name="'.$htmlname.'">';
			$out.='<OPTION value="">'.$langs->trans('ConsoGazAll').'</OPTION>';
			foreach ($date_array as $key=>$val) {
	
				$selected='';
				if ($selectedkey==$key) {
					$selected=' selected="selected" ';
				}
	
				$out.='<OPTION value="'.$key.'"'.$selected.'>'.$val.'</OPTION>';
			}
			$out.='</SELECT>';
		}
	
		return $out;
	}
	
	/**
	 *	Return select filer with date of transaction
	 *
	 *  @param	string	$htmlname 		name of input
	 *  @param	string	$selectedkey	selected default value
	 *  @param	int		$custid 		customerid
	 *  @param	int 	$shopid 		shopid
	 *  @param	string 	$type 			'histoshop' or 'histocust' or ''
	 *	@return	string					HTML select input
	 */
	function select_year_report($htmlname,$selectedkey) {
	
		global $langs;
	
		$date_array=array();
	
		$sql="SELECT DISTINCT date_format(dt_hr_take,'%Y') as yeardt from ".MAIN_DB_PREFIX."consogazoil_vehtake ";
		$sql.=" ORDER BY date_format(dt_hr_take,'%Y')";
	
		dol_syslog(get_class($this)."::select_year_report sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$i=0;
			$num = $this->db->num_rows($resql);
	
			while ($i<$num)
			{
				$obj = $this->db->fetch_object($resql);
	
				if (!array_key_exists($keydate,$date_array)) {
					$date_array[$obj->yeardt]=$obj->yeardt;
				}
	
				$i++;
			}
	
		}else {
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::select_year_report ".$this->error, LOG_ERR);
			return -1;
		}
	
		if (count($date_array)>0) {
			$out='<SELECT name="'.$htmlname.'">';
			foreach ($date_array as $key=>$val) {
	
				$selected='';
				if ($selectedkey==$key) {
					$selected=' selected="selected" ';
				}
	
				$out.='<OPTION value="'.$key.'"'.$selected.'>'.$val.'</OPTION>';
			}
			$out.='</SELECT>';
		}
	
		return $out;
	}
}
