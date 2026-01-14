<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Home Controller
 * 
 * Landing page for the application.
 */
class Home extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Landing page
     */
    public function index()
    {
        $this->data['title'] = 'FinanceAI - Pencatatan Keuangan Cerdas';
        $this->data['meta_description'] = 'Kelola keuangan Anda dengan mudah menggunakan AI dan Telegram Bot. Catat pemasukan dan pengeluaran hanya dengan chat atau upload foto struk.';

        $this->load->view('home/index', $this->data);
    }
}
