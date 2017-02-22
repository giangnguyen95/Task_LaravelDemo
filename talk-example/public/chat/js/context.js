$(document).ready(function () {
	$("a img").on('click', function () {
		var img = $(this).attr('src');
		$(".chat-history").hide().css('background', 'url(' + img + ')');

		var $img = $("<img>").attr('src', img).one('load', function () {
			$(".chat-history").fadeIn();
		});
			
		if ($img.get(0).complete) {
			$(".chat-history").fadeIn(2000);
		}
  });
});