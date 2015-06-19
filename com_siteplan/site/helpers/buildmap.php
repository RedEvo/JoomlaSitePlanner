<?php
/**
 * @copyright	Copyright (C)2012 Red Evolution Ltd, Inc. All rights reserved.
 * @license
 */

// No direct access.
defined('_JEXEC') or die;

class SiteplanBuildmap{
	private $db;
	private $params;
	private $user;
    private $siteplan_id;

    public function __construct(){
        $doc=JFactory::getDocument();
        $doc->addScript(JURI::base().'/components/com_siteplan/js/modernizr.min.js');
        $doc->addScriptDeclaration("jQuery(function() {
        // SVG / PNG
        if(!Modernizr.svg) {
          jQuery('img[src*=\"svg\"]').attr('src', function () {
            return jQuery(this).attr('src').replace('.svg', '.png');
          });
        }
        });
        ");

    }


	public function createMap(){

		$this->params = JComponentHelper::getParams('com_siteplan');
		$this->user=JFactory::getUser();
        $this->db = JFactory::getDBO();

        $query="SELECT extension_id FROM #__extensions WHERE type='component' AND element ='com_siteplan'";
		$this->db->setQuery($query);
		$this->siteplan_id = $this->db->loadResult();


		try{

			$query="SELECT menutype, title  FROM #__menu_types  order by menutype='mainmenu' desc;"; //make mainmenu first
			$this->db->setQuery($query);
			$types = $this->db->loadObjectList();

			$items=array();
			foreach($types as $type){
				$items[$type->title]=array(
                    "name"=>$type->menutype,
                    "html"=>"<div class='siteplan_menu_title'>".$type->title."</div><div class='siteplan_menu_title_spacer'>&nbsp;</div>",
                    "level"=>"0",
                    "children"=>$this->getChildren(1, $type->menutype),
                    'component_is_handled'=>true,

                );
			}


			$return = new stdClass();
			$return->items=$items;
		}
		catch (JException $e)
		{
			echo $e;
			$this->setError($e);
			$return = false;
		}


		return $return;

	}


	private function getChildren($parent_id, $menutype){
		$return=array();
		$this->db = JFactory::getDBO();
		$query="SELECT * FROM #__menu WHERE
		            parent_id='$parent_id' and
		            menutype='".$menutype."' and
		            component_id!='".$this->siteplan_id."' and
		            published>=0
		            ORDER BY lft";
		$this->db->setQuery($query);
		$res = $this->db->loadObjectList();

        // Loop through children of this menu item
		foreach($res as $item){
            $node_data=array(); // info for building html node
    		$link_params=explode_with_keys(str_replace("?","&",$item->link),"&","=");
            $children=$this->getChildren($item->id, $menutype); // get this nodes children

            $asset_id=((array_key_exists("id",$link_params))?$link_params["id"]:""); // whatever component type this is get the id of the article/category etc.
            $node_data['asset_id']=$asset_id;
            $node_data['menu_item']=$item;
            $node_data['link']=$item->link;
            $node_data['id']=$item->id;
            $node_data['title']=$item->title;
            $node_data['published']=$item->published;
            $node_data['level']=$item->level;

            // is this an item type we deal with?
            // where do we find the siteplaner data?
            // has user got authority?
            switch(true){
                case (
                        $item->type=="component" &&
                        array_element_has_value($link_params,'view','article')
                    ):
                    $node_data['component_is_handled']=true;
            		$node_data['component_name']="ARTICLES";

                    $node_data['component_edit_url']="administrator/index.php?option=com_content&task=article.edit&id=".$asset_id;
                    $node_data['attribs']=$this->getAttribs('content',$asset_id);
                    $action="core.edit";
                    $asset="com_content.article.".$asset_id;
                    break;
                case (
                        $item->type=="component" &&
                        array_element_has_value($link_params,'view','category')
                    ):
                    $node_data['component_is_handled']=true;
            		$node_data['component_name']="CATEGORY";
                    $node_data['component_edit_url']="administrator/index.php?option=com_categories&task=category.edit&extension=com_content&id=".$asset_id;
                    $node_data['attribs']=$this->getAttribs('category',$asset_id);
                    $action="core.edit";
                    $asset="com_content.article.".$asset_id;

                    $children=array_merge($children,$this->getContentCategoryItems($item->id,$asset_id, $item->level+1));
                    break;
                case (
                        $item->type=="component" &&
                         array_element_has_value($link_params,'option','com_k2') &&
                         array_element_has_value($link_params,'view','itemlist') &&
                         array_element_has_value($link_params,'layout','category')
                    ):
                    $node_data['component_is_handled']=true;
            		$node_data['component_name']="K2 ITEMLIST";
                    $node_data['attribs']=$this->getAttribs('k2category',$asset_id);
                    $node_data['component_edit_url']="administrator/index.php?option=com_k2&view=category&cid=".$asset_id;
                    $action="core.edit";
                    $asset="com_k2.item.".$asset_id;

                    $children=array_merge($children,$this->getK2CategoryItems($item->id,$asset_id, $item->level+1));
                    break;
                case (
                        $item->type=="component" &&
                         array_element_has_value($link_params,'option','com_k2') &&
                          array_element_has_value($link_params,'view','item') &&
                          array_element_has_value($link_params,'layout','item')
                    ):
                    $node_data['component_is_handled']=true;
            		$node_data['component_name']="K2 ITEM";
                    $node_data['component_table']="#__k2_items";
                    $node_data['component_field']="plugins";
                    $node_data['attribs']=$this->getAttribs('k2item',$asset_id);
                    $node_data['component_edit_url']="administrator/index.php?option=com_k2&view=item&cid=".$asset_id;
                    $action="core.edit";
                    $asset="com_k2.item.".$asset_id;
                    break;
                default:
                    $node_data['component_is_handled']=false;
                    $node_data['component_name']=$this->getComponentName($item); //could be anything so go find out

            }

            if(!$asset_id) $node_data['component_is_handled']=false; //if we don't have an id for the asset don't try getting it's attributes :)

            // does the user have edit access?
            $node_data['edit_access']=false;
            if ($node_data['component_is_handled']){
                if ($this->user->authorise($action, $asset)) {
                    $node_data['edit_access']=true;
                }
            }



			$return[$item->id]=array(
				'name' => $item->title,
				'html' => $this->make_node($node_data),
				"level"=>$item->level,
				'children' => $children,
				'link'=>$item->link,
                'type'=>'menu',

			);
		}
		return $return;
	}

    private function getContentCategoryItems($menu_item_id,$cat_id, $level){
		$return=array();
		$this->db = JFactory::getDBO();
		$query="SELECT *,
 		            CASE WHEN now()
		            	BETWEEN
			            	CASE WHEN publish_up = 0 THEN '2000-01-01' ELSE publish_up END
			            AND
				            CASE WHEN publish_down = 0 THEN '2050-01-01' ELSE publish_down END
                        THEN 1
                        ELSE 0
                    END published
    		 FROM #__content WHERE
		            catid='$cat_id'
		            ORDER BY ordering";
		$this->db->setQuery($query);
		$res = $this->db->loadObjectList();


		foreach($res as $item){

            $node_data=array();
            $node_data['component_name']='ARTICLES';
            $node_data['component_is_handled']=true;
            $node_data['asset_id']=$item->id;
            $node_data['link']='/index.php?option=com_content&view=article&id='.$item->id.':'.$item->alias.'&catid='.$cat_id.'&Itemid='.$menu_item_id; // link to view the article
            $node_data['id']=$item->id;
            $node_data['title']=$item->title;
            $node_data['published']=$item->published;
            $node_data['level']=$level;

            $node_data['component_is_handled']=true;
            $node_data['component_edit_url']="administrator/index.php?option=com_content&view=article&layout=edit&id=".$item->id; //link to edit the article
            $node_data['attribs']=$this->getAttribs('content',$item->id);
            $node_data['edit_access']=true;

			$return[$menu_item_id.'.'.$item->id]=array(
				'name' => $item->title,
				'html' => $this->make_node($node_data),
				"level"=>$level,
				'children' => array(),
				'link'=>$node_data['link']  ,
                'type'=>'k2item'

			);
		}
		return $return;

    }

    private function getK2CategoryItems($menu_item_id,$cat_id, $level){
		$return=array();
		$this->db = JFactory::getDBO();
		$query="SELECT * FROM #__k2_items WHERE
		            catid='$cat_id' and
		            published>=0
		            ORDER BY ordering";
		$this->db->setQuery($query);
		$res = $this->db->loadObjectList();


		foreach($res as $item){

            $item->link=
            $item->level=$level;
            $node_data=array();
            $node_data['component_name']='K2 ITEM';
            $node_data['component_is_handled']=true;
            $node_data['asset_id']=$item->id;
            $node_data['link']='/index.php?option=com_k2&view=item&layout=item&id='.$item->id; // link to view the
            $node_data['id']=$item->id;
            $node_data['title']=$item->title;
            $node_data['published']=$item->published;
            $node_data['level']=$level;

            $node_data['component_is_handled']=true;
            $node_data['attribs']=$this->getAttribs('k2item',$item->id);
            $node_data['component_edit_url']="administrator/index.php?option=com_k2&view=item&cid=".$item->id;
            $node_data['edit_access']=true;

			$return[$menu_item_id.'.'.$item->id]=array(
				'name' => $item->title,
				'html' => $this->make_node($node_data),
				"level"=>$level,
				'children' => array(),
				'link'=>JRoute::_("index.php?option=com_k2&view=item&id=".$item->id.":".$item->alias."&Itemid=".$menu_item_id),
                'type'=>'k2item'

			);
		}
		return $return;

    }

	private function make_node($item){
 		$image_html="<div class=''>";
		$admin_link="<li class='siteplan_context_menu_item hastip' title='[title]'><a class='siteplan_context_menu_item_link' href='[link_location]'>[link_text]</a></li>";
		$admin_links=array();

//		$link_params=explode_with_keys(str_replace("?","&",$item->link),"&","=");

/*        $asset_id=((array_key_exists("id",$link_params))?$link_params["id"]:"");
 		// is this an item type we deal with?
 		// where do we find the siteplaner data?
        // has user got authority?
		$component_is_handled=false;
        switch(true){
            case (
                    $item->type=="component" &&
                    array_element_has_value($link_params,'view','article')
                ):
                $component_is_handled=true;
                $component_table="#__content";
                $component_field="attribs";
                $component_edit_url="administrator/index.php?option=com_content&task=article.edit&id=";
                $action="core.edit";
                $asset="com_content.article.".$asset_id;
                break;
            case (
                    $item->type=="component" &&
                    array_element_has_value($link_params,'view','category')
                ):
                $component_is_handled=true;
                $component_table="#__categories";
                $component_field="params";
                $component_edit_url="administrator/index.php?option=com_categories&task=category.edit&extension=com_content&id=";
                $action="core.edit";
                $asset="com_content.article.".$asset_id;
                break;
            case (
                    $item->type=="component" &&
                     array_element_has_value($link_params,'option','com_k2') &&
                     array_element_has_value($link_params,'view','itemlist') &&
                     array_element_has_value($link_params,'layout','category')
                ):
                $component_is_handled=true;
                $component_table="#__k2_categories";
                $component_field="plugins";
                $component_edit_url="administrator/index.php?option=com_k2&view=category&cid=";
                $action="core.edit";
                $asset="com_k2.item.".$asset_id;
                break;
            case (
                    $item->type=="component" &&
                     array_element_has_value($link_params,'option','com_k2') &&
                      array_element_has_value($link_params,'view','item') &&
                      array_element_has_value($link_params,'layout','item')
                ):
                $component_is_handled=true;
                $component_table="#__k2_items";
                $component_field="plugins";
                $component_edit_url="administrator/index.php?option=com_k2&view=item&cid=";

                $action="core.edit";
                $asset="com_k2.item.".$asset_id;
                break;
            default:
                $component_is_handled=false;
        }

        if(!$asset_id) $component_is_handled=false; //if we don't have an id for the asset don't try getting it's attributes :)

		// does the user have edit access?
		$edit_access=false;
		if ($component_is_handled){
			if ($this->user->authorise($action, $asset)) {
				$edit_access=true;
			}
		}
*/
		$html="";

		if ($item['component_is_handled']){
//			$link_params=explode_with_keys(str_replace("?","&",$item->link),"&","=");
/*				$query="SELECT id, ".$item['component_field']." AS attribs FROM ".$item['component_table']." WHERE id=".$item['asset_id']."";
				$this->db->setQuery($query);
				if (!$atts=$this->db->loadObject()){
					echo "db error 1:".$this->db->getErrorMsg()."<br>".$query;
				}

				$attrib_string=$atts->attribs;

					if (!$attribs=json_decode($attrib_string)){
						$attribs=new stdClass();
					}*/
					if ($mailto=$this->params->get("siteplan_mailto")){ //check mailto has been set in parameters
						$mailto.="?subject=".$this->params->get("siteplan_project_name").": ".$item['title']."";
						$admin_links[]=str_replace(
									"[link_location]",
									"mailto:$mailto",
									str_replace(
										"[link_text]",
										"Mail Content",
										str_replace(
											"[title]",
											"Submit Content by Mail",
											$admin_link
											)
										)
									);
					}
					if ($item['edit_access']) {
						$admin_links[]=str_replace(
									"[link_location]",
									JRoute::_(JURI::root( ).$item['component_edit_url']),
									str_replace(
										"[link_text]",
										"Edit",
										str_replace(
											"[title]",
											"Edit Content",
											$admin_link
											)
										)
									);
					}
					$image_count=0;
					for($idx=1; $idx<=6; $idx++){
						$attrib_property="siteplan_type".$idx;
						$value="DONE";
						if ($this->params->get("siteplan_type".$idx."_enabled",0)!=0){
                            if (property_exists($item['attribs'],$attrib_property)){
                                switch(strtoupper($item['attribs']->$attrib_property)){
                                    case 'INCOMPLETE':
                                        $value='INCOMPLETE';
                                        break;
                                    case 'NEEDED':
                                        $value='NEEDED';
                                        break;
                                    default:
                                        $value='DONE';
                                        break;
                                }
                            }
						}
						if ($this->params->get("siteplan_type".$idx."_enabled")){

							$image_html.='
								<span class="hasTip" title="'.$this->params->get("siteplan_type".$idx."_label")."::".$this->params->get("siteplan_type".$idx."_".strtolower($value)."_tip").'">
								<a href="javascript:{}" value="'.$value.'" class="siteplan_type_link" itemid="'.$item['id'].'" >
								<img alt="" src="'.JURI::root().'/components/com_siteplan/images/types/'.strtolower($value).'/'.$this->params->get("siteplan_type".$idx."_image").'">
								</a>
								</span>
							';

							$image_count++;
							if ($image_count==3) $image_html.="</div><div>";
						}
					}

		}
		$image_html.="</div>";
		$admin_links_html='<ul id="siteplan_menu_'.$item['id'].'" class="menu siteplan_context_menu" >';
		if(count($admin_links)){
			$admin_links_html.='
					<li class="siteplan_context_menu_item siteplan_context_menu_heading">Actions</li>
					'.implode("",$admin_links).'
			';

		}
		$admin_links_html.='</ul>';
		$html.='
			<div class="siteplan_wrapper ">
						<div class="siteplan_top_wrap">
							<div class="siteplan_top_left"></div>
							<div class="siteplan_top_right"></div>
							<div class="siteplan_top_center"></div>
						</div>
				<div class="siteplan_outer_wrap " >
					'.$admin_links_html.'

						<div class="siteplan_center_wrap ">

							<div class="siteplan_center_left_wrap">
								<div class="siteplan_center_left_left">
									<div class="siteplan_center_left_left_top"></div>
									<div class="siteplan_center_left_left_middle"></div>
									<div class="siteplan_center_left_left_bottom"></div>
								</div>
								<div class="siteplan_center_left_center">
									<div class="siteplan_center_left_center_top"></div>
									<div class="siteplan_center_left_center_middle"></div>
									<div class="siteplan_center_left_center_bottom"></div>
								</div>
								<div class="siteplan_center_left_right">
									<div class="siteplan_center_left_right_top"></div>
									<div class="siteplan_center_left_right_middle"></div>
									<div class="siteplan_center_left_right_bottom"></div>
								</div>
							</div>
							<div class="siteplan_inner_wrap ">
								<div class="siteplan_item">
									<div class="siteplan_inner_top"></div>
									<div  class="siteplanInner '.(($item['published']==0)?"siteplan_unpublished":"").' siteplan_level_'.$item['level'].'">
										<div class="siteplan_item_title">
											<a href="'.str_replace("/administrator/","/",JRoute::_($item['link']."&Itemid=".$item['id'])).'">'.$item['title'].'</a>
										</div>
										<div class="siteplan_item_icons">'.$image_html.'</div>
										<div class="siteplan_item_type">
											'.strtoupper($item['component_name']).'[debug]
										</div>
									</div>
									<div class="siteplan_inner_bottom"></div>
								</div>
								<!--<div class="siteplan_bottom_wrap">
									<div class="siteplan_bottom_wrap_left"></div>
									<div class="siteplan_bottom_wrap_center"></div>
									<div class="siteplan_bottom_wrap_right"></div>
								</div>-->
							</div>
							<div class="siteplan_center_right_wrap">
							</div>
						</div>
				</div>
			</div>
		';
	return $html;
	}

	function getComponentName($item){
		$component_id=(property_exists($item,"component_id"))?$item->component_id:"0";
		$type=(property_exists($item,"type"))?$item->type:"";
		$link_params=explode_with_keys(str_replace("?","&",$item->link),"&","=");
		if ($type=="component"){

			try{
				$this->db = JFactory::getDBO();
				$query="SELECT element FROM #__extensions WHERE extension_id=$component_id;";
				$this->db->setQuery($query);
				$component_name = $this->db->loadResult();
				$this->component_names[$component_id]=strtoupper($component_name);
			}
			catch (JException $e)
			{
				$this->component_names[$component_id]="unfound";
			}

			JFactory::getLanguage()->load(strtolower($this->component_names[$component_id]),JPATH_ADMINISTRATOR);
			if($this->component_names[$component_id]=="COM_CONTENT"){
				if (array_key_exists("view",$link_params)){
					if($link_params["view"]!="article"){
						return JText::_(strtoupper($link_params["view"]));
					}
				}

			}
			if($this->component_names[$component_id]=="COM_K2"){
				if (array_key_exists("view",$link_params)){
					return "K2 ".JText::_(strtoupper($link_params["view"]));
				}

			}

			return JText::_(strtoupper($this->component_names[$component_id]));
		}
		return $type;

	}


	public function showMap($data=null)
	{
    if(!isset($data)) $data=$this->createMap();
    $map_html="";
		foreach($data->items as $type=>$item){

				$map_html.='
				<div class="siteplan_panel_outer" >
						<div class="siteplan_panel_inner">
						'.$this->buildMap($item, "first","first",0)."<BR>".'
						</div>
				</div>
				';
		}
		return $map_html;
	}

	/*
	 *
	 * $item: the item to be added to the map
	 * $vertical: vertical position of item (first,norm,last)
	 * $horizontal: horizontal position of item (first,norm,last)
	 * $stack: set when no "nephews" so the siblings "stack" ontop of each other
	 *
	 */
	function buildMap($item, $vertical, $horizontal, $stack  ){
		$children=count($item["children"]);
		$grandchildren=0;foreach($item["children"] as $key=>$child){$grandchildren+=count($child["children"]);}
		$next_stack=($children>0&&$grandchildren==0&&$item["level"]>0)?1:0;

		$html="<!--vertical-$vertical horizontal-$horizontal children-$children grandchildren-$grandchildren stack-$stack level-".$item["level"]."-->";
		if (!$stack) {
			#$html.="<div class='siteplan_block ".(($item["level"]>1)?"siteplan_node_hidden":"siteplan_node_visible")." ' children='".$children."'>";
			$html.="<div class='siteplan_block' style='display:".(($item["level"]>1)?"none":"inline-block")." ' children='".$children."'>";
			#$html.="<div class='siteplan_block '>";
		}

		$html.=$this->buildConnections(
			str_replace("[debug]","",$item["html"]),
			$vertical,
			$horizontal,
			$children,
			$grandchildren,
			$stack,
			$item["level"]
		);
		foreach($item["children"] as $key=>$child){

			$nextv="norm";
			$nexth="norm";

			reset($item["children"]);if ($key === key($item["children"])) $nexth="first"; //test for first child
			end($item["children"]);if ($key === key($item["children"])) $nexth=($nexth=="first")?"sole":"last"; //test for last child
			if ($item["level"]==0&&$next_stack&&$nexth=="first") $nextv="first"; //stop upward linkage on 1st item of single level menu.
			if ($item["level"]==0&&$children==1) $nextv="first";

			$html.=$this->buildMap($child, $nextv, $nexth, $next_stack);
		}
		if (!$stack) $html.="</div>";
		return $html;
	}


	function buildConnections($html, $vertical, $horizontal, $children, $grandchildren, $stack, $level){

		if ($vertical!="last"&&($children!=0&&$grandchildren!=0)){
			$html=str_replace("siteplan_inner_bottom","siteplan_inner_bottom siteplan_link $level ".(($level>0)?"siteplan_link_expand ":""),$html);
		}
		if ($vertical!="first"&&$stack==0){
			$html=str_replace("siteplan_inner_top","siteplan_inner_top siteplan_link",$html);
			$html=str_replace("siteplan_top_center","siteplan_top_center siteplan_link",$html);
				if ($horizontal!="last"&&$horizontal!="sole"){
					$html=str_replace("siteplan_top_right","siteplan_top_right siteplan_link",$html);
				}
				if ($horizontal!="first"&&$horizontal!="sole"){
					$html=str_replace("siteplan_top_left","siteplan_top_left siteplan_link",$html);
				}
		}
		if ($stack){

			if ($horizontal!="sole"||$level>1) {
				$html=str_replace("siteplan_center_left_right_middle","siteplan_center_left_right_middle siteplan_link",$html);
				$html=str_replace("siteplan_center_left_center_middle","siteplan_center_left_center_middle siteplan_link",$html);
			}

			if($horizontal!="last"&&$horizontal!="sole"){
					$html=str_replace("siteplan_center_left_center_bottom","siteplan_center_left_center_bottom siteplan_link",$html);

			}
			if($children==0&&$vertical!="first"&&($horizontal!="sole"||$level>1)){
					$html=str_replace("siteplan_center_left_center_top","siteplan_center_left_center_top siteplan_link",$html);
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

    function getAttribs($type, $id)
    {
        switch ($type) {
            case 'content':
                $query = "SELECT id, attribs AS attribs FROM #__content WHERE id=" . $id . "";
                break;
            case 'category':
                $query = "SELECT id, params AS attribs FROM #__categories WHERE id=" . $id . "";
                break;
            case 'k2category':
                $query = "SELECT id, plugins AS attribs FROM #__k2_categories WHERE id=" . $id . "";
                break;
            case 'k2item':
                $query = "SELECT id, plugins AS attribs FROM #__k2_items WHERE id=" . $id . "";
                break;

        }

        $this->db->setQuery($query);
        if (!$atts = $this->db->loadObject()) {
            echo "db error 1:" . $this->db->getErrorMsg() . "<br>" . $query;
        }

        $attrib_string = $atts->attribs;

        if (!$attribs = json_decode($attrib_string)) {
            $attribs = new stdClass();
        }
        return $attribs;
    }

}
function explode_with_keys($string, $del1, $del2){
	$return=array();
	foreach (explode($del1, $string) as $p) {
		$bits=explode($del2, $p);
		if (count($bits)>1) $return[$bits[0]]=$bits[1];
	}

	return $return;
}
function array_element_has_value($array, $key, $value){
    if (array_key_exists($key,$array)){
        if($array[$key]==$value ) return true;
    }
    return false;
}