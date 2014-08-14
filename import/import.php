<?php
/* Copyright (C) 2013 Florian Henry  		<florian.henry@open-concept.pro>
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
 *      \file       consogazoil/imports/import.php
 *      \ingroup    consogazoil
 *      \brief      Pages of import Wizard
 */


$res=@include("../../main.inc.php");					// For root directory
if (! $res) $res=@include("../../../main.inc.php");	// For "custom" directory
if (! $res) die("Include of main fails");

require_once '../class/consogazoildriver.class.php';
require_once '../class/consogazoilstation.class.php';
require_once '../class/consogazoilvehicule.class.php';
require_once '../class/consogazoilvehtake.class.php';
require_once '../class/consogazoilimport.class.php';

require_once 'ajax/import.ajax.lib.php';

require_once DOL_DOCUMENT_ROOT . '/core/lib/files.lib.php';


if (!$user->rights->consogazoil->import) accessforbidden();

$langs->load("exports");
$langs->load("errors");
$langs->load("consogazoil@consogazoil");

$datatoimport	= GETPOST('datatoimport');
$step			= GETPOST('step','int');
$action			= GETPOST('action','alpha');
$confirm		= GETPOST('confirm','alpha');
$urlfile		= GETPOST('urlfile');
$filetoimport	= GETPOST('filetoimport');

$error			= 0;

$dir = $conf->consogazoil->dir_output;

if ($step == 2 && $action=='sendit')
{
	
	if (GETPOST('sendit') && ! empty($conf->global->MAIN_UPLOAD_DOC))
	{
		$nowyearmonth=dol_print_date(dol_now(),'%Y%m%d%H%M%S');
	
		$fullpath=$dir . "/" . $nowyearmonth . '-'.$_FILES['userfile']['name'];
		if (dol_move_uploaded_file($_FILES['userfile']['tmp_name'], $fullpath,1) > 0)
		{
			dol_syslog("File ".$fullpath." was added for import");
		}
		else
		{
			$langs->load("errors");
			setEventMessage($langs->trans("Missingfile"), 'errors');
			setEventMessage($langs->trans("ErrorFailedToSaveFile"), 'errors');
		}
	}
}

// Delete file
if ($action == 'confirm_deletefile' && $confirm == 'yes')
{
	$langs->load("other");
	$file = $dir . '/' . $urlfile;	// Do not use urldecode here ($_GET and $_REQUEST are already decoded by PHP).
	$ret=dol_delete_file($file);
	if ($ret) setEventMessage($langs->trans("FileWasRemoved", $urlfile));
	else setEventMessage($langs->trans("ErrorFailToDeleteFile", $urlfile), 'errors');
	Header('Location: '.$_SERVER["PHP_SELF"].'?step=1');
	exit;
}

//Check data file look like
if ($step == 3 && !(empty($filetoimport)))
{
	$importobject= new ConsogazoilImport($db);
	
	$arrayrecords = array();
	$linearray = array();
	
	$file=$dir.'/'.$filetoimport;
	
	$result=$importobject->import_open_file($file);
	$importobject->separator =";";
	if ($result > 0)
	{
		$nboflines=dol_count_nb_of_line($file);
		if ($nboflines>5) {
			$nboflinestmp=6;
		}else {
			$nboflinestmp=$nboflines;
		}
		$sourcelinenb=0;
		// Loop on each input file record
		while ($sourcelinenb < $nboflinestmp && !$error)
		{
			$sourcelinenb++;
			 
			$linearray=$importobject->import_read_record();
			
			//Do not read the first line : title line
			if ($sourcelinenb != 1 && is_array($linearray)) {
				$arrayrecords[]=$linearray;
				
				if (!is_array($linearray)) {
					$error++;
					setEventMessage($importobject->error, 'errors');
				}
			}
		}
		// Close file
		$importobject->import_close_file();
	}
	else
	{
		$error++;
		setEventMessage($importobject->error, 'errors');
	}
}

