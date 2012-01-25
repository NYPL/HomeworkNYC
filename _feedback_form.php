<style type="text/css" media="screen">
	#show_feedback_form {
		cursor:pointer;
		text-align:right;
	}
	#feedback_form {
		font-size:11px;
		margin-top:4px;
		padding:4px;
		line-height:10px;
	}
	#googleform {
		display:none;
		font-size:11px;
	}
	#googleform textarea {
		width:275px;
		height:80px;
	}
</style>
<div id="feedback_form">
	<div id="show_feedback_form">Feedback?!</div>
	<div id="googleform">

		<form target="_new" action="http://spreadsheets.google.com/formResponse?key=<? echo $your_google_form_key_here; ?>" method="post" id="ss-form">

		<br />
		<div class="errorbox-good">
			<div class="ss-item ss-item-required ss-text">
				<div class="ss-form-entry"><label class="ss-q-title" for="entry_0">your email address<span class="ss-required-asterisk">*</span></label>
					<label class="ss-q-help" for="entry_0"></label>
					<input type="text" name="entry.0.single" value="" class="ss-q-short" id="entry_0" /></div></div></div>
		<br /> <div class="errorbox-good">
		<div class="ss-item  ss-text"><div class="ss-form-entry"><label class="ss-q-title" for="entry_2">Where are you using the app/widget?</label>
		<label class="ss-q-help" for="entry_2"></label>
		<input type="text" name="entry.2.single" value="<?=$platform;?>" class="ss-q-short" id="entry_2" /></div></div></div>
		<br /> <div class="errorbox-good">
		<div class="ss-item ss-item-required ss-paragraph-text"><div class="ss-form-entry"><label class="ss-q-title" for="entry_3">Enter your question or problem here.
		<span class="ss-required-asterisk">*</span></label>
		<label class="ss-q-help" for="entry_3"></label>
		<textarea name="entry.3.single" rows="8" cols="75" class="ss-q-long" id="entry_3"></textarea></div></div></div>
		<br /> <div class="errorbox-good">
		<div class="ss-item  ss-radio"><div class="ss-form-entry"><label class="ss-q-title" for="entry_5">Are you a...
		</label>
		<label class="ss-q-help" for="entry_5"></label>
		<ul class="ss-choices"><li class="ss-choice-item"><input type="radio" name="entry.5.group" value="Teacher" class="ss-q-radio" id="group_5_1" />
		<label class="ss-choice-label" for="group_5_1">Teacher</label></li> <li class="ss-choice-item"><input type="radio" name="entry.5.group" value="Student" class="ss-q-radio" id="group_5_2" />
		<label class="ss-choice-label" for="group_5_2">Student</label></li> <li class="ss-choice-item"><input type="radio" name="entry.5.group" value="Parent" class="ss-q-radio" id="group_5_3" />
		<label class="ss-choice-label" for="group_5_3">Parent</label></li>
		<li class="ss-choice-item"><input type="radio" name="entry.5.group" value="__option__" class="ss-q-radio" id="other_option:5" />
		<label for="other_option:5">Other:</label>
		<input type="text" name="entry.5.group.other_option_" value="" class="ss-q-other" /></li></ul></div></div></div>
		<br />
		<input type="hidden" name="pageNumber" value="0" />
		<input type="hidden" name="backupCache" value="" />
		<div class="ss-item ss-navigate"><div class="ss-form-entry">
		<input type="submit" name="submit" value="Submit" /></div></div></form>

	</div>
</div>
<script type="text/javascript" charset="utf-8">
	$('#show_feedback_form').click(function() {
		$('#googleform').slideToggle();
	});
</script>