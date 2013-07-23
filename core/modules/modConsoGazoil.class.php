<?php
/* Consomation Gazoil 
 * Copyright (C) 2013 florian Henry <florian.henry@open-concept.pro>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * 	\defgroup	consogazoil	ConsoGazoil module
 * 	\brief		ConsoGazoil module descriptor.
 * 	\file		core/modules/modConsoGazoil.class.php
 * 	\ingroup	consogazoil
 * 	\brief		Description and activation file for module ConsoGazoil
 */
include_once DOL_DOCUMENT_ROOT . "/core/modules/DolibarrModules.class.php";

/**
 * Description and activation class for module ConsoGazoil
 */
class modConsoGazoil extends DolibarrModules
{

    /**
     * 	Constructor. Define names, constants, directories, boxes, permissions
     *
     * 	@param	DoliDB		$db	Database handler
     */
    public function __construct($db)
    {
        global $langs, $conf;

        $this->db = $db;

        // Id for module (must be unique).
        // Use a free id here
        // (See in Home -> System information -> Dolibarr for list of used modules id).
        $this->numero = 103040;
        // Key text used to identify module (for permissions, menus, etc...)
        $this->rights_class = 'consogazoil';

        // Family can be 'crm','financial','hr','projects','products','ecm','technic','other'
        // It is used to group modules in module setup page
        $this->family = "other";
        // Module label (no space allowed)
        // used if translation string 'ModuleXXXName' not found
        // (where XXX is value of numeric property 'numero' of module)
        $this->name = preg_replace('/^mod/i', '', get_class($this));
        // Module description
        // used if translation string 'ModuleXXXDesc' not found
        // (where XXX is value of numeric property 'numero' of module)
        $this->description = "Description of module ConsoGazoil";
        // Possible values for version are: 'development', 'experimental' or version
        $this->version = '0.1';
        // Key used in llx_const table to save module status enabled/disabled
        // (where MYMODULE is value of property name of module in uppercase)
        $this->const_name = 'MAIN_MODULE_' . strtoupper($this->name);
        // Where to store the module in setup page
        // (0=common,1=interface,2=others,3=very specific)
        $this->special = 2;
        // Name of image file used for this module.
        // If file is in theme/yourtheme/img directory under name object_pictovalue.png
        // use this->picto='pictovalue'
        // If file is in module/img directory under name object_pictovalue.png
        // use this->picto='pictovalue@module'
        $this->picto = 'consogazoil@consogazoil'; // mypicto@consogazoil
        // Defined all module parts (triggers, login, substitutions, menus, css, etc...)
        // for default path (eg: /consogazoil/core/xxxxx) (0=disable, 1=enable)
        // for specific path of parts (eg: /consogazoil/core/modules/barcode)
        // for specific css file (eg: /consogazoil/css/consogazoil.css.php)
        $this->module_parts = array(
            // Set this to 1 if module has its own trigger directory
            //'triggers' => 1,
            // Set this to 1 if module has its own login method directory
            //'login' => 0,
            // Set this to 1 if module has its own substitution function file
            //'substitutions' => 0,
            // Set this to 1 if module has its own menus handler directory
            //'menus' => 0,
            // Set this to 1 if module has its own barcode directory
            //'barcode' => 0,
            // Set this to 1 if module has its own models directory
            //'models' => 0,
            // Set this to relative path of css if module has its own css file
            //'css' => '/consogazoil/css/mycss.css.php',
            // Set here all hooks context managed by module
            //'hooks' => array('hookcontext1','hookcontext2')
            // Set here all workflow context managed by module
            //'workflow' => array('order' => array('WORKFLOW_ORDER_AUTOCREATE_INVOICE'))
        );

        // Data directories to create when module is enabled.
        // Example: this->dirs = array("/consogazoil/temp");
        $this->dirs = array('/consogazoil');

        // Config pages. Put here list of php pages
        // stored into consogazoil/admin directory, used to setup module.
        $this->config_page_url = array("admin_consogazoil.php@consogazoil");

        // Dependencies
        // List of modules id that must be enabled if this module is enabled
        $this->depends = array();
        // List of modules id to disable if this one is disabled
        $this->requiredby = array();
        // Minimum version of PHP required by module
        $this->phpmin = array(5, 2);
        // Minimum version of Dolibarr required by module
        $this->need_dolibarr_version = array(3, 3);
        $this->langfiles = array("consogazoil@consogazoil"); // langfiles@consogazoil
        // Constants
        // List of particular constants to add when module is enabled
        // (key, 'chaine', value, desc, visible, 'current' or 'allentities', deleteonunactive)
        // Example:
      
        $r=0;
        $r++;
		$this->const[$r][0] = "GAZOIL_LAST_VERION_INSTALL";
		$this->const[$r][1] = "chaine";
		$this->const[$r][2] = $this->version;
		$this->const[$r][3] = 'Last version installed to know change table to execute';
		$this->const[$r][4] = 0;
		$this->const[$r][5] = 'allentities';
		$this->const[$r][6] = 0;
		
		$r++;
		$this->const[$r][0] = "GAZOIL_THRESOLD_CONSO";
		$this->const[$r][1] = "chaine";
		$this->const[$r][2] = '10';
		$this->const[$r][3] = 'Threshold color flag gazoil conso';
		$this->const[$r][4] = 0;
		$this->const[$r][5] = 'current';
		$this->const[$r][6] = 0;
		
		$r++;
		$this->const[$r][0] = "GAZOIL_THRESOLD_KM";
		$this->const[$r][1] = "chaine";
		$this->const[$r][2] = '250';
		$this->const[$r][3] = 'Threshold color flag KM';
		$this->const[$r][4] = 0;
		$this->const[$r][5] = 'current';
		$this->const[$r][6] = 0;
		
		$r++;
		$this->const[$r][0] = "GAZOIL_EMAIL_EXPLOIT";
		$this->const[$r][1] = "chaine";
		$this->const[$r][2] = '';
		$this->const[$r][3] = 'Email list to inform of import';
		$this->const[$r][4] = 0;
		$this->const[$r][5] = 'current';
		$this->const[$r][6] = 0;
		
		$r++;
		$this->const[$r][0] = "GAZOIL_ID_VEH_NO_IMPORT";
		$this->const[$r][1] = "chaine";
		$this->const[$r][2] = '';
		$this->const[$r][3] = 'Id of vehicule to not import';
		$this->const[$r][4] = 0;
		$this->const[$r][5] = 'current';
		$this->const[$r][6] = 0;
		
		$r++;
		$this->const[$r][0] = 	'REQUIRE_JQUERY_DATATABLES';
		$this->const[$r][1] = 	'yesno';
		$this->const[$r][2] = 	'1';
		$this->const[$r][3] = 	'use JQUERY DataTable Module';
		$this->const[$r][4] = 	0;
		$this->const[$r][5] = 	'allentities';
		$this->const[$r][6] = 	1;

        // Array to add new pages in new tabs
        // Example:
        $this->tabs = array(
            //	// To add a new tab identified by code tabname1
            //	'objecttype:+tabname1:Title1:langfile@consogazoil:$user->rights->consogazoil->read:/consogazoil/mynewtab1.php?id=__ID__',
            //	// To add another new tab identified by code tabname2
            //	'objecttype:+tabname2:Title2:langfile@consogazoil:$user->rights->othermodule->read:/consogazoil/mynewtab2.php?id=__ID__',
            //	// To remove an existing tab identified by code tabname
            //	'objecttype:-tabname'
        );
        // where objecttype can be
        // 'thirdparty'			to add a tab in third party view
        // 'intervention'		to add a tab in intervention view
        // 'order_supplier'		to add a tab in supplier order view
        // 'invoice_supplier'	to add a tab in supplier invoice view
        // 'invoice'			to add a tab in customer invoice view
        // 'order'				to add a tab in customer order view
        // 'product'			to add a tab in product view
        // 'stock'				to add a tab in stock view
        // 'propal'				to add a tab in propal view
        // 'member'				to add a tab in fundation member view
        // 'contract'			to add a tab in contract view
        // 'user'				to add a tab in user view
        // 'group'				to add a tab in group view
        // 'contact'			to add a tab in contact view
        // 'categories_x'		to add a tab in category view
        // (replace 'x' by type of category (0=product, 1=supplier, 2=customer, 3=member)
        // Dictionnaries
        if (! isset($conf->consogazoil->enabled)) {
            $conf->consogazoil=new stdClass();
            $conf->consogazoil->enabled = 0;
        }
        $this->dictionnaries = array();
        /* Example:
          // This is to avoid warnings
          if (! isset($conf->consogazoil->enabled)) $conf->consogazoil->enabled=0;
          $this->dictionnaries=array(
          'langs'=>'consogazoil@consogazoil',
          // List of tables we want to see into dictonnary editor
          'tabname'=>array(
          MAIN_DB_PREFIX."table1",
          MAIN_DB_PREFIX."table2",
          MAIN_DB_PREFIX."table3"
          ),
          // Label of tables
          'tablib'=>array("Table1","Table2","Table3"),
          // Request to select fields
          'tabsql'=>array(
          'SELECT f.rowid as rowid, f.code, f.label, f.active'
          . ' FROM ' . MAIN_DB_PREFIX . 'table1 as f',
          'SELECT f.rowid as rowid, f.code, f.label, f.active'
          . ' FROM ' . MAIN_DB_PREFIX . 'table2 as f',
          'SELECT f.rowid as rowid, f.code, f.label, f.active'
          . ' FROM ' . MAIN_DB_PREFIX . 'table3 as f'
          ),
          // Sort order
          'tabsqlsort'=>array("label ASC","label ASC","label ASC"),
          // List of fields (result of select to show dictionnary)
          'tabfield'=>array("code,label","code,label","code,label"),
          // List of fields (list of fields to edit a record)
          'tabfieldvalue'=>array("code,label","code,label","code,label"),
          // List of fields (list of fields for insert)
          'tabfieldinsert'=>array("code,label","code,label","code,label"),
          // Name of columns with primary key (try to always name it 'rowid')
          'tabrowid'=>array("rowid","rowid","rowid"),
          // Condition to show each dictionnary
          'tabcond'=>array(
          $conf->consogazoil->enabled,
          $conf->consogazoil->enabled,
          $conf->consogazoil->enabled
          )
          );
         */

        // Boxes
        // Add here list of php file(s) stored in core/boxes that contains class to show a box.
        $this->boxes = array(); // Boxes list
        $r = 0;
        // Example:

        //$this->boxes[$r][1] = "MyBox@consogazoil";
        //$r ++;
        /*
          $this->boxes[$r][1] = "myboxb.php";
          $r++;
         */

        // Permissions
        $this->rights = array(); // Permission array used by this module
        $r = 0;
        
        $this->rights[$r][0] = 103041;
		$this->rights[$r][1] = 'Lecture';
		$this->rights[$r][3] = 1;
		$this->rights[$r][4] = 'lire';
		$r++;

		$this->rights[$r][0] = 103042;
		$this->rights[$r][1] = 'Modification';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'modifier';
		$r++;

		$this->rights[$r][0] = 103043;
		$this->rights[$r][1] = 'Ajout';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'creer';
		$r++;


		$this->rights[$r][0] = 103044;
		$this->rights[$r][1] = 'Suppression';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'supprimer';
		$r++;

		$this->rights[$r][0] = 103045;
		$this->rights[$r][1] = 'Imports';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'import';
		$r++;

		$this->rights[$r][0] = 103046;
		$this->rights[$r][1] = 'Export';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'export';
        
        
        // Main menu entries
        $this->menus = array(); // List of menus to add
        $r = 0;

        // Add here entries to declare new menus
        //
        // Example to declare a new Top Menu entry and its Left menu entry:
        //$this->menu[$r]=array(
        //	// Put 0 if this is a top menu
        //	'fk_menu'=>0,
        //	// This is a Top menu entry
        //	'type'=>'top',
        //	'titre'=>'ConsoGazoil top menu',
        //	'mainmenu'=>'consogazoil',
        //	'leftmenu'=>'consogazoil',
        //	'url'=>'/consogazoil/pagetop.php',
        //	// Lang file to use (without .lang) by module.
        //	// File must be in langs/code_CODE/ directory.
        //	'langs'=>'mylangfile',
        //	'position'=>100,
        //	// Define condition to show or hide menu entry.
        //	// Use '$conf->consogazoil->enabled' if entry must be visible if module is enabled.
        //	'enabled'=>'$conf->consogazoil->enabled',
        //	// Use 'perms'=>'$user->rights->consogazoil->level1->level2'
        //	// if you want your menu with a permission rules
        //	'perms'=>'1',
        //	'target'=>'',
        //	// 0=Menu for internal users, 1=external users, 2=both
        //	'user'=>2
        //);
        
        $this->menu[$r]=array(
        	'fk_menu'=>0,
        	'type'=>'top',
        	'titre'=>'Module103040Name',
        	'mainmenu'=>'consogazoil',
        	'url'=>'/consogazoil/index.php',
        	'langs'=>'consogazoil@consogazoil',
        	'position'=>100,
        	'enabled'=>'$conf->consogazoil->enabled',
        	'perms'=>'$user->rights->consogazoil->lire',
        	'target'=>'',
        	'user'=>2
        );
       
        $r++;
        $this->menu[$r]=array(
        	'fk_menu'=>'fk_mainmenu=consogazoil',
        	'type'=>'left',
        	'titre'=>'ConsoGazManageVeh',
        	'leftmenu'=>'consogazoilveh',
        	'url'=>'/consogazoil/vehicule/list.php',
        	'langs'=>'consogazoil@consogazoil',
        	'position'=>101,
        	'enabled'=>'$conf->consogazoil->enabled',
        	'perms'=>'$user->rights->consogazoil->lire',
        	'target'=>'',
        	'user'=>2
        );
        $r++;
        $this->menu[$r]=array(
        	'fk_menu'=>'fk_mainmenu=consogazoil,fk_leftmenu=consogazoilveh',
        	'type'=>'left',
        	'titre'=>'ConsoGazList',
        	'url'=>'/consogazoil/vehicule/list.php',
        	'langs'=>'consogazoil@consogazoil',
        	'position'=>103,
        	'enabled'=>'$conf->consogazoil->enabled',
        	'perms'=>'$user->rights->consogazoil->lire',
        	'target'=>'',
        	'user'=>2
        );
        $r++;
        $this->menu[$r]=array(
        	'fk_menu'=>'fk_mainmenu=consogazoil,fk_leftmenu=consogazoilveh',
        	'type'=>'left',
        	'titre'=>'ConsoGazNew',
        	'url'=>'/consogazoil/vehicule/card.php?action=create',
        	'langs'=>'consogazoil@consogazoil',
        	'position'=>104,
        	'enabled'=>'$conf->consogazoil->enabled',
        	'perms'=>'$user->rights->consogazoil->creer',
        	'target'=>'',
        	'user'=>2
        );
        $r++;
        $this->menu[$r]=array(
        	'fk_menu'=>'fk_mainmenu=consogazoil',
        	'type'=>'left',
        	'titre'=>'ConsoGazManageServ',
        	'leftmenu'=>'consogazoilserv',
        	'url'=>'/consogazoil/service/list.php',
        	'langs'=>'consogazoil@consogazoil',
        	'position'=>105,
        	'enabled'=>'$conf->consogazoil->enabled',
        	'perms'=>'$user->rights->consogazoil->lire',
        	'target'=>'',
        	'user'=>2
        );
        $r++;
        $this->menu[$r]=array(
        	'fk_menu'=>'fk_mainmenu=consogazoil,fk_leftmenu=consogazoilserv',
        	'type'=>'left',
        	'titre'=>'ConsoGazList',
        	'url'=>'/consogazoil/service/list.php',
        	'langs'=>'consogazoil@consogazoil',
        	'position'=>106,
        	'enabled'=>'$conf->consogazoil->enabled',
        	'perms'=>'$user->rights->consogazoil->lire',
        	'target'=>'',
        	'user'=>2
        );
        $r++;
        $this->menu[$r]=array(
        	'fk_menu'=>'fk_mainmenu=consogazoil,fk_leftmenu=consogazoilserv',
        	'type'=>'left',
        	'titre'=>'ConsoGazNew',
        	'url'=>'/consogazoil/service/card.php?action=create',
        	'langs'=>'consogazoil@consogazoil',
        	'position'=>107,
        	'enabled'=>'$conf->consogazoil->enabled',
        	'perms'=>'$user->rights->consogazoil->creer',
        	'target'=>'',
        	'user'=>2
        );
        $r++;
        $this->menu[$r]=array(
        	'fk_menu'=>'fk_mainmenu=consogazoil',
        	'type'=>'left',
        	'titre'=>'ConsoGazManageSta',
        	'leftmenu'=>'consogazoilsta',
        	'url'=>'/consogazoil/station/list.php',
        	'langs'=>'consogazoil@consogazoil',
        	'position'=>108,
        	'enabled'=>'$conf->consogazoil->enabled',
        	'perms'=>'$user->rights->consogazoil->lire',
        	'target'=>'',
        	'user'=>2
        );
        $r++;
        $this->menu[$r]=array(
        	'fk_menu'=>'fk_mainmenu=consogazoil,fk_leftmenu=consogazoilsta',
        	'type'=>'left',
        	'titre'=>'ConsoGazList',
        	'url'=>'/consogazoil/station/list.php',
        	'langs'=>'consogazoil@consogazoil',
        	'position'=>109,
        	'enabled'=>'$conf->consogazoil->enabled',
        	'perms'=>'$user->rights->consogazoil->lire',
        	'target'=>'',
        	'user'=>2
        );
        $r++;
        $this->menu[$r]=array(
        	'fk_menu'=>'fk_mainmenu=consogazoil,fk_leftmenu=consogazoilsta',
        	'type'=>'left',
        	'titre'=>'ConsoGazNew',
        	'url'=>'/consogazoil/station/card.php?action=create',
        	'langs'=>'consogazoil@consogazoil',
        	'position'=>110,
        	'enabled'=>'$conf->consogazoil->enabled',
        	'perms'=>'$user->rights->consogazoil->creer',
        	'target'=>'',
        	'user'=>2
        );
        $r++;
        $this->menu[$r]=array(
        	'fk_menu'=>'fk_mainmenu=consogazoil',
        	'type'=>'left',
        	'titre'=>'ConsoGazManageDriv',
        	'leftmenu'=>'consogazoildriv',
        	'url'=>'/consogazoil/driver/list.php',
        	'langs'=>'consogazoil@consogazoil',
        	'position'=>111,
        	'enabled'=>'$conf->consogazoil->enabled',
        	'perms'=>'$user->rights->consogazoil->lire',
        	'target'=>'',
        	'user'=>2
        );
        $r++;
        $this->menu[$r]=array(
        	'fk_menu'=>'fk_mainmenu=consogazoil,fk_leftmenu=consogazoildriv',
        	'type'=>'left',
        	'titre'=>'ConsoGazList',
        	'url'=>'/consogazoil/driver/list.php',
        	'langs'=>'consogazoil@consogazoil',
        	'position'=>112,
        	'enabled'=>'$conf->consogazoil->enabled',
        	'perms'=>'$user->rights->consogazoil->lire',
        	'target'=>'',
        	'user'=>2
        );
        $r++;
        $this->menu[$r]=array(
        	'fk_menu'=>'fk_mainmenu=consogazoil,fk_leftmenu=consogazoildriv',
        	'type'=>'left',
        	'titre'=>'ConsoGazNew',
        	'url'=>'/consogazoil/driver/card.php?action=create',
        	'langs'=>'consogazoil@consogazoil',
        	'position'=>113,
        	'enabled'=>'$conf->consogazoil->enabled',
        	'perms'=>'$user->rights->consogazoil->creer',
        	'target'=>'',
        	'user'=>2
        );
        $r++;
        $this->menu[$r]=array(
        	'fk_menu'=>'fk_mainmenu=consogazoil',
        	'type'=>'left',
        	'titre'=>'ConsoGazManageImport',
        	'leftmenu'=>'consogazoilimport',
        	'url'=>'/consogazoil/import/import.php?step=1',
        	'langs'=>'consogazoil@consogazoil',
        	'position'=>114,
        	'enabled'=>'$conf->consogazoil->enabled',
        	'perms'=>'$user->rights->consogazoil->import',
        	'target'=>'',
        	'user'=>2
        );
        $r++;
        $this->menu[$r]=array(
        	'fk_menu'=>'fk_mainmenu=consogazoil,fk_leftmenu=consogazoilimport',
        	'type'=>'left',
        	'titre'=>'ConsoGazManageImport',
        	'url'=>'/consogazoil/import/import.php?step=1',
        	'langs'=>'consogazoil@consogazoil',
        	'position'=>115,
        	'enabled'=>'$conf->consogazoil->enabled',
        	'perms'=>'$user->rights->consogazoil->import',
        	'target'=>'',
        	'user'=>2
        );
        $r++;
        $this->menu[$r]=array(
        	'fk_menu'=>'fk_mainmenu=consogazoil',
        	'type'=>'left',
        	'titre'=>'ConsoGazManageTake',
        	'leftmenu'=>'consogazoiltake',
        	'url'=>'/consogazoil/take/list.php',
        	'langs'=>'consogazoil@consogazoil',
        	'position'=>116,
        	'enabled'=>'$conf->consogazoil->enabled',
        	'perms'=>'$user->rights->consogazoil->import',
        	'target'=>'',
        	'user'=>2
        );
        $r++;
        $this->menu[$r]=array(
        	'fk_menu'=>'fk_mainmenu=consogazoil,fk_leftmenu=consogazoiltake',
        	'type'=>'left',
        	'titre'=>'ConsoGazList',
        	'url'=>'/consogazoil/take/list.php',
        	'langs'=>'consogazoil@consogazoil',
        	'position'=>117,
        	'enabled'=>'$conf->consogazoil->enabled',
        	'perms'=>'$user->rights->consogazoil->import',
        	'target'=>'',
        	'user'=>2
        );
        $r++;
        $this->menu[$r]=array(
        	'fk_menu'=>'fk_mainmenu=consogazoil,fk_leftmenu=consogazoiltake',
        	'type'=>'left',
        	'titre'=>'ConsoGazNew',
        	'url'=>'/consogazoil/take/card.php?action=create',
        	'langs'=>'consogazoil@consogazoil',
        	'position'=>118,
        	'enabled'=>'$conf->consogazoil->enabled',
        	'perms'=>'$user->rights->consogazoil->import',
        	'target'=>'',
        	'user'=>2
        );
        $r++;
        $this->menu[$r]=array(
        	'fk_menu'=>'fk_mainmenu=consogazoil',
        	'type'=>'left',
        	'titre'=>'ConsoGazManageReport',
        	'leftmenu'=>'consogazoilreport',
        	'url'=>'/consogazoil/report/conso.php',
        	'langs'=>'consogazoil@consogazoil',
        	'position'=>119,
        	'enabled'=>'$conf->consogazoil->enabled',
        	'perms'=>'$user->rights->consogazoil->import',
        	'target'=>'',
        	'user'=>2
        );
        $r++;
        $this->menu[$r]=array(
        	'fk_menu'=>'fk_mainmenu=consogazoil,fk_leftmenu=consogazoilreport',
        	'type'=>'left',
        	'titre'=>'ConsoGazReportConso',
        	'url'=>'/consogazoil/report/conso.php',
        	'langs'=>'consogazoil@consogazoil',
        	'position'=>120,
        	'enabled'=>'$conf->consogazoil->enabled',
        	'perms'=>'$user->rights->consogazoil->lire',
        	'target'=>'',
        	'user'=>2
        );
        $r++;
        $this->menu[$r]=array(
        	'fk_menu'=>'fk_mainmenu=consogazoil,fk_leftmenu=consogazoilreport',
        	'type'=>'left',
        	'titre'=>'ConsoGazReportKM',
        	'url'=>'/consogazoil/report/km.php',
        	'langs'=>'consogazoil@consogazoil',
        	'position'=>121,
        	'enabled'=>'$conf->consogazoil->enabled',
        	'perms'=>'$user->rights->consogazoil->lire',
        	'target'=>'',
        	'user'=>2
        );
        $r++;
        $this->menu[$r]=array(
        	'fk_menu'=>'fk_mainmenu=consogazoil,fk_leftmenu=consogazoilreport',
        	'type'=>'left',
        	'titre'=>'ConsoGazReportTakeNoPref',
        	'url'=>'/consogazoil/report/takepref.php',
        	'langs'=>'consogazoil@consogazoil',
        	'position'=>122,
        	'enabled'=>'$conf->consogazoil->enabled',
        	'perms'=>'$user->rights->consogazoil->lire',
        	'target'=>'',
        	'user'=>2
        );
        //
        // Example to declare a Left Menu entry into an existing Top menu entry:
        //$this->menu[$r]=array(
        //	// Use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy'
        //	'fk_menu'=>'fk_mainmenu=mainmenucode',
        //	// This is a Left menu entry
        //	'type'=>'left',
        //	'titre'=>'ConsoGazoil left menu',
        //	'mainmenu'=>'mainmenucode',
        //	'leftmenu'=>'consogazoil',
        //	'url'=>'/consogazoil/pagelevel2.php',
        //	// Lang file to use (without .lang) by module.
        //	// File must be in langs/code_CODE/ directory.
        //	'langs'=>'mylangfile',
        //	'position'=>100,
        //	// Define condition to show or hide menu entry.
        //	// Use '$conf->consogazoil->enabled' if entry must be visible if module is enabled.
        //	// Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
        //	'enabled'=>'$conf->consogazoil->enabled',
        //	// Use 'perms'=>'$user->rights->consogazoil->level1->level2'
        //	// if you want your menu with a permission rules
        //	'perms'=>'1',
        //	'target'=>'',
        //	// 0=Menu for internal users, 1=external users, 2=both
        //	'user'=>2
        //);
        //$r++;
        // Exports
        $r = 1;

        // Example:
        //$this->export_code[$r]=$this->rights_class.'_'.$r;
        //// Translation key (used only if key ExportDataset_xxx_z not found)
        //$this->export_label[$r]='CustomersInvoicesAndInvoiceLines';
        //// Condition to show export in list (ie: '$user->id==3').
        //// Set to 1 to always show when module is enabled.
        //$this->export_enabled[$r]='1';
        //$this->export_permission[$r]=array(array("facture","facture","export"));
        //$this->export_fields_array[$r]=array(
        //	's.rowid'=>"IdCompany",
        //	's.nom'=>'CompanyName',
        //	's.address'=>'Address',
        //	's.cp'=>'Zip',
        //	's.ville'=>'Town',
        //	's.fk_pays'=>'Country',
        //	's.tel'=>'Phone',
        //	's.siren'=>'ProfId1',
        //	's.siret'=>'ProfId2',
        //	's.ape'=>'ProfId3',
        //	's.idprof4'=>'ProfId4',
        //	's.code_compta'=>'CustomerAccountancyCode',
        //	's.code_compta_fournisseur'=>'SupplierAccountancyCode',
        //	'f.rowid'=>"InvoiceId",
        //	'f.facnumber'=>"InvoiceRef",
        //	'f.datec'=>"InvoiceDateCreation",
        //	'f.datef'=>"DateInvoice",
        //	'f.total'=>"TotalHT",
        //	'f.total_ttc'=>"TotalTTC",
        //	'f.tva'=>"TotalVAT",
        //	'f.paye'=>"InvoicePaid",
        //	'f.fk_statut'=>'InvoiceStatus',
        //	'f.note'=>"InvoiceNote",
        //	'fd.rowid'=>'LineId',
        //	'fd.description'=>"LineDescription",
        //	'fd.price'=>"LineUnitPrice",
        //	'fd.tva_tx'=>"LineVATRate",
        //	'fd.qty'=>"LineQty",
        //	'fd.total_ht'=>"LineTotalHT",
        //	'fd.total_tva'=>"LineTotalTVA",
        //	'fd.total_ttc'=>"LineTotalTTC",
        //	'fd.date_start'=>"DateStart",
        //	'fd.date_end'=>"DateEnd",
        //	'fd.fk_product'=>'ProductId',
        //	'p.ref'=>'ProductRef'
        //);
        //$this->export_entities_array[$r]=array('s.rowid'=>"company",
        //	's.nom'=>'company',
        //	's.address'=>'company',
        //	's.cp'=>'company',
        //	's.ville'=>'company',
        //	's.fk_pays'=>'company',
        //	's.tel'=>'company',
        //	's.siren'=>'company',
        //	's.siret'=>'company',
        //	's.ape'=>'company',
        //	's.idprof4'=>'company',
        //	's.code_compta'=>'company',
        //	's.code_compta_fournisseur'=>'company',
        //	'f.rowid'=>"invoice",
        //	'f.facnumber'=>"invoice",
        //	'f.datec'=>"invoice",
        //	'f.datef'=>"invoice",
        //	'f.total'=>"invoice",
        //	'f.total_ttc'=>"invoice",
        //	'f.tva'=>"invoice",
        //	'f.paye'=>"invoice",
        //	'f.fk_statut'=>'invoice',
        //	'f.note'=>"invoice",
        //	'fd.rowid'=>'invoice_line',
        //	'fd.description'=>"invoice_line",
        //	'fd.price'=>"invoice_line",
        //	'fd.total_ht'=>"invoice_line",
        //	'fd.total_tva'=>"invoice_line",
        //	'fd.total_ttc'=>"invoice_line",
        //	'fd.tva_tx'=>"invoice_line",
        //	'fd.qty'=>"invoice_line",
        //	'fd.date_start'=>"invoice_line",
        //	'fd.date_end'=>"invoice_line",
        //	'fd.fk_product'=>'product',
        //	'p.ref'=>'product'
        //);
        //$this->export_sql_start[$r] = 'SELECT DISTINCT ';
        //$this->export_sql_end[$r] = ' FROM (' . MAIN_DB_PREFIX . 'facture as f, '
        //	. MAIN_DB_PREFIX . 'facturedet as fd, ' . MAIN_DB_PREFIX . 'societe as s)';
        //$this->export_sql_end[$r] .= ' LEFT JOIN ' . MAIN_DB_PREFIX
        //	. 'product as p on (fd.fk_product = p.rowid)';
        //$this->export_sql_end[$r] .= ' WHERE f.fk_soc = s.rowid '
        //	. 'AND f.rowid = fd.fk_facture';
        //$r++;
    }

