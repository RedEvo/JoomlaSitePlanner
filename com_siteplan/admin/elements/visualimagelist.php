<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('radio');
jimport( 'joomla.filesystem.folder' );
jimport('joomla.filesystem.file');
/**
 * Supports an visual HTML select list of image
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class JFormFieldVisualimageList extends JFormFieldRadio
{

	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $type = 'VisualImageList';

	/**
	 * Method to get the list of images field options.
	 * Use the filter attribute to specify allowable file extensions.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   11.1
	 */
	protected function getOptions()
	{
		// Initialize variables.
		$options = array();

		// Initialize some field attributes.
		$filter = '\.png$'; //|\.png$|\.gif$|\.jpg$|\.bmp$|\.ico$|\.jpeg$|\.psd$|\.eps$';
		$exclude = (string) $this->element['exclude'];
		$stripExt = (string) $this->element['stripext'];
		$hideNone = (string) "yes";
		$hideDefault = (string) "yes";

		// Get the path in which to search for file options.
		$path = (string) $this->element['directory'];
		$orig_path=$path;
		$http_path=JURI::root( true ).'/'. $path;
		if (!is_dir($path))
		{
			$path = JPATH_ROOT. '/' . $path;
		}

		// Prepend some default options based on field attributes.
		if (!$hideNone)
		{
			$options[] = JHtml::_('select.option', '-1', JText::alt('JOPTION_DO_NOT_USE', preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)));
		}
		if (!$hideDefault)
		{
			$options[] = JHtml::_('select.option', '', JText::alt('JOPTION_USE_DEFAULT', preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)));
		}

		// Get a list of files in the search path with the given filter.
		$files = JFolder::files($path, $filter);

		// Build the options list from the list of files.
		if (is_array($files))
		{
			foreach ($files as $file)
			{

				// Check to see if the file is in the exclude mask.
				if ($exclude)
				{
					if (preg_match(chr(1) . $exclude . chr(1), $file))
					{
						continue;
					}
				}

				// If the extension is to be stripped, do it.
				if ($stripExt)
				{
					$file = JFile::stripExt($file);
				}

				$options[] = JHtml::_('select.option', $file, "<img src='".$http_path."/".$file.".png'/>");
			}
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
