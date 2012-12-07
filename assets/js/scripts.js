jQuery(document).ready(function($) {

	var sb_restricted_elements = '';
	if( ss_spamblocker_settings.restricted_elements.length > 0 ) {
		sb_restricted_elements = ', ' + ss_spamblocker_settings.restricted_elements;
	}

	if(jQuery(':submit:not(.adminbar-button, #searchsubmit, .widget :submit' + sb_restricted_elements + ')').length > 0) {

		var sb_user_title = ss_spamblocker_settings.display_text;
		var sb_container;
		sb_container  = '<div id="ss_spamblocker_drag">';
		sb_container += '<p id="sb_title">' + sb_user_title + '</p>';
		sb_container += '<div id="ss_spamblocker_form"></div><input type="hidden" id="ss_spamblocker_present" name="ss_spamblocker_present" value="1"/>';
		sb_container += '</div>';
		$(':submit:not(.adminbar-button, #searchsubmit, .widget :submit' + sb_restricted_elements + ')').before(sb_container);

		var sb_user_image = ss_spamblocker_settings.display_image;
		var sb_img = new Image();
		sb_img.src = sb_user_image;

		sb_img.onload = function() {

			var sb_img_width = this.width;
			var sb_img_height = this.height;

			var sb;
			sb  = '<div id="sb_start" style="width:' + sb_img_width + 'px;height:' + sb_img_height + 'px;">';
			sb += '<div id="sb_object" draggable="true" ondragstart="ss_spamblocker_drag(event)" style="background-image:url(' + sb_user_image + ');width:' + sb_img_width + 'px;height:' + sb_img_height + 'px;"></div>';
			sb += '</div>';
			sb += '<div id="sb_arrow" style="height:' + sb_img_height + 'px;"></div>';
			sb += '<div id="sb_target" ondrop="ss_spamblocker_drop(event)" ondragover="ss_spamblocker_allow_drop(event)" style="width:' + sb_img_width + 'px;height:' + sb_img_height + 'px;"></div>';
			sb += '<div class="fix"></div>';

			$('#ss_spamblocker_form').html(sb);

		}

	}
	
});

function ss_spamblocker_drag(ev) { ev.dataTransfer.setData("Text",ev.target.id); }
function ss_spamblocker_allow_drop(ev) { ev.preventDefault(); }
function ss_spamblocker_drop(ev) {
	ev.preventDefault();
	var data = ev.dataTransfer.getData("Text");
	ev.target.appendChild(document.getElementById(data));
	var sb_field = '<input type="hidden" name="ss_spamblocker_check" value="1"/>';
	jQuery('#ss_spamblocker_present').after(sb_field);
}