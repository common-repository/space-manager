<?php

require_once('php/space-manager-widget.class.php');

if(!class_exists('DefaultSpaceManagerWidget'))
{

	class DefaultSpaceManagerWidget extends SpaceManagerWidget {
		
		public function __construct() {
			global $myDefaultSpaceManager;
			parent::__construct($myDefaultSpaceManager);
			
		}
		
		public function ns(){
			
			return 'space_manager_widget';
			
		}
				
	}

}

?>