<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Cronjob extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('cron_model');
        $this->load->model('invoice_model');
        $this->load->model('estimates_model');
        $this->load->helper('string');
        $this->load->library('email');
    }

    function index()
    {

        if (config_item('active_cronjob') == "on" && time() > (config_item('last_cronjob_run') + 300)) {

            $input_data['last_cronjob_run'] = time();
            foreach ($input_data as $key => $value) {
                $data = array('value' => $value);
                $this->db->where('config_key', $key)->update('tbl_config', $data);
                $exists = $this->db->where('config_key', $key)->get('tbl_config');
                if ($exists->num_rows() == 0) {
                    $this->db->insert('tbl_config', array("config_key" => $key, "value" => $value));
                }
            }
            // send overdue invoice email
            $this->invoices_cron();
            //send recurring_invoice email
            $this->recurring_invoice();
            // send expire_estimate email
            $this->expire_estimate();
            // send projects_cron email
            $this->projects_cron();
            // send goal_tracking_cron email
            $this->goal_tracking_cron();

        }


    }

    function goal_tracking_cron()
    {
        $mdate = date('Y-m-d');
        $all_goal_tracking = $this->cron_model->get_permission('tbl_goal_tracking');

        if (!empty($all_goal_tracking)) {
            foreach ($all_goal_tracking as $v_goal_track) {
                $goal_achieve = $this->cron_model->get_progress($v_goal_track);

                if ($v_goal_track->end_date <= $mdate) { // check today is last date or not


                    if ($v_goal_track->email_send == 'no') {// check mail are send or not

                        if ($v_goal_track->achievement <= $goal_achieve['achievement']) {
                            if ($v_goal_track->notify_goal_achive == 'on') {// check is notify is checked or not check

                                $this->cron_model->send_goal_mail('goal_achieve', $v_goal_track);
                            }
                        } else {
                            if ($v_goal_track->notify_goal_not_achive == 'on') {// check is notify is checked or not check
                                $this->cron_model->send_goal_mail('goal_not_achieve', $v_goal_track);
                            }
                        }
                    }
                }
            }
        }
    }


    function expire_estimate()
    {
        $expire_estimate = $this->cron_model->get_overdue('tbl_estimates');

        if (!empty($expire_estimate)) {
            foreach ($expire_estimate as $v_estimate) {
                $currencies = $this->estimates_model->client_currency_sambol($v_estimate->client_id);

                $email_template = $this->cron_model->check_by(array('email_group' => 'estimate_email'), 'tbl_email_templates');

                $message = $email_template->template_body;

                $subject = '[' . $this->config->item('company_name') . '] Your Estimate is Overdue';

                $client_name = str_replace("{CLIENT}", $v_estimate->name, $message);
                $Ref = str_replace("{ESTIMATE_REF}", $v_estimate->reference_no, $client_name);
                $Amount = str_replace("{AMOUNT}", $this->estimates_model->estimate_calculation('estimate_amount', $v_estimate->estimates_id), $Ref);
                $Currency = str_replace("{CURRENCY}", $currencies->symbol, $Amount);

                $link = str_replace("{ESTIMATE_LINK}", base_url() . 'admin/estimates/index/estimates_details/' . $v_estimate->estimates_id, $Currency);
                $message = str_replace("{SITE_NAME}", config_item('company_name'), $link);


                $data['message'] = $message;
                $message = $this->load->view('email_template', $data, TRUE);
                $params = array(
                    'recipient' => $v_estimate->email,
                    'subject' => $subject,
                    'message' => $message
                );

                $params['resourceed_file'] = '';
                $this->invoice_model->send_email($params);

                $data = array('emailed' => 'Yes', 'date_sent' => date("Y-m-d H:i:s", time()));

                $this->estimates_model->_table_name = 'tbl_estimates';
                $this->estimates_model->_primary_key = 'estimates_id';
                $this->estimates_model->save($data, $v_estimate->estimates_id);
            }
            return TRUE;
        } else {
            log_message('error', 'There are no overdue invoices to send emails');
            return TRUE;
        }

    }

    function invoices_cron()
    {
        $overdue_invoices = $this->cron_model->get_overdue('tbl_invoices');

        if (!empty($overdue_invoices)) {
            foreach ($overdue_invoices as $invoice_info) {

                $email_template = $this->cron_model->check_by(array('email_group' => 'invoice_overdue_email'), 'tbl_email_templates');

                $message = $email_template->template_body;

                $subject = $email_template->subject;

                $client_name = str_replace("{CLIENT}", $invoice_info->name, $message);
                $Ref = str_replace("{REF}", $invoice_info->reference_no, $client_name);
                $Amount = str_replace("{AMOUNT}", $this->invoice_model->calculate_to('invoice_due', $invoice_info->invoices_id), $Ref);
                $Currency = str_replace("{CURRENCY}", $invoice_info->currency, $Amount);
                $Due_date = str_replace("{DUE_DATE}", $invoice_info->due_date, $Currency);
                if (!empty($Due_date)) {
                    $Due_date = $Due_date;
                } else {
                    $Due_date = $Currency;
                }
                $link = str_replace("{INVOICE_LINK}", base_url() . 'client/invoice/manage_invoice/invoice_details/' . $invoice_info->invoices_id, $Due_date);
                $message = str_replace("{SITE_NAME}", config_item('company_name'), $link);


                $data['message'] = $message;
                $message = $this->load->view('email_template', $data, TRUE);
                $params = array(
                    'recipient' => $invoice_info->email,
                    'subject' => $subject,
                    'message' => $message
                );
                $params['resourceed_file'] = '';
                $this->invoice_model->send_email($params);
            }
            return TRUE;
        } else {
            log_message('error', 'There are no overdue invoices to send emails');
            return TRUE;
        }

    }

    public function recurring_invoice()
    {
        // Gather a list of recurring invoices to generate
        $invoices_recurring = $this->cron_model->get_recurring_invoice();


        if (!empty($invoices_recurring)) {
            foreach ($invoices_recurring as $v_r_invoice) {

                // Create the new invoice
                $invoice_date = array(
                    'client_id' => $v_r_invoice->client_id,
                    'due_date' => $this->cron_model->get_date_due($v_r_invoice->recur_next_date),
                    'reference_no' => config_item('invoice_prefix') . ' ' . $this->invoice_model->generate_invoice_number(),
                    'discount' => $v_r_invoice->discount,
                    'tax' => $v_r_invoice->tax,
                    'currency' => $v_r_invoice->currency,
                    'notes' => $v_r_invoice->notes
                );
                $this->invoice_model->_table_name = 'tbl_invoices';
                $this->invoice_model->_primary_key = 'invoices_id';
                $return_id = $this->invoice_model->save($invoice_date);

                // Copy the original invoice to the new invoice
                $this->cron_model->copy_invoice_items($v_r_invoice->invoices_id, $return_id);

                // Update the next recur date for the recurring invoice
                $this->cron_model->set_next_recur_date($v_r_invoice->invoices_id);

                // Email the new invoice if applicable
                if (config_item('send_email_when_recur') == 'TRUE') {

                    $new_invoice = $this->db->where('invoices_id', $return_id)->get('tbl_invoices')->row();
                    $client_info = $this->db->where('client_id', $new_invoice->client_id)->get('tbl_client')->row();

                    $email_template = $this->cron_model->check_by(array('email_group' => 'invoice_message'), 'tbl_email_templates');

                    $message = $email_template->template_body;

                    $subject = $email_template->subject;

                    $ClientName = str_replace("{CLIENT}", $client_info->name, $message);
                    $Amount = str_replace("{AMOUNT}", $this->invoice_model->calculate_to('invoice_due', $new_invoice->invoices_id), $ClientName);
                    $Currency = str_replace("{CURRENCY}", $new_invoice->currency, $Amount);
                    $link = str_replace("{INVOICE_LINK}", base_url() . 'invoices/view/' . $new_invoice->invoices_id, $Currency);
                    $message = str_replace("{SITE_NAME}", config_item('company_name'), $link);

                    $this->send_email_invoice($new_invoice->invoices_id, $message, $subject); // Email Invoice

                    $data = array('emailed' => 'Yes', 'date_sent' => date("Y-m-d H:i:s", time()));

                    $this->db->where('invoices_id', $new_invoice->invoices_id)->update('tbl_invoices', $data);

                }
            }
        }
    }

    function send_email_invoice($invoice_id, $message, $subject)
    {
        $invoice_info = $this->invoice_model->check_by(array('invoices_id' => $invoice_id), 'tbl_invoices');
        $client_info = $this->invoice_model->check_by(array('client_id' => $invoice_info->client_id), 'tbl_client');

        $recipient = $client_info->email;

        $data['message'] = $message;

        $message = $this->load->view('email_template', $data, TRUE);
        $params = array(
            'recipient' => $recipient,
            'subject' => $subject,
            'message' => $message
        );
        $params['resourceed_file'] = '';
        $this->invoice_model->send_email($params);

    }

    function projects_cron()
    {
        $overdue_project = $this->cron_model->get_overdue('tbl_project');

        if ($overdue_project) {
            foreach ($overdue_project as $v_project) {

                $body = "
                                    Hello " . $v_project->name . ",<br><br>

                                    One of your Project is Overdue.<br><br>
                                    <strong>Project Name: " . $v_project->project_name . "</strong>.<br><br>
                                    <strong>Due Date: " . strftime(config_item('date_format'), strtotime($v_project->end_date)) . "</strong>.<br><br>

                                    To view the project, click on the link below.<br><br>

                                    <a href=\" " . base_url() . "client/project\">View Project</a> <br><br>

                                    Note: Do not reply to this email as this email is not monitored.<br><br>
                                    Best Regards,<br />
<br />
The " . $this->config->item('company_name') . " Team</p>

<p>&nbsp;</p>";

                $message = $body;

                $subject = '[' . $this->config->item('company_name') . '] Your Project is Overdue';

                $data['message'] = $message;
                $message = $this->load->view('email_template', $data, TRUE);

                $params['subject'] = $subject;
                $params['message'] = $message;
                $params['resourceed_file'] = '';

                $params['recipient'] = $v_project->email;
                $this->cron_model->send_email($params);
            }
            return TRUE;
        } else {
            log_message('error', 'There are no overdue projects to send emails');
            return TRUE;
        }
    }


}

