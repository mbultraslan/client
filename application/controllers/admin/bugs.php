<?php

/**
 * Description of bugs
 *
 * @author Nayeem
 */
class Bugs extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('bugs_model');
        $this->load->helper('ckeditor');
        $this->data['ckeditor'] = array(
            'id' => 'ck_editor',
            'path' => 'asset/js/ckeditor',
            'config' => array(
                'toolbar' => "Full",
                'width' => "90%",
                'height' => "200px"
            )
        );
    }

    public function index($id = NULL, $opt_id = NULL)
    {

        $data['title'] = lang('all_bugs');
        // get permission user by menu id
        $data['assign_user'] = $this->bugs_model->allowad_user('58');

        $data['all_bugs_info'] = $this->bugs_model->get_permission('tbl_bug');
        if ($id) { // retrive data from db by id
            $data['active'] = 2;
            $can_edit = $this->bugs_model->can_action('tbl_bug', 'edit', array('bug_id' => $id));
            if (!empty($can_edit)) {
                if ($id == 'project') {
                    $data['project_id'] = $opt_id;
                } elseif ($id == 'opportunities') {
                    $data['opportunities_id'] = $opt_id;
                } else {
                    //get all bug information
                    $data['bug_info'] = $this->db->where('bug_id', $id)->get('tbl_bug')->row();
                }
            }
            $data['all_opportunities_info'] = $this->bugs_model->get_permission('tbl_opportunities');
        } else {
            $data['active'] = 1;
        }

        $data['editor'] = $this->data;
        $data['subview'] = $this->load->view('admin/bugs/bugs', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function save_bug($id = NULL)
    {
        $data = $this->bugs_model->array_from_post(array(
            'bug_title',
            'bug_description',
            'priority',
            'reporter',
            'client_visible',
            'bug_status'));

        $result = 0;
        $project_id = $this->input->post('project_id', TRUE);
        if (!empty($project_id)) {
            $data['project_id'] = $project_id;
        } else {
            $data['project_id'] = NULL;
            $result += count(1);
        }
        $opportunities_id = $this->input->post('opportunities_id', TRUE);
        if (!empty($opportunities_id)) {
            $data['opportunities_id'] = $opportunities_id;
        } else {
            $data['opportunities_id'] = NULL;
            $result += count(1);
        }
        if ($result == 2) {
            if (!empty($id)) {
                $bugs_info = $this->db->where('bug_id', $id)->get('tbl_bug')->row();
                $data['project_id'] = $bugs_info->project_id;
                $data['opportunities_id'] = $bugs_info->opportunities_id;
            } else {
                $data['project_id'] = $this->input->post('un_project_id', TRUE);
                $data['opportunities_id'] = $this->input->post('un_opportunities_id', TRUE);
            }
        }

        $permission = $this->input->post('permission', true);
        if (!empty($permission)) {

            if ($permission == 'everyone') {
                $assigned = 'all';
                $assigned_to['assigned_to'] = $this->bugs_model->allowad_user_id('58');
            } else {
                $assigned_to = $this->bugs_model->array_from_post(array('assigned_to'));
                if (!empty($assigned_to['assigned_to'])) {
                    foreach ($assigned_to['assigned_to'] as $assign_user) {
                        $assigned[$assign_user] = $this->input->post('action_' . $assign_user, true);
                    }
                }
            }
            if ($assigned != 'all') {
                $assigned = json_encode($assigned);
            }
            $data['permission'] = $assigned;
        } else {
            set_message('error', lang('assigned_to') . ' Field is required');
            redirect($_SERVER['HTTP_REFERER']);
        }

        //save data into table.
        $this->bugs_model->_table_name = "tbl_bug"; // table name
        $this->bugs_model->_primary_key = "bug_id"; // $id
        $return_id = $this->bugs_model->save($data, $id);
        if (!empty($id)) {
            $msg = lang('update_bug');
            $activity = 'activity_update_bug';
            $id = $id;
            if (!empty($assigned_to['assigned_to'])) {
                // send update
                $this->notify_update_bugs($assigned_to['assigned_to'], $id, TRUE);
            }
        } else {
            $id = $return_id;
            $msg = lang('save_bug');
            $activity = 'activity_new_bug';
            if (!empty($assigned_to['assigned_to'])) {
                $this->notify_bugs($assigned_to['assigned_to'], $id);
                $this->notify_bugs_reported($id);
            }
        }
        save_custom_field(6, $id);
        // save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'bugs',
            'module_field_id' => $id,
            'activity' => $activity,
            'icon' => 'fa-ticket',
            'value1' => $data['bug_title'],
        );
        // Update into tbl_project
        $this->bugs_model->_table_name = "tbl_activities"; //table name
        $this->bugs_model->_primary_key = "activities_id";
        $this->bugs_model->save($activities);

        $type = "success";
        $message = $msg;
        set_message($type, $message);
        redirect($_SERVER['HTTP_REFERER']);
    }

    function notify_update_bugs($users, $bug_id)
    {

        $email_template = $this->bugs_model->check_by(array('email_group' => 'bug_updated'), 'tbl_email_templates');

        $bugs_info = $this->bugs_model->check_by(array('bug_id' => $bug_id), 'tbl_bug');
        $message = $email_template->template_body;

        $subject = $email_template->subject;

        $bug_title = str_replace("{BUG_TITLE}", $bugs_info->bug_title, $message);
        $bug_status = str_replace("{STATUS}", lang($bugs_info->bug_status), $bug_title);

        $assigned_by = str_replace("{MARKED_BY}", ucfirst($this->session->userdata('name')), $bug_status);
        $Link = str_replace("{BUG_URL}", base_url() . 'admin/bugs/view_bug_details/' . $bugs_info->bug_id, $assigned_by);
        $message = str_replace("{SITE_NAME}", config_item('company_name'), $Link);

        $data['message'] = $message;
        $message = $this->load->view('email_template', $data, TRUE);

        $params['subject'] = $subject;
        $params['message'] = $message;
        $params['resourceed_file'] = '';

        foreach ($users as $v_user) {
            $login_info = $this->bugs_model->check_by(array('user_id' => $v_user), 'tbl_users');
            $params['recipient'] = $login_info->email;
            $this->bugs_model->send_email($params);
        }
    }

    function notify_bugs_reported($bug_id)
    {

        $email_template = $this->bugs_model->check_by(array('email_group' => 'bug_reported'), 'tbl_email_templates');
        $bugs_info = $this->bugs_model->check_by(array('bug_id' => $bug_id), 'tbl_bug');

        $message = $email_template->template_body;

        $subject = $email_template->subject;

        $bug_title = str_replace("{BUG_TITLE}", $bugs_info->bug_title, $message);

        $assigned_by = str_replace("{ADDED_BY}", ucfirst($this->session->userdata('name')), $bug_title);
        $Link = str_replace("{BUG_URL}", base_url() . 'admin/bugs/view_bug_details/' . $bugs_info->bug_id, $assigned_by);
        $message = str_replace("{SITE_NAME}", config_item('company_name'), $Link);

        $data['message'] = $message;
        $message = $this->load->view('email_template', $data, TRUE);

        $params['subject'] = $subject;
        $params['message'] = $message;
        $params['resourceed_file'] = '';

        $login_info = $this->bugs_model->check_by(array('user_id' => $bugs_info->reporter), 'tbl_users');
        $params['recipient'] = $login_info->email;
        $this->bugs_model->send_email($params);
    }

    function notify_bugs($users, $bug_id, $update = NULL)
    {
        if (!empty($update)) {
            $email_template = $this->bugs_model->check_by(array('email_group' => 'bugs_updated'), 'tbl_email_templates');
        } else {
            $email_template = $this->bugs_model->check_by(array('email_group' => 'bug_assigned'), 'tbl_email_templates');
        }
        $bugs_info = $this->bugs_model->check_by(array('bug_id' => $bug_id), 'tbl_bug');
        $message = $email_template->template_body;

        $subject = $email_template->subject;

        $bug_title = str_replace("{BUG_TITLE}", $bugs_info->bug_title, $message);

        $assigned_by = str_replace("{ASSIGNED_BY}", ucfirst($this->session->userdata('name')), $bug_title);
        $Link = str_replace("{BUG_URL}", base_url() . 'admin/bugs/view_bug_details/' . $bugs_info->bug_id, $assigned_by);
        $message = str_replace("{SITE_NAME}", config_item('company_name'), $Link);

        $data['message'] = $message;
        $message = $this->load->view('email_template', $data, TRUE);

        $params['subject'] = $subject;
        $params['message'] = $message;
        $params['resourceed_file'] = '';

        foreach ($users as $v_user) {
            $login_info = $this->bugs_model->check_by(array('user_id' => $v_user), 'tbl_users');
            $params['recipient'] = $login_info->email;
            $this->bugs_model->send_email($params);
        }
    }

    public function update_users($id)
    {
        // get all assign_user
        $can_edit = $this->bugs_model->can_action('tbl_bug', 'edit', array('bug_id' => $id));
        if (!empty($can_edit)) {

            $data['assign_user'] = $this->bugs_model->allowad_user('58');

            $data['bugs_info'] = $this->bugs_model->check_by(array('bug_id' => $id), 'tbl_bug');
            $data['modal_subview'] = $this->load->view('admin/bugs/_modal_users', $data, FALSE);
            $this->load->view('admin/_layout_modal', $data);
        } else {
            set_message('error', lang('there_in_no_value'));
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function update_member($id)
    {
        $can_edit = $this->bugs_model->can_action('tbl_bug', 'edit', array('bug_id' => $id));
        if (!empty($can_edit)) {
            $bugs_info = $this->bugs_model->check_by(array('bug_id' => $id), 'tbl_bug');

            $permission = $this->input->post('permission', true);
            if (!empty($permission)) {

                if ($permission == 'everyone') {
                    $assigned = 'all';
                    $assigned_to['assigned_to'] = $this->bugs_model->allowad_user_id('58');
                } else {
                    $assigned_to = $this->bugs_model->array_from_post(array('assigned_to'));
                    if (!empty($assigned_to['assigned_to'])) {
                        foreach ($assigned_to['assigned_to'] as $assign_user) {
                            $assigned[$assign_user] = $this->input->post('action_' . $assign_user, true);
                        }
                    }
                }
                if ($assigned != 'all') {
                    $assigned = json_encode($assigned);
                }
                $data['permission'] = $assigned;
            } else {
                set_message('error', lang('assigned_to') . ' Field is required');
                redirect($_SERVER['HTTP_REFERER']);
            }

            //save data into table.
            $this->bugs_model->_table_name = "tbl_bug"; // table name
            $this->bugs_model->_primary_key = "bug_id"; // $id
            $this->bugs_model->save($data, $id);

            $msg = lang('update_bug');
            $activity = 'activity_update_bug';
            if (!empty($assigned_to['assigned_to'])) {
                $this->notify_update_bugs($assigned_to['assigned_to'], $id);
            }

            // save into activities
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'bugs',
                'module_field_id' => $id,
                'activity' => $activity,
                'icon' => 'fa-ticket',
                'value1' => $bugs_info->bug_title,
            );
            // Update into tbl_project
            $this->bugs_model->_table_name = "tbl_activities"; //table name
            $this->bugs_model->_primary_key = "activities_id";
            $this->bugs_model->save($activities);

            $type = "success";
            $message = $msg;
            set_message($type, $message);
        } else {
            set_message('error', lang('there_in_no_value'));

        }
        redirect($_SERVER['HTTP_REFERER']);

    }

    public function change_status($id, $status)
    {
        $can_edit = $this->bugs_model->can_action('tbl_bug', 'edit', array('bug_id' => $id));
        if (!empty($can_edit)) {
            $bugs_info = $this->bugs_model->check_by(array('bug_id' => $id), 'tbl_bug');

            if (!empty($bugs_info->permission) && $bugs_info->permission != 'all') {
                $user = json_decode($bugs_info->permission);
                foreach ($user as $key => $v_user) {
                    $allowad_user[] = $key;
                }
            } else {
                $allowad_user = $this->bugs_model->allowad_user_id('58');
            }

            if (!empty($allowad_user)) {
                $this->notify_update_bugs($allowad_user, $id, TRUE);
            }

            $data['bug_status'] = $status;

//save data into table.
            $this->bugs_model->_table_name = "tbl_bug"; // table name
            $this->bugs_model->_primary_key = "bug_id"; // $id
            $id = $this->bugs_model->save($data, $id);
// save into activities
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'bugs',
                'module_field_id' => $id,
                'activity' => 'activity_update_bug',
                'icon' => 'fa-ticket',
                'value1' => lang($data['bug_status']),
            );
// Update into tbl_project
            $this->bugs_model->_table_name = "tbl_activities"; //table name
            $this->bugs_model->_primary_key = "activities_id";
            $this->bugs_model->save($activities);

            $type = "success";
            $message = lang('update_bug');
            set_message($type, $message);
        } else {
            set_message('error', lang('there_in_no_value'));

        }
        redirect($_SERVER['HTTP_REFERER']);

    }

    public function save_bugs_notes($id = NULL)
    {

        $data = $this->bugs_model->array_from_post(array('notes'));

//save data into table.
        $this->bugs_model->_table_name = "tbl_bug"; // table name
        $this->bugs_model->_primary_key = "bug_id"; // $id
        $id = $this->bugs_model->save($data, $id);
// save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'bugs',
            'module_field_id' => $id,
            'activity' => 'activity_update_bug',
            'icon' => 'fa-ticket',
            'value1' => $data['bugs_notes'],
        );
// Update into tbl_project
        $this->bugs_model->_table_name = "tbl_activities"; //table name
        $this->bugs_model->_primary_key = "activities_id";
        $this->bugs_model->save($activities);

        $type = "success";
        $message = lang('update_bug');
        set_message($type, $message);
        redirect('admin/bugs/view_bug_details/' . $id . '/' . $data['active'] = 4);
    }

    public function save_comments()
    {

        $data['bug_id'] = $this->input->post('bug_id', TRUE);
        $data['comment'] = $this->input->post('comment', TRUE);
        $data['user_id'] = $this->session->userdata('user_id');

//save data into table.
        $this->bugs_model->_table_name = "tbl_task_comment"; // table name
        $this->bugs_model->_primary_key = "task_comment_id"; // $id
        $comment_id = $this->bugs_model->save($data);
// save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'bugs',
            'module_field_id' => $data['bug_id'],
            'activity' => 'activity_new_bug_comment',
            'icon' => 'fa-ticket',
            'value1' => $data['comment'],
        );