    /**
     * Function called when module is enabled.
     * The init function add constants, boxes, permissions and menus
     * (defined in constructor) into Dolibarr database.
     * It also creates data directories
     *
     * 	@param		string	$options	Options when enabling module ('', 'noboxes')
     * 	@return		int					1 if OK, 0 if KO
     */
    public function init($options = '')
    {
        $sql = array();

        $result = $this->loadTables();

        return $this->_init($sql, $options);
    }

    /**
     * Function called when module is disabled.
     * Remove from database constants, boxes and permissions from Dolibarr database.
     * Data directories are not deleted
     *
     * 	@param		string	$options	Options when enabling module ('', 'noboxes')
     * 	@return		int					1 if OK, 0 if KO
     */
    public function remove($options = '')
    {
        $sql = array();

        return $this->_remove($sql, $options);
    }

    /**
     * Create tables, keys and data required by module
     * Files llx_table1.sql, llx_table1.key.sql llx_data.sql with create table, create keys
     * and create data commands must be stored in directory /consogazoil/sql/
     * This function is called by this->init
     *
     * 	@return		int		<=0 if KO, >0 if OK
     */
    private function loadTables()
    {
        return $this->_load_tables_consogazoil('/consogazoil/sql/');
    }
    
    /**
     *  Create tables and keys required by module.
     *  Do not use version of Dolibarr because execute script only if version requiered it
     *  Files module.sql and module.key.sql with create table and create keys
     *  commands must be stored in directory reldir='/module/sql/'
     *  This function is called by this->init
     *
     *  @param	string	$reldir		Relative directory where to scan files
     *  @return	int     			<=0 if KO, >0 if OK
     */
    function _load_tables_consogazoil($reldir)
    {
    	global $db,$conf;
    
    	$error=0;
    
    	include_once(DOL_DOCUMENT_ROOT ."/core/lib/admin.lib.php");
    
    	$ok = 1;
    	foreach($conf->file->dol_document_root as $dirroot)
    	{
    		if ($ok)
    		{
    			$dir = $dirroot.$reldir;
    			$ok = 0;
    
    			// Run llx_mytable.sql files
    			$handle=@opendir($dir);         // Dir may not exists
    			if (is_resource($handle))
    			{
    				while (($file = readdir($handle))!==false)
    				{
    					if (preg_match('/\.sql$/i',$file) && ! preg_match('/\.key\.sql$/i',$file) && substr($file,0,4) == 'llx_' && substr($file,0,4) != 'data')
    					{
    						$result=run_sql($dir.$file,1,'',1);
    						if ($result <= 0) $error++;
    					}
    				}
    				closedir($handle);
    			}
    
    			// Run llx_mytable.key.sql files (Must be done after llx_mytable.sql)
    			$handle=@opendir($dir);         // Dir may not exist
    			if (is_resource($handle))
    			{
    				while (($file = readdir($handle))!==false)
    				{
    					if (preg_match('/\.key\.sql$/i',$file) && substr($file,0,4) == 'llx_' && substr($file,0,4) != 'data')
    					{
    						$result=run_sql($dir.$file,1,'',1);
    						if ($result <= 0) $error++;
    					}
    				}
    				closedir($handle);
    			}
    
    			// Run data_xxx.sql files (Must be done after llx_mytable.key.sql)
    			$handle=@opendir($dir);         // Dir may not exist
    			if (is_resource($handle))
    			{
    				while (($file = readdir($handle))!==false)
    				{
    					if (preg_match('/\.sql$/i',$file) && ! preg_match('/\.key\.sql$/i',$file) && substr($file,0,4) == 'data')
    					{
    						$result=run_sql($dir.$file,1,'',1);
    						if ($result <= 0) $error++;
    					}
    				}
    				closedir($handle);
    			}
    
    			// Run update_xxx.sql files
    			$handle=@opendir($dir);         // Dir may not exist
    			if (is_resource($handle))
    			{
    				while (($file = readdir($handle))!==false)
    				{
    					$dorun = false;
    					if (preg_match('/\.sql$/i',$file) && ! preg_match('/\.key\.sql$/i',$file) && substr($file,0,6) == 'update')
    					{
    						//dol_syslog(get_class($this)."::_load_tables_agefodd analyse file:".$file, LOG_DEBUG);
    
    						//Special test to know what kind of update script to run
    						$sql="SELECT value FROM ".MAIN_DB_PREFIX."const WHERE name='GAZOIL_LAST_VERION_INSTALL'";
    
    						//dol_syslog(get_class($this)."::_load_tables_agefodd sql:".$sql, LOG_DEBUG);
    						$resql=$this->db->query($sql);
    						if ($resql) {
    							if ($this->db->num_rows($resql)==1) {
    								$obj = $this->db->fetch_object($resql);
    								$last_version_install=$obj->value;
    								//dol_syslog(get_class($this)."::_load_tables_agefodd last_version_install:".$last_version_install, LOG_DEBUG);
    
    								$tmpversion=explode('_',$file);
    								$fileversion_array=explode('-',$tmpversion[1]);
    								$fileversion=str_replace('.sql','',$fileversion_array[1]);
    								//dol_syslog(get_class($this)."::_load_tables_agefodd fileversion:".$fileversion, LOG_DEBUG);
    								if (version_compare($last_version_install, $fileversion)==-1) {
    									$dorun = true;
    									//dol_syslog(get_class($this)."::_load_tables_agefodd run file:".$file, LOG_DEBUG);
    								}
    
    							}
    						}
    						else
    						{
    							$this->error="Error ".$this->db->lasterror();
    							dol_syslog(get_class($this)."::_load_tables_consogazoil ".$this->error, LOG_ERR);
    							$error ++;
    						}
    
    						if ($dorun) {
    							$result=run_sql($dir.$file,1,'',1);
    							if ($result <= 0) $error++;
    						}
    					}
    				}
    				closedir($handle);
    			}
    
    			if ($error == 0)
    			{
    				$ok = 1;
    			}
    		}
    	}
    
    	//DELETE AGF_LAST_VERION_INSTALL to update with the new one
    	$sql='DELETE FROM '.MAIN_DB_PREFIX.'const WHERE name=\'AGF_LAST_VERION_INSTALL\'';
    	dol_syslog(get_class($this)."::_load_tables_agefodd sql:".$sql, LOG_DEBUG);
    	$resql=$this->db->query($sql);
    	if (!$resql) {
    		$this->error="Error ".$this->db->lasterror();
    		dol_syslog(get_class($this)."::_load_tables_agefodd ".$this->error, LOG_ERR);
    		$error ++;
    	}
    
    	return $ok;
    }
}
