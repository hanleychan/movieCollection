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
							<form action="{{ url("movieCategory/{$movieCategory->id}/delete") }}" method="post">
								{{ method_field('delete') }}
								{{ csrf_field() }}

								<div class="row">
									<div class="col-xs-10">
										<a href="{{ url("movieCategory/{$movieCategory->id}") }}">
											{{ $movieCategory->name }} ({{ count($movieCategory->movieCollections) }})
										</a>
									</div>
									<div class="col-xs-2">
										<button class="deleteCategory btn btn-info" type="submit"><span class="glyphicon glyphicon-trash"></span></button>
									</div>
								</div>
							</form>
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
								<form action=" {{ url("tvCategory/{$tvCategory->id}/delete") }}" method="post">
									{{ method_field('delete') }}
									{{ csrf_field() }}

									<div class="row">
										<div class="col-xs-10">
											<a href="{{ url("tvCategory/{$tvCategory->id}") }}">
												{{ $tvCategory->name }} ({{ count($tvCategory->tvCollections) }})
											</a>
										</div>
										<div class="col-xs-2">
											<button class="deleteCategory btn btn-info" type="submit"><span class="glyphicon glyphicon-trash"></span></button>
										</div>
									</div>
								</form>
							</li>
						@endforeach
					</ul>
				@endif
			</div>
		</div>
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
				    	deleteCategory.parentsUntil('form').parent().submit();
				    },
			        No: function() {
				        $( this ).dialog( "close" );
			    	}
			    },
			    close: function(event, ui) {
			    	$("#dialog-confirm").empty();
			    }
		    });


		});
	</script>
@endsection