// Update into tbl_project
        $this->bugs_model->_table_name = "tbl_activities"; //table name
        $this->bugs_model->_primary_key = "activities_id";
        $this->bugs_model->save($activities);

// send notification
        $this->notify_comments_bugs($comment_id);

        $type = "success";
        $message = lang('bug_comment_save');
        set_message($type, $message);
        redirect('admin/bugs/view_bug_details/' . $data['bug_id'] . '/' . $data['active'] = 2);
    }

    function notify_comments_bugs($comment_id)
    {
        $email_template = $this->bugs_model->check_by(array('email_group' => 'bug_comments'), 'tbl_email_templates');
        $bugs_comment_info = $this->bugs_model->check_by(array('task_comment_id' => $comment_id), 'tbl_task_comment');

        $bugs_info = $this->bugs_model->check_by(array('bug_id' => $bugs_comment_info->bug_id), 'tbl_bug');
        $message = $email_template->template_body;

        $subject = $email_template->subject;

        $bug_name = str_replace("{BUG_TITLE}", $bugs_info->bug_title, $message);
        $assigned_by = str_replace("{POSTED_BY}", ucfirst($this->session->userdata('name')), $bug_name);
        $Link = str_replace("{COMMENT_URL}", base_url() . 'admin/bugs/view_bug_details/' . $bugs_info->bug_id . '/' . $data['active'] = 2, $assigned_by);
        $comments = str_replace("{COMMENT_MESSAGE}", $bugs_comment_info->comment, $Link);
        $message = str_replace("{SITE_NAME}", config_item('company_name'), $comments);

        $data['message'] = $message;
        $message = $this->load->view('email_template', $data, TRUE);

        $params['subject'] = $subject;
        $params['message'] = $message;
        $params['resourceed_file'] = '';

        if (!empty($bugs_info->permission) && $bugs_info->permission != 'all') {
            $user = json_decode($bugs_info->permission);
            foreach ($user as $key => $v_user) {
                $allowad_user[] = $key;
            }
        } else {
            $allowad_user = $this->bugs_model->allowad_user_id('58');
        }

        foreach ($allowad_user as $v_user) {
            $login_info = $this->bugs_model->check_by(array('user_id' => $v_user), 'tbl_users');
            $params['recipient'] = $login_info->email;
            $this->bugs_model->send_email($params);
        }
    }

    public function delete_bug_comments($bug_id, $task_comment_id)
    {
//save data into table.
        $this->bugs_model->_table_name = "tbl_task_comment"; // table name
        $this->bugs_model->_primary_key = "task_comment_id"; // $id
        $this->bugs_model->delete($task_comment_id);

        $type = "success";
        $message = lang('bug_comment_deleted');
        set_message($type, $message);
        redirect('admin/bugs/view_bug_details/' . $bug_id . '/' . $data['active'] = 2);
    }

    public function delete_bug_files($bug_id, $task_attachment_id)
    {
        $file_info = $this->bugs_model->check_by(array('task_attachment_id' => $task_attachment_id), 'tbl_task_attachment');
// save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'bugs',
            'module_field_id' => $bug_id,
            'activity' => 'activity_bug_attachfile_deleted',
            'icon' => 'fa-ticket',
            'value1' => $file_info->title,
        );
