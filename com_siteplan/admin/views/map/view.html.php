<?php
/**
 * @copyright	Copyright (C) 2012 Red Evolution Ltd. All rights reserved.
 * @license
 */

defined('_JEXEC') or die;

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * View class for a list of siteplanion links.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_siteplan
 * @since		2.4
 */
class SiteplanViewMap extends JViewLegacy
{

	/**
	 * Display the view
	 *
	 */
	public function display($tpl = null)
	{

		$doc =JFactory::getDocument();
		$doc->setMetaData( 'X-UA-Compatible', 'IE=8,chrome=IE8',true ); //needed to display in IE
		JFactory::getApplication()->JComponentTitle="<div class='pagetitle '><h2>Site Plan</h2></div>";
		JToolBarHelper::title(JText::_('COM_SITEPLAN'), 'siteplan.png');
		JToolBarHelper::preferences('com_siteplan');
JHtml::_('jquery.framework');
		$css_file='/components/com_siteplan/css/siteplan.css';
		$js_file='/components/com_siteplan/js/siteplan.js';
		//if(JFile::exists(JPATH_SITE.$css_file)){
			$doc->addStyleSheet(JURI::root().$css_file);
			$doc->addScript(JURI::root().$js_file);
		//}
		$map=new SiteplanBuildmap();
		if (!isset($this->data)) $this->data=new stdClass();
		$this->data->map=$map->showMap($this->get("Map"));

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 */
	protected function showMap()
	{


		$this->data=$this->get("Map");
		$this->data->map="";

		foreach($this->data->items as $type=>$item){
				$this->data->map.='
				<div class="siteplan_panel_outer" >
						<div class="siteplan_panel_inner">
						'.$this->buildMap($item, "first","first",0)."<BR>".'
						</div>
				</div>
				';
		}

	}

	/*
	 *
	 * $item: the item to be added to the map
	 * $vertical: vertical position of item (first,norm,last)
	 * $horizontal: horizontal position of item (first,norm,last)
	 * $stack: set when no "nephews" so the siblings "stack" ontop of each other
	 *
	 */
	private function buildMap($item, $vertical, $horizontal, $stack  ){
		$children=count($item["children"]);
		$grandchildren=0;foreach($item["children"] as $key=>$child){$grandchildren+=count($child["children"]);}
		$next_stack=($children>0&&$grandchildren==0)?1:0;

		$html="";
		if (!$stack) $html.="<div class='siteplan_block'>";
		$html.=$this->buildConnections(
								 str_replace("[debug]","",$item["html"]),
								 $vertical,
								 $horizontal,
								 $children,
								 $grandchildren,
								 $stack
		);
		foreach($item["children"] as $key=>$child){

			$nextv="norm";
			$nexth="norm";

			reset($item["children"]);if ($key === key($item["children"])) $nexth="first"; //test for first child
			end($item["children"]);if ($key === key($item["children"])) $nexth=($nexth=="first")?"sole":"last"; //text for last child
			if ($item["level"]==0&&$next_stack&&$nexth=="first") $nextv="first"; //stop upward linkage on 1st item of single level menu.
			$html.=$this->buildMap($child, $nextv, $nexth, $next_stack);
		}
		if (!$stack) $html.="</div>";
		return $html;
	}


	function buildConnections($html, $vertical, $horizontal, $children, $grandchildren, $stack){
		if ($vertical!="last"&&($children!=0&&$grandchildren!=0)){
			$html=str_replace("siteplan_inner_bottom","siteplan_inner_bottom siteplan_link",$html);
		}
		if ($vertical!="first"&&$stack==0){
			$html=str_replace("siteplan_inner_top","siteplan_inner_top siteplan_link",$html);
			$html=str_replace("siteplan_top_center","siteplan_inner_top siteplan_link",$html);
				if ($horizontal!="last"&&$horizontal!="sole"){
					$html=str_replace("siteplan_top_right","siteplan_top_right siteplan_link",$html);
				}
				if ($horizontal!="first"&&$horizontal!="sole"){
					$html=str_replace("siteplan_top_left","siteplan_top_left siteplan_link",$html);
				}
		}
		if ($stack){

			$html=str_replace("siteplan_center_left_right_middle","siteplan_center_left_right_middle siteplan_link",$html);
			$html=str_replace("siteplan_center_left_center_middle","siteplan_center_left_center_middle siteplan_link",$html);

			if($horizontal!="last"&&$horizontal!="sole"){
					$html=str_replace("siteplan_center_left_center_bottom","siteplan_center_left_center_bottom siteplan_link",$html);

			}
			if($children==0&&$vertical!="first"){
					$html=str_replace("siteplan_center_left_center_top","siteplan_center_left_center_top siteplan_link ".$vertical." ".$children."",$html);
			}
		}else{
			if ($children!=0&&$grandchildren==0){
				$html=str_replace("siteplan_center_left_right_middle","siteplan_center_left_right_middle siteplan_link",$html);
				$html=str_replace("siteplan_center_left_center_middle","siteplan_center_left_center_middle siteplan_link",$html);
				$html=str_replace("siteplan_center_left_center_bottom","siteplan_center_left_center_bottom siteplan_link",$html);

			}
		}
		return $html;
	}

}
