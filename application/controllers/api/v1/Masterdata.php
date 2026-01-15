<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * API Masterdata Controller
 * 
 * Handles master data endpoints (categories, etc).
 */
class Masterdata extends API_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Category_model');
    }

    /**
     * Get all categories
     * GET /api/v1/masterdata/categories
     * Requires Bearer token authentication
     */
    public function categories()
    {
        if (!$this->authenticate()) {
            return;
        }

        $type = $this->input->get('type'); // 'income', 'expense', or null for all

        $categories = $this->Category_model->get_all($this->user->id, $type);

        // Group by type for easier consumption
        $grouped = array(
            'expense' => array(),
            'income' => array()
        );

        foreach ($categories as $cat) {
            $grouped[$cat->type][] = array(
                'id' => $cat->id,
                'name' => $cat->name,
                'icon' => $cat->icon
            );
        }

        $this->json_response(array(
            'success' => true,
            'data' => $type ? $categories : $grouped
        ));
    }

    /**
     * Create custom category
     * POST /api/v1/masterdata/categories/create
     * Requires Bearer token authentication
     */
    public function create_category()
    {
        if (!$this->authenticate()) {
            return;
        }

        $input = $this->get_json_input();

        if (empty($input['name']) || empty($input['type'])) {
            $this->json_response(array('error' => 'Nama dan tipe kategori diperlukan'), 400);
            return;
        }

        if (!in_array($input['type'], array('income', 'expense'))) {
            $this->json_response(array('error' => 'Tipe harus income atau expense'), 400);
            return;
        }

        $category_data = array(
            'user_id' => $this->user->id,
            'name' => $input['name'],
            'type' => $input['type'],
            'icon' => isset($input['icon']) ? $input['icon'] : '📌'
        );

        $category_id = $this->Category_model->create($category_data);

        $this->json_response(array(
            'success' => true,
            'message' => 'Kategori berhasil dibuat',
            'category_id' => $category_id
        ), 201);
    }
}
