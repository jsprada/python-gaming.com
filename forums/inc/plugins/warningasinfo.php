<?php
/* ****************************************************************************
 * ********************************             *******************************
 * ********************************   LICENSE   *******************************
 * ********************************             *******************************
 * ****************************************************************************
 *
 * Copyright 2015 Nikolai Neugebauer
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * 
 * ****************************************************************************
 * *******************************               ******************************
 * *******************************   FILE INFO   ******************************
 * *******************************               ******************************
 * ****************************************************************************
 * 
 * Warning as info plugin
 * Plugin file (/inc/plugins/warningasinfo.php)
 * 
 * Author: Nik101010
 * Copyright: © 2015 Nikolai Neugebauer
 *  
 * Website: http://nik101010.de/mybb-plugins/warning-as-info/
 * License: Apache License, Version 2.0
 *
 * Allows to send warnings without increasing the warning level.
 * 
 * ***************************************************************************/
 
if(!defined('IN_MYBB')) 
{
	die('This file cannot be accessed directly.');
}

if(defined('THIS_SCRIPT'))
{
	if(THIS_SCRIPT == 'warnings.php')
	{
		global $templatelist;
		$templatelist .= ',warnings_warningasinfo,warnings_warningasinfo_script';

		// add hooks
		if(!defined('IN_ADMINCP'))
		{
			$plugins->add_hook('warnings_start', 'warningasinfo_warningStart');
			$plugins->add_hook('warnings_do_warn_start', 'warningasinfo_warningDoStart');
			$plugins->add_hook('datahandler_warnings_validate_warning', 'warningasinfo_validateWarning');
		}
	}
}

function warningasinfo_info()
{
	global $lang;
	$lang->load("warningasinfo");

	return array(
		"name"			=> "Warning as info",
		"description"	=> $lang->warningasinfo_desc,
		"website"		=> "http://nik101010.de/mybb-plugins/warning-as-info/",
		"author"		=> "Nik101010",
		"authorsite"	=> "http://nik101010.de/",
		"version"		=> "1.0",
		"compatibility"	=> "18*",
		"codename"      => "warningasinfo"
	);
}

function warningasinfo_activate()
{
	global $db;
	require_once MYBB_ROOT.'inc/adminfunctions_templates.php';

	/* **********
	 * TEMPLATES
	 */
	$script_template = "if(checked == 'custom' || !$('#warningasinfo_sendInfo').prop('checked'))
{
	$('#type_'+checked).show();
}";

	$checkbox_template = '<tr>
	<td class="trow1"><strong>{$lang->warningasinfo_title}:</strong></td>
	<td class="trow1"><label style="vertical-align: middle;"><input type="checkbox" id="warningasinfo_sendInfo" class="checkbox"  style="vertical-align: middle;" onclick="checkType();" name="warningasinfo_sendInfo" value="1" /> {$lang->warningasinfo_label}</label></td>
</tr>';

	/* **********
	 * TEMPLATES END
	 */
	


	// delete the templates we want to create, just in case they are still existing from a previous activation
	$db->delete_query('templates', '`title` = \'warnings_warningasinfo_script\' OR `title` = \'warnings_warningasinfo\'');

	// create the templates
	$script_template_db = array(
		'title' => 'warnings_warningasinfo_script',
		'template' => $db->escape_string($script_template),
		'sid' => -2,
		'version' => '1000',
		'status' => '',
		'dateline' => TIME_NOW
	);

	$checkbox_template_db = array(
		'title' => 'warnings_warningasinfo',
		'template' => $db->escape_string($checkbox_template),
		'sid' => -2,
		'version' => '1000',
		'status' => '',
		'dateline' => TIME_NOW
	);

	$db->insert_query_multiple('templates', array($script_template_db, $checkbox_template_db));


	// modify the warning_warn template
	find_replace_templatesets('warnings_warn', '!'.preg_quote("\$('#type_'+checked).show();").'!', "{\$warningasinfo_script}");
	find_replace_templatesets('warnings_warn', '#'.preg_quote("{\$pm_notify}").'#', "{\$warningasinfo_checkbox}\n\t\t{\$pm_notify}");
}


function warningasinfo_deactivate()
{
	global $db;
	require_once MYBB_ROOT.'inc/adminfunctions_templates.php';
	
	// reset the templates
	find_replace_templatesets('warnings_warn', '#'.preg_quote('{$warningasinfo_script}').'#', "$('#type_'+checked).show();");
	find_replace_templatesets('warnings_warn', '#'.preg_quote("\n\t\t{\$warningasinfo_checkbox}").'#', '');

	// delete custom templates
	$db->delete_query('templates', '`title` = \'warnings_warningasinfo_script\' OR `title` = \'warnings_warningasinfo\'');
}

function warningasinfo_warningStart()
{
	global $lang, $templates, $warningasinfo_checkbox, $warningasinfo_script;
	$lang->load('warningasinfo');

	$warningasinfo_checkbox = eval($templates->render('warnings_warningasinfo'));
	$warningasinfo_script = eval($templates->render('warnings_warningasinfo_script'));
}

function warningasinfo_warningDoStart()
{
	global $mybb;

	if($mybb->get_input('warningasinfo_sendInfo', MyBB::INPUT_INT))
	{
		// we are not giving any points --> "disable" max warning points setting
		$mybb->settings['warningasinfo_temp_maxwarningpoints'] = $mybb->settings['maxwarningpoints'];
		$mybb->settings['maxwarningpoints'] = PHP_INT_MAX;

		// set custom points to a value > 0 to make sure validation does not fail
		$mybb->input['custom_points'] = 1;
	}
}

function warningasinfo_validateWarning(&$datahandler)
{
	global $mybb;

	if($mybb->get_input('warningasinfo_sendInfo', MyBB::INPUT_INT))
	{
		// set back the settings change
		$mybb->settings['maxwarningpoints'] = $mybb->settings['warningasinfo_temp_maxwarningpoints'];
		unset($mybb->settings['warningasinfo_temp_maxwarningpoints']);

		// pointer on the warning data
		$warning = &$datahandler->data;

		// set the points to 0
		$warning['points'] = 0;
	}
}