<?php

	$zones = $this->getOption();
	
?>

<div class="wrap">

	<h2><?php echo $this->getString(self::STR_PAGE_NAME) ?></h2>
	
	<?php $this->doFeedback() ?>
	
	<h3><?php echo $this->getString(self::STR_EDIT_ZONES); ?></h3>
	
	<p><?php printf($this->getString(self::STR_EXPLAIN_ZONES), '<a href="'.admin_url('widgets.php').'">','</a>'); ?></p>
		
	<table id="AdSpace_Setup" class="widefat post fixed">
	
		<thead>
		
			<tr>
			
				<th style="width:75%" scope="col"><?php echo $this->getString(self::STR_ZONE_NAME); ?></th>
				
				<th style="width:25%" scope="col"><?php echo $this->getString(self::STR_NUM_SPACES); ?></th>
			</tr>
			
		</thead>
		
		<tbody>
		
			<?php
			
			
			if(is_array($zones) && sizeof($zones) > 0)
			{
			
				foreach($zones as $zone_id=>$zone) 
				{
					
			?>
					
			<tr>
					
				<td>
				
					<strong>
					
						<a href="<?php echo $this->configPageUri(
							array(
								$this->pageVar()=>self::PAGE_SPACES_VIEW,
								self::VAR_ZONE_ID=>$zone_id
							)
						); ?>">
						
							<?php echo esc_attr(stripslashes($zone[$this->nameKey()])); ?>
							
						</a>
					
					</strong>
										
					<div class="row-actions">
					
						<a href="<?php echo $this->configPageUri(
							array(
								$this->pageVar()=>self::PAGE_SPACE_VIEW,
								self::VAR_ZONE_ID=>$zone_id,
								self::VAR_SPACE_ID=>self::VAR_SPACE_EMPTY_ID
							)
						); ?>" ><?php echo $this->getString(self::STR_NEW_SPACE); ?></a> | <a href="<?php echo $this->configPageUri(
							array(
								$this->pageVar()=>self::PAGE_SPACES_VIEW,
								self::VAR_ZONE_ID=>$zone_id
							)
						); ?>" ><?php echo $this->getString(self::STR_EDIT_SPACES); ?></a> |
						<a href="<?php 
						echo $this->configPageUri(
							array(
								$this->actionVar()=>self::ACTION_ZONE_DELETE,
								self::VAR_ZONE_ID=>$zone_id
								)
							); 
						?>" class="delete"><?php echo $this->getString(self::STR_DELETE_ZONE); ?></a>
						
					</div>
					
				</td>
				
				<td>

					<?php echo (is_array($zone[$this->spacesKey()])) ?  sizeof($zone[$this->spacesKey()]) : '0'; ?>
				
				</td>
				
			</tr>

			<?php
		
				}
		
			}			

			?>
			
			<tr>
				
				<td>	
							
					<form action="<?php echo $this->configPageUri(); ?>" method="post">
						
						<label><?php echo $this->getString(self::STR_NEW_ZONE); ?></label> 
						
						<input type="text" id="<?php echo $this->getFieldId(self::VAR_ZONE_EMPTY_ID); ?>" name="<?php echo $this->getFieldName(self::VAR_ZONE_EMPTY_ID); ?>[name]" value=""/> 						
						
						<?php wp_nonce_field(self::ACTION_ZONE_ADD, $this->nonceVar()); ?>
						
						<input type="hidden" name="<?php echo $this->actionVar() ?>" value=<?php echo self::ACTION_ZONE_ADD; ?> />
						
						<input type="submit" class="button" value="<?php echo $this->getString(self::STR_SAVE); ?>" />
						
					</form>
				
				</td>
				
			</tr>

		</tbody>
	
	</table>
	
</div>