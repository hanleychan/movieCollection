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
		<form action="{{ url("movieCategory/new") }}" method="post">
			{{ csrf_field() }}
			<label for="name">New Category: </label>
			<input type="text" id="name" name="movieCategoryName" maxlength="20" value="{{ old('movieCategoryName') }}">
			<button type="submit">Add</button>
		</form>
		@if(!empty($movieCategories))
			<ul>
				@foreach($movieCategories as $movieCategory)
					<li>
						<form class="deleteCategoryForm" action="{{ url("movieCategory/{$movieCategory->id}/delete") }}" method="post">
							{{ method_field('delete') }}
							{{ csrf_field() }}

							<a href="{{ url("movieCategory/{$movieCategory->id}") }}">
								{{ $movieCategory->name }} ({{ count($movieCategory->movieCollections) }})
							</a>

							<button class="deleteCategory" type="submit">Delete</button>
						</form>
					</li>
				@endforeach
			</ul>
		@endif
		<h3>TV Shows:</h3>
		<form action="{{ url("tvCategory/new") }}" method="post">
			{{ csrf_field() }}
			<label for="name">New Category: </label>
			<input type="text" id="name" name="tvCategoryName" maxlength="20" value="{{ old('tvCategoryName') }}">
			<button type="submit">Add</button>
		</form>
		@if(!empty($tvCategories))
			<ul>
				@foreach($tvCategories as $tvCategory)
					<li>
						<form class="deleteCategoryForm" action=" {{ url("tvCategory/{$tvCategory->id}/delete") }}" method="post">
							{{ method_field('delete') }}
							{{ csrf_field() }}
							<a href="{{ url("tvCategory/{$tvCategory->id}") }}">
								{{ $tvCategory->name }} ({{ count($tvCategory->tvCollections) }})
							</a>
							<button class="deleteCategory" type="submit">Delete</button>
						</form>
					</li>
				@endforeach
			</ul>
		@endif
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
				    	deleteCategory.parent().submit();
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