//integrate data in temporary tables
if ($step == 4 && !(empty($filetoimport)))
{
	$importobject= new ConsogazoilImport($db);

	$arrayrecords = array();
	$linearray = array();

	$file=$dir.'/'.$filetoimport;

	$result=$importobject->import_open_file($file);
	$importobject->separator =";";
	if ($result > 0)
	{
		$importobject->truncate_temp_table();
		
		$nboflines=dol_count_nb_of_line($file);
		$sourcelinenb=0;
		// Loop on each input file record
		while ($sourcelinenb < $nboflines && !$error)
		{
			$sourcelinenb++;

			$linearray=$importobject->import_read_record();
			
			//Do not read the first line : title line
			if ($sourcelinenb != 1 && is_array($linearray) && count($linearray)>0) {
				
				if ($importobject->col!=$importobject->nbcol) {
						$error++;
						setEventMessage($langs->trans("ConsoGazImportErrorCols",$sourcelinenb), 'errors');
						break;
				}
				if (!$error) {
					$ret=$importobject->import_file_in_temp_table($linearray);
					if ($ret<0) {
						$error++;
						setEventMessage($importobject->error.' on line:'.var_export($linearray,true), 'errors');
					}
				}
			}
		}
		// Close file
		$importobject->import_close_file();
		
		if (!$error) {
			$ret=$importobject->import_check_data();
			if ($ret<0) {
				$error++;
				setEventMessage($importobject->error, 'errors');
			}
		}
	}
	else
	{
		$error++;
		setEventMessage($importobject->error, 'errors');
	}
}

if ($step == 5)
{
	$importobject= new ConsogazoilImport($db);
	$ret=$importobject->import_data($user);
	if ($ret<0) {
		$error++;
		setEventMessage($importobject->error, 'errors');
	}
}

if ($step == 6 && $action='send') {

	$langs->load('mails');
			    
	$sendto = GETPOST('sendto');
			
	if (!empty($sendto))
	{
		$from = GETPOST('fromname') . ' <' . GETPOST('frommail') .'>';
		$message = GETPOST('message');

		if (dol_strlen(GETPOST('subject'))) $subject=GETPOST('subject');
		
		// Send mail
		require_once DOL_DOCUMENT_ROOT.'/core/class/CMailFile.class.php';
		$mailfile = new CMailFile($subject,$sendto,$from,$message);
		if ($mailfile->error)
		{
			setEventMessage($mailfile->error,'errors');
			$error++;
		}
		else
		{
			$result=$mailfile->sendfile();
			if ($result)
			{
				$mesg=$langs->trans('MailSuccessfulySent',$mailfile->getValidAddress($from,2),$mailfile->getValidAddress($sendto,2));	// Must not contains "
				setEventMessage($mesg,'mesgs');
			}
			else
			{
				$langs->load("other");
				if ($mailfile->error)
				{
					$mesg.=$langs->trans('ErrorFailedToSendMail',$from,$sendto);
					$mesg.='<br>'.$mailfile->error;
				}
				else
				{
					$mesg.='No mail sent. Feature is disabled by option MAIN_DISABLE_ALL_MAILS';
				}
				setEventMessage($mesg,'errors');
				$error++;
			}
		}
	}else {
		setEventMessage($langs->trans('ErrorMailRecipientIsEmpty'),'errors');
		$error++;
	}
			
}

/*
 *	View
*/

llxHeader('',$langs->trans("Module103040Name"));

dol_fiche_head($head, 'business', $langs->trans("InformationOnSourceFile"),0,($object->public?'consogazoil@consogazoil':'consogazoil@consogazoil'));

$form = new Form($db);

