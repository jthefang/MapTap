<!--
	If changing anything on this file: make sure to make it sticky in mainAddstoryJS.js for the crowdfunding redirect uri to save
-->


<!--<h1 id="add_story_heading">ADD YOUR story!</h1>-->
<form action="<?php echo $story_form_action; ?>" method="POST" id="<?php echo $story_form_id; ?>">
	<fieldset class="<?php echo $story_form_element_class; ?>">
		<legend class="<?php echo $story_form_element_class; ?>"><?php echo $story_form_legend; ?></legend>
		<div class="error"><span class="form_required"></span></div>

		<div id="form_stuff">
			#<input type="text" name="story_topic" id="story_topic" class="<?php echo $story_form_element_class; ?>" placeholder="Story topic (ie. #extinction)" 
				style="width:25%; display: inline-block; margin-right: 1em;" value="<?php if (!empty($_POST) && !$storySubmitted) { print $_POST['story_topic']; } //Sticky form
					elseif(isset($edit_story_topic)){ echo $edit_story_topic; } //edit story page ?>"/>

			<input type="text" name="story_headline" id="story_headline" class="<?php echo $story_form_element_class; ?>" placeholder="Story headline (ie. Giant asteroid headed for Earth)" 
				style="width:45%; display: inline-block;" value="<?php if (!empty($_POST) && !$storySubmitted) { print $_POST['story_headline']; } //Sticky form
					elseif(isset($edit_story_headline)){ echo $edit_story_headline; } //edit story page ?>"/>

			<textarea name="story_text" id="story_text" class="<?php echo $story_form_element_class; ?>" rows="12"
			placeholder='Breaking news story (ie. Well folks, hug your daddy, and kiss your momma because this is it...)'><?php 
				if (!empty($_POST) && !$storySubmitted) { print $_POST['story_text']; } //Sticky form
				elseif(isset($edit_story_descript)){ echo $edit_story_descript; } //edit story page ?></textarea>

			<br/>
			<input type="text" name="story_loc" id="story_loc" class="<?php echo $story_form_element_class; ?>" placeholder="Location of story - where it will show up on map (ie. Hollywood)"
				value="<?php if (!empty($_POST) && !$storySubmitted) { print $_POST['story_loc']; } //Sticky form 
				elseif(isset($edit_story_loc)){ echo $edit_story_loc; } //edit story page ?>"/>
			<input type="hidden" name="hidden_loc_lat" id="hidden_loc_lat" />
			<input type="hidden" name="hidden_loc_lng" id="hidden_loc_lng" />
			<input type="hidden" name="hidden_loc_address" id="hidden_loc_address" />
			<input type="hidden" name="hidden_loc_city" id="hidden_loc_city" />
			<input type="hidden" name="hidden_loc_state" id="hidden_loc_state" />
			<input type="hidden" name="hidden_loc_zip" id="hidden_loc_zip" />
			<input type="hidden" name="hidden_loc_country" id="hidden_loc_country" />
			<input type="hidden" name="hidden_loc_formatted_address" id="hidden_loc_formatted_address" />
		</div>

		<a id="submit_story" class="<?php echo $story_form_element_class; ?> buttonOne"><?php echo $story_form_submit_button_value; ?></a>
		<?php if($story_form_id == "edit_story_form"){ ?>
			<button type="button" name="delete_story" id="delete_story" class="edit_story">
				<img src="images/delete_icon.png" id="delete_icon"/><span id="delete_story">Delete this story</span>
			</button>
			<div id="delete_dialog" title="Delete <?php echo $edit_story_headline; ?>?">
				<p>Are you sure you want to delete "<?php echo $edit_story_headline; ?>"?</p>
			</div>
		<?php } //end if story_form_id == edit_story?>
	</fieldset>
</form>