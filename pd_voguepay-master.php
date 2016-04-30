<?php
/** 
 * @author Ojosipe Ayomikun
 * @email ojosipeayo@gmail.com
 * @package Pd Compisoft
 * @copyright (C) 2014 - Compisoft
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html  
**/

// Check to ensure this file is included in Joomla!
defined ( '_JEXEC' ) or die ( 'Restricted' );

/**
 * @ Pd Compisoft
 * @ Vers達o 1.0
**/ 
 
jimport ( 'joomla.plugin.plugin' );
class plgContentPd_VoguePay extends JPlugin { 
	
	public function onContentAfterDisplay($context,&$article,&$params,$page=0){ 
		
		// initialize Joomla
		
		$app = JFactory::getApplication();
		$doc = JFactory::getDocument();
		$docType = $doc->getType();
		
		
		if ($app->isAdmin()||JRequest::getCmd('task')=='edit'||JRequest::getCmd('layout')=='edit'){
			return;
		}
		
		$matches = array();
		$overrides = array();

		if(!isset($article->text)){
			$article->text = &$article->introtext;
		}
		
		if (strcmp("html", $docType)!=0) {
			$article->text = preg_replace("/{pdvoguepay}(.*?){\/pdvoguepay}/i",'',$article->text);
			return;
		}

		if(JRequest::getCmd('print')){
			$article->text = preg_replace("/{pdvoguepay}(.*?){\/pdvoguepay}/i",'',$article->text);
			return;
		}
		
		preg_match_all('/{pdvoguepay}(.*?){\/pdvoguepay}/',$article->text,$matches,PREG_PATTERN_ORDER);
		if(count($matches[0])){
			for($i=0;$i<count($matches[0]);$i++){
			
				// loop through tags inner content
				
				$overridesArray = array();
				$overrides = strlen(trim($matches[1][$i])) ? explode( "|",trim($matches[1][$i])) : array(); 
				if(count($overrides)){
					foreach ($overrides as $overrideParam){
						$temp = explode("=",$overrideParam);
						$paramKey = trim($temp[0]);
						$paramVal = trim($temp[1]);
						$overridesArray[$paramKey] = $paramVal;
					}
				}

				// overide params defaults from shortcode 
				$item = $originalAction = array_key_exists('item', $overridesArray) ? $overridesArray['item'] : $this->params->get('p_item');
				$code = array_key_exists('code', $overridesArray) ? $overridesArray['code'] : $this->$params->get('p_code');
				

				 
				$rand = floor(mt_rand(20,1000));
				$developer_code = $params->get('developer_code');


				if($this->params->get('quantity_1', 1)){
					$qty_label = 'Specify Quantity<br /><input type="text" name="total" style="width:120px" /><br />';
				} else {
					$qty_label = '<input type="hidden" name="total" value="1" style="width:120px" /><br />';
				}
				
					
				
				$button_color = $params->get('button_color','blue');
				$button_color = empty($button_color) ? 'blue' : $button_color;
				
				$merchant_id = $params->get('merchant_id');
				
					
				
			$f = '<form method="POST" action="https://voguepay.com/pay/">
			<input type="hidden" name="v_merchant_id" value="'.$this->params->get('merchant_id').'" />';
			
			if(!empty($qty_label)) $f .= $qty_label;

			$f .= "<input type='hidden' name='developer_code' value='56e0022f80a0c' />";

			
			$f .= '<input type="hidden" name="memo" value='.$item.'/>

			<input type="hidden" name="xid_code" value='.$code.'/>
			
			<input type="image" src="https://voguepay.com/images/buttons/buynow_'.$button_color.'.png" alt="Pay with VoguePay" />
			</form>';  


				$finalform = $f;
				$conteudoAnterior = $matches[1][$i];
				$article->text = $article->introtext = str_replace("{pdvoguepay}$conteudoAnterior{/pdvoguepay}",$finalform,$article->text);
			
			}
		}
		return null;
	}
}