if ($step==1 || $step==2) {
	
	/*
	 * Confirm delete file
	 */
	if ($action == 'delete')
	{
		$ret=$form->form_confirm($_SERVER["PHP_SELF"].'?urlfile='.urlencode(GETPOST('urlfile')).$param, $langs->trans('DeleteFile'), $langs->trans('ConfirmDeleteFile'), 'confirm_deletefile', '', 0, 1);
		if ($ret == 'html') print '<br>';
	}

	print '<form name="userfile" action="'.$_SERVER["PHP_SELF"].'" enctype="multipart/form-data" METHOD="POST">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="max_file_size" value="'.$conf->maxfilesize.'">';
	print '<input type="hidden" value="2" name="step">';
	print '<input type="hidden" value="sendit" name="action">';
	print '<table class="noborder" width="100%" cellspacing="0" cellpadding="4">';

	$filetoimport='';
	$var=true;

	print '<tr><td colspan="6">'.$langs->trans("ChooseFileToImport",img_picto('','filenew')).'</td></tr>';
	print '<tr><td colspan="6">'.$langs->trans("ConsoGazSampleFile").': <a href="sample/sample_consogazoil_import.csv">'.img_picto('','file').'</a></td></tr>';

	print '<tr class="liste_titre"><td colspan="6">'.$langs->trans("FileWithDataToImport").'</td></tr>';

	// Input file name box
	$var=false;
	print '<tr '.$bc[$var].'><td colspan="6">';
	print '<input type="file"   name="userfile" size="20" maxlength="80"> &nbsp; &nbsp; ';
	print '<input type="submit" class="button" value="'.$langs->trans("AddFile").'" name="sendit">';
	
	print "</tr>\n";

	// Search available imports
	$filearray=dol_dir_list($dir, 'files', 0, '', '', 'name', SORT_DESC);
	if (count($filearray) > 0)
	{
		// Search available files to import
		$i=0;
		foreach ($filearray as $key => $val)
		{
		    $file=$val['name'];

			// readdir return value in ISO and we want UTF8 in memory
			if (! utf8_check($file)) $file=utf8_encode($file);

			if (preg_match('/^\./',$file)) continue;

			$modulepart='b2simport';
			$urlsource=$_SERVER["PHP_SELF"].'?step='.$step.$param.'&filetoimport='.urlencode($filetoimport);
			$relativepath=$file;
			$var=!$var;
			print '<tr '.$bc[$var].'>';
			print '<td width="16">'.img_mime($file).'</td>';
			print '<td>';
    		print '<a href="'.DOL_URL_ROOT.'/document.php?modulepart='.$modulepart.'&file='.urlencode($relativepath).'&step=1'.$param.'" target="_blank">';
    		print $file;
    		print '</a>';
			print '</td>';
			// Affiche taille fichier
			print '<td align="right">'.dol_print_size(dol_filesize($dir.'/'.$file)).'</td>';
			// Affiche date fichier
			print '<td align="right">'.dol_print_date(dol_filemtime($dir.'/'.$file),'dayhour').'</td>';
			// Del button
			print '<td align="right"><a href="'.$_SERVER['PHP_SELF'].'?action=delete&step=2'.$param.'&urlfile='.urlencode($relativepath);
			print '">'.img_delete().'</a></td>';
			// Action button
			print '<td align="right">';
			print '<a href="'.$_SERVER['PHP_SELF'].'?step=3'.$param.'&filetoimport='.urlencode($relativepath).'">'.img_picto($langs->trans("NewImport"),'filenew').'</a>';
			print '</td>';
			print '</tr>';
		}
	}

	print '</table></form>';
}

//Check data file look like
if ($step==3) {
	
	print_fiche_titre($langs->trans("InformationOnSourceFile") .' : '. $filetoimport);
	
	
	print '<b>'.$langs->trans("ConsoGazoilSampleFileData").'</b>';	
	print '<table width="100%" cellspacing="0" cellpadding="4" class="border">';
	print '<tr class="liste_titre">';	
	
	print '<td>'.$langs->trans('ConsoGazColContrat').'</td>';
	print '<td>'.$langs->trans('ConsoGazColCardVeh').'</td>';
	print '<td>'.$langs->trans('ConsoGazColCardDriv').'</td>';
	print '<td>'.$langs->trans('ConsoGazColCdProd').'</td>';
	print '<td>'.$langs->trans('ConsoGazColProd').'</td>';
	print '<td>'.$langs->trans('ConsoGazColVol').'</td>';
	print '<td>'.$langs->trans('ConsoGazColDate').'</td>';
	print '<td>'.$langs->trans('ConsoGazColHour').'</td>';
	print '<td>'.$langs->trans('ConsoGazColPays').'</td>';
	print '<td>'.$langs->trans('ConsoGazColNumSta').'</td>';
	print '<td>'.$langs->trans('ConsoGazColStation').'</td>';
	print '<td>'.$langs->trans('ConsoGazColDtInvoice').'</td>';
	print '<td>'.$langs->trans('ConsoGazColNumInvoice').'</td>';
	print '<td>'.$langs->trans('ConsoGazColTvaTx').'</td>';
	print '<td>'.$langs->trans('ConsoGazColCurrEnle').'</td>';
	print '<td>'.$langs->trans('ConsoGazColHT').'</td>';
	print '<td>'.$langs->trans('ConsoGazColTVA').'</td>';
	print '<td>'.$langs->trans('ConsoGazColTTC').'</td>';
	print '<td>'.$langs->trans('ConsoGazColCurrRec').'</td>';
	print '<td>'.$langs->trans('ConsoGazColHT').'</td>';
	print '<td>'.$langs->trans('ConsoGazColTVA').'</td>';
	print '<td>'.$langs->trans('ConsoGazColTTC').'</td>';
	print '<td>'.$langs->trans('ConsoGazColKM').'</td>';
	print '<td>'.$langs->trans('ConsoGazColImmat').'</td>';
	print '<td>'.$langs->trans('ConsoGazColTypePiece').'</td>';
	
	
	print '</tr>';
	
	$style='impair';	
	for ($i=0; $i < $nboflinestmp-1; $i++) {
		if ($style=='pair') {$style='impair';}
		else {$style='pair';}
	
		$arrayrecord = $arrayrecords[$i];
		print '<tr class="'.$style.'">';
		foreach($arrayrecord as $val) {
			print '<td>'.$val['val'].'</td>';
		}
		print '</tr>';
	}
	
	print '</table>';
	
	dol_fiche_end();
	
	print '<BR>';	
	print '<table><tr>';
	print '<td><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?step=4&filetoimport='.$filetoimport.'">'.$langs->trans("ConsoGazLoadFileOK",$nboflines-1).'</a></td>';
	print '<td><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?step=1">'.$langs->trans("ConsoGazLoadFileKO").'</a></td>';
	print '</tr></table>';
}

