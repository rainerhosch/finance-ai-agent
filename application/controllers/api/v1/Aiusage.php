<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * API AI Usage Controller
 * 
 * Handles AI usage tracking and limit checking endpoints.
 * Used by Telegram Bot to check limits before processing AI requests.
 */
class Aiusage extends API_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Ai_server_status_model');
    }

    /**
     * Check if AI usage is within limits
     * GET /api/v1/ai/check-limit?server_name=default
     * 
     * Response:
     * {
     *   "success": true,
     *   "allowed": true,
     *   "limits": {
     *     "rpm": { "current": 5, "limit": 5, "remaining": 0, "exceeded": false },
     *     "rpd": { "current": 10, "limit": 20, "remaining": 10, "exceeded": false },
     *     "tpm": { "current": 1000, "limit": 250000, "remaining": 249000, "exceeded": false }
     *   }
     * }
     */
    public function check_limit()
    {
        $server_name = $this->input->get('server_name');
        if (empty($server_name)) {
            $server_name = 'default';
        }

        $result = $this->Ai_server_status_model->check_limit($server_name);

        $this->json_response(array(
            'success' => true,
            'allowed' => $result['allowed'],
            'message' => $result['message'],
            'limits' => $result['limits']
        ));
    }

    /**
     * Update usage counter after AI request
     * POST /api/v1/ai/counter
     * 
     * Body:
     * {
     *   "server_name": "default",
     *   "tokens_used": 150
     * }
     * 
     * Response:
     * {
     *   "success": true,
     *   "message": "Counter updated",
     *   "current_usage": { "rpm": 6, "rpd": 11, "tpm": 1150 }
     * }
     */
    public function counter()
    {
        if ($this->input->method() !== 'post') {
            $this->json_response(array(
                'error' => 'Method not allowed. Use POST.'
            ), 405);
            return;
        }

        $input = $this->get_json_input();

        $server_name = isset($input['server_name']) ? $input['server_name'] : 'default';
        $tokens_used = isset($input['tokens_used']) ? (int) $input['tokens_used'] : 0;

        // First check if limit is already reached
        $limit_check = $this->Ai_server_status_model->check_limit($server_name);

        if (!$limit_check['allowed']) {
            $this->json_response(array(
                'success' => false,
                'message' => $limit_check['message'],
                'limits' => $limit_check['limits']
            ), 429); // Too Many Requests
            return;
        }

        // Increment counter
        $current_usage = $this->Ai_server_status_model->increment_counter($server_name, $tokens_used);

        $this->json_response(array(
            'success' => true,
            'message' => 'Counter updated',
            'current_usage' => $current_usage
        ));
    }

    /**
     * Get current AI usage status
     * GET /api/v1/ai/status?server_name=default
     * 
     * Response:
     * {
     *   "success": true,
     *   "data": {
     *     "server_name": "default",
     *     "usage_date": "2026-01-17",
     *     "request_count": 10,
     *     "request_count_minute": 2,
     *     "token_count_minute": 500,
     *     "rpm_limit": 5,
     *     "rpd_limit": 20,
     *     "tpm_limit": 250000
     *   }
     * }
     */
    public function status()
    {
        $server_name = $this->input->get('server_name');
        if (empty($server_name)) {
            $server_name = 'default';
        }

        $status = $this->Ai_server_status_model->get_status($server_name);

        $this->json_response(array(
            'success' => true,
            'data' => array(
                'server_name' => $status->server_name,
                'usage_date' => $status->usage_date,
                'request_count' => (int) $status->request_count,
                'request_count_minute' => (int) $status->request_count_minute,
                'token_count_minute' => (int) $status->token_count_minute,
                'rpm_limit' => (int) $status->rpm_limit,
                'rpd_limit' => (int) $status->rpd_limit,
                'tpm_limit' => (int) $status->tpm_limit,
                'last_minute_reset' => $status->last_minute_reset,
                'is_active' => (bool) $status->is_active
            )
        ));
    }

    /**
     * Get usage history
     * GET /api/v1/ai/history?server_name=default&days=7
     * 
     * Response:
     * {
     *   "success": true,
     *   "data": [
     *     { "usage_date": "2026-01-17", "request_count": 15 },
     *     { "usage_date": "2026-01-16", "request_count": 20 }
     *   ]
     * }
     */
    public function history()
    {
        $server_name = $this->input->get('server_name');
        if (empty($server_name)) {
            $server_name = 'default';
        }

        $days = $this->input->get('days');
        if (empty($days) || !is_numeric($days)) {
            $days = 7;
        }

        $history = $this->Ai_server_status_model->get_history($server_name, (int) $days);

        $data = array_map(function ($record) {
            return array(
                'usage_date' => $record->usage_date,
                'request_count' => (int) $record->request_count,
                'rpm_limit' => (int) $record->rpm_limit,
                'rpd_limit' => (int) $record->rpd_limit,
                'tpm_limit' => (int) $record->tpm_limit
            );
        }, $history);

        $this->json_response(array(
            'success' => true,
            'data' => $data
        ));
    }
}