// Update into tbl_project
        $this->bugs_model->_table_name = "tbl_activities"; //table name
        $this->bugs_model->_primary_key = "activities_id";
        $this->bugs_model->save($activities);

//save data into table.
        $this->bugs_model->_table_name = "tbl_task_attachment"; // table name        
        $this->bugs_model->delete_multiple(array('task_attachment_id' => $task_attachment_id));

        $type = "success";
        $message = lang('bug_attachfile_deleted');
        set_message($type, $message);
        redirect('admin/bugs/view_bug_details/' . $bug_id . '/' . $data['active'] = 3);
    }

    public function save_bug_attachment($task_attachment_id = NULL)
    {
        $data = $this->bugs_model->array_from_post(array('title', 'description', 'bug_id'));
        $data['user_id'] = $this->session->userdata('user_id');

// save and update into tbl_files
        $this->bugs_model->_table_name = "tbl_task_attachment"; //table name
        $this->bugs_model->_primary_key = "task_attachment_id";
        if (!empty($task_attachment_id)) {
            $id = $task_attachment_id;
            $this->bugs_model->save($data, $id);
            $msg = lang('bug_file_updated');
        } else {
            $id = $this->bugs_model->save($data);
            $msg = lang('bug_file_added');
        }

        if (!empty($_FILES['bug_files']['name']['0'])) {
            $old_path_info = $this->input->post('uploaded_path');
            if (!empty($old_path_info)) {
                foreach ($old_path_info as $old_path) {
                    unlink($old_path);
                }
            }
            $mul_val = $this->bugs_model->multi_uploadAllType('bug_files');

            foreach ($mul_val as $val) {
                $val == TRUE || redirect('admin/bugs/view_bug_details/3/' . $data['bug_id']);
                $fdata['files'] = $val['path'];
                $fdata['file_name'] = $val['fileName'];
                $fdata['uploaded_path'] = $val['fullPath'];
                $fdata['size'] = $val['size'];
                $fdata['ext'] = $val['ext'];
                $fdata['is_image'] = $val['is_image'];
                $fdata['image_width'] = $val['image_width'];
                $fdata['image_height'] = $val['image_height'];
                $fdata['task_attachment_id'] = $id;
                $this->bugs_model->_table_name = "tbl_task_uploaded_files"; // table name
                $this->bugs_model->_primary_key = "uploaded_files_id"; // $id
                $this->bugs_model->save($fdata);
            }
        }
// save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'bugs',
            'module_field_id' => $data['bug_id'],
            'activity' => 'activity_new_bug_attachment',
            'icon' => 'fa-ticket',
            'value1' => $data['title'],
        );
