jQuery( document ).ready( function( $ ) {

	var sb_restricted_elements = '';
	if( ss_spamblocker_settings.restricted_elements.length > 0 ) {
		sb_restricted_elements = ', ' + ss_spamblocker_settings.restricted_elements;
	}

	if( jQuery( ':submit:not(.adminbar-button, #searchsubmit, .widget :submit' + sb_restricted_elements + ')' ).length > 0 ) {

		var sb_user_title = ss_spamblocker_settings.display_text;
		var sb_container;
		sb_container  = '<div id="ss_spamblocker_drag">';
		sb_container += '<p id="sb_title">' + sb_user_title + '</p>';
		sb_container += '<div id="ss_spamblocker_form"></div><input type="hidden" id="ss_spamblocker_present" name="ss_spamblocker_present" value="1"/>';
		sb_container += '</div>';
		$( ':submit:not(.adminbar-button, #searchsubmit, .widget :submit' + sb_restricted_elements + ')' ).before( sb_container );

		var sb_user_image = ss_spamblocker_settings.display_image;
		var sb_img = new Image();
		sb_img.src = sb_user_image;

		sb_img.onload = function() {

			var sb_img_width = this.width;
			var sb_img_height = this.height;

			var sb;
			sb  = '<div id="sb_start" style="width:' + sb_img_width + 'px;height:' + sb_img_height + 'px;">';
			sb += '<div id="sb_object" draggable="true" style="background-image:url(' + sb_user_image + ');width:' + sb_img_width + 'px;height:' + sb_img_height + 'px;"></div>';
			sb += '</div>';
			sb += '<div id="sb_arrow" style="height:' + sb_img_height + 'px;"></div>';
			sb += '<div id="sb_target" style="width:' + sb_img_width + 'px;height:' + sb_img_height + 'px;"></div>';
			sb += '<div class="fix"></div>';

			$( '#ss_spamblocker_form' ).html( sb );

			

			// Once HTML has been rendered, load drag event listeners
			var start = document.querySelectorAll('#sb_object');
			[].forEach.call(start, function(el) {
				el.addEventListener('dragstart', handleDragStart, false);
				el.addEventListener('touchstart', handleDragStart, false);
			});

			var target = document.querySelectorAll('#sb_target');
			[].forEach.call(target, function(el) {
				el.addEventListener('dragenter', handleDragEnter, false);
				el.addEventListener('dragover', handleDragOver, false);
				el.addEventListener('dragleave', handleDragLeave, false);
				el.addEventListener('drop', handleDrop, false);
  				el.addEventListener('dragend', handleDragEnd, false);
  				
  				el.addEventListener('touchenter', handleDragOver, false);
  				el.addEventListener('touchleave', handleDragLeave, false);
  				el.addEventListener('touchmove', handleDragOver, false);
  				el.addEventListener('touchend', handleDrop, false);
			});

		}

	}
	
});


function handleDragStart(e) {
	e.dataTransfer.effectAllowed = 'move';
	e.dataTransfer.setData('text/html', this.innerHTML);
}

function handleDragOver(e) {
	if ( e.preventDefault ) {
		e.preventDefault();
	}
	e.dataTransfer.dropEffect = 'move';
	return false;
}

function handleDragEnter(e) {
	this.classList.add( 'over' );
}

function handleDragLeave(e) {
	this.classList.remove( 'over' );
}

function handleDrop(e) {
	if (e.stopPropagation) {
		e.stopPropagation();
	}

	var drag_image = jQuery('#sb_object').css('background-image');
	var sb_field = '<input type="hidden" name="ss_spamblocker_check" value="1"/>';

	jQuery('#ss_spamblocker_present').after(sb_field);
	jQuery('#sb_target').removeClass( 'over' );
	jQuery('#sb_target').css( 'background-image' , drag_image );
	jQuery('#sb_object').addClass( 'complete' );

	return false;
}

function handleDragEnd(e) {

	// var target = document.querySelectorAll('#sb_target');
	// [].forEach.call(target, function (el) {
	// 	el.classList.remove( 'over' );
	// });
}






// function ss_spamblocker_drag(ev) { ev.dataTransfer.setData("Text",ev.target.id); }
// function ss_spamblocker_allow_drop(ev) { ev.preventDefault(); }
// function ss_spamblocker_drop(ev) {
// 	ev.preventDefault();
// 	var data = ev.dataTransfer.getData("Text");
// 	ev.target.appendChild(document.getElementById(data));
// 	var sb_field = '<input type="hidden" name="ss_spamblocker_check" value="1"/>';
// 	jQuery('#ss_spamblocker_present').after(sb_field);
// }