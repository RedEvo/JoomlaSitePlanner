<?php
/**
 * @version		1.0.1
 * @package		K2 Plugin to manage siteplan component parameters
 * @author    Redevolution - http://www.redevolution.com
 * @copyright	Copyright (c) 2012 Red Eevolution Ltd. All rights reserved.
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

		$ps=$dom->getElementsByTagName('fields');
		foreach($ps as $p) $p->parentNode->removeChild($p);
		foreach($ps as $p) $p->parentNode->removeChild($p);
		foreach($ps as $p) $p->parentNode->removeChild($p);

		//$ps=$dom->getElementsByTagName('fields');
        $cat=$dom->createElement("fields");
        $a=$dom->createAttribute("group");
        $a->value="category";
        $cat->appendChild($a);
        $item=$dom->createElement("fields");
        $a=$dom->createAttribute("group");
        $a->value="item-content";
        $item->appendChild($a);

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
                $a->value="INCOMPLETE";
                $n->appendChild($a);

                $a=$dom->createAttribute("class");
                $a->value="siteplan_radio";
                $n->appendChild($a);

                $a=$dom->createAttribute("ink");
                $a->value="$idx";
                $n->appendChild($a);

                $o=$dom->createElement("option",JText::_("PLG_K2_SITEPLAN_INCOMPLETE"));
                $a=$dom->createAttribute("value"); $a->value="INCOMPLETE";
                $o->appendChild($a);
                $n->appendChild($o);

                $o=$dom->createElement("option",JText::_("PLG_K2_SITEPLAN_NEEDED"));
                $a=$dom->createAttribute("value"); $a->value="NEEDED";
                $o->appendChild($a);
                $n->appendChild($o);

                $o=$dom->createElement("option",JText::_("PLG_K2_SITEPLAN_DONE"));
                $a=$dom->createAttribute("value"); $a->value="DONE";
                $o->appendChild($a);
                $n->appendChild($o);

                //foreach($ps as $p)	$p->appendChild($n);
                $n1=clone $n;
                $cat->appendChild($n);
                $item->appendChild($n1);

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

//                foreach($ps as $p)	$p->appendChild($n);

                $n1= clone $n;
                $cat->appendChild($n);
                $item->appendChild($n1);

        }

        $ext=$dom->getElementsByTagName('extension');
        foreach($ext as $e) {
            $e->appendChild($cat);
            $e->appendChild($item);
        }
        //echo var_export($dom->saveXML(),true);exit();
        if (!$dom->save($filename)){
            echo "cannot write to $filename";
        }
	}

	function onK2PrepareContent( &$item, &$params, $limitstart) {
	}

	function onK2AfterDisplay( &$item, &$params, $limitstart) {
	}

	function onK2BeforeDisplay( &$item, &$params, $limitstart) {
	}

	function onK2AfterDisplayTitle( &$item, &$params, $limitstart) {
	}

	function onK2BeforeDisplayContent( &$item, &$params, $limitstart) {
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