//Check data file look like
if ($step==4 && $conf->use_javascript_ajax) {
	print_fiche_titre($langs->trans("InformationDataConsistency") .' : '. $filetoimport);
	
	$noimport=false;
	if (count($importobject->lines)>0){
		print '<b>'.$langs->trans("ConsoGazImportExplanation").'</b>';
		print '<BR>';	
		print '<table width="100%" cellspacing="0" cellpadding="4" class="border">';
		print '<tr class="liste_titre">';
		print '<td><table class="nobordernopadding"><tr><td>'.$langs->trans('ConsoGazColTraitementVeh').'</td><td>'.$form->textwithpicto('',$langs->trans("ConsoGazExplanationColVeh"),1,'help').'</td></tr></table></td>';
		print '<td><table class="nobordernopadding"><tr><td>'.$langs->trans('ConsoGazColTraitementStation').'</td><td>'.$form->textwithpicto('',$langs->trans("ConsoGazExplanationColStation"),1,'help').'</td></tr></table></td>';
		print '<td>'.$langs->trans('ConsoGazColContrat').'</td>';
		print '<td>'.$langs->trans('ConsoGazColCardVeh').'</td>';
		print '<td>'.$langs->trans('ConsoGazColCardDriv').'</td>';
		print '<td>'.$langs->trans('ConsoGazColCdProd').'</td>';
		print '<td>'.$langs->trans('ConsoGazColProd').'</td>';
		print '<td>'.$langs->trans('ConsoGazColVol').'</td>';
		print '<td>'.$langs->trans('ConsoGazColDate').'</td>';
		print '<td>'.$langs->trans('ConsoGazColHour').'</td>';
		print '<td>'.$langs->trans('ConsoGazColPays').'</td>';
		print '<td>'.$langs->trans('ConsoGazColNumSta').'</td>';
		print '<td>'.$langs->trans('ConsoGazColStation').'</td>';
		print '<td>'.$langs->trans('ConsoGazColDtInvoice').'</td>';
		print '<td>'.$langs->trans('ConsoGazColNumInvoice').'</td>';
		print '<td>'.$langs->trans('ConsoGazColTvaTx').'</td>';
		print '<td>'.$langs->trans('ConsoGazColCurrEnle').'</td>';
		print '<td>'.$langs->trans('ConsoGazColHT').'</td>';
		print '<td>'.$langs->trans('ConsoGazColTVA').'</td>';
		print '<td>'.$langs->trans('ConsoGazColTTC').'</td>';
		print '<td>'.$langs->trans('ConsoGazColCurrRec').'</td>';
		print '<td>'.$langs->trans('ConsoGazColHT').'</td>';
		print '<td>'.$langs->trans('ConsoGazColTVA').'</td>';
		print '<td>'.$langs->trans('ConsoGazColTTC').'</td>';
		print '<td>'.$langs->trans('ConsoGazColKM').'</td>';
		print '<td>'.$langs->trans('ConsoGazColImmat').'</td>';
		print '<td>'.$langs->trans('ConsoGazColTypePiece').'</td>';
		
		print '</tr>'."\n";
		
		$style='impair';
		$nblineko=0;
		
		if (!empty($importobject->error)) {
			foreach ($importobject->lines as $line) {
				if ($style=='pair') {$style='impair';}
				else {$style='pair';}
				
				$stylerow='';
				
				print '<tr class="'.$style.'">';
				
				
				//Action Veh
				print '<td>';
				if (empty($line->pb_quality)) {
					if ($line->conflitveh) {
						print'<table class="nobordernopadding"><tr><td>'.img_picto($langs->trans('ConsoGazColTraitementVeh'), 'warning').'</td><td>'.ajax_selectactions($line->id,'veh').'</td></tr></table>';
						print '<input type="hidden" name="veh_id_conflict_'.$line->id.'" value="'.$line->conflitvehrowid.'"/>';
					}else {
						print img_picto('', 'tick');
					}
				}else {
					print'<table class="nobordernopadding"><tr><td>'.img_picto($langs->trans("ConsoGazImportExplanationCol1"), 'error').'</td><td>'.$langs->trans('ConsoGazColNoImport').'<br>'.$line->pb_quality.'</td></tr></table>';
				}
				print '</td>'."\n";
				
				//Action station
				print '<td>';
				if (empty($line->pb_quality)) {
					if ($line->conflitstation) {
						print'<table class="nobordernopadding"><tr><td>'.img_picto($langs->trans('ConsoGazColTraitementStation'), 'warning').'</td>';
						print'<td>'.ajax_selectactions($line->id,'station').'</td></tr></table>';
						print '<input type="hidden" name="contact_id_conflict_'.$line->id.'" value="'.$line->conflitstationrowid.'"/>';
					}else {
						print img_picto('', 'tick');
					}
				}else {
					print'<table class="nobordernopadding"><tr><td>'.img_picto($langs->trans("ConsoGazImportExplanationCol1"), 'error').'</td><td>'.$langs->trans('ConsoGazColNoImport').'<br>'.$line->pb_quality.'</td></tr></table>';
				}
				print '</td>'."\n";
		
				foreach($line->record as $key => $val) {
					if ($key!='rowid' && is_numeric($key)) {
						print '<td>'.$val.'</td>';
					}
				}
				print '</tr>'."\n";
			}
			
			print '</table>';
			
			print '<BR>';
	
		}else {
			print '<b>'.$langs->trans("ConsoGazNoConsistencyProblem").'</b>';
		}
	}else {
		$nblinesintemptable = $importobject->nb_line_to_import();
		if ($nblinesintemptable<0) {
			setEventMessage($importobject->error, 'errors');
		}
		if ($nblinesintemptable>0) {
			print '<b>'.$langs->trans("ConsoGazNoConsistencyProblem").'</b>';
		} else {
			$noimport=true;
			print '<b style="color:red">'.img_picto($langs->trans("ConsoGazErrorImport"), 'error').$langs->trans("ConsoGazErrorImport").'</b>';
		}
	}
	
	dol_fiche_end();
	
	print '<BR>';
	if ($nblineko)	print '<b>'.$langs->trans("ConsoGazNbLigneKO",$nblineko).'</b>';
	print '<BR>';
	print '<BR>';
	print '<table><tr>';
	if (!$noimport) {
		print '<td><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?step=5">'.$langs->trans("ConsoGazNextStep").'</a></td>';
	}
	print '<td><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?step=1">'.$langs->trans("ConsoGazLoadFileKO").'</a></td>';
	print '</tr></table>';
}

