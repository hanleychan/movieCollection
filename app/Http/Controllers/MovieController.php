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

	public function __construct(MovieRepository $movies)
	{
		$this->middleware('auth');
        $this->movies = $movies;
	}

	public function myCollection(Request $request)
	{
		// Fetch user categories
		$movieCategories = $request->user()->movieCategories->load('movieCollections');
		$tvCategories = $request->user()->tvCategories->load('tvCollections');

		return view('movies.myCollection', compact('movieCategories', 'tvCategories'));
	}

	public function movieCategory(Request $request, MovieCategory $movieCategory)
	{
		$itemsList = $movieCategory->load('movieCollections');

		// fetch movie info
		foreach($itemsList->movieCollections as $movie) {
			$movieInfo = $this->movies->getMovie($movie->moviedb_id);
			$movie->title = $movieInfo['title'];
			$movie->release_date = $movieInfo['release_date'];
		}

		return view('movies.collectionItems', compact('itemsList'));

	}

	public function tvCategory(Request $request, TVCategory $tvCategory) 
	{

		$itemsList = $tvCategory->load('tvCollections');
		return view('movies.collectionItems', compact('itemsList'));
	}

	public function newMovieCategory(Request $request)
	{
		$this->validate($request, [
			'movieCategoryName'=>"required|max:20|unique:movie_categories,name,NULL,id,user_id,{$request->user()->id}",
		]);

		$request->user()->movieCategories()->create(array('name'=>trim($request->movieCategoryName)));

		return back();
	}

	public function newTVCategory(Request $request)
	{
		$this->validate($request, [
			'tvCategoryName'=>"required|max:20|unique:tv_categories,name,NULL,id,user_id,{$request->user()->id}",
		]);

		$request->user()->tvCategories()->create(array('name'=>trim($request->tvCategoryName)));

		return back();
	}

	public function deleteMovieCategory(Request $request, $id)
	{
		// Fetch movie category
		$movieCategory = $request->user()->movieCategories->where('id', (int)$id)->first();
		if(!empty($movieCategory)) {
			$movieCategory->delete();
		} else {
			return "movie category not found";
		}
		return back();
	}

	public function deleteTVCategory(Request $request, $id) 
	{
		// Fetch tv show category
		$tvCategory = $request->user()->tvCategories->where('id', (int)$id)->first();

		if(!empty($tvCategory)) {
			$tvCategory->delete();
		} else {
			return "tv category not found";
		}

		return back();
	}

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

		return view('movies.find', compact('results', 'search', 'type', 'numPages'));
	}

	public function movie(Request $request, $id)
	{
		$type = 'movie';

		$result = $this->movies->getMovie((int)$id);

		if(!empty($result)) {
			// fetch user movie note
			$note = $request->user()->movieNotes->where('movie_id', (int)$id)->first();

			// fetch user movie collections 
			$userCategories = $request->user()->movieCategories->load('movieCollections');
			foreach($userCategories as $userCategory) {
				if(!empty($userCategory->movieCollections->where('movie_id', (int)$id)->first())) {
					$userCategory->inCollection = true;
				} else {
					$userCategory->inCollection = false;
				}
			}
		}
		return view('movies.movie', compact('result', 'type', 'note', 'userCategories'));
	}

	public function tvShow(Request $request, $id)
	{
		$type = 'tv';

		$result = $this->movies->getTVShow((int)$id);

		if(!empty($result)) {
			// fetch tv show note
			$note = $request->user()->tvNotes->where('tvShow_id', (int)$id)->first();	

			// fetch user tv show collections
			$userCategories = $request->user()->tvCategories->load('tvCollections');
			foreach($userCategories as $userCategory) {
				if(!empty($userCategory->tvCollections->where('tvShow_id', (int)$id)->first())) {
					$userCategory->inCollection = true;
				} else {
					$userCategory->inCollection = false;
				}
			}
		}
		return view('movies.movie', compact('result', 'type', 'note', 'userCategories'));
	}

	public function updateMovieCollection(Request $request, $id)
	{
		// Check if movie exists
		if(empty($movieInfo = $this->movies->getMovie((int)$id))) {
			return "NO SUCH MOVIE";
		}

		// Fetch movie
		$movie = Movie::where('moviedb_id', (int)$id)->first();
		// Add movie to movies table if it doesn't exist
		if(empty($movie)) {
			$movie = Movie::create(['title' => $movieInfo['title'], 
									'release' => $movieInfo['release_date'],
									'moviedb_id' => $movieInfo['id']]);
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

		return back();
	}

	public function updateTVCollection(Request $request, $id) 
	{
		// Check if movie exists
		if(empty($tvShowInfo = $this->movies->getTVShow((int)$id))) {
			return "NO SUCH TV SHOW";
		}


		// Fetch TV show
		$tvShow = TVShow::where('moviedb_id', (int)$id)->first();
		// Add movie to movies table if it doesn't exist
		if(empty($tvShow)) {
			$tvShow = TVShow::create(['title' => $tvShowInfo['name'], 
									'release' => $tvShowInfo['first_air_date'],
									'moviedb_id' => $tvShowInfo['id']]);
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

		return back();
	}
}