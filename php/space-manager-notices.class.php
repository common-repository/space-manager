<?php

require_once('space-manager-notice.class.php');

if(!class_exists('SpaceManagerNotices'))
{

	class SpaceManagerNotices
	{
		
		const STATUS_SUCCESS = 1;
		const STATUS_ERROR = -1;
		const STATUS_NEUTRAL = 0;
		
		private $status = self::STATUS_NEUTRAL;
		
		private $notices = array();
		
		public function __construct(SpaceManagerNotice $notice = null)
		{
			if(isset($notice))
			{
				$this->addNotice($notice);
			}
		}
		
		public function addNotice(SpaceManagerNotice $notice)
		{
			$this->notices[] = $notice;
		}
		
		public function getNotices()
		{
			
			$output = '';
						
			foreach($this->notices as $notice)
			{
			
				$output .= $notice->getNoticeHtml();
			
			}
			
			return $output;
		
		}
		
		public function isSuccess()
		{

			foreach($this->notices as $notice)
			{
			
				if($notice->isError())
				{
					return false;
				}
			
			}
			
			return true;
			
		}
		
		public function isError()
		{

			foreach($this->notices as $notice)
			{
			
				if($notice->isError())
				{
					return true;
				}
			
			}
			
			return false;
			
		}
	
	}

}
?>