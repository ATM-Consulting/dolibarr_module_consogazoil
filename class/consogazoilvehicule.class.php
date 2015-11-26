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
 *  \file       /consogazoil/class/consogazoilvehicule.class.php
 *  \ingroup    consogazoil
 */

// Put here all includes required by your class file
require_once 'commonobjectconsogazoil.class.php';


/**
 *	Put here description of your class
 */
class ConsogazoilVehicule extends CommonObjectConsoGazoil
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	var $element='consogazoilvehicule';			//!< Id that identify managed objects
	var $table_element='consogazoil_vehicule';	//!< Name of table without prefix where object is stored
	protected $ismultientitymanaged = 1;	// 0=No test on entity, 1=Test with field entity, 2=Test with link by societe

    var $id;
    
	var $entity;
	var $ref;
	var $immat_veh;
	var $brand_veh;
	var $first_road_dt_veh='';
	var $variant_veh;
	var $commercial_name_veh;
	var $avg_conso;
	var $datec='';
	var $tms='';
	var $fk_user_creat;
	var $fk_user_modif;
	var $activ;
	var $import_key;
	
	var $lines=array();

    


    /**
     *  Constructor
     *
     *  @param	DoliDb		$db      Database handler
     */
    function __construct($db)
    {
        $this->db = $db;
        return 1;
    }


    /**
     *  Create object into database
     *
     *  @param	User	$user        User that creates
     *  @param  int		$notrigger   0=launch triggers after, 1=disable triggers
     *  @return int      		   	 <0 if KO, Id of created object if OK
     */
    function create($user, $notrigger=0)
    {
    	global $conf, $langs;
		$error=0;

		// Clean parameters
        
		if (isset($this->entity)) $this->entity=trim($this->entity);
		if (isset($this->ref)) $this->ref=trim($this->ref);
		if (isset($this->immat_veh)) $this->immat_veh=trim($this->immat_veh);
		if (isset($this->brand_veh)) $this->brand_veh=trim($this->brand_veh);
		if (isset($this->variant_veh)) $this->variant_veh=trim($this->variant_veh);
		if (isset($this->commercial_name_veh)) $this->commercial_name_veh=trim($this->commercial_name_veh);
		if (isset($this->avg_conso)) $this->avg_conso=trim($this->avg_conso);
		if (isset($this->fk_user_creat)) $this->fk_user_creat=trim($this->fk_user_creat);
		if (isset($this->fk_user_modif)) $this->fk_user_modif=trim($this->fk_user_modif);
		if (isset($this->activ)) $this->fk_user_modif=trim($this->activ);
		if (isset($this->import_key)) $this->import_key=trim($this->import_key);

        

		// Check parameters
		// Put here code to add control on parameters values

        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."consogazoil_vehicule(";
		
		$sql.= "entity,";
		$sql.= "ref,";
		$sql.= "immat_veh,";
		$sql.= "brand_veh,";
		$sql.= "first_road_dt_veh,";
		$sql.= "variant_veh,";
		$sql.= "commercial_name_veh,";
		$sql.= "avg_conso,";
		$sql.= "datec,";
		$sql.= "fk_user_creat,";
		$sql.= "fk_user_modif,";
		$sql.= "activ,";
		$sql.= "import_key";

		
        $sql.= ") VALUES (";
        
		$sql.= " ".$conf->entity.",";
		$sql.= " ".(! isset($this->ref)?'NULL':"'".$this->db->escape($this->ref)."'").",";
		$sql.= " ".(! isset($this->immat_veh)?'NULL':"'".$this->db->escape($this->immat_veh)."'").",";
		$sql.= " ".(! isset($this->brand_veh)?'NULL':"'".$this->db->escape($this->brand_veh)."'").",";
		$sql.= " ".(! isset($this->first_road_dt_veh) || dol_strlen($this->first_road_dt_veh)==0?'NULL':$this->db->idate($this->first_road_dt_veh)).",";
		$sql.= " ".(! isset($this->variant_veh)?'NULL':"'".$this->db->escape($this->variant_veh)."'").",";
		$sql.= " ".(! isset($this->commercial_name_veh)?'NULL':"'".$this->db->escape($this->commercial_name_veh)."'").",";
		$sql.= " ".(! isset($this->avg_conso)?'NULL':"'".$this->avg_conso."'").",";
		$sql.= "'".$this->db->idate(dol_now())."',";
		$sql.= " ".$user->id.",";
		$sql.= " ".$user->id.",";
		$sql.= " ".(! isset($this->activ)?'1':"'".$this->activ."'").",";
		$sql.= " ".(! isset($this->import_key)?'NULL':"'".$this->db->escape($this->import_key)."'")."";

        
		$sql.= ")";

		$this->db->begin();

	   	dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

    	if (! $error) {
    	
    		if (empty($conf->global->MAIN_EXTRAFIELDS_DISABLED)) {
    			$result = $this->insertExtraFields();
    			if ($result < 0) {
    				$error ++;
    			}
    		}
    	}
    	
		if (! $error)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."consogazoil_vehicule");

			if (! $notrigger)
			{
	            // Uncomment this and change MYOBJECT to your own tag if you
	            // want this action calls a trigger.

	            //// Call triggers
	            //include_once DOL_DOCUMENT_ROOT . '/core/class/interfaces.class.php';
	            //$interface=new Interfaces($this->db);
	            //$result=$interface->run_triggers('MYOBJECT_CREATE',$this,$user,$langs,$conf);
	            //if ($result < 0) { $error++; $this->errors=$interface->errors; }
	            //// End call triggers
			}
        }
        
       

        // Commit or rollback
        if ($error)
		{
			foreach($this->errors as $errmsg)
			{
	            dol_syslog(get_class($this)."::create ".$errmsg, LOG_ERR);
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
     *  Load object in memory from the database
     *
     *  @param	int		$id    Id object
     *  @param	int		$ref   ref object
     *  @return int          	<0 if KO, >0 if OK
     */
    function fetch($id,$ref='')
    {
    	global $langs;
        $sql = "SELECT";
		$sql.= " t.rowid,";
		
		$sql.= " t.entity,";
		$sql.= " t.ref,";
		$sql.= " t.immat_veh,";
		$sql.= " t.brand_veh,";
		$sql.= " t.first_road_dt_veh,";
		$sql.= " t.variant_veh,";
		$sql.= " t.commercial_name_veh,";
		$sql.= " t.avg_conso,";
		$sql.= " t.datec,";
		$sql.= " t.tms,";
		$sql.= " t.fk_user_creat,";
		$sql.= " t.fk_user_modif,";
		$sql.= " t.activ,";
		$sql.= " t.import_key";

		
        $sql.= " FROM ".MAIN_DB_PREFIX."consogazoil_vehicule as t";
        if (!empty($id)) {
        	$sql.= " WHERE t.rowid = ".$id;
        }else if (!empty($ref)) $sql.= " WHERE t.ref = '".$ref."'";
        
    	dol_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);

                $this->id    = $obj->rowid;
                
				$this->entity = $obj->entity;
				$this->ref = $obj->ref;
				$this->immat_veh = $obj->immat_veh;
				$this->brand_veh = $obj->brand_veh;
				$this->first_road_dt_veh = $this->db->jdate($obj->first_road_dt_veh);
				$this->variant_veh = $obj->variant_veh;
				$this->commercial_name_veh = $obj->commercial_name_veh;
				$this->avg_conso = $obj->avg_conso;
				$this->datec = $this->db->jdate($obj->datec);
				$this->tms = $this->db->jdate($obj->tms);
				$this->fk_user_creat = $obj->fk_user_creat;
				$this->fk_user_modif = $obj->fk_user_modif;
				$this->activ = $obj->activ;
				$this->import_key = $obj->import_key;

				$extrafields = new ExtraFields($this->db);
				$extralabels = $extrafields->fetch_name_optionals_label($this->table_element, true);
				if (count($extralabels) > 0) {
					$this->fetch_optionals($this->id, $extralabels);
				}
				
				
            }
            $this->db->free($resql);

            return 1;
        }
        else
        {
      	    $this->error="Error ".$this->db->lasterror();
            dol_syslog(get_class($this)."::fetch ".$this->error, LOG_ERR);
            return -1;
        }
    }


    /**
     *  Update object into database
     *
     *  @param	User	$user        User that modifies
     *  @param  int		$notrigger	 0=launch triggers after, 1=disable triggers
     *  @return int     		   	 <0 if KO, >0 if OK
     */
    function update($user=0, $notrigger=0)
    {
    	global $conf, $langs;
		$error=0;

		// Clean parameters
        
		if (isset($this->entity)) $this->entity=trim($this->entity);
		if (isset($this->ref)) $this->ref=trim($this->ref);
		if (isset($this->immat_veh)) $this->immat_veh=trim($this->immat_veh);
		if (isset($this->brand_veh)) $this->brand_veh=trim($this->brand_veh);
		if (isset($this->variant_veh)) $this->variant_veh=trim($this->variant_veh);
		if (isset($this->commercial_name_veh)) $this->commercial_name_veh=trim($this->commercial_name_veh);
		if (isset($this->avg_conso)) $this->avg_conso=trim($this->avg_conso);
		if (isset($this->fk_user_creat)) $this->fk_user_creat=trim($this->fk_user_creat);
		if (isset($this->fk_user_modif)) $this->fk_user_modif=trim($this->fk_user_modif);
		if (isset($this->activ)) $this->activ=trim($this->activ);
		if (isset($this->import_key)) $this->import_key=trim($this->import_key);

        

		// Check parameters
		// Put here code to add a control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."consogazoil_vehicule SET";
        
		$sql.= " entity=".$conf->entity.",";
		$sql.= " ref=".(isset($this->ref)?"'".$this->db->escape($this->ref)."'":"null").",";
		$sql.= " immat_veh=".(isset($this->immat_veh)?"'".$this->db->escape($this->immat_veh)."'":"null").",";
		$sql.= " brand_veh=".(isset($this->brand_veh)?"'".$this->db->escape($this->brand_veh)."'":"null").",";
		$sql.= " first_road_dt_veh=".(dol_strlen($this->first_road_dt_veh)!=0 ? "'".$this->db->idate($this->first_road_dt_veh)."'" : 'null').",";
		$sql.= " variant_veh=".(isset($this->variant_veh)?"'".$this->db->escape($this->variant_veh)."'":"null").",";
		$sql.= " commercial_name_veh=".(isset($this->commercial_name_veh)?"'".$this->db->escape($this->commercial_name_veh)."'":"null").",";
		$sql.= " avg_conso=".(isset($this->avg_conso)?$this->avg_conso:"null").",";
		$sql.= " fk_user_modif=".$user->id.",";
		$sql.= " activ=".(isset($this->activ)?$this->activ:"1").",";
		$sql.= " import_key=".(isset($this->import_key)?"'".$this->db->escape($this->import_key)."'":"null")."";

        
        $sql.= " WHERE rowid=".$this->id;

		$this->db->begin();

		dol_syslog(get_class($this)."::update sql=".$sql, LOG_DEBUG);
        $resql = $this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

    	if (! $error) {
    	
    		if (empty($conf->global->MAIN_EXTRAFIELDS_DISABLED)) {
    			$result = $this->insertExtraFields();
    			if ($result < 0) {
    				$error ++;
    			}
    		}
    	}
    	
    	
		if (! $error)
		{
			if (! $notrigger)
			{
	            // Uncomment this and change MYOBJECT to your own tag if you
	            // want this action calls a trigger.

	            //// Call triggers
	            //include_once DOL_DOCUMENT_ROOT . '/core/class/interfaces.class.php';
	            //$interface=new Interfaces($this->db);
	            //$result=$interface->run_triggers('MYOBJECT_MODIFY',$this,$user,$langs,$conf);
	            //if ($result < 0) { $error++; $this->errors=$interface->errors; }
	            //// End call triggers
	    	}
		}

        // Commit or rollback
		if ($error)
		{
			foreach($this->errors as $errmsg)
			{
	            dol_syslog(get_class($this)."::update ".$errmsg, LOG_ERR);
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
	 *  Delete object in database
	 *
     *	@param  User	$user        User that deletes
     *  @param  int		$notrigger	 0=launch triggers after, 1=disable triggers
	 *  @return	int					 <0 if KO, >0 if OK
	 */
	function delete($user, $notrigger=0)
	{
		global $conf, $langs;
		$error=0;

		$this->db->begin();

		if (! $error)
		{
			if (! $notrigger)
			{
				// Uncomment this and change MYOBJECT to your own tag if you
		        // want this action calls a trigger.

		        //// Call triggers
		        //include_once DOL_DOCUMENT_ROOT . '/core/class/interfaces.class.php';
		        //$interface=new Interfaces($this->db);
		        //$result=$interface->run_triggers('MYOBJECT_DELETE',$this,$user,$langs,$conf);
		        //if ($result < 0) { $error++; $this->errors=$interface->errors; }
		        //// End call triggers
			}
		}

		if (! $error)
		{
    		$sql = "DELETE FROM ".MAIN_DB_PREFIX."consogazoil_vehicule";
    		$sql.= " WHERE rowid=".$this->id;

    		dol_syslog(get_class($this)."::delete sql=".$sql);
    		$resql = $this->db->query($sql);
        	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }
		}

        // Commit or rollback
		if ($error)
		{
			foreach($this->errors as $errmsg)
			{
	            dol_syslog(get_class($this)."::delete ".$errmsg, LOG_ERR);
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
	 *	Load an object from its id and create a new one in database
	 *
	 *	@param	int		$fromid     Id of object to clone
	 * 	@return	int					New id of clone
	 */
	function createFromClone($fromid)
	{
		global $user,$langs;

		$error=0;

		$object=new Consogazoilvehicule($this->db);

		$this->db->begin();

		// Load source object
		$object->fetch($fromid);
		$object->id=0;
		$object->statut=0;

		// Clear fields
		// ...

		// Create clone
		$result=$object->create($user);

		// Other options
		if ($result < 0)
		{
			$this->error=$object->error;
			$error++;
		}

		if (! $error)
		{


		}

		// End
		if (! $error)
		{
			$this->db->commit();
			return $object->id;
		}
		else
		{
			$this->db->rollback();
			return -1;
		}
	}


	/**
	 *	Initialise object with example values
	 *	Id must be 0 if object instance is a specimen
	 *
	 *	@return	void
	 */
	function initAsSpecimen()
	{
		$this->id=0;
		
		$this->entity='';
		$this->ref='';
		$this->immat_veh='';
		$this->brand_veh='';
		$this->first_road_dt_veh='';
		$this->variant_veh='';
		$this->commercial_name_veh='';
		$this->avg_conso='';
		$this->datec='';
		$this->tms='';
		$this->fk_user_creat='';
		$this->fk_user_modif='';
		$this->activ='';
		$this->import_key='';
	}
	
	/**
	 *  Load object in memory from the database
	 *
	 *  @param	string		$sortorder    sort order
	 *  @param	string		$sortfield    sort field
	 *  @param	int			$limit		  limit page
	 *  @param	int			$offset    	  page
	 *  @param	int			$arch    	  display archive or not
	 *  @param	array		$filter    	  filter output
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function fetch_all($sortorder='DESC', $sortfield='t.ref', $limit=0, $offset=0, $filter='')
	{
		global $langs;
	
	    $sql = "SELECT";
		$sql.= " t.rowid,";
		
		$sql.= " t.entity,";
		$sql.= " t.ref,";
		$sql.= " t.immat_veh,";
		$sql.= " t.brand_veh,";
		$sql.= " t.first_road_dt_veh,";
		$sql.= " t.variant_veh,";
		$sql.= " t.commercial_name_veh,";
		$sql.= " t.avg_conso,";
		$sql.= " t.datec,";
		$sql.= " t.tms,";
		$sql.= " t.fk_user_creat,";
		$sql.= " t.fk_user_modif,";
		$sql.= " t.activ,";
		$sql.= " t.import_key";

		
        $sql.= " FROM ".MAIN_DB_PREFIX."consogazoil_vehicule as t";
		$sql.= " WHERE t.entity IN (".getEntity($this->element, 1).")";
		//Manage filter
		if (is_array($filter) && count($filter)>0){
			foreach($filter as $key => $value) {
				if (strpos($key,'date') || $key=='t.first_road_dt_veh') {
					$sql.= ' AND '.$key.' = \''.$this->db->idate($value).'\'';
				}
				else if (strpos($key,'avg_conso')) {
					$sql.= ' AND '.$key.'='.$value;
				}
				else if (strpos($key,'fk_')) {
					$sql.= ' AND '.$key.' = '.$value;
				}
				else {
					$sql.= ' AND '.$key.' LIKE \'%'.$value.'%\'';
				}
			}
		}
		$sql.= " ORDER BY ".$sortfield." ".$sortorder;
		if (!empty($limit)) $sql.= $this->db->plimit( $limit + 1 ,$offset);
	
		dol_syslog(get_class($this)."::fetch_all sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$this->lines=array();
	
			$num = $this->db->num_rows($resql);
			while ($obj = $this->db->fetch_object($resql))
			{
				$line = new ConsogazoilVehiculeLine();
	
				$line->id    = $obj->rowid;
                
				$line->entity = $obj->entity;
				$line->ref = $obj->ref;
				$line->immat_veh = $obj->immat_veh;
				$line->brand_veh = $obj->brand_veh;
				$line->first_road_dt_veh = $this->db->jdate($obj->first_road_dt_veh);
				$line->variant_veh = $obj->variant_veh;
				$line->commercial_name_veh = $obj->commercial_name_veh;
				$line->avg_conso = $obj->avg_conso;
				$line->datec = $this->db->jdate($obj->datec);
				$line->tms = $this->db->jdate($obj->tms);
				$line->fk_user_creat = $obj->fk_user_creat;
				$line->fk_user_modif = $obj->fk_user_modif;
				$line->activ = $obj->activ;
				$line->import_key = $obj->import_key;
	
	
				$this->lines[]=$line;
	
			}
			$this->db->free($resql);
	
			return $num;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::fetch_all ".$this->error, LOG_ERR);
			return -1;
		}
	}
	
	/**
	 *      Return clicable link of object (with eventually picto)
	 *
	 *      @return string 			         String with URL
	 */
	function getNomUrl()
	{
		global $langs;
	
		$result='';
	
		$url = dol_buildpath('/consogazoil/vehicule/card.php',1).'?id='.$this->id;
	
		$label=$langs->trans("Show").': '.$this->ref;

		$result='<a href="'.$url.'">'.$label.'</a>';
		
		return $result;
	}

}

class ConsogazoilVehiculeLine
{

	var $id;

	var $entity;
	var $ref;
	var $immat_veh;
	var $brand_veh;
	var $first_road_dt_veh='';
	var $variant_veh;
	var $commercial_name_veh;
	var $avg_conso;
	var $datec='';
	var $tms='';
	var $fk_user_creat;
	var $fk_user_modif;
	var $activ;
	var $import_key;
	
	function __construct()
	{
		return 1;
	}
}