//Check data file look like
if ($step==5) {
	print_fiche_titre($langs->trans("InformationResult") .' : '. $filetoimport);
	if (!$error) {
		print '<b>'.$langs->trans("InformationResultSuccess").'</b>';
	
		print_titre($langs->trans('ConsoGazSendEmail'));
		
		// Cree l'objet formulaire mail
		include_once DOL_DOCUMENT_ROOT.'/core/class/html.formmail.class.php';
		$formmail = new FormMail($db);
		$formmail->fromtype = 'user';
		$formmail->fromid   = $user->id;
		$formmail->fromname = $user->getFullName($langs);
		$formmail->frommail = $user->email;
		$formmail->withfrom=1;
		$formmail->withto=$conf->global->GAZOIL_EMAIL_EXPLOIT;;
		$formmail->withbody=1;
		$formmail->withcancel=1;
		$formmail->withtopic=$langs->trans('ConsoGazMailExploitSubject');
		$formmail->withbody=$langs->trans('ConsoGazMailExploitMail');
		$formmail->param['action']='send';
		$formmail->param['returnurl']=$_SERVER["PHP_SELF"].'?step=6';
		
		// Show form
		$formmail->show_form();
	
	
	
	}
	dol_fiche_end();
}

if ($step==6) {
	if (!$error) {
		print '<b>'.$langs->trans("InformationResultSuccess").'</b>';
	}
}

llxFooter();
$db->close();