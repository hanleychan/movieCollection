@extends('layouts.app')
@section('content')
<section id="myCollectionSection">
	<div class="container">

		@if (count($errors) > 0)
		    <!-- Form Error List -->
		    <div class="alert alert-danger alertDismiss">
		        <ul>
		            @foreach ($errors->all() as $error)
		                <li>{{ $error }}</li>
		            @endforeach
		        </ul>
		    </div>
		@endif

		@if ($message = Session::get('errorMessage'))
		    <div class="alert alert-danger alertDismiss">
		        <ul>
	                <li>{{ $message }}</li>
		        </ul>
		    </div>
		@endif

		@if ($message = Session::get('successMessage'))
		    <div class="alert alert-success alertDismiss">
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
<script src="js/myCollection.js"></script>
@endsection