@extends('layouts.app')
@section('content')
	<div class="container">
		<h1>My Collection</h1>

		@if (count($errors) > 0)
		    <!-- Form Error List -->
		    <div class="alert alert-danger">
		        <ul>
		            @foreach ($errors->all() as $error)
		                <li>{{ $error }}</li>
		            @endforeach
		        </ul>
		    </div>
		@endif

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
									<input type="text" class="editCategory" value="{{ $movieCategory->first()->name }}" maxlength="20">
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
										<input type="text" class="editCategory" value="{{ $tvCategory->first()->name }}" maxlength="20">
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
<div id="dialog-confirm" title="Confirm Delete Category">
</div>

<div id="dialog-confirm" title="Confirm Delete Category">
</div>


@endsection

@section('scripts')
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>

<script>
	$(document).ready(function() {
		$(".deleteCategory").attr("type", "button");
	});

	$.ajaxSetup({
	        headers: {
	            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	        }
	});

	$(".deleteCategory").on("click", function() {
		var deleteCategory = $(this);
		var warningMessage = $("<p></p>");

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
						    	deleteCategory.parentsUntil("li").parent().remove();
				    		},
				    		error: function(data) {
				    			location.reload();
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
			// Update with ajax
			var updatedName = $(this).parentsUntil("div").parent().prev().children("input").val();
	    	var categoryId = $(this).prevAll("input.categoryId").val();
	    	var type = $(this).prevAll("input.type").val();

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
					newName: updatedName
				},
				success: function(data) {
					console.log(data);
					editCategory.parentsUntil("div").parent().prev().children("a").children("span.categoryName").html(updatedName);
					editCategory.children("span").toggleClass("glyphicon-edit").toggleClass("glyphicon-save");
					editCategory.parentsUntil("div").parent().prev().children("a").toggleClass("editMode").next("input").toggleClass("editMode").focus();
					editCategory.toggleClass("saveCategory");
				},
				error: function(data) {
					console.log(data);
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