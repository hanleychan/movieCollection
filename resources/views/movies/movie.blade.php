@extends('layouts.app')
@section('content')
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

	<div class="row">
		<h1>
			@if($type === 'movie')
				{{ $result['title'] }}
			@elseif($type === 'tv')
				{{ $result['name'] }}
			@endif
		</h1>
	</div>

	<div class="row">
		<div class="col-sm-4">
			@if(!empty($result['poster_path']))
				<img src="http://image.tmdb.org/t/p/w185{{ $result['poster_path'] }}" alt="poster">
			@else
				<p>No image available</p>
			@endif
		</div>
		<div class="col-sm-8">
			<h4>Release Date:</h4>
			@if($type === 'movie' && !empty($result['release_date']))
				<p>{{ $result['release_date'] }}</p>
			@elseif($type === 'tv' && !empty($result['first_air_date']))
				<p>{{ $result['first_air_date'] }}</p>
			@else
				<p>Not available</p>
			@endif

			<h4>Genre:</h4>
			@if(!empty($result['genres']))
				<p>
					@for($ii = 0; $ii < count($result['genres']); $ii++)
						{{ $result['genres'][$ii]['name'] }}{{ ($ii+1 !== count($result['genres'])) ? ', ' : '' }}
					@endfor
				</p>
			@else
				<p>Not available</p>
			@endif

			<h4>Overview</h4>
			@if(!empty($result['overview']))
				<p>{{ $result['overview'] }}</p>
			@else
				<p>Not available</p>
			@endif

			<h4>Update Collection:</h4>
			@if ($type === 'movie' || $type !== 'tv')
			<form action="/movie/{{ $result['id'] }}/updateMovieCollection" method="post">
			@elseif ($type ==='tv')
			<form action="/tv/{{ $result['id'] }}/updateTVCollection" method="post">
			@endif
				{{ csrf_field() }}
				<input type="hidden" name="prevPage" value="{{ $prevPage }}">

				@foreach($userCategories as $userCategory)
					<input type="checkbox" id="{{ $userCategory->name }}" name="categories[]" value="{{ $userCategory->id }}"{{ $userCategory->inCollection ? ' checked' : '' }}>
					<label for="{{ $userCategory->name }}">{{ $userCategory->name }}</label><br>
				@endforeach	

				<label for="note">Notes:</label><br>
				<textarea class="form-control" rows="5" id="note" name="note" maxlength="255">{{ (old('note') != "") ? old('note') : ((!empty($note)) ? $note->note : '') }}</textarea><br>
				<button type="submit">Update</button>
			</form>

		</div>
	</div>

	<p id="prevPageLink"><a href="{{ $prevPage }}">Return to previous page</a></p>
</div>
@endsection
