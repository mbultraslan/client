<?php

class Login_Model extends MY_Model {

    public $_table_name;
    protected $_order_by;
    public $_primary_key;
    public $rules = array(
        'user_name' => array(
            'field' => 'user_name',
            'label' => 'User Name',
            'rules' => 'trim|required|xss_clean'
        ),
        'password' => array(
            'field' => 'password',
            'label' => 'Password',
            'rules' => 'trim|required'
        )
    );

    public function login() {
        //check user type
        $this->_table_name = 'tbl_users';
        $this->_order_by = 'user_id';

        $admin = $this->get_by(array(
            'username' => $this->input->post('user_name'),
            'password' => $this->hash($this->input->post('password')),
                ), TRUE);
        if (!empty($admin) && $admin->activated == 1 && $admin->banned == 0) {
            $user_info = $this->check_by(array('user_id' => $admin->user_id), 'tbl_account_details');
            $this->set_action(array('user_id' => $admin->user_id), array('online_status' => '1'), 'tbl_users');

            if ($admin->role_id != '2') {

                $data = array(
                    'user_name' => $admin->username,
                    'email' => $admin->email,
                    'name' => $user_info->fullname,
                    'photo' => $user_info->avatar,
                    'user_id' => $admin->user_id,
                    'last_login' => $admin->last_login,
                    'loggedin' => TRUE,
                    'user_type' => $admin->role_id,
                    'user_flag' => 1,
                    'url' => 'admin/dashboard',
                );
                $this->session->set_userdata($data);
            } else {
                $data = array(
                    'user_name' => $admin->username,
                    'email' => $admin->email,
                    'name' => $user_info->fullname,
                    'photo' => $user_info->avatar,
                    'client_id' => $user_info->company,
                    'user_id' => $admin->user_id,
                    'last_login' => $admin->last_login,
                    'loggedin' => TRUE,
                    'user_type' => $admin->role_id,
                    'user_flag' => 2,
                    'url' => 'client/dashboard',
                );
                $this->session->set_userdata($data);
            }
        }
    }

    public function activate_user($user_id, $activation_key, $activate_by_email = TRUE) {
        $this->purge_na($this->config->item('email_activation_expire', 'login'));
        if ((strlen($user_id) > 0) AND (strlen($activation_key) > 0)) {
            return $this->activated_user($user_id, $activation_key, $activate_by_email);
        }
        return FALSE;
    }

    function purge_na() {
        $expire_period = 172800;
        $this->db->where('activated', 0);
        $this->db->where('UNIX_TIMESTAMP(created) <', time() - $expire_period);
        $this->db->delete('tbl_users');
    }

    function activated_user($user_id, $activation_key, $activate_by_email) {

        $this->db->select('1', FALSE);
        $this->db->where('user_id', $user_id);
        if ($activate_by_email) {
            $this->db->where('new_email_key', $activation_key);
        } else {
            $this->db->where('new_password_key', $activation_key);
        }
        $this->db->where('activated', 0);
        $query = $this->db->get('tbl_users');

        if ($query->num_rows() == 1) {
            $this->db->set('activated', 1);
            $this->db->set('new_email_key', NULL);
            $this->db->where('user_id', $user_id);
            $this->db->update('tbl_users');
            return TRUE;
        }
        return FALSE;
    }

    function get_user_details($login) {
        $this->db->where('LOWER(username)=', strtolower($login));
        $this->db->or_where('LOWER(email)=', strtolower($login));

        $query = $this->db->get('tbl_users');
        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }

    function set_password_key($user_id, $new_pass_key) {
        $this->db->set('new_password_key', $new_pass_key);
        $this->db->set('new_password_requested', date('Y-m-d H:i:s'));
        $this->db->where('user_id', $user_id);
        $this->db->update('tbl_users');
        return $this->db->affected_rows() > 0;
    }

    function get_user_by_id($user_id, $activated) {
        $this->db->where('id', $user_id);
        $this->db->where('activated', $activated ? 1 : 0);

        $query = $this->db->get($this->table_name);
        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }

    function can_reset_password($user_id, $new_pass_key) {
        $expire_period = 900;
        $this->db->select('1', FALSE);
        $this->db->where('user_id', $user_id);
        $this->db->where('new_password_key', $new_pass_key);
        $this->db->where('UNIX_TIMESTAMP(new_password_requested) >', time() - $expire_period);
        $query = $this->db->get('tbl_users');
        return $query->num_rows() == 1;
    }

    function get_reset_password($user_id, $new_pass_key) {
        $expire_period = 900;
        $this->db->set('password', $this->hash('123456'));
        $this->db->set('new_password_key', NULL);
        $this->db->set('new_password_requested', NULL);
        $this->db->where('user_id', $user_id);
        $this->db->where('new_password_key', $new_pass_key);
        $this->db->where('UNIX_TIMESTAMP(new_password_requested) >=', time() - $expire_period);
        $this->db->update('tbl_users');
        return $this->db->affected_rows() > 0;
    }

    function activate_new_email($user_id, $new_email_key) {
        $this->db->set('email', 'new_email', FALSE);
        $this->db->set('new_email', NULL);
        $this->db->set('new_email_key', NULL);
        $this->db->where('user_id', $user_id);
        $this->db->where('new_email_key', $new_email_key);
        $this->db->update('tbl_users');
        return $this->db->affected_rows() > 0;
    }

    public function logout() {
        $this->set_action(array('user_id' => $this->session->userdata('user_id')), array('online_status' => '0', 'last_login' => date('Y-m-d H:i:s')), 'tbl_users');
        $this->session->sess_destroy();
    }

    public function loggedin() {
        return (bool) $this->session->userdata('loggedin');
    }

    public function hash($string) {
        return hash('sha512', $string . config_item('encryption_key'));
    }

}
