<?php

if(!class_exists('SpaceManagerWidget'))
{

	abstract class SpaceManagerWidget extends WP_Widget {
		
		public $manager = false;
		
		const STR_DESCRIPTION = 'description';
		const STR_NAME = 'name';
		const STR_DEFAULT_TITLE = 'default_title';
		const STR_NO_SPACE_ZONES = 'no_space_zones';
		const STR_SETUP_SPACES = 'setup_spaces';
		const STR_TITLE = 'title';
		const STR_ZONE= 'zone';
		const STR_NUM_TO_SHOW = 'num_to_show';
		const STR_SHOW_RANDOM = 'show_random';
		const STR_SHOW_SPACE_NAME = 'show_space_name';
		
		public function __construct($manager) {
			
			if(!is_subclass_of($manager, 'SpaceManager')) return false;
			
			$this->manager = $manager;
			
			$widget_ops = array( 
				'classname' => $this->cssClass(), 
				'description' => $this->getString(self::STR_DESCRIPTION)
			);
			
			parent::__construct(
				get_class($this), // replaced get_called_class()
				$this->getString(self::STR_NAME), 
				$widget_ops 
			);
			
		}
		
		public function widget( $args=false, $instance=false ) {
	
			if($args) extract( $args );

			echo $before_widget;
			
			$title = apply_filters('widget_title', $instance['title']);

			if ( $title )
				echo $before_title . $title . $after_title;

				echo $this->manager->getZoneHTML(
					$instance['zone'],
					$instance['random'],
					$instance['num'],
					$instance['ad_name']	
				);
			
			echo $after_widget;
			
		}
		
		public function update($new_instance, $old_instance) {
		
			$instance = $old_instance;
			
			$instance['title'] = strip_tags($new_instance['title']);
			
			$instance['zone'] = intval($new_instance['zone']);
			
			if(0 == strcmp('on' , $new_instance['random']))
			{
				$instance['random'] = 1;
			}
			elseif(0 == strcmp('off' , $new_instance['random']))
			{
				$instance['random'] = 0;
			}
			
			if(!isset($new_instance['random'])) $instance['random'] = 0;
			
			$instance['random'] = intval($new_instance['random']);
			
			if("" == $new_instance['num']) $new_instance['num'] = -1;
			
			$instance['num'] = intval($new_instance['num']);
			
			$instance['ad_name'] = intval($new_instance['ad_name']);
			
			return $instance;
			
		}
	
		
		public function form($instance) {
		
			$instance = wp_parse_args( 
				(array)$instance, 
				array(
					'title'=>$this->getString(self::STR_DEFAULT_TITLE),
					'zone'=>0, 
					'random'=>1, 
					'num'=>-1,
					'ad_name'=>0
				)
			);
			
			$title = esc_attr($instance['title']);
			
			$zone = esc_attr($instance['zone']);
			
			$random = esc_attr($instance['random']);
			
			$num = esc_attr($instance['num']);
			
			$space_name = esc_attr($instance['ad_name']);
			
			$zones = $this->manager->getOption();
			
			if(!is_array($zones) || 0 == sizeof($zones))
			{
				$output = '<p>';
				$output .= sprintf(
					$this->getString(self::STR_NO_SPACE_ZONES),
					'<a href="'.$this->manager->configPageUri().'">',
					'</a>'
				);
				$output .= '</p>';		
				
				echo $output;
					
			}
			else
			{
				require('html/widget_form.php');
			}	
		}
		
		public function ns()
		{
			return 'space_manager_widget';
		}
		
		public function prefix()
		{
			return $this->ns().'_';
		}
		
		public function cssClass()
		{
			return str_replace('_','-',$this->ns());
		}
		
		public function getString($string)
		{
		
			$strings = $this->strings();
			
			if(array_key_exists($string, $strings))
			{
				return $strings[$string];
			}
			
			return '';
			
		}
		
		public function strings()
		{
			return array(
				self::STR_DESCRIPTION => __('Displays the zones setup using the space manager.', $this->ns()),
				self::STR_NAME => __('Space Manager Widget', $this->ns()),
				self::STR_DEFAULT_TITLE => '',
				self::STR_NO_SPACE_ZONES => __('No zones have been set up. %sGo to the space manager configuration page%s and create a zone.',$this->ns()),
				self::STR_SETUP_SPACES => __('Setup spaces using the %sspace manager%s configuration page.',$this->ns()),
				self::STR_TITLE => __('Title',$this->ns()),
				self::STR_ZONE => __('Zone',$this->ns()),
				self::STR_NUM_TO_SHOW => __('Number of spaces to show (-1 for unlimited):', $this->ns()),
				self::STR_SHOW_RANDOM => __('Select spaces at random', $this->ns()),
				self::STR_SHOW_SPACE_NAME => __('Show space name', $this->ns())
			);
		}
		
	}

}

?>