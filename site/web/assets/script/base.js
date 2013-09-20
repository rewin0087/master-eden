
	// ------------ GLABAL VARIABLES
	var error = 'We Have encountered and error while processing your request, Please Try Again.';
	
	(function($) {
	$.extend({
		loader : function(show) {
			show = show || false;
			if(show == true) {
				$('.modal-white').fadeIn();
				$('.page-loader').fadeIn();
			} else {
				$('.modal-white').fadeOut();
				$('.page-loader').fadeOut();
			}
		}
	});
	}(jQuery));
	
	$.extend({
		message : function(type, message) {
			if(type == 'error') {
				$('div.ajax-message').html(message).css({ background : 'rgba(189, 54, 47, 0.9)', border : '1px solid rgba(255, 255, 255, 0.7)', height : '20px'}).fadeIn();
				setTimeout(function() {
					$('div.ajax-message').fadeOut(300);
				}, 3000);
			} else if(type == 'success') {
				$('div.ajax-message').html(message).css({background: 'rgba(0, 136, 204, 0.9)', border: '1px solid rgba(21, 155, 32, 0.7)', height : '20px'}).fadeIn();
				setTimeout(function() {
					$('div.ajax-message').fadeOut(300);
				}, 3000);
			} else {
				$('div.ajax-message').html(message).css({background: 'rgba(0, 0, 0, 0.9)', border: '1px solid rgba(21, 155, 32, 0.7)', height : '20px'}).fadeIn();
				setTimeout(function() {
					$('div.ajax-message').fadeOut(300);
				}, 3000);
			}
			return false;		
		}	
	});
	
	/*
	* Ajax File Upload
	* @param int
	* @param string
	* @param files
	*
	*/
	$.extend({
		 upload : function(name, url, data) {
			jQuery.each($('input#get-upload')[0].files, function(i, file) {
				data.append('upload[]', file);
			});
				data.append('badge_name', name);
				data.append('saveupload', 1);
		 	$.ajax({
				url			: url,
				type		: 'post',
				data		: data,
				cache		: false,
				contentType	: false, 
				processData : false,
				success		: function(response) {
					$.loader(false);
					if(response == '1') {
						$.message('error', error);
					} else if(response == '2') {
						$.message('error', 'Please select atleast 1 image file.');
					} else if(response == '3') {
						$.message('error', 'Please select image file that are not bigger than 4mb.');
					} else if(response == '4') {
						$.message('error', 'Please select Image file only.');
					} else {
						$.message('success', 'Successfully Uploaded.');
						$('ul.ul-draw').prepend(response);
						$('form#badge-create')[0].reset();
						$('li.li-draw').fadeIn();
						image();
					}
					
					$('.close').click();
					return false;
				}
				
		 	});
		 }
	});

	