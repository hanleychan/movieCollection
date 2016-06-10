@extends('layouts.app')
@section('content')
<section id="myCollectionSection">
	<div class="container">

		@if (count($errors) > 0)
		    <!-- Form Error List -->
		    <div class="alert alert-danger">
		        <ul>
		            @foreach ($errors->all() as $error)
		                <li>{{ $error }}</li>
		            @endforeach
		        </ul>
		    </div>
		@elseif ($message = Session::get('successMessage'))
		    <div class="alert alert-success">
		        <ul>
		        	<li>{{ $message }}</li>
		        </ul>
		    </div>
		@endif

		<h1>My Collection</h1>
		<h3>Movies:</h3>
		<form class="form-inline" action="{{ url("movieCategory/new") }}" method="post">
			{{ csrf_field() }}
			<div class="form-group">
				<label for="movieCategoryName">New Category: </label>
				<input type="text" class="form-control newCategoryInput" id="movieCategoryName" name="movieCategoryName" maxlength="20" value="{{ old('movieCategoryName') }}">
				<button type="submit" class="btn btn-primary">Add</button>
			</div>
		</form>

		<div class="row">
			<div class="col-xs-12 col-sm-8 col-md-6">
				@if(!empty($movieCategories))
					<ul class="categoriesList">
					@foreach($movieCategories as $movieCategory)
						<li>
							<div class="row">
								<div class="col-xs-7 col-sm-8 col-md-9">
									<a href="{{ url("movieCategory/{$movieCategory->first()->id}") }}" class="categoryLink">
										<span class="categoryName">{{ $movieCategory->first()->name }}</span>
										({{ $movieCategory->first()->movieCollections->count() }})
									</a>
									<input type="text" class="editCategoryInput" value="{{ $movieCategory->first()->name }}" maxlength="20">
								</div>
								<div class="col-xs-5 col-sm-4 col-md-3 pull-right">
									<form class="deleteCategoryForm" action="{{ url("movieCategory/{$movieCategory->first()->id}/delete") }}" method="post">
										{{ method_field('delete') }}
										{{ csrf_field() }}
										<input type="hidden" class="type" value="movie">
										<input type="hidden" class="categoryId" value="{{ $movieCategory->first()->id }}">	
										<button class="editCategory btn btn-info" type="button" title="Edit"><span class="glyphicon glyphicon-edit"></span></button>
										<button class="deleteCategory btn btn-info" type="submit" title="Delete"><span class="glyphicon glyphicon-trash"></span></button>
									</form>
								</div>
							</div>
						</li>
					@endforeach
					</ul>
				@endif
			</div>
		</div>
		
		<h3>TV Shows:</h3>
		<form class="form-inline" action="{{ url("tvCategory/new") }}" method="post">
			{{ csrf_field() }}
			<div class="form-group">
				<label for="tvCategoryName">New Category: </label>
				<input type="text" class="form-control newCategoryInput" id="tvCategoryName" name="tvCategoryName" maxlength="20" value="{{ old('tvCategoryName') }}">
				<button type="submit" class="btn btn-primary">Add</button>
			</div>
		</form>

		<div class="row">
			<div class="col-xs-12 col-sm-8 col-md-6">
				@if(!empty($tvCategories))
					<ul class="categoriesList">
						@foreach($tvCategories as $tvCategory)
							<li>
								<div class="row">
									<div class="col-xs-7 col-sm-8 col-md-9">
										<a href="{{ url("tvCategory/{$tvCategory->first()->id}") }}" class="categoryLink">
											<span class="categoryName">{{ $tvCategory->first()->name }}</span>
											({{ $tvCategory->first()->tvCollections->count() }})
										</a>
										<input type="text" class="editCategoryInput" value="{{ $tvCategory->first()->name }}" maxlength="20">
									</div>
									<div class="col-xs-5 col-sm-4 col-md-3 pull-right">
										<form class="deleteCategoryForm" action=" {{ url("tvCategory/{$tvCategory->first()->id}/delete") }}" method="post">
											{{ method_field('delete') }}
											{{ csrf_field() }}
											<input type="hidden" class="type" value="tv">
											<input type="hidden" class="categoryId" value="{{ $tvCategory->first()->id }}">	
											<button class="editCategory btn btn-info" type="button" title="Edit"><span class="glyphicon glyphicon-edit"></span></button>
											<button class="deleteCategory btn btn-info" type="submit"><span class="glyphicon glyphicon-trash"></span></button>
										</form>
									</div>
								</div>
							</li>
						@endforeach
					</ul>
				@endif
			</div>
		</div>
	</div>
</section>

<div id="dialog-confirm" title="Confirm Delete Category">
</div>
@endsection

@section('scripts')

<script>
	$(document).ready(function() {
		$("button.deleteCategory").attr("type", "button");
	});


	$(".deleteCategory").on("click", function() {
		var deleteCategory = $(this);
		var warningMessage = $("<p></p>");

    	// Remove previous alert messages
    	$(this).parentsUntil("div.container").parent().children("div.alert").remove();

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
								var $successDiv = $('<div class="alert alert-success"></div>');
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
					var $successDiv = $('<div class="alert alert-success"></div>');
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
					data = JSON.parse(data['responseText']);
					if(data['movieCategoryName'] !== null) {
						var messages = data['movieCategoryName'];
					} else {
						var messages = data['tvCategoryName'];
					}

					var $errorsDiv = $('<div class="alert alert-danger"></div>');
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

</script>
@endsection