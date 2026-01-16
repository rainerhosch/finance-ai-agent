<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * AI Server Status Model
 * 
 * Handles AI usage tracking and limit checking for rate limiting.
 * Tracks RPM (Requests Per Minute), RPD (Requests Per Day), and TPM (Tokens Per Minute).
 */
class Ai_server_status_model extends CI_Model
{
    protected $table = 'ai_server_status';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get or create today's record for a server
     * 
     * @param string $server_name Server identifier
     * @return object The status record
     */
    public function get_or_create_today($server_name = 'default')
    {
        $today = date('Y-m-d');

        // Try to get existing record
        $record = $this->db->where('server_name', $server_name)
            ->where('usage_date', $today)
            ->get($this->table)
            ->row();

        if ($record) {
            // Check if we need to reset minute counters
            $record = $this->check_and_reset_minute($record);
            return $record;
        }

        // Create new record for today
        $data = array(
            'server_name' => $server_name,
            'usage_date' => $today,
            'request_count' => 0,
            'request_count_minute' => 0,
            'token_count_minute' => 0,
            'last_minute_reset' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );

        $this->db->insert($this->table, $data);

        return $this->db->where('server_name', $server_name)
            ->where('usage_date', $today)
            ->get($this->table)
            ->row();
    }

    /**
     * Check and reset minute counters if more than 1 minute has passed
     * 
     * @param object $record The status record
     * @return object Updated record
     */
    private function check_and_reset_minute($record)
    {
        if (empty($record->last_minute_reset)) {
            return $record;
        }

        $last_reset = strtotime($record->last_minute_reset);
        $now = time();

        // If more than 60 seconds have passed, reset minute counters
        if (($now - $last_reset) >= 60) {
            $this->db->where('id', $record->id)
                ->update($this->table, array(
                    'request_count_minute' => 0,
                    'token_count_minute' => 0,
                    'last_minute_reset' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ));

            $record->request_count_minute = 0;
            $record->token_count_minute = 0;
            $record->last_minute_reset = date('Y-m-d H:i:s');
        }

        return $record;
    }

    /**
     * Increment usage counters
     * 
     * @param string $server_name Server identifier
     * @param int $tokens_used Number of tokens used in this request
     * @return array Updated usage counts
     */
    public function increment_counter($server_name = 'default', $tokens_used = 0)
    {
        $record = $this->get_or_create_today($server_name);

        $new_request_count = $record->request_count + 1;
        $new_request_count_minute = $record->request_count_minute + 1;
        $new_token_count_minute = $record->token_count_minute + $tokens_used;

        $this->db->where('id', $record->id)
            ->update($this->table, array(
                'request_count' => $new_request_count,
                'request_count_minute' => $new_request_count_minute,
                'token_count_minute' => $new_token_count_minute,
                'updated_at' => date('Y-m-d H:i:s')
            ));

        return array(
            'rpm' => $new_request_count_minute,
            'rpd' => $new_request_count,
            'tpm' => $new_token_count_minute
        );
    }

    /**
     * Check if usage is within limits
     * 
     * @param string $server_name Server identifier
     * @return array Limit check result with details
     */
    public function check_limit($server_name = 'default')
    {
        $record = $this->get_or_create_today($server_name);

        $rpm_exceeded = $record->request_count_minute >= $record->rpm_limit;
        $rpd_exceeded = $record->request_count >= $record->rpd_limit;
        $tpm_exceeded = $record->token_count_minute >= $record->tpm_limit;

        $allowed = !$rpm_exceeded && !$rpd_exceeded && !$tpm_exceeded;

        $message = null;
        if ($rpd_exceeded) {
            $message = 'Limit harian (RPD) sudah tercapai. Silakan coba lagi besok.';
        } elseif ($rpm_exceeded) {
            $message = 'Limit per menit (RPM) sudah tercapai. Silakan tunggu sebentar.';
        } elseif ($tpm_exceeded) {
            $message = 'Limit token per menit (TPM) sudah tercapai. Silakan tunggu sebentar.';
        }

        return array(
            'allowed' => $allowed,
            'message' => $message,
            'limits' => array(
                'rpm' => array(
                    'current' => (int) $record->request_count_minute,
                    'limit' => (int) $record->rpm_limit,
                    'remaining' => max(0, $record->rpm_limit - $record->request_count_minute),
                    'exceeded' => $rpm_exceeded
                ),
                'rpd' => array(
                    'current' => (int) $record->request_count,
                    'limit' => (int) $record->rpd_limit,
                    'remaining' => max(0, $record->rpd_limit - $record->request_count),
                    'exceeded' => $rpd_exceeded
                ),
                'tpm' => array(
                    'current' => (int) $record->token_count_minute,
                    'limit' => (int) $record->tpm_limit,
                    'remaining' => max(0, $record->tpm_limit - $record->token_count_minute),
                    'exceeded' => $tpm_exceeded
                )
            )
        );
    }

    /**
     * Get current usage status
     * 
     * @param string $server_name Server identifier
     * @return object|null The status record
     */
    public function get_status($server_name = 'default')
    {
        return $this->get_or_create_today($server_name);
    }

    /**
     * Update limit settings
     * 
     * @param string $server_name Server identifier
     * @param array $limits Array with rpm_limit, rpd_limit, tpm_limit
     * @return bool Success status
     */
    public function update_limits($server_name, $limits)
    {
        $record = $this->get_or_create_today($server_name);

        $update_data = array('updated_at' => date('Y-m-d H:i:s'));

        if (isset($limits['rpm_limit'])) {
            $update_data['rpm_limit'] = (int) $limits['rpm_limit'];
        }
        if (isset($limits['rpd_limit'])) {
            $update_data['rpd_limit'] = (int) $limits['rpd_limit'];
        }
        if (isset($limits['tpm_limit'])) {
            $update_data['tpm_limit'] = (int) $limits['tpm_limit'];
        }

        return $this->db->where('id', $record->id)
            ->update($this->table, $update_data);
    }

    /**
     * Get usage history
     * 
     * @param string $server_name Server identifier
     * @param int $days Number of days to retrieve
     * @return array Array of usage records
     */
    public function get_history($server_name = 'default', $days = 7)
    {
        return $this->db->where('server_name', $server_name)
            ->order_by('usage_date', 'DESC')
            ->limit($days)
            ->get($this->table)
            ->result();
    }
}