// Update into tbl_project
        $this->bugs_model->_table_name = "tbl_activities"; //table name
        $this->bugs_model->_primary_key = "activities_id";
        $this->bugs_model->save($activities);
// send notification message
        $this->notify_attchemnt_bugs($id);
// messages for user
        $type = "success";
        $message = $msg;
        set_message($type, $message);
        redirect('admin/bugs/view_bug_details/' . $data['bug_id'] . '/3');
    }

    function notify_attchemnt_bugs($task_attachment_id)
    {
        $email_template = $this->bugs_model->check_by(array('email_group' => 'bug_attachment'), 'tbl_email_templates');
        $bugs_comment_info = $this->bugs_model->check_by(array('task_attachment_id' => $task_attachment_id), 'tbl_task_attachment');

        $bugs_info = $this->bugs_model->check_by(array('bug_id' => $bugs_comment_info->bug_id), 'tbl_bug');

        $message = $email_template->template_body;

        $subject = $email_template->subject;

        $bug_name = str_replace("{BUG_TITLE}", $bugs_info->bug_title, $message);
        $assigned_by = str_replace("{UPLOADED_BY}", ucfirst($this->session->userdata('name')), $bug_name);
        $Link = str_replace("{BUG_URL}", base_url() . 'admin/bugs/view_bug_details/' . $bugs_info->bug_id . '/' . $data['active'] = 3, $assigned_by);
        $message = str_replace("{SITE_NAME}", config_item('company_name'), $Link);

        $data['message'] = $message;
        $message = $this->load->view('email_template', $data, TRUE);

        $params['subject'] = $subject;
        $params['message'] = $message;
        $params['resourceed_file'] = '';
        if (!empty($bugs_info->permission) && $bugs_info->permission != 'all') {
            $user = json_decode($bugs_info->permission);
            foreach ($user as $key => $v_user) {
                $allowad_user[] = $key;
            }
        } else {
            $allowad_user = $this->bugs_model->allowad_user_id('58');
        }

        foreach ($allowad_user as $v_user) {
            $login_info = $this->bugs_model->check_by(array('user_id' => $v_user), 'tbl_users');
            $params['recipient'] = $login_info->email;
            $this->bugs_model->send_email($params);
        }
    }

    public function view_bug_details($id, $active = NULL, $edit = NULL)
    {
        $data['title'] = lang('bug_details');
        $data['page_header'] = lang('bug_management');

        //get all bug information
        $data['bug_details'] = $this->bugs_model->check_by(array('bug_id' => $id), 'tbl_bug');

//        //get all comments info
//        $data['comment_details'] = $this->bugs_model->get_all_comment_info($id);
// get all assign_user
        $this->bugs_model->_table_name = 'tbl_users';
        $this->bugs_model->_order_by = 'user_id';
        $data['assign_user'] = $this->bugs_model->get_by(array('role_id !=' => '2'), FALSE);

        $this->bugs_model->_table_name = "tbl_task_attachment"; //table name
        $this->bugs_model->_order_by = "bug_id";
        $data['files_info'] = $this->bugs_model->get_by(array('bug_id' => $id), FALSE);

        foreach ($data['files_info'] as $key => $v_files) {
            $this->bugs_model->_table_name = "tbl_task_uploaded_files"; //table name
            $this->bugs_model->_order_by = "task_attachment_id";
            $data['project_files_info'][$key] = $this->bugs_model->get_by(array('task_attachment_id' => $v_files->task_attachment_id), FALSE);
        }


        if ($active == 2) {
            $data['active'] = 2;
        } elseif ($active == 3) {
            $data['active'] = 3;
        } elseif ($active == 4) {
            $data['active'] = 4;
        } else {
            $data['active'] = 1;
        }

        $data['subview'] = $this->load->view('admin/bugs/view_bugs', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function download_files($bug_id, $uploaded_files_id)
    {
        $this->load->helper('download');
        $uploaded_files_info = $this->bugs_model->check_by(array('uploaded_files_id' => $uploaded_files_id), 'tbl_task_uploaded_files');

        if ($uploaded_files_info->uploaded_path) {
            $data = file_get_contents($uploaded_files_info->uploaded_path); // Read the file's contents            
            force_download($uploaded_files_info->file_name, $data);
        } else {
            $type = "error";
            $message = lang('operation_failed');
            set_message($type, $message);
            redirect('admin/bugs/view_bug_details/' . $bug_id . '/3');
        }
    }

    public function delete_bug($id)
    {
        $can_delete = $this->bugs_model->can_action('tbl_bug', 'delete', array('bug_id' => $id));
        if (!empty($can_delete)) {
            $bug_info = $this->bugs_model->check_by(array('bug_id' => $id), 'tbl_bug');

// save into activities
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'bugs',
                'module_field_id' => $bug_info->bug_id,
                'activity' => 'activity_bug_deleted',
                'icon' => 'fa-ticket',
                'value1' => $bug_info->bug_title,
            );
// Update into tbl_project
            $this->bugs_model->_table_name = "tbl_activities"; //table name
            $this->bugs_model->_primary_key = "activities_id";
            $this->bugs_model->save($activities);

            $this->bugs_model->_table_name = "tbl_task_attachment"; //table name
            $this->bugs_model->_order_by = "bug_id";
            $files_info = $this->bugs_model->get_by(array('bug_id' => $id), FALSE);

            foreach ($files_info as $v_files) {
                $this->bugs_model->_table_name = "tbl_task_uploaded_files"; //table name
                $this->bugs_model->delete_multiple(array('task_attachment_id' => $v_files->task_attachment_id));
            }
            //delete into table.
            $this->bugs_model->_table_name = "tbl_task_attachment"; // table name
            $this->bugs_model->delete_multiple(array('bug_id' => $id));

            //delete data into table.
            $this->bugs_model->_table_name = "tbl_task_comment"; // table name
            $this->bugs_model->delete_multiple(array('bug_id' => $id));

            $this->bugs_model->_table_name = "tbl_bug"; // table name
            $this->bugs_model->_primary_key = "bug_id"; // $id
            $this->bugs_model->delete($id);

            $type = "success";
            $message = lang('bug_deleted');
            set_message($type, $message);
        } else {
            set_message('error', lang('there_in_no_value'));
        }
        redirect($_SERVER['HTTP_REFERER']);
    }

}
