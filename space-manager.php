<?php

if(!class_exists('DefaultSpaceManager'))
{

	class DefaultSpaceManager extends SpaceManager
	{
		
		public function __construct()
		{
			
			parent::__construct();
			
		}
		
		public function ns()
		{
			return 'space_manager';
		}
		
		public function spacesKey()
		{
			return 'ads';
		}
		
		public function contentKey()
		{
			return 'content';
		}
		
		public function widgetClass()
		{
			return 'DefaultSpaceManagerWidget';
		}
			
	}

}
?>