@extends('layouts.app')
@section('content')
<div class="container">
	<pre>
		@foreach($itemsList->movieCollections as $item)
			{{ var_dump($item->moviedb_id) }}
		@endforeach
	</pre>
</div>
@endsection