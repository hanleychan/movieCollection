<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Repositories\MovieRepository;
use App\User;
use App\Movie;
use App\MovieNote;
use App\MovieCategory;
use App\MovieCollection;
use App\TVShow;
use App\TVNote;
use App\TVCategory;
use App\TVCollection;

class MovieController extends Controller
{
    protected $movies;

    /**
     * Create a new MovieController instance
     */
	public function __construct(MovieRepository $movies)
	{
		$this->middleware('auth');
        $this->movies = $movies;
	}

	/**
	 * Show user categories
	 */
	public function myCollection(Request $request)
	{
		// Fetch user categories
		$movieCategories = $request->user()->movieCategories()->with('movieCollections')->orderBy('name', 'asc')->get()->groupBy('name');
		$tvCategories = $request->user()->tvCategories()->with('tvCollections')->orderBy('name', 'asc')->get()->groupBy('name');

		return view('movies.myCollection', compact('movieCategories', 'tvCategories'));
	}

	/**
	 * Show all movies from a movie category
	 */
	public function movieCategory(Request $request, MovieCategory $movieCategory)
	{
		$type = 'movie';
		$category = $movieCategory;
		$items = $movieCategory->movieCollections()->join('movies', 'movie_collections.movie_id', '=', 'movies.moviedb_id')->orderBy('title', 'asc')->paginate(20);

		return view('movies.collectionItems', compact('type', 'category', 'items'));

	}

	/**
	 * Show all tv shows from a tv show category
	 */
	public function tvCategory(Request $request, TVCategory $tvCategory) 
	{
		$type = 'tv';
		$category = $tvCategory;
		$items = $category->tvCollections()->join('tvShows', 'tv_collections.tvShow_id', '=', 'tvShows.moviedb_id')->orderBy('title', 'asc')->paginate(20);
		return view('movies.collectionItems', compact('type', 'category', 'items'));
	}

	/**
	 * Process adding a new movie category
	 */
	public function newMovieCategory(Request $request)
	{
		$this->validate($request, [
			'movieCategoryName'=>"required|max:20|unique:movie_categories,name,NULL,id,user_id,{$request->user()->id}",
		]);

		session()->flash('successMessage', "New movie category has been successfully created.");

		$request->user()->movieCategories()->create(array('name'=>trim($request->movieCategoryName)));

		return back();
	}

	/**
	 * Process adding a new tv show category
	 */
	public function newTVCategory(Request $request)
	{
		$this->validate($request, [
			'tvCategoryName'=>"required|max:20|unique:tv_categories,name,NULL,id,user_id,{$request->user()->id}",
		]);

		session()->flash('successMessage', "New TV show category has been successfully created.");

		$request->user()->tvCategories()->create(array('name'=>trim($request->tvCategoryName)));

		return back();
	}

	/**
	 *	Process updating a movie category name
	 */
	public function editMovieCategory(Request $request, $id)
	{
		$this->validate($request, [
			'movieCategoryName' => "required|max:20|unique:movie_categories,name,{$id},id,user_id,{$request->user()->id}",
		]);

		$movieCategory = $request->user()->movieCategories->where('id', (int)$id)->first();
		if(!empty($movieCategory)) {
			$movieCategory->update(array('name' => $request->movieCategoryName));
			return json_encode(array('success' => true,
									 'message' => 'Category has been successfully renamed')); 
		} else {
			return json_encode(array('success' => false,
									 'message' => 'Error: There was a problem processing your request'));
		}
	}

	/**
	 * Process updating a tv category name
	 */
	public function editTVCategory(Request $request, $id)
	{
		$this->validate($request, [
			'tvCategoryName' => "required|max:20|unique:tv_categories,name,{$id},id,user_id,{$request->user()->id}",
		]);

		$tvCategory = $request->user()->tvCategories->where('id', (int)$id)->first();
		if(!empty($tvCategory)) {
			$tvCategory->update(array('name' => $request->tvCategoryName));
			return json_encode(array('success' => true,
									 'message' => 'Category has been successfully renamed')); 
		} else {
			return json_encode(array('success' => false,
									 'message' => 'Error: There was a problem processing your request'));
		}
	}

	/**
	 * Process deleting a movie category
	 */
	public function deleteMovieCategory(Request $request, $id)
	{
		// Fetch movie category
		$movieCategory = $request->user()->movieCategories->where('id', (int)$id)->first();
		if(!empty($movieCategory)) {
			$movieCategory->delete();
		} else {
			return json_encode(array('success' => false, 
									 'message' => 'Error: There was a problem processing your request'));
		}

		return json_encode(array('success' => true,
								 'message' => 'Category has been successfully deleted'));
	}

