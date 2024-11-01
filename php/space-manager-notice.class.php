<?php
if(!class_exists('SpaceManagerNotice'))
{

	class SpaceManagerNotice
	{
		
		const STATUS_NEUTRAL = 0;
		
		const STATUS_SUCCESS = 1;
		
		const STATUS_ERROR = -1;
		
		private $error_code = null;

		private $status = null;
		
		private $msg = null;

		public function __construct(
				$msg, 
				$status = self::STATUS_NEUTRAL, 
				$error_code = null
			)
		{
		
			$this->msg = $msg;
			
			$this->status = $status;
			
			$this->error_code = $error_code;
			
			
		}
		
		public function getNoticeHtml()
		{
			
			$classes = array();

			if(self::STATUS_SUCCESS == $this->status)
			{
				
				$classes[] = 'updated';
				
			}
			else if(self::STATUS_ERROR == $this->status)
			{
			
				$classes[] = 'error';
			
			}
			
			if(isset($this->error_code))
			{
				$classes[] = 'error_code_'.$this->error_code;
			}
			
			return '<div class="'.implode(' ', $classes).'"><p>'.$this->msg.'</p></div>';
		}
		
		public function getStatus()
		{
			return $this->status;
		}
		
		public function isSuccess()
		{
			if(self::STATUS_SUCCESS == $this->status) return true;
			return false;
		}
		
		public function isError()
		{
			if(self::STATUS_ERROR == $this->status) return true;
			return false;
		}
		
		public function isNeutral()
		{
			if(self::STATUS_NEUTRAL == $this->status) return true;
			return false;
		}
	
	}

}


?>