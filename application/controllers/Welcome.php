<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Welcome extends CI_Controller
{
	public function index()
	{
		$this->load->view('welcome_message');
	}

	public function about()
	{
		//fungsi untuk meload view about.php
		$this->load->view('about');
	}

	public function profile()
	{
		//fungsi untuk meload view profile.php
		$this->load->view('profile');
	}
}
