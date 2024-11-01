<div class="wrap">

	<h2><?php echo $this->getString(self::STR_PAGE_NAME) ?></h2>
	
	<?php $this->doFeedback() ?>
	
	<h3><?php printf($this->getString(self::STR_EDITING_SPACES_IN), esc_attr(stripslashes($zone[$this->nameKey()]))); ?></h3>
	
	<p><a href="<?php echo $this->configPageUri(
		array(
			$this->pageVar()=>self::PAGE_ZONES_VIEW
		)
	); ?>"><?php echo $this->getString(self::STR_RETURN_TO_ZONES); ?></a></p>
	
	<table id="AdSpace_Setup" class="widefat post fixed">
	
		<thead>
			
			<tr>
				
				<th style="width:100%" scope="col"><?php echo $this->getString(self::STR_SPACE_NAME); ?></th>
			
			</tr>
			
		</thead>
			
		<tbody>
			
		<?php

			if(is_array($zone[$this->spacesKey()]))
			{
			
				foreach($zone[$this->spacesKey()] as $space_id => $space)
				{
				
		?>
			<tr>
		
				<td>
				
					<a href="<?php echo $this->configPageUri(
							array(
								$this->pageVar()=>self::PAGE_SPACE_VIEW,
								self::VAR_ZONE_ID=>$zone_id,
								self::VAR_SPACE_ID=>$space_id
							)
						); ?>" >
						
						<?php echo esc_attr(stripslashes($space[$this->nameKey()])); ?>
						
					</a>
					
					<div class="row-actions">
					
						<a href="<?php echo $this->configPageUri(
							array(
								$this->pageVar()=>self::PAGE_SPACE_VIEW,
								self::VAR_ZONE_ID=>$zone_id,
								self::VAR_SPACE_ID=>$space_id
							)
						); ?>" ><?php echo $this->getString(self::STR_EDIT_SPACE); ?></a> |
						
						<a href="<?php echo $this->configPageUri(
							array(
								$this->actionVar()=>self::ACTION_SPACE_DELETE,
								$this->pageVar()=>self::PAGE_SPACES_VIEW,
								self::VAR_ZONE_ID=>$zone_id,
								self::VAR_SPACE_ID=>$space_id
							)
						); ?>" class="delete"><?php echo $this->getString(self::STR_DELETE_SPACE); ?></a>
	
					</div>
					
				</td>
				
			</tr>
				<?php
				}
			}
			else
			{
				?>
			<tr>
			
				<td>
				
					<p>
					
						<?php echo $this->getString(self::STR_ZONE_EMPTY); ?>
					
					</p>
					
				</td>
				
			</tr>
			
			<?php
			
			}
			
			?>
		
		</tbody>
		
	</table>
		
	<p>
	
		<a href="<?php echo $this->configPageUri(
							array(
								$this->pageVar()=>self::PAGE_SPACE_VIEW,
								self::VAR_ZONE_ID=>$zone_id
							)
						); ?>" class="button">
			<?php echo $this->getString(self::STR_NEW_SPACE); ?>
		
		</a>
		
	</p>
	
</div>