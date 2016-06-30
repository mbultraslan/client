<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of expense
 *
 * @author NaYeM
 */
class Expense extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('expense_model');
    }

    public function expense_category($action = NULL, $id = NULL) {
        $data['page'] = lang('expense');
        $data['sub_active'] = lang('expense_category');

        if ($action == 'edit_category') {
            $data['active'] = 2;
            if (!empty($id)) {
                $data['expense_category_info'] = $this->expense_model->check_by(array('expense_category_id' => $id), 'tbl_expense_category');
            }
        } else {
            $data['active'] = 1;
        }
        $this->expense_model->_table_name = "tbl_expense_category"; //table name
        $this->expense_model->_order_by = "expense_category_id";
        if ($id) { // retrive data from db by id
            // get all expense_info by id
            $data['expense_category_info'] = $this->expense_model->get_by(array('expense_category_id' => $id), TRUE);

            if (empty($data['expense_category_info'])) {
                $type = "error";
                $message = "No Record Found";
                set_message($type, $message);
                redirect('admin/expense/expense_category'); //redirect page
            }
        }

        $data['all_expense_category_info'] = $this->expense_model->get();

        $data['title'] = lang('expense_category');
        $data['subview'] = $this->load->view('admin/expense/expense_category', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function save_expense_category($id = NULL) {

        $this->expense_model->_table_name = "tbl_expense_category"; //table name        
        $this->expense_model->_primary_key = "expense_category_id";    //id
        // input data
        $data = $this->expense_model->array_from_post(array('expense_category')); //input post
        // dublicacy check 
        if (!empty($id)) {
            $expense_category_id = array('expense_category_id !=' => $id);
        } else {
            $expense_category_id = null;
        }
        // check check_expense_category by where        
        // if not empty show alert message else save data
        $check_expense_category = $this->expense_model->check_update('tbl_expense_category', $where = array('expense_category' => $data['expense_category']), $expense_category_id);

        if (!empty($check_expense_category)) {
            $type = "error";
            $message = lang('expense_category_exist');
            set_message($type, $message);
        } else {
            $this->expense_model->save($data, $id);
            // messages for user
            $type = "success";
            $message = lang('expense_category_added');
            set_message($type, $message);
        }
// Log Activity
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'expense',
            'module_field_id' => $id,
            'activity' => lang('expense_category_added'),
            'icon' => 'fa-laptop',
            'value1' => $data['expense_category']
        );
        $this->expense_model->_table_name = 'tbl_activities';
        $this->expense_model->_primary_key = 'activities_id';
        $this->expense_model->save($activity);

        redirect('admin/expense/expense_category'); //redirect page
    }

    public function delete_expense_category($id) {
        // check into tbl expense
        $where = array('expense_category_id' => $id);
        // check existing expense_category into tbl_application_list
        $check_existing_ctgry = $this->expense_model->check_by($where, 'tbl_expense');
        $check_ctgry = $this->expense_model->check_by($where, 'tbl_expense_category');
        if (!empty($check_existing_ctgry)) { // if not empty do not delete this else delete
            // messages for user
            $type = "error";
            $message = lang('expense_category_used');
            set_message($type, $message);
        } else {
            // Log Activity
            $activity = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'expense',
                'module_field_id' => $id,
                'activity' => lang('delete_expense_category'),
                'icon' => 'fa-laptop',
                'value1' => $check_ctgry->expense_category
            );
            $this->expense_model->_table_name = 'tbl_activities';
            $this->expense_model->_primary_key = 'activities_id';
            $this->expense_model->save($activity);

            $this->expense_model->_table_name = "tbl_expense_category"; //table name        
            $this->expense_model->_primary_key = "expense_category_id";    //id
            $this->expense_model->delete($id);
            $type = "success";
            $message = lang('delete_expense_category');
            set_message($type, $message);
        }


        redirect('admin/expense/expense_category'); //redirect page
    }

    public function add_expense($id = NULL) {
        $data['page'] = lang('expense');
        $data['sub_active'] = lang('expense_report');
        $this->expense_model->_table_name = "tbl_expense"; //table name
        $this->expense_model->_order_by = "expense_id";
        if ($id) { // retrive data from db by id
            // get all expense_info by id
            $data['expense_info'] = $this->expense_model->get_by(array('expense_id' => $id), TRUE);

            if (empty($data['expense_info'])) {
                $type = "error";
                $message = "No Record Found";
                set_message($type, $message);
                redirect('admin/expense/add_expense');
            }
        }
        // get all expense category
        $this->expense_model->_table_name = "tbl_expense_category"; //table name
        $this->expense_model->_order_by = "expense_category_id";
        $data['expense_category'] = $this->expense_model->get();

        // get all assign_user
        $this->expense_model->_table_name = 'tbl_users';
        $this->expense_model->_order_by = 'user_id';
        $data['assign_user'] = $this->expense_model->get_by(array('role_id !=' => '2'), FALSE);


        $data['title'] = lang('new_expense');
        $data['subview'] = $this->load->view('admin/expense/add_expense', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function save_expense($id = NULL) {
        // input data
        $data = $this->expense_model->array_from_post(array('expense_category_id', 'item_name', 'purchase_from', 'purchase_date', 'amount', 'user_id')); //input post  
        //        
        // save into tbl expense and return expense id
        $this->expense_model->_table_name = "tbl_expense"; // table name
        $this->expense_model->_primary_key = "expense_id"; // $id
        $expense_id = $this->expense_model->save($data, $id);
        //upload bill info
        if (!empty($_FILES['bill_copy']['name']['0'])) {

            $old_path = $this->input->post('bill_copy_path');
            if ($old_path) {
                unlink($old_path);
            }
            $mul_val = $this->expense_model->multi_uploadAllType('bill_copy');
            foreach ($mul_val as $val) {
                $val == TRUE || redirect('admin/expense/add_expense');
                $bdata['bill_copy'] = $val['path'];
                $bdata['bill_copy_filename'] = $val['fileName'];
                $bdata['bill_copy_path'] = $val['fullPath'];
                $bdata['expense_id'] = $expense_id;
                $this->expense_model->_table_name = "tbl_expense_bill_copy"; // table name
                $this->expense_model->_primary_key = "expense_bill_copy_id"; // $id
                $this->expense_model->save($bdata, $id);
            }
        }
        if (!empty($id)) {
            $action = lang('activity_expense_update');
        } else {
            $action = lang('activity_expense_added');
        }
        // Log Activity
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'expense',
            'module_field_id' => $id,
            'activity' => $action,
            'icon' => 'fa-laptop',
            'value1' => $data['item_name']
        );
        $this->expense_model->_table_name = 'tbl_activities';
        $this->expense_model->_primary_key = 'activities_id';
        $this->expense_model->save($activity);

        $type = "success";
        $message = lang('expense_added');
        set_message($type, $message);
        redirect('admin/expense/expense_report'); //redirect page
    }

    public function delete_expense($id) {
        // Log Activity
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'expense',
            'module_field_id' => $id,
            'activity' => lang('activity_expense_delete'),
            'icon' => 'fa-laptop',
            'value1' => $data['item_name']
        );
        $this->expense_model->_table_name = 'tbl_activities';
        $this->expense_model->_primary_key = 'activities_id';
        $this->expense_model->save($activity);

        // delete all expense by id
        $this->expense_model->_table_name = "tbl_expense"; // table name
        $this->expense_model->_primary_key = "expense_id"; // $id
        $this->expense_model->delete($id);

        $type = "success";
        $message = lang('expense_delete');
        set_message($type, $message);
        redirect('admin/expense/expense_report'); //redirect page
    }

    public function expense_report() {
        $data['title'] = "Expense Report";
        $data['page'] = lang('expense');
        $data['sub_active'] = lang('expense_report');
        // active check with current month
        $data['current_month'] = date('m');

        if ($this->input->post('year', TRUE)) { // if input year 
            $data['year'] = $this->input->post('year', TRUE);
        } else { // else current year
            $data['year'] = date('Y'); // get current year
        }
        // get all expense list by year and month
        $data['all_expense_list'] = $this->get_expense_list($data['year']);

        $data['subview'] = $this->load->view('admin/expense/expense_report', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function get_expense_list($year, $month = NULL) {// this function is to create get monthy recap report 
        if (!empty($month)) {
            if ($month >= 1 && $month <= 9) { // if i<=9 concate with Mysql.becuase on Mysql query fast in two digit like 01.
                $start_date = $year . "-" . '0' . $month . '-' . '01';
                $end_date = $year . "-" . '0' . $month . '-' . '31';
            } else {
                $start_date = $year . "-" . $month . '-' . '01';
                $end_date = $year . "-" . $month . '-' . '31';
            }
            $get_expense_list = $this->expense_model->get_expense_list_by_date($start_date, $end_date); // get all report by start date and in date 
        } else {
            for ($i = 1; $i <= 12; $i++) { // query for months
                if ($i >= 1 && $i <= 9) { // if i<=9 concate with Mysql.becuase on Mysql query fast in two digit like 01.
                    $start_date = $year . "-" . '0' . $i . '-' . '01';
                    $end_date = $year . "-" . '0' . $i . '-' . '31';
                } else {
                    $start_date = $year . "-" . $i . '-' . '01';
                    $end_date = $year . "-" . $i . '-' . '31';
                }
                $get_expense_list[$i] = $this->expense_model->get_expense_list_by_date($start_date, $end_date); // get all report by start date and in date 
            }
        }
        return $get_expense_list; // return the result
    }

    public function expense_details($id) {
        $data['title'] = lang('expense_report');        
        $data['page'] = lang('expense');
        $data['sub_active'] = lang('expense_report');
        $this->expense_model->_table_name = "tbl_expense"; //table name
        $this->expense_model->_order_by = "expense_id";
        // get all expense_info by id
        $data['expense_info'] = $this->expense_model->get_expense_info_by_id($id, TRUE);

        $data['bill_info'] = $this->expense_model->get_expense_info_by_id($id);

        $data['subview'] = $this->load->view('admin/expense/expense_details', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function report_pdf($year, $month) {
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'expense',
            'module_field_id' => $year,
            'activity' => lang('activity_expense_pdf'),
            'icon' => 'fa-laptop',
            'value1' => $year,
            'value2' => $month
        );
        $this->expense_model->_table_name = 'tbl_activities';
        $this->expense_model->_primary_key = 'activities_id';
        $this->expense_model->save($activity);

        $data['expense_list'] = $this->get_expense_list($year, $month);
        $month_name = date('F', strtotime($year . '-' . $month)); // get full name of month by date query                
        $data['monthyaer'] = $month_name . '  ' . $year;
        $this->load->helper('dompdf');
        $viewfile = $this->load->view('admin/expense/expense_report_pdf', $data, TRUE);
        pdf_create($viewfile, 'Expense Report  - ' . $data['monthyaer']);
    }

}
