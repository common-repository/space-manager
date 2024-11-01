<p>

<?php printf($this->getString(self::STR_SETUP_SPACES),'<a href="'.$this->manager->configPageUri().'">','</a>'); ?>

</p>

<p>

	<label for="<?php echo $this->get_field_id('title') ?>">
	
		<?php echo $this->getString(self::STR_TITLE); ?>
		
	</label>
	
	<input class="widefat" type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title') ?>" value="<?php echo $title ?>" />
	
</p>

<p>
	
	<label for="<?php echo $this->get_field_id('zone') ?>">
		
		<?php echo $this->getString(self::STR_ZONE); ?>
		
	</label>
	
	<select class="widefat" id="<?php echo $this->get_field_id('zone') ?>" name="<?php echo $this->get_field_name('zone') ?>">
		
		<?php
			foreach($zones as $the_zone_id => $the_zone)
			{
		?>
		
		<option value="<?php echo $the_zone_id ?>" <?php selected($the_zone_id, $zone); ?>>
		
			<?php echo $the_zone[$this->manager->nameKey()]; ?>
			
		</option>
		
		<?php
			}
		?>
		
	</select>

</p>
	
<p>

	<label for="<?php echo $this->get_field_id('num') ?>">
	
		<?php echo $this->getString(self::STR_NUM_TO_SHOW); ?>
		
	</label>
	
	<input type="text" id="<?php echo $this->get_field_id('num') ?>" name="<?php echo $this->get_field_name('num') ?>" value="<?php echo $num ?>" maxlength="2" size="1" />
	
</p>

<p>

	<input type="checkbox" id="<?php echo $this->get_field_id('random') ?>" name="<?php echo $this->get_field_name('random') ?>" value="1" <?php checked(1, $random) ?> />
	
	<label for="<?php echo $this->get_field_id('random') ?>">
	
		<?php echo $this->getString(self::STR_SHOW_RANDOM); ?>
		
	</label>
	
</p>

<p>

	<input type="checkbox" id="<?php echo $this->get_field_id('ad_name') ?>" name="<?php echo $this->get_field_name('ad_name') ?>" value="1" <?php checked(1, $space_name) ?> />
	
	<label for="<?php echo $this->get_field_id('ad_name') ?>">
	
		<?php echo $this->getString(self::STR_SHOW_SPACE_NAME); ?>
		
	</label>
	
</p>