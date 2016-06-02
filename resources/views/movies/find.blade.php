@extends('layouts.app')
@section('content')
	<div class="container">
        <div class="row">
    		<h1>Find Movie</h1>
    		<form action="/find" method="get">
                <label for="movie">Movie</label>
                <input type="radio" name="type" value="movie" id="movie"@if ($type==='movie' || empty($type)){{ ' checked' }}@endif required>
                <label for="tv">TV Show</label>
                <input type="radio" name="type" value="tv" id="tv"@if ($type==='tv'){{ ' checked' }}@endif required><br>
    			<label for="search">Search:</label>
    			<input type="text" id="search" name="search" value="{{ $search }}">
    			<button type="submit">Go</button>
    		</form>

            <h3>Results:</h3>
            <div class="col-md-7">
        		@if($results)
                    @if(count($results['results'])== 0)
                        <p>No results found</p>
                    @else
                        <div class="list-group">
                        @foreach($results['results'] as $result)
                            @if(isset($result['title']))
                                    <input type="hidden" class="poster" value="{{ $result['poster_path'] }}">
                                    <a href="/movie/{{ $result['id'] }}" class="movieLink list-group-item">
                                        {{ $result['title'] }} 
                                        @if(!empty($result['release_date']))
                                            {{' (' . substr($result['release_date'],0,4) . ')'  }}
                                        @endif
                                    </a>
                            @elseif(isset($result['name']))
                                    <input type="hidden" class="poster" value="{{ $result['poster_path'] }}">
                                    <a href="/tv/{{ $result['id'] }}" class="movieLink list-group-item">
                                        {{ $result['name'] }}
                                        @if(!empty($result['first_air_date']))
                                            {{ ' (' . substr($result['first_air_date'],0,4) . ')' }}
                                        @endif
                                    </a>
                            @endif
                        @endforeach
                        </div>
                    @endif
                    @include('movies.moviePagination')
        		@else
            		<p>Please enter a search term above to find a movie or tv show.</p>
        		@endif
            </div>

            <div class="col-sm-5 hidden-xs hidden-sm">
                <div id="poster">
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <script src="js/showPoster.js"></script>
@endsection
