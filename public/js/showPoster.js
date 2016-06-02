$(".movieLink").mouseover(function() {
	var posterPath = $(this).prev("input[type='hidden']").val();

	if(posterPath) {
		$("#poster").html('<img src="http://image.tmdb.org/t/p/w185' + posterPath + '" alt="poster">');
	} else {
		$("#poster").html("<p>No image available</p>");
	}
});

$(".movieLink").mouseleave(function() {
	$("#poster").html("");
});