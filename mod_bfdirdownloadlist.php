<?php
/**
 * @package      Joomla.Site
 * @subpackage   mod_bfdirdownloadlist
 * @copyright    Copyright 2021 Jonathan Brain. All rights reserved.
 * @license      GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;

/**
 * @var Registry $params
 * @var stdClass    $module
 */

// no direct access
defined('_JEXEC') or die;

$lang = Factory::getLanguage();
$lang->load('mod_bfdirdownloadlist', __DIR__);

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

$dirname = $params->get('dirname');
if ($dirname[0] != '/' && $dirname[1] != ':')
{
	$dirname = dirname(JPATH_SITE) . '/' . $dirname;
}

$uri = Joomla\CMS\Uri\Uri::getInstance()->toString();

$filelist = $params->get('filelist');
$translatable = $params->get('translatable');

$filelistIdPrefix = '__field';

$missingFile = false;
foreach($filelist as $id=>&$file)
{
	if ($file->status != '1')
	{
		$file->id = null;
		continue;
	}

	$file->id = substr($id, strlen($filelistIdPrefix));

	if ($translatable)
	{
		$file->userfilename = Text::_($file->userfilename);
		$file->title = Text::_($file->title);
	}

	$file->href = $uri . (strpos($uri, '?') ? '&' : '?') .
		'modbffiledl=' . $module->id . '-' . $file->id . '-' . $file->userfilename;

	$file->realFilename = $dirname . '/' . $file->realfilename;
	$file->exists = is_file($file->realFilename);

	$missingFile |= !$file->exists;
}
unset($file);

$app = Factory::getApplication();

$modbffiledl = $app->input->getCmd('modbffiledl');

if (!empty($modbffiledl))
{
	list($module_id, $file_id, $userfilename) = explode('-', $modbffiledl, 3);

	if($module->id == $module_id)
	{
		foreach($filelist as $id=>$file)
		{
			if ($file->id != $file_id ||
				$file->status != '1' ||
				!$file->exists)
			{
				continue;
			}

			while(ob_get_clean()) {}

			header("Content-Type: application/octet-stream");
			header('Content-Disposition: attachment; filename="' . $file->userfilename . '"');
			header("Content-Length: " . filesize($file->realFilename));
			if (readfile($file->realFilename) !== false)
			{
				$app->close();
			}
		}
		$app->enqueueMessage(Text::_('MOD_BFDIRDOWNLOADLIST_FILENOTFOUND'));
	}
}

if ($missingFile)
{
	$app->enqueueMessage(Text::_('MOD_BFDIRDOWNLOADLIST_MISSINGFILE'));
}

require JModuleHelper::getLayoutPath('mod_bfdirdownloadlist', $params->get('layout', 'default'));