	/**
	 * Process deleting a tv show category
	 */
	public function deleteTVCategory(Request $request, $id) 
	{
		// Fetch tv show category
		$tvCategory = $request->user()->tvCategories->where('id', (int)$id)->first();

		if(!empty($tvCategory)) {
			$tvCategory->delete();
		} else {
			return json_encode(array('success' => false,
									 'message' => 'Error: There was a problem processing your request'));
		}
		return json_encode(array('success' => true,
								 'message' => 'Category has been successfully deleted'));
	}

	/**
	 * Show all movies or tv show by a given search term 
	 */
	public function find(Request $request)
	{
		$search = trim($request->search);
        $type = trim($request->type);
		$page = (int)trim($request->page);

		if($search) {
            $results = $this->movies->searchMovies($search, $page, $type);
            $numPages = ($results['total_pages'] <= 1000) ? $results['total_pages'] : 1000;
        } else {
            $results = null;
        }

		return view('movies.find', compact('results', 'search', 'type', 'page', 'numPages'));
	}

	/**
	 * Show a single movie
	 */
	public function movie(Request $request, $id)
	{
		$type = 'movie';
		$result = $this->movies->getMovie((int)$id);

		// Set previous page url
		if(\URL::current() !== \URL::previous()) {
			$request->session()->put('prevPage', \URL::previous());
			$prevPage = \URL::previous();
		} else {
			$prevPage = $request->session()->pull('prevPage');
		}

		if(!empty($result)) {
			// fetch user movie note
			$note = $request->user()->movieNotes->where('movie_id', (int)$id)->first();

			// fetch user movie collections 
			$userCategories = $request->user()->movieCategories()->orderBy('name', 'asc')->with(['movieCollections' => function($query) use ($id) {
				$query->where('movie_id', (int)$id);
			}])->get();

			foreach($userCategories as $userCategory) {
				if(!empty($userCategory->movieCollections->first())) {
					$userCategory->inCollection = true;
				} else {
					$userCategory->inCollection = false;
				}
			}
		} else {
			// Return to homepage if movie doesn't exist
			return redirect(url("/myCollection"))->with('errorMessage', 'Movie does not exist');
		}

		return view('movies.movie', compact('result', 'type', 'note', 'userCategories', 'prevPage'));
	}

	/**
	 * Show a single tv show
	 */
	public function tvShow(Request $request, $id)
	{
		$type = 'tv';
		$result = $this->movies->getTVShow((int)$id);

		// Set previous page url
		if(\URL::current() !== \URL::previous()) {
			$request->session()->put('prevPage', \URL::previous());
			$prevPage = \URL::previous();
		} else {
			$prevPage = $request->session()->pull('prevPage');
		}

		if(!empty($result)) {
			// fetch tv show note
			$note = $request->user()->tvNotes->where('tvShow_id', (int)$id)->first();	

			// fetch user tv show collections
			$userCategories = $request->user()->tvCategories()->orderBy('name', 'asc')->with(['tvCollections' => function($query) use ($id) {
				$query->where('tvShow_id', (int)$id);
			}])->get();

			foreach($userCategories as $userCategory) {
				if(!empty($userCategory->tvCollections->first())) {
					$userCategory->inCollection = true;
				} else {
					$userCategory->inCollection = false;
				}
			}
		} else {
			// Redirect to homepage if movie doesn't exist
			return redirect(url("/myCollection"))->with('errorMessage', 'TV Show does not exist');
		}

		return view('movies.movie', compact('result', 'type', 'note', 'userCategories', 'prevPage'));
	}

