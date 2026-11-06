<?php

namespace App\Controllers;

class Home extends BaseController
{
	public function index()
	{
		return view('welcome_message');
	}

	public function test()
	{
		return view('home');
	}

	public function privacy()
	{
		return view('privacy');
	}
}
