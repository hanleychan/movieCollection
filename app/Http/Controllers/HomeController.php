<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class HomeController extends Controller
{
	/**
	 * Redirect user to the correct page based on whether they are logged in
	 */
	public function index()
	{
		if(\Auth::user()) {
			return redirect('/myCollection');
		} else {
			return redirect('/login');
		}
	}
}
