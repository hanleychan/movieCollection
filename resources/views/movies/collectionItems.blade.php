@extends('layouts.app')
@section('content')
<div class="container">
	<div class="row">
		<h1>
			{{ $category->name }}
			@if($type === 'movie')
				({{ count($category->movieCollections) }})
			@elseif($type ==='tv')
				({{ count($category->tvCollections) }})
			@endif
		</h1>
		<div class="col-md-7">
			@if(count($items) > 0)
				<div class="list-group">
					@foreach($items as $item)
						@if($type === 'movie')
	                        <input type="hidden" class="poster" value="{{ $item->movie->poster }}">
							<a href="{{ url("movie/{$item->movie->moviedb_id}") }}" class="movieLink list-group-item">
								{{ $item->movie->title }}
								@if(!empty($item->movie->release))
									({{ substr($item->movie->release,0,4) }})
								@endif
							</a>
						@elseif($type === 'tv')
	                        <input type="hidden" class="poster" value="{{ $item->tvShow->poster }}">
							<a href="{{ url("tv/{$item->tvShow->moviedb_id}") }}" class="movieLink list-group-item">
								{{ $item->tvShow->title }}
								@if(!empty($item->tvShow->release))
									({{ substr($item->tvShow->release,0,4) }})
								@endif
							</a>
						@endif
					@endforeach
				</div>
            @else
                <p>No items have been added to this cateogry yet.</p>
			@endif
			{!! $items->links() !!}
            <p><a href="{{ url('myCollection') }}">Return to My Collection</a></p>
		</div>	
	    <div class="col-sm-5 hidden-xs hidden-sm">
	        <div id="poster">
	        </div>
	    </div>
	</div>


</div>
@endsection

@section('scripts')
    <script src="{{ url('js/showPoster.js') }}"></script>
@endsection
