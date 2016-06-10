<?php

namespace App\Repositories;

use App\User;

class MovieRepository
{
    protected $client;

    /**
     * Create a new MovieRepository instance
     */
    public function __construct()
    {
        $token = new \Tmdb\ApiToken('65b0462213517b3598c24abb7ce5ac74');
        $this->client = new \Tmdb\Client($token, [
            'cache' => [
                'enabled' => false
            ]
        ]);
    }

    /**
     * Get a movie for the given id
     */
    public function getMovie($id)
    {
        try {
            $movie = $this->client->getMoviesApi()->getMovie((int)$id);
        } catch(\Exception $e) {
            return array();
        }

        return $movie;
    }

    /**
     * Get a tv show for the given id
     */
    public function getTVShow($id)
    {
        try {
            $tvShow = $this->client->getTVApi()->getTVshow((int)$id);
        } catch (\Exception $e) {
            return array();
        }

        return $tvShow;
    }

    /**
     * Get all movies or tv shows for a given search term
     */
    public function searchMovies($searchTerm = "", $page = 1, $type="movie")
    {
		if(!empty($searchTerm)) {
			if(!$page || $page < 1) {
				$page = 1;
            } elseif ($page > 1000) {
                $page = 1000;
            }

            if($type === 'tv') {
                $results = $this->client->getSearchApi()->searchTV($searchTerm, ["page"=>$page]);

                // Get last page if page number is greater than the total number of pages
                if((int)$page > $results['total_pages']) {
                    $results = $this->client->getSearchApi()->searchTV($searchTerm, ["page"=>$results['total_pages']]);
                }
            } else {
                $results = $this->client->getSearchApi()->searchMovies($searchTerm, ["page"=>$page]);

                // Get last page if page number is greater than the total number of pages
                if((int)$page > $results['total_pages']) {
                    $results = $this->client->getSearchApi()->searchMovies($searchTerm, ["page"=>$results['total_pages']]);
                }
            }

            return $results;
		} else {
			return false;
		}
    }
}
