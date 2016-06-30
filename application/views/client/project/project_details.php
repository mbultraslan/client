<?php echo message_box('success'); ?>
<?php echo message_box('error'); ?>

<style>
    .note-editor .note-editable {
        height: 150px;
    }
</style>
<?php
$can_edit = $this->items_model->can_action('tbl_project', 'edit', array('project_id' => $project_details->project_id));

$comment_details = $this->db->where('project_id', $project_details->project_id)->get('tbl_task_comment')->result();

$where = array('project_id' => $project_details->project_id, 'client_visible' => 'Yes');
$all_milestones_info = $this->db->where($where)->get('tbl_milestones')->result();

$all_task_info = $this->db->where($where)->order_by('task_id', 'DESC')->get('tbl_task')->result();
$all_bugs_info = $this->db->where($where)->order_by('bug_id', 'DESC')->get('tbl_bug')->result();
$total_timer = $this->db->where(array('project_id' => $project_details->project_id))->get('tbl_tasks_timer')->result();
$activities_info = $this->db->where(array('module' => 'project', 'module_field_id' => $project_details->project_id))->order_by('activity_date', 'desc')->get('tbl_activities')->result();
?>
<div class="row">
    <div class="col-sm-3">

        <ul class="nav nav-pills nav-stacked navbar-custom-nav">
            <li class="btn-success" style="margin-right: 0px; "></li>
            <li class="<?= $active == 1 ? 'active' : '' ?>" style="margin-right: 0px; "><a href="#task_details"
                                                                                           data-toggle="tab"><?= lang('project_details') ?></a>
            </li>

            <li class="<?= $active == 3 ? 'active' : '' ?>"><a href="#task_comments"
                                                               data-toggle="tab"><?= lang('comments') ?><strong
                        class="pull-right"><?= (!empty($comment_details) ? count($comment_details) : null) ?></strong></a>
            </li>
            <li class="<?= $active == 4 ? 'active' : '' ?>"><a href="#task_attachments"
                                                               data-toggle="tab"><?= lang('attachment') ?><strong
                        class="pull-right"><?= (!empty($project_files_info) ? count($project_files_info) : null) ?></strong></a>
            </li>
            <?php if (!empty($all_milestones_info)) { ?>
                <li class="<?= $active == 5 ? 'active' : '' ?>"><a href="#milestones"
                                                                   data-toggle="tab"><?= lang('milestones') ?><strong
                            class="pull-right"><?= count($all_milestones_info) ?></strong></a>
                </li>
            <?php } ?>
            <?php if (!empty($all_task_info)) { ?>
                <li class="<?= $active == 6 ? 'active' : '' ?>"><a href="#task" data-toggle="tab"><?= lang('tasks') ?>
                        <strong
                            class="pull-right"><?= (!empty($all_task_info) ? count($all_task_info) : null) ?></strong></a>
                </li>
            <?php } ?>
            <?php if (!empty($all_bugs_info)) { ?>
                <li class="<?= $active == 9 ? 'active' : '' ?>"><a href="#bugs" data-toggle="tab"><?= lang('bugs') ?>
                        <strong
                            class="pull-right"><?= (!empty($all_bugs_info) ? count($all_bugs_info) : null) ?></strong></a>
                </li>
            <?php } ?>

            <li class="<?= $active == 2 ? 'active' : '' ?>" style="margin-right: 0px; "><a href="#activities"
                                                                                           data-toggle="tab"><?= lang('activities') ?>
                    <strong
                        class="pull-right"><?= (!empty($activities_info) ? count($activities_info) : null) ?></strong></a>
            </li>
        </ul>
    </div>
    <div class="col-sm-9">
        <!-- Tabs within a box -->
        <div class="tab-content" style="border: 0;padding:0;">
            <!-- Task Details tab Starts -->
            <div class="tab-pane <?= $active == 1 ? 'active' : '' ?>" id="task_details" style="position: relative;">
                <div class="panel panel-custom">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?php if (!empty($project_details->project_name)) echo $project_details->project_name; ?>
                            <span class="btn-xs pull-right">
                                <?php if (!empty($can_edit)) { ?>
                                    <a href="<?= base_url() ?>client/project/index/<?= $project_details->project_id ?>"><?= lang('edit') . ' ' . lang('project') ?></a>
                                <?php } ?>
                    </span>
                        </h3>
                    </div>
                    <div class="panel-body row form-horizontal task_details">

                        <div class="form-group col-sm-12">
                            <label class="control-label col-sm-2"><strong><?= lang('project_name') ?> :</strong>
                            </label>
                            <div class="col-sm-10">
                                <p class="form-control-static">
                                    <?php
                                    if (!empty($project_details->project_name)) {
                                        echo $project_details->project_name;
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-6">
                                <label class="control-label col-sm-4"><strong><?= lang('start_date') ?> :</strong>
                                </label>
                                <p class="form-control-static">
                                    <?= strftime(config_item('date_format'), strtotime($project_details->start_date)) ?>
                                </p>
                            </div>
                            <div class="col-sm-6">
                                <label class="control-label col-sm-4"><strong><?= lang('end_date') ?> :</strong>
                                </label>
                                <p class="form-control-static">
                                    <?= strftime(config_item('date_format'), strtotime($project_details->end_date)) ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-6">
                                <label class="control-label col-sm-4"><strong><?= lang('demo_url') ?> :</strong>
                                </label>
                                <p class="form-control-static">
                                    <?php
                                    if (!empty($project_details->demo_url)) {
                                        ?>
                                        <a href="//<?php echo $project_details->demo_url; ?>"
                                           target="_blank"><?php echo $project_details->demo_url ?></a>
                                        <?php
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </p>
                            </div>
                            <div class="col-sm-6">
                                <label class="control-label col-sm-4"><strong><?= lang('project_cost') ?> :</strong>
                                </label>
                                <p class="form-control-static">
                                    <?php
                                    if (!empty($project_details->client_id)) {
                                        $currency = $this->items_model->client_currency_sambol($project_details->client_id);
                                    } else {
                                        $currency = $this->db->where('code', config_item('default_currency'))->get('tbl_currencies')->row();
                                    }
                                    ?>
                                    <strong><?= display_money($project_details->project_cost, $currency->symbol); ?>
                                    </strong>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-6">
                                <label class="control-label col-sm-4"><strong><?= lang('status') ?>
                                        :</strong></label>
                                <div class="pull-left">
                                    <?php
                                    if (!empty($project_details->project_status)) {
                                        if ($project_details->project_status == 'completed') {
                                            $status = "<span class='label label-success'>" . lang($project_details->project_status) . "</span>";
                                        } elseif ($project_details->project_status == 'in_progress') {
                                            $status = "<span class='label label-primary'>" . lang($project_details->project_status) . "</span>";
                                        } elseif ($project_details->project_status == 'cancel') {
                                            $status = "<span class='label label-danger'>" . lang($project_details->project_status) . "</span>";
                                        } else {
                                            $status = "<span class='label label-warning'>" . lang($project_details->project_status) . "</span>";
                                        } ?>
                                        <p class="form-control-static"><?= $status; ?></p>
                                    <?php }
                                    ?>
                                </div>

                            </div>
                            <div class="col-sm-6">
                                <label class="control-label col-sm-4"><strong><?= lang('participants') ?>
                                        :</strong></label>
                                <div class="col-sm-8 ">
                                    <?php
                                    if ($project_details->permission != 'all') {
                                        $get_permission = json_decode($project_details->permission);
                                        if (!empty($get_permission)) :
                                            foreach ($get_permission as $permission => $v_permission) :
                                                $user_info = $this->db->where(array('user_id' => $permission))->get('tbl_users')->row();
                                                if ($user_info->role_id == 1) {
                                                    $label = 'circle-danger';
                                                } else {
                                                    $label = 'circle-success';
                                                }
                                                $profile_info = $this->db->where(array('user_id' => $permission))->get('tbl_account_details')->row();
                                                ?>


                                                <a href="#" data-toggle="tooltip" data-placement="top"
                                                   title="<?= $profile_info->fullname ?>"><img
                                                        src="<?= base_url() . $profile_info->avatar ?>"
                                                        class="img-circle img-xs" alt="">
                                                <span style="margin: 0px 0 8px -10px;"
                                                      class="circle <?= $label ?>  circle-lg"></span>
                                                </a>
                                                <?php
                                            endforeach;
                                        endif;
                                    } else { ?>
                                    <p class="form-control-static"><strong><?= lang('everyone') ?></strong>
                                        <i
                                            title="<?= lang('permission_for_all') ?>"
                                            class="fa fa-question-circle" data-toggle="tooltip"
                                            data-placement="top"></i>

                                        <?php
                                        }
                                        ?>

                                </div>
                            </div>
                        </div>

                        <div class="form-group  col-sm-12 mt">
                            <label class="control-label col-sm-2 "><strong class="mr-sm"><?= lang('completed') ?>
                                    :</strong></label>
                            <div class="col-sm-8 " style="margin-left: -5px;">
                                <?php
                                if ($project_details->progress < 49) {
                                    $progress = 'progress-bar-danger';
                                } elseif ($project_details->progress > 50 && $project_details->progress < 99) {
                                    $progress = 'progress-bar-primary';
                                } else {
                                    $progress = 'progress-bar-success';
                                }
                                ?>
                                <span class="">
                                <div class="mt progress progress-striped ">
                                    <div class="progress-bar <?= $progress ?> " data-toggle="tooltip"
                                         data-original-title="<?= $project_details->progress ?>%"
                                         style="width: <?= $project_details->progress ?>%"></div>
                                </div>
                                </span>
                            </div>
                        </div>
                        <div class="form-group col-sm-12">

                            <?= $this->items_model->get_time_spent_result($this->items_model->task_spent_time_by_id($project_details->project_id, true)) ?>


                        </div>

                        <div class="form-group col-sm-12">
                            <div class="col-sm-12">
                                <blockquote style="font-size: 12px; height: 100px;"><?php
                                    if (!empty($project_details->description)) {
                                        echo $project_details->description;
                                    }
                                    ?></blockquote>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
            <!-- Task Details tab Ends -->

            <!-- Task Comments Panel Starts --->
            <div class="tab-pane <?= $active == 2 ? 'active' : '' ?>" id="activities" style="position: relative;">
                <div class="panel panel-custom">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?= lang('activities') ?>

                        </h3>
                    </div>
                    <div class="panel-body " id="chat-box">
                        <div id="activity">
                            <ul class="list-group no-radius   m-t-n-xxs list-group-lg no-border">
                                <?php

                                if (!empty($activities_info)) {
                                    foreach ($activities_info as $v_activities) {
                                        $profile_info = $this->db->where(array('user_id' => $v_activities->user))->get('tbl_account_details')->row();

                                        $user_info = $this->db->where(array('user_id' => $v_activities->user))->get('tbl_users')->row();
                                        ?>
                                        <li class="list-group-item">
                                            <a class="recect_task pull-left mr-sm">

                                                <?php if (!empty($profile_info)) {
                                                    ?>
                                                    <img style="width: 30px;margin-left: 18px;
                                                             height: 29px;
                                                             border: 1px solid #aaa;"
                                                         src="<?= base_url() . $profile_info->avatar ?>"
                                                         class="img-circle">
                                                <?php } ?>
                                            </a>


                                            <a class="clear">
                                                <small
                                                    class="pull-right"><?= strftime(config_item('date_format') . " %H:%M:%S", strtotime($v_activities->activity_date)) ?></small>
                                                <strong class="block"><?= ucfirst($user_info->username) ?></strong>
                                                <small>
                                                    <?php
                                                    echo sprintf(lang($v_activities->activity) . ' <strong style="color:#000"><em>' . $v_activities->value1 . '</em>' . '<em>' . $v_activities->value2 . '</em></strong>');
                                                    ?>
                                                </small>
                                            </a>
                                        </li>
                                        <?php
                                    }
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane <?= $active == 3 ? 'active' : '' ?>" id="task_comments" style="position: relative;">
                <div class="panel panel-custom">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?= lang('comments') ?></h3>
                    </div>
                    <div class="panel-body chat" id="chat-box">

                        <form id="form_validation" action="<?php echo base_url() ?>client/project/save_comments"
                              method="post" class="form-horizontal">
                            <input type="hidden" name="project_id" value="<?php
                            if (!empty($project_details->project_id)) {
                                echo $project_details->project_id;
                            }
                            ?>" class="form-control">
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <textarea class="form-control textarea"
                                              placeholder="<?= $project_details->project_name . ' ' . lang('comments') ?>"
                                              name="comment" style="height: 70px;"></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <div class="pull-right">
                                        <button type="submit" id="sbtn"
                                                class="btn btn-primary"><?= lang('post_comment') ?></button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <hr/>

                        <?php
                        if (!empty($comment_details)):foreach ($comment_details as $key => $v_comment):
                            $user_info = $this->db->where(array('user_id' => $v_comment->user_id))->get('tbl_users')->row();
                            $profile_info = $this->db->where(array('user_id' => $v_comment->user_id))->get('tbl_account_details')->row();
                            if ($user_info->role_id == 1) {
                                $label = '<small style="font-size:10px;padding:2px;" class="label label-danger ">Admin</small>';
                            } else {
                                $label = '<small style="font-size:10px;padding:2px;" class="label label-primary">Staff</small>';
                            }
                            ?>

                            <div class="col-sm-12 item ">

                                <img src="<?php echo base_url() . $profile_info->avatar ?>" alt="user image"
                                     class="img-circle"/>


                                <p class="message">
                                    <?php
                                    $today = time();
                                    $comment_time = strtotime($v_comment->comment_datetime);
                                    ?>
                                    <small class="text-muted pull-right"><i
                                            class="fa fa-clock-o"></i> <?= $this->items_model->get_time_different($today, $comment_time) ?> <?= lang('ago') ?>
                                        <?php if ($v_comment->user_id == $this->session->userdata('user_id')) { ?>
                                            <?= btn_delete('client/project/delete_comments/' . $v_comment->project_id . '/' . $v_comment->task_comment_id) ?>
                                        <?php } ?></small>
                                    <a href="#" class="name">
                                        <?= ($profile_info->fullname) . ' ' . $label ?>
                                    </a>

                                    <?php if (!empty($v_comment->comment)) echo $v_comment->comment; ?>
                                </p>

                            </div><!-- /.item -->
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <!-- Task Comments Panel Ends--->

            <!-- Task Attachment Panel Starts --->
            <div class="tab-pane <?= $active == 4 ? 'active' : '' ?>" id="task_attachments" style="position: relative;">
                <div class="panel panel-custom">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?= lang('attachment') ?></h3>
                    </div>
                    <div class="panel-body">

                        <form action="<?= base_url() ?>client/project/save_attachment/<?php
                        if (!empty($add_files_info)) {
                            echo $add_files_info->task_attachment_id;
                        }
                        ?>" enctype="multipart/form-data" method="post" id="form" class="form-horizontal">
                            <div class="form-group">
                                <label class="col-lg-3 control-label"><?= lang('file_title') ?> <span
                                        class="text-danger">*</span></label>
                                <div class="col-lg-6">
                                    <input name="title" class="form-control" value="<?php
                                    if (!empty($add_files_info)) {
                                        echo $add_files_info->title;
                                    }
                                    ?>" required placeholder="<?= lang('file_title') ?>"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-3 control-label"><?= lang('description') ?></label>
                                <div class="col-lg-6">
                                        <textarea name="description" class="form-control"
                                                  placeholder="<?= lang('description') ?>"><?php
                                            if (!empty($add_files_info)) {
                                                echo $add_files_info->description;
                                            }
                                            ?></textarea>
                                </div>
                            </div>
                            <?php if (empty($add_files_info)) { ?>
                                <div id="add_new">
                                    <div class="form-group" style="margin-bottom: 0px">
                                        <label for="field-1"
                                               class="col-sm-3 control-label"><?= lang('upload_file') ?></label>
                                        <div class="col-sm-6">
                                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                                <?php if (!empty($project_files)):foreach ($project_files as $v_files_image): ?>
                                                    <span class=" btn btn-default btn-file"><span class="fileinput-new"
                                                                                                  style="display: none">Select file</span>
                                                                <span class="fileinput-exists"
                                                                      style="display: block"><?= lang('change') ?></span>
                                                                <input type="hidden" name="task_files[]"
                                                                       value="<?php echo $v_files_image->files ?>">
                                                                <input type="file" name="task_files[]">
                                                            </span>
                                                    <span
                                                        class="fileinput-filename"> <?php echo $v_files_image->file_name ?></span>
                                                <?php endforeach; ?>
                                                <?php else: ?>
                                                    <span class="btn btn-default btn-file"><span
                                                            class="fileinput-new"><?= lang('select_file') ?></span>
                                                            <span class="fileinput-exists"><?= lang('change') ?></span>
                                                            <input type="file" name="task_files[]">
                                                        </span>
                                                    <span class="fileinput-filename"></span>
                                                    <a href="#" class="close fileinput-exists" data-dismiss="fileinput"
                                                       style="float: none;">&times;</a>
                                                <?php endif; ?>
                                            </div>
                                            <div id="msg_pdf" style="color: #e11221"></div>
                                        </div>
                                        <div class="col-sm-2">
                                            <strong><a href="javascript:void(0);" id="add_more" class="addCF "><i
                                                        class="fa fa-plus"></i>&nbsp;<?= lang('add_more') ?>
                                                </a></strong>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <br/>
                            <input type="hidden" name="project_id" value="<?php
                            if (!empty($project_details->project_id)) {
                                echo $project_details->project_id;
                            }
                            ?>" class="form-control">
                            <div class="form-group">
                                <div class="col-sm-3">
                                </div>
                                <div class="col-sm-3">
                                    <button type="submit" class="btn btn-primary"><?= lang('upload_file') ?></button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
                <?php
                if (!empty($project_files_info)) {
                    ?>
                    <div class="panel">
                        <div class="panel-heading" style="border-bottom: 2px solid #00BCD4">
                            <strong><?= lang('attach_file_list') ?></strong></div>
                        <div class="panel-body">
                            <?php
                            $this->load->helper('file');
                            foreach ($project_files_info as $key => $v_files_info) {
                                ?>
                                <div class="panel-group" id="accordion" style="margin:8px 5px" role="tablist"
                                     aria-multiselectable="true">
                                    <div class="box box-info" style="border-radius: 0px ">
                                        <div class="panel-heading" role="tab" id="headingOne">
                                            <h4 class="panel-title">
                                                <a data-toggle="collapse" data-parent="#accordion"
                                                   href="#<?php echo $key ?>" aria-expanded="true"
                                                   aria-controls="collapseOne">
                                                    <strong><?php echo $files_info[$key]->title; ?> </strong>
                                                    <small class="pull-right">
                                                        <?php if ($files_info[$key]->user_id == $this->session->userdata('user_id')) { ?>
                                                            <?= btn_delete('client/project/delete_files/' . $files_info[$key]->project_id . '/' . $files_info[$key]->task_attachment_id) ?>
                                                        <?php } ?></small>
                                                </a>
                                            </h4>
                                        </div>
                                        <div id="<?php echo $key ?>" class="panel-collapse collapse <?php
                                        if (!empty($in) && $files_info[$key]->files_id == $in) {
                                            echo 'in';
                                        }
                                        ?>" role="tabpanel" aria-labelledby="headingOne">
                                            <div class="content">
                                                <div class="table-responsive">
                                                    <table id="table-files" class="table table-striped ">
                                                        <thead>
                                                        <tr>
                                                            <th width="45%"><?= lang('files') ?></th>
                                                            <th class=""><?= lang('size') ?></th>
                                                            <th><?= lang('date') ?></th>
                                                            <th><?= lang('uploaded_by') ?></th>
                                                            <th><?= lang('action') ?></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <?php
                                                        $this->load->helper('file');

                                                        if (!empty($v_files_info)) {
                                                            foreach ($v_files_info as $v_files) {
                                                                $user_info = $this->db->where(array('user_id' => $files_info[$key]->user_id))->get('tbl_users')->row();
                                                                ?>
                                                                <tr class="file-item">
                                                                    <td>
                                                                        <?php if ($v_files->is_image == 1) : ?>
                                                                            <div class="file-icon"><a
                                                                                    href="<?= base_url() ?>client/project/download_files/<?= $files_info[$key]->project_id ?>/<?= $v_files->uploaded_files_id ?>">
                                                                                    <img
                                                                                        style="width: 50px;border-radius: 5px;"
                                                                                        src="<?= base_url() . $v_files->files ?>"/></a>
                                                                            </div>
                                                                        <?php else : ?>
                                                                            <div class="file-icon"><i
                                                                                    class="fa fa-file-o"></i>
                                                                                <a href="<?= base_url() ?>client/project/download_files/<?= $files_info[$key]->project_id ?>/<?= $v_files->uploaded_files_id ?>"><?= $v_files->file_name ?></a>
                                                                            </div>
                                                                        <?php endif; ?>

                                                                        <a data-toggle="tooltip" data-placement="top"
                                                                           data-original-title="<?= $files_info[$key]->description ?>"
                                                                           class="text-info"
                                                                           href="<?= base_url() ?>client/project/download_files/<?= $files_info[$key]->project_id ?>/<?= $v_files->uploaded_files_id ?>">
                                                                            <?= $files_info[$key]->title ?>
                                                                            <?php if ($v_files->is_image == 1) : ?>
                                                                                <em><?= $v_files->image_width . "x" . $v_files->image_height ?></em>
                                                                            <?php endif; ?>
                                                                        </a>
                                                                        <p class="file-text"><?= $files_info[$key]->description ?></p>
                                                                    </td>
                                                                    <td class=""><?= $v_files->size ?> Kb</td>
                                                                    <td class="col-date"><?= date('Y-m-d' . "<br/> h:m A", strtotime($files_info[$key]->upload_time)); ?></td>
                                                                    <td>
                                                                        <?= $user_info->username ?>
                                                                    </td>
                                                                    <td>
                                                                        <a class="btn btn-xs btn-dark"
                                                                           data-toggle="tooltip" data-placement="top"
                                                                           title="Download"
                                                                           href="<?= base_url() ?>client/project/download_files/<?= $files_info[$key]->project_id ?>/<?= $v_files->uploaded_files_id ?>"><i
                                                                                class="fa fa-download"></i></a>
                                                                    </td>

                                                                </tr>
                                                                <?php
                                                            }
                                                        } else {
                                                            ?>
                                                            <tr>
                                                                <td colspan="5">
                                                                    <?= lang('nothing_to_display') ?>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <!-- Task Attachment Panel Ends --->
            <!-- // milestones-->
            <?php
            if (!empty($all_milestones_info)) {
                ?>
                <div class="tab-pane <?= $active == 5 ? 'active' : '' ?>" id="milestones" style="position: relative;">
                    <div class="panel panel-custom">
                        <div class="panel-heading">
                            <div class="panel-title">
                                <?= lang('milestones') ?>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="table-responsive">
                                <table id="table-milestones" class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th><?= lang('milestone_name') ?></th>
                                        <th class="col-date"><?= lang('start_date') ?></th>
                                        <th class="col-date"><?= lang('due_date') ?></th>
                                        <th><?= lang('progress') ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php

                                    foreach ($all_milestones_info as $key => $v_milestones) {
                                        $progress = $this->items_model->calculate_milestone_progress($v_milestones->milestones_id);
                                        $all_milestones_task = $this->db->where('milestones_id', $v_milestones->milestones_id)->get('tbl_task')->result();
                                        ?>
                                        <tr>
                                            <td><a class="text-info" href="#"
                                                   data-original-title="<?= $v_milestones->description ?>"
                                                   data-toggle="tooltip" data-placement="top"
                                                   title=""><?= $v_milestones->milestone_name ?></a></td>
                                            <td><?= strftime(config_item('date_format'), strtotime($v_milestones->start_date)) ?></td>
                                            <td><?php
                                                $due_date = $v_milestones->end_date;
                                                $due_time = strtotime($due_date);
                                                $current_time = time();
                                                ?>
                                                <?= strftime(config_item('date_format'), strtotime($due_date)) ?>
                                                <?php if ($current_time > $due_time && $progress < 100) { ?>
                                                    <span
                                                        class="label label-danger"><?= lang('overdue') ?></span>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <div class="inline ">
                                                    <div class="easypiechart text-success"
                                                         style="margin: 0px;"
                                                         data-percent="<?= $progress ?>" data-line-width="5"
                                                         data-track-Color="#f0f0f0" data-bar-color="#<?php
                                                    if ($progress >= 100) {
                                                        echo '8ec165';
                                                    } else {
                                                        echo 'fb6b5b';
                                                    }
                                                    ?>" data-rotate="270" data-scale-Color="false"
                                                         data-size="50" data-animate="2000">
                                                                    <span class="small text-muted"><?= $progress ?>
                                                                        %</span>
                                                    </div>
                                                </div>

                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <!-- End milestones-->

            <!-- Start Tasks Management-->
            <div class="tab-pane <?= $active == 6 ? 'active' : '' ?>" id="task" style="position: relative;">
                <!-- Start Tasks Management-->
                <?php if (!empty($all_task_info)): ?>
                    <div class="panel panel-custom">
                        <div class="panel-heading">
                            <div class="panel-title">
                                <?= lang('task') ?>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="table-responsive">
                                <table id="table-milestones" class="table table-striped     DataTables">
                                    <thead>
                                    <tr>
                                        <th><?= lang('task_name') ?></th>
                                        <th><?= lang('due_date') ?></th>
                                        <th class="col-sm-1"><?= lang('progress') ?></th>
                                        <th class="col-sm-1"><?= lang('status') ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    foreach ($all_task_info as $key => $v_task):
                                        ?>
                                        <tr>

                                            <td><a class="text-info" style="<?php
                                                if ($v_task->task_progress >= 100) {
                                                    echo 'text-decoration: line-through;';
                                                }
                                                ?>"
                                                   href="<?= base_url() ?>client/tasks/view_task_details/<?= $v_task->task_id ?>"><?php echo $v_task->task_name; ?></a>
                                            </td>

                                            <td><?php
                                                $due_date = $v_task->due_date;
                                                $due_time = strtotime($due_date);
                                                $current_time = time();
                                                ?>
                                                <?= strftime(config_item('date_format'), strtotime($due_date)) ?>
                                                <?php if ($current_time > $due_time && $v_task->task_progress < 100) { ?>
                                                    <span
                                                        class="label label-danger"><?= lang('overdue') ?></span>
                                                <?php } ?></td>
                                            <td>
                                                <div class="inline ">
                                                    <div class="easypiechart text-success" style="margin: 0px;"
                                                         data-percent="<?= $v_task->task_progress ?>"
                                                         data-line-width="5" data-track-Color="#f0f0f0"
                                                         data-bar-color="#<?php
                                                         if ($v_task->task_progress == 100) {
                                                             echo '8ec165';
                                                         } else {
                                                             echo 'fb6b5b';
                                                         }
                                                         ?>" data-rotate="270" data-scale-Color="false"
                                                         data-size="50" data-animate="2000">
                                                            <span class="small text-muted"><?= $v_task->task_progress ?>
                                                                %</span>
                                                    </div>
                                                </div>

                                            </td>
                                            <td>
                                                <?php
                                                if ($v_task->task_status == 'completed') {
                                                    $label = 'success';
                                                } elseif ($v_task->task_status == 'not_started') {
                                                    $label = 'info';
                                                } elseif ($v_task->task_status == 'deferred') {
                                                    $label = 'danger';
                                                } else {
                                                    $label = 'warning';
                                                }
                                                ?>
                                                <span
                                                    class="label label-<?= $label ?>"><?= lang($v_task->task_status) ?> </span>
                                            </td>

                                        </tr>
                                    <?php endforeach; ?>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

            </div><!-- End Tasks Management-->
            <!-- Start Bugs Management-->
            <div class="tab-pane <?= $active == 9 ? 'active' : '' ?>" id="bugs" style="position: relative;">
                <?php if (!empty($all_bugs_info)): ?>

                    <div class="panel panel-custom">
                        <div class="panel-heading">
                            <div class="panel-title">
                                <?= lang('bugs') ?>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="table-responsive">
                                <table id="table-milestones" class="table table-striped     DataTables">
                                    <thead>
                                    <tr>
                                        <th><?= lang('bug_title') ?></th>
                                        <th><?= lang('status') ?></th>
                                        <th><?= lang('priority') ?></th>
                                        <th><?= lang('reporter') ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    foreach ($all_bugs_info as $key => $v_bugs):
                                        $reporter = $this->db->where('user_id', $v_bugs->reporter)->get('tbl_users')->row();
                                        if ($reporter->role_id == '1') {
                                            $badge = 'danger';
                                        } elseif ($reporter->role_id == '2') {
                                            $badge = 'info';
                                        } else {
                                            $badge = 'primary';
                                        }
                                        ?>
                                        <tr>
                                            <td><a class="text-info" style="<?php
                                                if ($v_bugs->bug_status == 'resolve') {
                                                    echo 'text-decoration: line-through;';
                                                }
                                                ?>"
                                                   href="<?= base_url() ?>client/bugs/view_bug_details/<?= $v_bugs->bug_id ?>"><?php echo $v_bugs->bug_title; ?></a>
                                            </td>
                                            </td>
                                            <td><?php
                                                if ($v_bugs->bug_status == 'unconfirmed') {
                                                    $label = 'warning';
                                                } elseif ($v_bugs->bug_status == 'confirmed') {
                                                    $label = 'info';
                                                } elseif ($v_bugs->bug_status == 'in_progress') {
                                                    $label = 'primary';
                                                } else {
                                                    $label = 'success';
                                                }
                                                ?>
                                                <span
                                                    class="label label-<?= $label ?>"><?= lang("$v_bugs->bug_status") ?></span>
                                            </td>
                                            <td><?= ucfirst($v_bugs->priority) ?></td>
                                            <td>

                                                    <span
                                                        class="badge btn-<?= $badge ?> "><?= $reporter->username ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

            </div><!-- End Bugs Management-->
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        var maxAppend = 0;
        $("#add_more").click(function () {
            if (maxAppend >= 4) {
                alert("Maximum 5 File is allowed");
            } else {
                var add_new = $('<div class="form-group" style="margin-bottom: 0px">\n\
                    <label for="field-1" class="col-sm-3 control-label"><?= lang('upload_file') ?></label>\n\
        <div class="col-sm-5">\n\
        <div class="fileinput fileinput-new" data-provides="fileinput">\n\
<span class="btn btn-default btn-file"><span class="fileinput-new" >Select file</span><span class="fileinput-exists" >Change</span><input type="file" name="task_files[]" ></span> <span class="fileinput-filename"></span><a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none;">&times;</a></div></div>\n\<div class="col-sm-2">\n\<strong>\n\
<a href="javascript:void(0);" class="remCF"><i class="fa fa-times"></i>&nbsp;Remove</a></strong></div>');
                maxAppend++;
                $("#add_new").append(add_new);
            }
        });

        $("#add_new").on('click', '.remCF', function () {
            $(this).parent().parent().parent().remove();
        });
    });
</script>