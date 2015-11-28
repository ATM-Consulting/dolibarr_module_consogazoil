<?php
/* Copyright (C) 2013      Florian Henry  <florian.henry@open-concept.pro>
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
 * \file consogazoil/lib/import.ajax.lib.php
 * \brief Page called by Ajax request
 */

/**
 * Select for action in quality data import test action
 *
 * @param string $id rowid to update
 * @param string $type type of action type
 * @return string HTML select
 */
function ajax_selectactions($id, $type) {
	global $conf, $langs;
	
	$out = '<script type="text/javascript">
		$(function() {
			var url = \'' . dol_buildpath('/consogazoil/import/ajax/update_temp_table.php', 2) . '\';
			var code = \'' . $id . '\';
			var typesource=\'' . $type . '\';

			// Set value
			$("#act_" + typesource + "_" + code).change(function() {
					 $.get( url, {
						typesource: typesource,
						rowid: code,
						action: $(this).val()
						});
				});
		});
	</script>';
	
	$out .= '<select name="act_' . $type . '_' . $id . '" id="act_' . $type . '_' . $id . '">';
	$out .= '<option value="nothing" selected="selected">' . $langs->trans('ConsoGazTraitementDoNothing') . '</option>';
	if ($type != 'veh') {
		$out .= '<option value="update">' . $langs->trans('ConsoGazColTraitementUpdate') . '</option>';
	}
	
	$out .= '<option value="new">' . $langs->trans('ConsoGazColTraitementNew') . '</option>';
	
	$out .= '</select>';
	
	return $out;
}