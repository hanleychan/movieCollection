$(document).ready(function() {
	$("button.deleteCategory").attr("type", "button");
});

$(".deleteCategory").on("click", function() {
	var deleteCategory = $(this);
	var warningMessage = $("<p></p>");

	// Remove previous alert messages
	$(this).parentsUntil("div.container").parent().children("div.alertDismiss").remove();

	warningMessage.html("Warning: This category will be permanently deleted and cannot be recovered.<br><br>Are you sure?");

	$("#dialog-confirm").append(warningMessage);

	$( "#dialog-confirm" ).dialog({
    	resizable: false,
	    height:225,
	    width: 400,
	    modal: true,
	    buttons: {
		    "Yes": function() {
		    	var categoryId = deleteCategory.prevAll("input.categoryId").val();
		    	var type = deleteCategory.prevAll("input.type").val();

		    	if(type === 'movie' || type === 'tv') {
		    		if(type ==='movie') {
		    			var url = "/movieCategory/" + categoryId + "/delete";
		    		} else {
		    			var url = "/tvCategory/" + categoryId + "/delete";
		    		}

			    	$.ajax({
			    		url: url,
			    		type: 'post',
			    		data: {
			    			_method: 'delete'
			    		},
			    		success: function(data) {
							var $successDiv = $('<div class="alert alert-success alertDismiss"></div>');
							var $successList = $("<ul></ul");
							if(type === 'movie') {
								var message = "The movie category has been successfully deleted";
							} else {
								var message = "The TV Show category has been successfully deleted";
							}

							$successList.append("<li>" + message + "</li>");
							$successDiv.append($successList);
							deleteCategory.parentsUntil("div.container").parent().children("h1").before($successDiv);
							$successDiv.delay(500).fadeIn('normal', function() {
						    	$(this).delay(2500).fadeOut();
						   	});

					    	deleteCategory.parentsUntil("li").parent().remove();
			    		}
			    	});
			    }
		        $(this).dialog("close");
		    },
	        No: function() {
		        $(this).dialog("close");
	    	}
	    },
	    close: function(event, ui) {
	    	$("#dialog-confirm").empty();
	    }
    });
});

/**
 * Update and save a category name when the edit or save button is clicked
 */
$('body').on("click", '.editCategory', function() {
	var editCategory = $(this);

	if($(this).hasClass('saveCategory')) {
		var updatedName = $(this).parentsUntil("div").parent().prev().children("input").val();
    	var categoryId = $(this).prevAll("input.categoryId").val();
    	var type = $(this).prevAll("input.type").val();

    	// Remove previous alert messages
    	$(this).parentsUntil("div.container").parent().children("div.alert").remove();

    	if(type === 'movie') {
			var url = "/movieCategory/" + categoryId  + "/edit";
		} else {
			var url = "/tvCategory/" + categoryId + "/edit";
		}

		$.ajax({
			url: url,
			type: 'post',
			data: {
				_method: 'patch',
				movieCategoryName: updatedName,
				tvCategoryName: updatedName
			},
			success: function(data) {
				var message = JSON.parse(data)['message'];
				var $successDiv = $('<div class="alert alert-success alertDismiss"></div>');
				var $successList = $("<ul></ul");

				$successList.append("<li>" + message + "</li>");
				$successDiv.append($successList);
				editCategory.parentsUntil("div.container").parent().children("h1").before($successDiv);
				$successDiv.delay(500).fadeIn('normal', function() {
			    	$(this).delay(2500).fadeOut();
			   	});

				editCategory.parentsUntil("div").parent().prev().children("a").children("span.categoryName").html(updatedName);
				editCategory.children("span").toggleClass("glyphicon-edit").toggleClass("glyphicon-save");
				editCategory.parentsUntil("div").parent().prev().children("a").toggleClass("editMode").next("input").toggleClass("editMode").focus();
				editCategory.toggleClass("saveCategory");
			},
			error: function(data) {
				// Redirect to login page if timed out
				if(data.status === 401) {
                    window.location.replace("/login");
				}

				data = JSON.parse(data['responseText']);
				if(data['movieCategoryName'] !== null) {
					var messages = data['movieCategoryName'];
				} else {
					var messages = data['tvCategoryName'];
				}

				var $errorsDiv = $('<div class="alert alert-danger alertDismiss"></div>');
				var $errorsList = $("<ul></ul>");

				for(var ii = 0; ii < messages.length; ii++) {
					$errorsListItem = $("<li></li>");	
					$errorsListItem.html(messages[ii]);
					$errorsList.append($errorsListItem);
				}
				$errorsDiv.append($errorsList);

				editCategory.parentsUntil("div.container").parent().children("h1").before($errorsDiv);
				$errorsDiv.delay(500).fadeIn('normal', function() {
			    	$(this).delay(2500).fadeOut();
			   	});

			}
		});
	} else {
		$(this).children("span").toggleClass("glyphicon-edit").toggleClass("glyphicon-save");
		$(this).parentsUntil("div").parent().prev().children("a").toggleClass("editMode").next("input").toggleClass("editMode").focus();
		$(this).toggleClass("saveCategory");
	}
});

/**
 *	Save updated category name when the enter key is pressed
 */
$("input.editCategoryInput").keypress(function(e) {
	if(e.which === 13) {
		$(this).parent().next().find("button.saveCategory").click();
	}
});
