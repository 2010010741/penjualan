<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Login extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
    }

    public function index()
    {
        $this->form_validation->set_rules('email', 'Email', 'required|trim');
        $this->form_validation->set_rules('password', 'Password', 'required|trim');

        $this->load->view('login/index');
    }
    public function dologin()
    {
        // Ambil data dari POST request
        $user = $this->input->post('email');
        $pswd = $this->input->post('password');

        // Cari user berdasarkan email
        $user = $this->db->get_where('user', ['email' => $user])->row_array();

        if ($user) { // Jika user terdaftar
            if (password_verify($pswd, $user['password'])) { // Periksa password-nya
                // Siapkan data sesi
                $data = [
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'username' => $user['username'],
                    'role' => $user['role']
                ];

                // Set data ke session
                $this->session->set_userdata($data);

                // Cek role dan redirect
                if ($user['role'] == 'PEMILIK') {
                    $this->_updateLastLogin($user['id']);
                    redirect('menu');
                } else if ($user['role'] == 'ADMIN') {
                    $this->_updateLastLogin($user['id']);
                    redirect('user');
                } else if ($user['role'] == 'KASIR') {
                    $this->_updateLastLogin($user['id']);
                    redirect('kasir');
                } else {
                    redirect('login');
                }
            } else { // Jika password salah
                $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert"><b>Error:</b> Password Salah.</div>');
                redirect('/');
            }
        } else { // Jika user tidak terdaftar
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert"><b>Error:</b> User Tidak Terdaftar.</div>');
            redirect('/');
        }
    }

    public function logout()
    {
        // Hancurkan semua sesi
        $this->session->sess_destroy();
        redirect(site_url('login'));
    }

    public function block()
    {
        // Siapkan data untuk view
        $data = array(
            'infoLogin' => $this->infoLogin(), // Asumsikan infoLogin() adalah metode yang ada di controller ini
            'title' => 'Access Denied!'
        );

        // Load view dengan data
        $this->load->view('login/error404', $data);
    }



    private function _updateLastLogin($userid)
    {
        $sql = "UPDATE user SET last_login=now() WHERE id=$userid";
        $this->db->query($sql);
    }
}
