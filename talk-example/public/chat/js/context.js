$(document).ready(function () {
	$("a img").on('click', function () {

		var img = $(this).attr('src');
		$(".chat-history").hide().css('background', 'url(' + img + ')');

		console.log(img);

		var $img = $("<img>").attr('src', img).one('load', function () {
			$(".chat-history").fadeIn();
		});
			
		if ($img.get(0).complete) {
			$(".chat-history").fadeIn(2000);
		}

		// Get file name from src
		String.prototype.filename=function(extension){
		    var s= this.replace(/\\/g, '/');
		    s= s.substring(s.lastIndexOf('/')+ 1);
		    return extension? s.replace(/[?#].+$/, ''): s.split('.')[0];
		}

		nameImg = img.filename() + '.gif'; // Name image

		/*$.ajax({
			type: "GET",
			url: "...",
			data: "{imgName : nameImg}";
			success: function(data) {

			}
		});*/

  	});

  	// $("#form-bg").

  	/*$(".background_img").on('click', function () {
  		e.preventDefault();
  		var url, tag, request, data;

  		tag = $(this);
  	});*/

});

