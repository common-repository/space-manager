<div id="AdSpace" class="wrap">

	<h2><?php echo $this->getString(self::STR_PAGE_NAME) ?></h2>
	
	<?php $this->doFeedback() ?>

	<h3><?php echo $str_action ?></h3>
	
	<p>
		
		<a href="<?php echo $this->configPageUri(
							array(
								$this->pageVar()=>self::PAGE_SPACES_VIEW,
								self::VAR_ZONE_ID=>$zone_id
							)
						); ?>">
						
			<?php printf($this->getString(self::STR_RETURN_TO_ADS_IN_ZONE), esc_attr(stripslashes($zone[$this->nameKey()]))); ?>
			
		</a>
		
	</p>
	
	<p><?php echo $this->getString(self::STR_ADD_TITLE_AND_CONTENT); ?></p>
	
	<form method="post" action="<?php echo $this->configPageUri(
		array(
			$this->pageVar()=>self::PAGE_SPACE_VIEW,
			$this->actionVar()=>self::ACTION_SPACE_ADD,
			self::VAR_ZONE_ID=>$zone_id,
			self::VAR_SPACE_ID=>$space_id
		)
	); ?>">	
			
			<div id="poststuff">
			
				<div id="titlediv">
				
					<label class="screen-reader-text" for="title"><?php echo $this->getString(self::STR_TITLE); ?></label>
					
					<input type="text" name="<?php echo $this->getFieldName($zone_id, $space_id,$this->nameKey()) ?>" size="30" tabindex="1" value="<?php echo esc_attr(stripslashes($space[$this->nameKey()])); ?>" id="title" />
					
				</div>
				<?php
					wp_editor( 
						trim(stripslashes($space[$this->contentKey()])), 
						$this->getFieldId($zone_id, $space_id,$this->contentKey()), 
						array(
							'textarea_name'=>$this->getFieldName($zone_id, $space_id,$this->contentKey()),
							'tabindex'=>2
						) );
				
				?>
				</div>
		
		<?php do_action($this->prefix().'extra_fields', $zone_id, $space_id, $space, $this); ?>
		
		<p class="submit">
		
			<?php wp_nonce_field(self::ACTION_SPACE_ADD, $this->nonceVar()); ?>
			
			<input type="hidden" name="<?php echo self::VAR_SPACE_ID; ?>" value="<?php echo $space_id ?>" />
			
			<input type="hidden" name="<?php echo $this->actionVar() ?>" value=<?php echo self::ACTION_SPACE_ADD; ?> />
					
			<input tabindex="10" type="submit" class="button" value="<?php echo $this->getString(self::STR_SAVE); ?>" />
		
		</p>
	
	</form>
	
</div>