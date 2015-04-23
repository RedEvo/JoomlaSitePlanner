<?php
/**
 * @version		1.0.1
 * @package		K2 Plugin to manage siteplan component parameters
 * @author    Redevolution - http://www.redevolution.com
 * @copyright	Copyright (c) 2012 Redevolution Ltd. All rights reserved.
 * @license		
 */

// no direct access
defined('_JEXEC') or die ('Restricted access');

/**
 * K2 Plugin to manage siteplan parameters
 */

// Load the K2 Plugin API
JLoader::register('K2Plugin', JPATH_ADMINISTRATOR.'/components/com_k2/lib/k2plugin.php');

// Initiate class to hold plugin events
class plgK2Siteplan extends K2Plugin {

	// Some params
	var $pluginName = 'siteplan';
	var $pluginNameHumanReadable = 'Siteplan Options';

	function plgK2Siteplan( & $subject, $params) {
		parent::__construct($subject, $params);
		$this->loadLanguage();

		$app = JFactory::getApplication();
		if($app->isSite()) return; //admin only

		//load ccs from main siteplan admin compnent
		$css_file='/components/com_siteplan/css/siteplan.css';
		$document = JFactory::getDocument();
		//if(JFile::exists(JPATH_SITE.$css_file)){
			$document->addStyleSheet(JURI::root().$css_file);
		//}

		// update the siteplan.xml definition
		$filename = JPATH_ROOT.'/plugins/k2/siteplan/siteplan.xml';


		$dom=new DOMDocument();
        $dom->formatOutput = true;
        $dom->preserveWhiteSpace = false;

		if (!$dom->load($filename)){
			echo "not loading file...$filename";
			exit();
		}

		$ps=$dom->getElementsByTagName('field');
		foreach($ps as $p) $p->parentNode->removeChild($p);
		foreach($ps as $p) $p->parentNode->removeChild($p);
		foreach($ps as $p) $p->parentNode->removeChild($p);

		$ps=$dom->getElementsByTagName('fields');

        $params = JComponentHelper::getParams('com_siteplan');
        $enabledTypes=0;
        for($idx=1;$idx<=6;$idx++){
            if ($params->get("siteplan_type".$idx."_enabled")){
                $n=$dom->createElement("field");

                $a=$dom->createAttribute("name");
                $a->value="_type".$idx;
                $n->appendChild($a);

                $a=$dom->createAttribute("type");
                $a->value="radio";
                $n->appendChild($a);

                $a=$dom->createAttribute("id");
                $a->value="_type".$idx;
                $n->appendChild($a);

                $a=$dom->createAttribute("description");
                $a->value=$params->get("siteplan_type".$idx."_description");
                $n->appendChild($a);

                $a=$dom->createAttribute("label");
                $a->value=$params->get("siteplan_type".$idx."_label");
                $n->appendChild($a);

                $a=$dom->createAttribute("default");
                $a->value="NOTREQUIRED";
                $n->appendChild($a);

                $a=$dom->createAttribute("class");
                $a->value="siteplan_radio";
                $n->appendChild($a);

                $a=$dom->createAttribute("ink");
                $a->value="$idx";
                $n->appendChild($a);

                $o=$dom->createElement("option",JText::_("PLG_K2_SITEPLAN_NOTREQUIRED"));
                $a=$dom->createAttribute("value"); $a->value="NOTREQUIRED";
                $o->appendChild($a);
                $n->appendChild($o);

                $o=$dom->createElement("option",JText::_("PLG_K2_SITEPLAN_REQUIRED"));
                $a=$dom->createAttribute("value"); $a->value="REQUIRED";
                $o->appendChild($a);
                $n->appendChild($o);

                $o=$dom->createElement("option",JText::_("PLG_K2_SITEPLAN_PRESENT"));
                $a=$dom->createAttribute("value"); $a->value="PRESENT";
                $o->appendChild($a);
                $n->appendChild($o);

                foreach($ps as $p)	$p->appendChild($n);

                $enabledTypes++;
            }

        }
        if($enabledTypes==0) {
                $n=$dom->createElement("field");

                $a=$dom->createAttribute("name");
                $a->value="notenabled";
                $n->appendChild($a);

                $a=$dom->createAttribute("type");
                $a->value="spacer";
                $n->appendChild($a);

                $a=$dom->createAttribute("label");
                $a->value=JText::_("PLG_K2_SITEPLAN_NONE_ENABLED");
                $n->appendChild($a);

                foreach($ps as $p)	$p->appendChild($n);

        }

        //echo var_export($dom->saveXML(),true);exit();
        if (!$dom->save($filename)){
            echo "cannot write to $filename";
        }
	}

	function onK2PrepareContent( &$item, &$params, $limitstart) {
		$mainframe = &JFactory::getApplication();
	}

	function onK2AfterDisplay( &$item, &$params, $limitstart) {
		$mainframe = &JFactory::getApplication();
		return '';
	}

	function onK2BeforeDisplay( &$item, &$params, $limitstart) {
		$mainframe = &JFactory::getApplication();
		return '';
	}

	function onK2AfterDisplayTitle( &$item, &$params, $limitstart) {
		$mainframe = &JFactory::getApplication();
		return '';
	}

	function onK2BeforeDisplayContent( &$item, &$params, $limitstart) {
		$mainframe = &JFactory::getApplication();
		return '';
	}

	function onK2AfterDisplayContent( &$item, &$params, $limitstart) {
	}

	// Event to display (in the frontend) the YouTube URL as entered in the category form
	function onK2CategoryDisplay( & $category, & $params, $limitstart) {
	}

	// Event to display (in the frontend) the YouTube URL as entered in the user form
	function onK2UserDisplay( & $user, & $params, $limitstart) {
	}

} // END CLASS

