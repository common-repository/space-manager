<?php

require_once('space-manager-notices.class.php');

// Add array_replace function for PHP < 5.3

if (!function_exists('array_replace')){ 

	function array_replace() 
	{ 
    	
    	$array=array();    
    
    	$n=func_num_args(); 
	
	    while ($n-- >0) { 
    	
    	    $array+=func_get_arg($n); 
	
	    } 
    
    	return $array; 
	
	}

} 

if(!class_exists('SpaceManager'))
{

	abstract class SpaceManager
	{
	
		const ACTION_ZONE_DELETE = 'zone_delete';
		const ACTION_ZONE_ADD = 'zone_add';
		const ACTION_SPACE_DELETE = 'space_delete';
		const ACTION_SPACE_EDIT = 'space_edit';
		const ACTION_SPACE_ADD = 'space_add';
		
		const PAGE_ZONES_VIEW = 'zones_view';
		const PAGE_SPACES_VIEW = 'spaces_view';
		const PAGE_SPACE_VIEW = 'space_view';
		
		const VAR_ZONE_ID = 'zone_id';
		const VAR_SPACE_ID = 'space_id';
		
		const VAR_ZONE_EMPTY_ID = 'new';
		const VAR_SPACE_EMPTY_ID = 'new';
		
		const STR_SPACES_ZONE_DELETED = 'zone_deleted';
		const STR_SPACE_ZONE_NAME_EMPTY = 'zone_name_empty';
		const STR_SPACE_ZONE_MISSING = 'space_zone_missing';
		const STR_INVALID_POST = 'invalid_post';
		const STR_SPACE_NAME_EMPTY = 'space_name_empty';
		const STR_SPACE_MISSING = 'space_missing';
		const STR_OPTION_UPDATE_FAILED = 'option_update_failed';
		const STR_SPACE_ZONE_ADDED = 'space_zone_added';
		const STR_SPACE_ZONE_DELETED = 'space_zone_deleted';
		const STR_SPACE_ADDED = 'space_added';
		const STR_SPACE_UPDATED = 'space_updated';
		const STR_EDIT_ZONES = 'edit_zones';
		const STR_EXPLAIN_ZONES = 'explain_zones';
		const STR_ZONE_NAME = 'zone_name';
		const STR_NUM_SPACES = 'num_spaces';
		const STR_NEW_SPACE = 'new_space';
		const STR_EDIT_SPACES = 'edit_spaces';
		const STR_DELETE_ZONE = 'delete_zone';
		const STR_NEW_ZONE = 'new_zone';
		const STR_SAVE = 'save';
		const STR_EDITING_SPACES_IN = 'editing_spaces_in';
		const STR_RETURN_TO_ZONES = 'return_to_zones';
		const STR_SPACE_NAME = 'space_name';
		const STR_EDIT_SPACE = 'edit_space';
		const STR_DELETE_SPACE = 'delete_space';
		const STR_ZONE_EMPTY = 'zone_empty';
		const STR_RETURN_TO_ADS_IN_ZONE = 'return_to_ads_in_zone';
		const STR_ADD_TITLE_AND_CONTENT = 'add_title_and_content';
		const STR_TITLE = 'title';
		const STR_MENU_NAME = 'menu_name';
		const STR_PAGE_NAME = 'page_name';
		
		private $notices;
		
		public function __construct()
		{
		
			$this->notices = new SpaceManagerNotices();
			
			$this->checkUpdate();
			
			add_action('admin_menu', array($this, 'addSpaceManagerMenu'),5);
			
			add_filter('plugin_action_links', array($this, 'settingsLink'),5, 2);
			
			add_action('admin_init', array($this,'doAction'), 5);
			
			add_action('widgets_init', array($this,'registerWidget'), 5);
			
		}
		
		public function addSpaceManagerMenu()
		{
			add_submenu_page(
				$this->parentPage(), 
				$this->getString(self::STR_MENU_NAME),
				$this->getString(self::STR_PAGE_NAME), 
				$this->capability(), 
				$this->menuSlug(), 
				array($this, 'pages')
			);
		}
		
		public function settingsLink( $links, $file ) { 
			 
			 if ($file == plugin_basename(__FILE__)) {
			 
			 	$links[] = '<a href="'.admin_url('options-general.php?page='.$this->ns()).'">'.__('Settings',$this->ns()).'</a>'; 
			 	
			 }
			 
			 return $links;
		 
		}
		
		public function doAction()
		{
			
			if(!isset($_REQUEST[$this->actionVar()]) || !isset($_REQUEST[$this->nonceVar()])) return false;
			
			if(!current_user_can($this->capability())) die('Security check');
			
			$action = $_REQUEST[$this->actionVar()];
			
			$nonce = $_REQUEST[$this->nonceVar()];
			
			if(!wp_verify_nonce($nonce, $action)) die('Security check');

			switch($action)
			{
				case self::ACTION_ZONE_ADD:
				
					if(!isset($_REQUEST[$this->optName()])) return false;
					
					$this->actionZoneAdded($_REQUEST[$this->optName()]);
					
				break;
				case self::ACTION_ZONE_DELETE:
				
					if(!isset($_REQUEST[self::VAR_ZONE_ID])) return false;
					
					$this->actionZoneDelete($_REQUEST[self::VAR_ZONE_ID]);
					
				break;
				case self::ACTION_SPACE_ADD:
				
					if(!isset($_REQUEST[$this->optName()])) return false;
					
					if(!isset($_REQUEST[self::VAR_SPACE_ID])) return false;
					
					$option = $_REQUEST[$this->optName()];
					
					$space_id = $_REQUEST[self::VAR_SPACE_ID];
					
					if(self::VAR_SPACE_EMPTY_ID == $space_id)
					{
						$this->actionSpaceAdd($option);
					}
					else
					{
						$this->actionSpaceUpdate($option, $space_id);
					}
				
				break;
				case self::ACTION_SPACE_DELETE:
					
					if(!isset($_REQUEST[self::VAR_ZONE_ID])) return false;
					
					if(!isset($_REQUEST[self::VAR_SPACE_ID])) return false;
					
					$this->actionSpaceDelete($_REQUEST[self::VAR_ZONE_ID], $_REQUEST[self::VAR_SPACE_ID]);
				
				break;
			}
			
		}
		
		public function registerWidget()
		{

			if(false !== $this->widgetClass())
			{
			
				if(class_exists($this->widgetClass()))
				{
				
					return register_widget($this->widgetClass());
				
				}
			
			}
						
		}
		
		/* Actions */
		private function actionZoneAdded($new_zone)
		{
			
			if(!is_array($new_zone)) return;
			
			$option = $this->getOption();
			
			if(!array_key_exists(self::VAR_ZONE_EMPTY_ID, $new_zone)) {
				$this->addNotice(self::STR_INVALID_POST);
				return;
			
			}
			
			if(isset($new_zone[self::VAR_ZONE_EMPTY_ID][$this->nameKey()]))
			{

				if('' == $new_zone[self::VAR_ZONE_EMPTY_ID][$this->nameKey()])
				{
				
					$this->addNotice(self::STR_SPACE_ZONE_NAME_EMPTY);
					
					return;
					
				}
				
				$option[] = array(
					$this->nameKey() =>$new_zone[self::VAR_ZONE_EMPTY_ID][$this->nameKey()],
					$this->spacesKey() => array()
				);
				
			}
						
			if($this->updateOption($option))
			{
				
				$this->addNotice(self::STR_SPACE_ZONE_ADDED);
				
			}
			else
			{
			
				$this->addNotice(self::STR_OPTION_UPDATE_FAILED);
			
			}
			
		}
		
		private function actionZoneDelete($zone_id)
		{
		
			$old_zones = $this->getOption();
		
			if(array_key_exists($zone_id, $old_zones))
			{
		
				unset($old_zones[$zone_id]);
		
			}
			else
			{
				$this->addNotice(self::STR_SPACE_ZONE_MISSING);
				
				return;
				
			}
			
			if($this->updateOption($old_zones))
			{
			
				$this->addNotice(self::STR_SPACE_ZONE_DELETED);
			
			}
			else
			{
			
				$this->addNotice(self::STR_OPTION_UPDATE_FAILED);
			
			}
		
		}
		
		
		
		private function actionSpaceAdd($new_option)
		{
			
			$option = $this->getOption();
			
			if(!is_array($new_option)) return;
						
			$zone_id = key($new_option);
			
			if(!isset($option[$zone_id]))
			{
				
				$this->addNotice(self::STR_SPACES_ZONE_DELETED);
				
				return;
			
			}
			
			if(
				!isset($new_option[$zone_id][$this->spacesKey()]) || 
				!is_array($new_option[$zone_id][$this->spacesKey()]) ||
				!array_key_exists(self::VAR_SPACE_EMPTY_ID, $new_option[$zone_id][$this->spacesKey()])
			) 
			{
			
				$this->addNotice(self::STR_INVALID_POST);
				
				return;
				
			}
			
			if('' == $new_option[$zone_id][$this->spacesKey()][self::VAR_SPACE_EMPTY_ID][$this->nameKey()])
			{
			
				$this->addNotice(self::STR_SPACE_NAME_EMPTY);
				
				return;
			
			}
			
			if(!is_array($option[$zone_id][$this->spacesKey()]))
			{
			
				$option[$zone_id][$this->spacesKey()] = array();
				
			}
					
			$option[$zone_id][$this->spacesKey()][] = $new_option[$zone_id][$this->spacesKey()][self::VAR_SPACE_EMPTY_ID];
						
			if($this->updateOption($option))
			{
				
				$this->addNotice(self::STR_SPACE_ADDED);
				
				// Get the last element so we can reload edit page
				$option = $this->getOption();

				$space_id = array_pop(array_keys($option[$zone_id][$this->spacesKey()]));

				$_REQUEST[self::VAR_SPACE_ID] = $space_id;
				
			}
			else
			{
			
				$this->addNotice(self::STR_OPTION_UPDATE_FAILED);
			
			}
					
			
		}
		
		private function actionSpaceUpdate($new_option, $space_id)
		{
		
			$option = $this->getOption();
			
			if(!is_array($new_option)) return;
						
			$zone_id = key($new_option);
			
			if(!isset($option[$zone_id]))
			{
				
				$this->addNotice(self::STR_SPACES_ZONE_DELETED);
				
				return;
			
			}
			
			if('' == $new_option[$zone_id][$this->spacesKey()][$space_id][$this->nameKey()])
			{
			
				$this->addNotice(self::STR_SPACE_NAME_EMPTY);
				
				return;
			
			}
			
			
			if(
				!isset($new_option[$zone_id][$this->spacesKey()]) ||
				!is_array($new_option[$zone_id][$this->spacesKey()]) ||
				!array_key_exists($space_id, $new_option[$zone_id][$this->spacesKey()])
			)
			{
			
				$this->addNotice(self::STR_INVALID_POST);
				
				return;
				
			}
			
			if(!is_array($option[$zone_id][$this->spacesKey()]))
			{
			
				$option[$zone_id][$this->spacesKey()] = array();
				
			}
			
			if(!array_key_exists($space_id, $new_option[$zone_id][$this->spacesKey()]))
			{
			
				$new_option[$zone_id][$this->spacesKey()] = array($this->nameKey()=>'', $this->spacesKey()=>array());	
			
			}
			
			$option[$zone_id][$this->spacesKey()][$space_id] = $new_option[$zone_id][$this->spacesKey()][$space_id];
						
			if($this->updateOption($option))
			{
				
				$this->addNotice(self::STR_SPACE_UPDATED);
				
			}
			else
			{
			
				$this->addNotice(self::STR_OPTION_UPDATE_FAILED);
			
			}				
						
		}
		
		private function actionSpaceDelete($zone_id, $space_id)
		{
		
			$option = $this->getOption();
		
			if(array_key_exists($zone_id, $option))
			{
				
				if(array_key_exists($space_id, $option[$zone_id][$this->spacesKey()]))
				{
					unset($option[$zone_id][$this->spacesKey()][$space_id]);
				}
		
			}
			
			$this->updateOption($option);
			
		}
		
		/* Feedback */
		public function doFeedback()
		{

			$screen = get_current_screen();
			
			if(strcmp($screen->id, 'settings_page_'.$this->ns()) !== 0) return false;
			
			$output = $this->notices->getNotices();
			
			echo $output;
			
		}
		
		
		/* Pages */
		
		public function pages()
		{
			
			if(!isset($_GET[$this->pageVar()]))
			{
				$page = self::PAGE_ZONES_VIEW;
			}
			else
			{
				$page = $_GET[$this->pageVar()];
			}
			
			if(!current_user_can($this->capability())) die('Security check');
			
			switch($page)
			{
				case self::PAGE_SPACES_VIEW:
				
					if(!isset($_REQUEST[self::VAR_ZONE_ID])) return false;
					
					$this->pageSpacesView($_REQUEST[self::VAR_ZONE_ID]);
					
				break;
				case self::PAGE_SPACE_VIEW:
				
					if(!isset($_REQUEST[self::VAR_ZONE_ID])) return false;
					
					if(isset($_REQUEST[self::VAR_SPACE_ID])) {
					
						$this->pageSpaceView($_REQUEST[self::VAR_ZONE_ID], $_REQUEST[self::VAR_SPACE_ID]);
					
					}
					else
					{
					
						$this->pageSpaceView($_REQUEST[self::VAR_ZONE_ID]);
					
					}
					
				break;
				case self::PAGE_ZONES_VIEW:
				
					$this->pageZonesView();
					
				break;
				default:
				
					$this->pageZonesView();
					
				break;
			}
			
			return true;
		}
		
		private function pageZonesView()
		{

			require('html/'.self::PAGE_ZONES_VIEW.'.php');
			
		}
		
		private function pageSpaceView($zone_id, $space_id = self::VAR_SPACE_EMPTY_ID)
		{
			
			$str_action = __('New Space',$this->ns());
			
			$options = $this->getOption();
			
			if(!array_key_exists($zone_id, $options)) 
			{
				
				$this->addNotice(self::STR_SPACE_ZONE_MISSING);
				
				$this->pageZonesView();
				
				return;
				
			}
			
			$zone = $options[$zone_id];
			
			if(	
				self::VAR_SPACE_EMPTY_ID !== $space_id &&
				!array_key_exists($space_id, $zone[$this->spacesKey()])
			)
			{
				
				$this->addNotice(self::STR_SPACE_MISSING);
				
				$space_id = self::VAR_SPACE_EMPTY_ID;
				
			}
			
			if($space_id == self::VAR_SPACE_EMPTY_ID)
			{
				
				$zone[$this->spacesKey()][self::VAR_SPACE_EMPTY_ID] = array();
				
				$zone[$this->spacesKey()][self::VAR_SPACE_EMPTY_ID][$this->nameKey()] = '';
				$zone[$this->spacesKey()][self::VAR_SPACE_EMPTY_ID][$this->contentKey()] = '';
				
			}
			
			$space = $zone[$this->spacesKey()][$space_id];
			
			if($space_id !== self::VAR_SPACE_EMPTY_ID)
			{
				
				$str_action = sprintf(
					__('Editing Space "%s" in Zone "%s"', $this->ns()),
					esc_attr(stripslashes($space[$this->nameKey()])),
					esc_attr(stripslashes($zone[$this->nameKey()]))
				);
				
			}
			
			require('html/'.self::PAGE_SPACE_VIEW.'.php');
		
		}
		
		private function pageSpacesView($zone_id)
		{
			
			$options = $this->getOption();
			
			if(!array_key_exists($zone_id, $options)) 
			{
				
				$this->addNotice(self::STR_SPACE_ZONE_MISSING);
				
				$this->pageZonesView();
				
				return;
				
			}
			
			$zone = $options[$zone_id];
		
			require('html/'.self::PAGE_SPACES_VIEW.'.php');
			
		}
		
		/* Notices */
		
		private function addNotice($error_code)
		{
			
			$error = $this->getNotice($error_code);
			
			if(false != $error)
			{
			
				$this->notices->addNotice($error);
			
			}
			
		}
		
		private function getNotice($error_code)
		{

			$string = $this->getString($error_code);
			
			return new SpaceManagerNotice(
				$this->getString($error_code),
				$this->getErrorType($error_code),
				$error_code
			);
			
			return false;
		
		}
		
		/* Render Zones */
		
		public function getZoneHTML($zone_id = 0, $random = true, $num = -1, $space_name = false)
		{

			$output = '';
			
			$zones = $this->getOption();
			
			if(!is_array($zones)) return false;
			
			if(!array_key_exists($zone_id, $zones))
			{
			
				return '<!-- Space Zone is missing -->';

			}
			
			if(
				!isset($zones[$zone_id][$this->spacesKey()]) ||
				!is_array($zones[$zone_id][$this->spacesKey()]) || 
				0 == sizeof($zones[$zone_id][$this->spacesKey()])
			)
			{
				return '<!-- Space Zone has no ads -->';
			}

							
			if(true == $random)
			{
			
				shuffle($zones[$zone_id][$this->spacesKey()]);
				
			}
			
			$count = 1;
			
			$output .= $this->beforeZone($zone_id);
			
			
			foreach($zones[$zone_id][$this->spacesKey()] as $space_id => $space)
			{
				
				$output .= $this->getSpaceHtml($space, $count, $space_name);
				
				if($count >= $num && $num != -1)
				{
				
					break;
			
				}
			
				$count++;
					
			}
				
			$output .= $this->afterZone();
			
			return $output;
				
		}
		
		public function getSpaceHtml($space, $count = 1, $space_name = false)
		{
		
			$output .= '';

			$output .= $this->beforeSpace($space, $count);
				
			if(true == $space_name)
			{
				
				$output .= $this->getSpaceNameHtml($space);
			
			}
				
			$output .= $this->getSpaceContentHtml($space);
				
			$output .= $this->afterSpace();
			
			return $output;
			
		}
		
		public function getSpaceNameHtml($space)
		{

			$output = '';

			$output .= $this->beforeSpaceName($space);
					
			$output .= esc_attr(
					stripslashes(
						$space[$this->nameKey()]
					)
				);
			
			$output .= $this->afterSpaceName($space);
						
			return apply_filters($this->prefix().'space_name', $output, $space);
			
		}
		
		public function getSpaceContentHtml($space)
		{
			
			$output = '';
			
			$output = stripslashes(
						$space[$this->contentKey()]
					);
			
			return apply_filters($this->prefix().'space_content', $output, $space);
			
		}
		
		/* Form Fields */
			
		public function getFieldId($zone_id = false, $space_id = null, $opt = null)
		{
			
			$id = $this->optName();
			
			if(false !== $zone_id) $id .= '_'.$zone_id;
			
			if(!is_null($space_id)) $id .= '_'.$this->spacesKey().'_'.$space_id;
			
			if(!is_null($opt)) $id .= '_'.$opt;
			
			return $id;
			
		}
		
		public function getFieldName($zone_id = '', $space_id = null, $opt = null)
		{
			
			$name = $this->optName().'['.$zone_id.']';
			
			if(!is_null($space_id)) $name .= '['.$this->spacesKey().']['.$space_id.']';

			if(!is_null($opt)) $name .= '['.$opt.']';
			
			return $name;
			
		}
		
		/* Database Interaction */
		
		public function getOption()
		{
			return get_option($this->optName());
		}
		
		public function updateOption($option)
		{
			
			if(!is_array($option)) $option = array();
			
			return update_option($this->optName(), $option);
			
		}
		
		/* Check updates */
		private function checkUpdate()
		{
			
			$new_zones = array();
			
			if(false != get_option('AdSpaceZone'))
			{
				
				$old_zones = get_option('AdSpaceZone');
				
				if(!is_array($old_zones)) return false;
				
				foreach($old_zones as $zone_id => $zone)
				{
				
					$new_zones[$zone_id] = array($this->nameKey()=>$zone['Name'], $this->spacesKey()=>array());
					
					$old_ads = get_option('AdSpaceZone'.$zone_id);
					
					if(!is_array($old_ads)) continue;
					
					foreach($old_ads as $space_id => $space)
					{
						
						$new_zones[$zone_id][$this->spacesKey()][$space_id][$this->nameKey()] = $space['Name'];
						$new_zones[$zone_id][$this->spacesKey()][$space_id][$this->contentKey()] = $space['Code'];
						
					}
				
				}
				
			}
			
			if(!empty($new_zones))
			{
			
				update_option($this->optName(), $new_zones);
			
				if(false != $this->getOption())
				{
			
					delete_option('AdSpaceZone');
					
					foreach($old_zones as $zone_id => $zone)
					{
					
						delete_option('AdSpaceZone'.$zone_id);
					
					}
			
				}
			
			}
			
		}
		
		/* Extensible Properties */
		public function parentPage()
		{
			return apply_filters($this->prefix().'parent_page', 'options-general.php');
		}
		
		public function capability()
		{
			return apply_filters($this->prefix().'capability', 'manage_options');
		}
		
		public function menuSlug()
		{
			return apply_filters($this->prefix().'menu_slug', $this->ns());
		}
		
		public function actionVar()
		{
			return $this->prefix().'action';
		}
		
		public function nonceVar()
		{
			return '_'.$this->prefix().'nonce';
		}
		
		public function pageVar()
		{
			return $this->prefix().'page';
		}
		
		public function optGroup()
		{
			return apply_filters($this->prefix().'option_group',$this->ns());
		}
		
		public function optName()
		{
			return apply_filters($this->prefix().'option_name',$this->ns());
		}
		
		public function spacesKey()
		{
			return 'spaces';
		}
		
		public function nameKey()
		{
			return 'name';
		}
		
		public function contentKey()
		{
			return 'content';
		}
		
		public function configPageUri(array $user_query_vars = null)
		{
		
			$uri = $this->parentPage();
			
			$default_query_vars = array('page'=>$this->menuSlug());
			
			if(!is_array($user_query_vars)) 
			{
				$query_vars = $default_query_vars;
			}
			else
			{
				$query_vars = array_replace($default_query_vars, $user_query_vars);
			}
			
			if(isset($query_vars[$this->actionVar()]))
			{
				$query_vars[$this->nonceVar()] = wp_create_nonce($query_vars[$this->actionVar()]);
			}
			
			if(!empty($query_vars)) $uri .= '?';
			
			$uri .= http_build_query($query_vars);
			
			return admin_url($uri);
		
		}
		
		public function beforeZone($index = '')
		{
			
			$zones = self::getOption();
			
			$classes = array();
			
			if(!empty($index)) $classes[] = $this->className().'zone-'.$index;
			
			$classes[] = $this->className().'zone-'.self::convertStringToHtmlClass($zones[$index][$this->nameKey()]);
			
			return apply_filters($this->prefix().'before_zone','<div class="'.$this->className().'zone '.implode(' ', $classes).'">', $index);
			
		}
		
		public function afterZone()
		{
			
			return apply_filters($this->prefix().'after_zone','</div>');
			
		}
		
		public function beforeSpace($space, $index = '')
		{
			
			$classes = array();
			
			if(!empty($index)) $classes[] = $this->className().'space-'.$index;
			
			$classes[] = $this->className().'space-'.self::convertStringToHtmlClass($space[$this->nameKey()]);
			
			return apply_filters($this->prefix().'before_space','<div class="'.$this->className().'space '.implode(' ', $classes).'">', $index);
		
		}
		
		public function afterSpace()
		{
		
			return apply_filters($this->prefix().'after_space','</div>');
		
		}
		
		public function beforeSpaceName()
		{
		
			return apply_filters($this->prefix().'before_space_name','<div class="'.$this->className().'space-name">');
		
		}
		
		public function afterSpaceName()
		{
		
			return apply_filters($this->prefix().'after_space_name','</div>');
		
		}
		
		public function prefix()
		{
			return $this->ns().'_';
		}
		
		public function className()
		{
			return str_replace('_','-', $this->prefix());
		}
		
		protected function getString($string)
		{
			
			$strings = $this->strings();
			
			if(array_key_exists($string, $strings))
			{
				return $strings[$string];
			}
			
			return '';
			
		}
		
		public function getErrorType($error)
		{
		
			$notices = array(
				SpaceManagerNotice::STATUS_ERROR => array(
					self::STR_SPACES_ZONE_DELETED,
					self::STR_SPACE_ZONE_NAME_EMPTY,
					self::STR_SPACE_ZONE_MISSING,
					self::STR_INVALID_POST,
					self::STR_SPACE_NAME_EMPTY,
					self::STR_SPACE_MISSING,
					self::STR_OPTION_UPDATE_FAILED
				),
				SpaceManagerNotice::STATUS_SUCCESS => array(
					self::STR_SPACE_ZONE_ADDED,
					self::STR_SPACE_ZONE_DELETED,
					self::STR_SPACE_ADDED,
					self::STR_SPACE_UPDATED
				)
			);
			
			if(array_search($error, $notices[SpaceManagerNotice::STATUS_ERROR]))
			{
				return SpaceManagerNotice::STATUS_ERROR;
			}
			elseif(array_search($error, $notices[SpaceManagerNotice::STATUS_SUCCESS]))
			{
				return SpaceManagerNotice::STATUS_SUCCESS;
			}
			else
			{
				return SpaceManagerNotice::STATUS_NEUTRAL;
			}
			
		}
		
		public function strings()
		{
		
			return array(
				self::STR_SPACES_ZONE_DELETED => __('The zone this space belongs to has been deleted.',$this->ns()),
				self::STR_SPACE_ZONE_NAME_EMPTY => __('The zone name must not be empty.',$this->ns()),
				self::STR_SPACE_ZONE_MISSING => __('The zone is missing or has been deleted.',$this->ns()),
				self::STR_INVALID_POST => __('There was a problem with the form submission, please try again.',$this->ns()),
				self::STR_SPACE_NAME_EMPTY => __('The name of the space cannot be empty.',$this->ns()),
				self::STR_SPACE_MISSING => __('This space is missing or has been deleted.',$this->ns()),
				self::STR_OPTION_UPDATE_FAILED => __('An error occurred while trying to save, please try again.',$this->ns()),
				self::STR_SPACE_ZONE_ADDED => __('The zone has been created.', $this->ns()),
				self::STR_SPACE_ZONE_DELETED => __('The zone has been deleted.',$this->ns()),
				self::STR_SPACE_ADDED => __('The space has been created.',$this->ns()),
				self::STR_SPACE_UPDATED => __('The space has been updated.',$this->ns()),
				self::STR_EDIT_ZONES => __('Edit Zones', $this->ns()),
				self::STR_EXPLAIN_ZONES => __('Zones are groups of related items that you would like to display. Once you have created a zone you can choose to display all the spaces inside that zone or a random number of spaces in that zone. To show a zone on your website simply %sadd a widget%s to your sidebar.', $this->ns()),
				self::STR_ZONE_NAME => __('Zone Name', $this->ns()),
				self::STR_NUM_SPACES => __('Number of Spaces', $this->ns()),
				self::STR_NEW_SPACE => __('New Space', $this->ns()),
				self::STR_EDIT_SPACES => __('Edit Spaces', $this->ns()),
				self::STR_DELETE_ZONE => __('Delete Zone', $this->ns()),
				self::STR_NEW_ZONE => __('Add a New Zone', $this->ns()),
				self::STR_SAVE => __('Save', $this->ns()),
				self::STR_EDITING_SPACES_IN => __('Editing Spaces in %s', $this->ns()),
				self::STR_RETURN_TO_ZONES => __('Return to zones', $this->ns()),
				self::STR_SPACE_NAME => __('Space Name', $this->ns()),
				self::STR_EDIT_SPACE => __('Edit Space', $this->ns()),
				self::STR_DELETE_SPACE => __('Delete Space', $this->ns()),
				self::STR_ZONE_EMPTY => __('This zone currently has no spaces. Click "New Space" below to create one.', $this->ns()),
				self::STR_RETURN_TO_ADS_IN_ZONE => __('Return to ads in zone: %s', $this->ns()),
				self::STR_ADD_TITLE_AND_CONTENT => __('Add the title and content for the space.', $this->ns()),
				self::STR_TITLE => __('Title', $this->ns()),
				self::STR_MENU_NAME => __("Space Manager", $this->ns()),
				self::STR_PAGE_NAME => __("Space Manager", $this->ns()),
			);
			
		}
		
		public static function convertStringToHtmlClass($string)
		{
		
			$string = preg_replace('/\W+/','-',$string);
			
			$string = preg_replace('/\s+/','-',$string);
			
			$string = strtolower($string);
			
			return $string;
			
		}
		
		abstract public function ns();
		
		abstract public function widgetClass();
	
	}

}

?>