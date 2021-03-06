 <?php
    // no direct access
    defined('_JEXEC') or die;

    jimport( 'joomla.plugin.plugin' );
    jimport( 'joomla.filesystem.file' );

class plgContentSiteplan extends JPlugin
    {
		public function __construct(&$subject, $params )
			{
			parent::__construct( $subject, $params );
			$this->loadLanguage();

			$app = JFactory::getApplication();
			if($app->isSite()) return; //admin only

			$css_file='/components/com_siteplan/css/siteplan.css';
			$document = JFactory::getDocument();
			if(JFile::exists(JPATH_SITE.$css_file))
				{
				$document->addStyleSheet(JURI::root().$css_file);
				}
			}
		public function onContentPrepareForm($form, $data)
			{
				if (!($form instanceof JForm))
				{
					$this->_subject->setError('JERROR_NOT_A_FORM');
					return false;
				}
			// Check we are manipulating a valid form.
			switch($form->getName()){
                case 'com_content.article':
                    $fieldsName='attribs';
                    break;
                case 'com_categories.categorycom_content':
                    $fieldsName='params';
                    break;
                default:
                    return true;
            }
//			if (!in_array($name, array('com_content.article','com_categories.categorycom_content')))
//			{
//				return true;
//			}

			// Add the extra fields to the form.


			//JForm::addFormPath(dirname(__FILE__) . '/siteplan');
			//$form->loadFile('siteplan', false);

			$xml='
				<form>
					<fields name="'.$fieldsName.'">
						<fieldset name="siteplan" label="PLG_CONTENT_SITEPLAN_SLIDER_LABEL" >
			';

			$params = JComponentHelper::getParams('com_siteplan');
            $enabledTypes=0;
			for($idx=1;$idx<=6;$idx++){
				if ($params->get("siteplan_type".$idx."_enabled")){
					$xml.='
						<field
							name="siteplan_type'.$idx.'"
							type="radio"
							id="siteplan_type'.$idx.'"
							description="'.$params->get("siteplan_type".$idx."_description").'"
							label="'.$params->get("siteplan_type".$idx."_label").'"
							message="PLG_CONTENT_SITEPLAN_FIELD_TEXT_MESSAGE"
							default="0"
						>
							
						<option
							value="INCOMPLETE" >PLG_CONTENT_SITEPLAN_INCOMPLETE</option>
						<option
							value="NEEDED" >PLG_CONTENT_SITEPLAN_NEEDED</option>
						<option
							value="DONE" >PLG_CONTENT_SITEPLAN_DONE</option>
						</field>
					';
                    $enabledTypes++;
				}

			}
            if($enabledTypes==0) $xml.='<field type="spacer" name="notenabled" label="PLG_CONTENT_SITEPLAN_NONE_ENABLED" />';
			$xml.='
						</fieldset>
					</fields>
				</form>
			';
#echo $xml;exit();			
			$form->load($xml, true, false);
			return true;
			}

		public function onContentAfterSave($context, &$article, $isNew)
			{
			return true;
			}
		 public function onContentPrepareData($context, $data)
			{
			return true;
			}


		public function onContentAfterTitle($context, &$article, &$params, $limitstart)
			{
			}
    }
    ?> 