	/**
	 * Update movie collection and notes information
	 */
	public function updateMovieCollection(Request $request, $id)
	{
		// Update previous page
		$request->session()->put('prevPage', $request->prevPage);	

		// Check if movie exists
		if(empty($movieInfo = $this->movies->getMovie((int)$id))) {
			// Return to homepage if movie doesn't exist
			return redirect(url("/myCollection"))->with('errorMessage', 'Movie does not exist');
		}

		// Fetch movie
		$movie = Movie::where('moviedb_id', (int)$id)->first();

		// Add movie to movies table if it does not exist
		if(empty($movie)) {
			$movie = Movie::create(['title' => $movieInfo['title'], 
									'release' => $movieInfo['release_date'],
									'moviedb_id' => $movieInfo['id'],
									'poster' => $movieInfo['poster_path']
			]);
		} else {
			// Update movie entry if changed	
			$update = false;

			if($movie->title !== $movieInfo['title']) {
				$update = true;
				$movie->title = $movieInfo['title'];
			}

			if($movie->release !== $movieInfo['release_date']) {
				$update = true;
				$movie->release = $movieInfo['release_date'];
			}

			if($movie->poster !== $movieInfo['poster_path']) {
				$update = true;
				$movie->poster = $movieInfo['poster_path'];
			}

			if($update) {
				$movie->save();
			}
		}

		// Update user movie collections
		$movieCategories = $request->user()->movieCategories->load('movieCollections');
		foreach($movieCategories as $movieCategory) {
			$movieCollection = $movieCategory->movieCollections->where('movie_id', (int)$id)->first();

			if(!empty($request->categories)) {
				if(in_array($movieCategory->id, $request->categories)) {
					// Add to movie category if it does not already exist
					if(empty($movieCollection)) {
						$movieCategory->movieCollections()->save(new MovieCollection(['movie_id'=>(int)$id]));
					}
				} else {
					// Delete movie from category if it exists
					if(!empty($movieCollection)) {
						$movieCollection->delete();
					}
				}
			} else {
				// Delete the movie from all categories
				if(!empty($movieCollection)) {
					$movieCollection->delete();
				}
			}
		}

		// Validate note input
		$this->validate($request, [
			'note' => 'max:255',
		]);

		// fetch user movie note if exists
		$note = $request->user()->movieNotes->where('movie_id', (int)$id)->first();

		// Create a new note if there is no exisiting note
		if(empty($note)) {
			// Make sure note is not blank
			if(trim($request->note) !== '') {
				$request->user()->movieNotes()->save(new MovieNote(['movie_id'=>(int)$id, 'note'=>trim($request->note)]));
			}
		} else {
			// Update existing note
			if(trim($request->note) !== '') {
				$note->note = trim($request->note);
				$note->save();
			} else {
				// Delete existing note if new note is blank
				$note->delete();
			}
		}

		session()->flash('successMessage', "Your movie collection has been updated.");

		return back();
	}

	/**
	 * Update tv show collection and notes information
	 */
	public function updateTVCollection(Request $request, $id) 
	{
		// Update previous page
		$request->session()->put('prevPage', $request->prevPage);	

		// Check if movie exists
		if(empty($tvShowInfo = $this->movies->getTVShow((int)$id))) {	
			// Return to homepage if movie doesn't exist
			return redirect(url("/myCollection"))->with('errorMessage', 'TV show does not exist');
		}
		
		// Fetch TV show
		$tvShow = TVShow::where('moviedb_id', (int)$id)->first();
		// Add movie to movies table if it does not exist
		if(empty($tvShow)) {
			$tvShow = TVShow::create(['title' => $tvShowInfo['name'], 
									'release' => $tvShowInfo['first_air_date'],
									'moviedb_id' => $tvShowInfo['id'],
									'poster' => $tvShowInfo['poster_path']]);
		}

		// Update tv show collections 
		$tvCategories = $request->user()->tvCategories->load('tvCollections');

		foreach($tvCategories as $tvCategory) {
			$tvCollection = $tvCategory->tvCollections->where('tvShow_id', (int)$id)->first();
			if(!empty($request->categories)) {
				if(in_array($tvCategory->id, $request->categories)) {
					// Add to TV show category if it does not already exist
					if(empty($tvCollection)) {
						$tvCategory->tvCollections()->save(new tvCollection(['tvShow_id'=>(int)$id]));
					}
				} else {
					// Delete TV show from category if it exists
					if(!empty($tvCollection)) {
						$tvCollection->delete();
					}
				}
			} else {
				// Delete the TV show from all categories
				if(!empty($tvCollection)) {
					$tvCollection->delete();
				}
			}
		}

		// Validate note input
		$this->validate($request, [
			'note' => 'max:255',
		]);

		// fetch user movie note if exists
		$note = $request->user()->tvNotes->where('tvShow_id', (int)$id)->first();

		// Create a new note if there is no exisiting note
		if(empty($note)) {
			// Make sure note is not blank
			if(trim($request->note) !== '') {
				$request->user()->tvNotes()->save(new tvNote(['tvShow_id'=>(int)$id, 'note'=>trim($request->note)]));
			}
		} else {
			// Update existing note
			if(trim($request->note) !== '') {
				$note->note = trim($request->note);
				$note->save();
			} else {
				// Delete existing note if new note is blank
				$note->delete();
			}
		}

		session()->flash('successMessage', "Your TV show collection has been updated.");

		return back();
	}
}
