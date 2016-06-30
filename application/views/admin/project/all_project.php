<?php
$mdate = date('Y-m-d');
$last_7_days = date('Y-m-d', strtotime('today - 7 days'));
$all_goal_tracking = $this->items_model->get_permission('tbl_goal_tracking');

$all_goal = 0;
$bank_goal = 0;
$complete_achivement = 0;
if (!empty($all_goal_tracking)) {
    foreach ($all_goal_tracking as $v_goal_track) {
        $goal_achieve = $this->items_model->get_progress($v_goal_track, true);

        if ($v_goal_track->goal_type_id == 12) {

            if ($v_goal_track->end_date <= $mdate) { // check today is last date or not

                if ($v_goal_track->email_send == 'no') {// check mail are send or not
                    if ($v_goal_track->achievement <= $goal_achieve['achievement']) {
                        if ($v_goal_track->notify_goal_achive == 'on') {// check is notify is checked or not check
                            $this->items_model->send_goal_mail('goal_achieve', $v_goal_track);
                        }
                    } else {
                        if ($v_goal_track->notify_goal_not_achive == 'on') {// check is notify is checked or not check
                            $this->items_model->send_goal_mail('goal_not_achieve', $v_goal_track);
                        }
                    }
                }
            }
            $all_goal += $v_goal_track->achievement;
            $complete_achivement += $goal_achieve['achievement'];
        }
    }
}
// 30 days before

for ($iDay = 7; $iDay >= 0; $iDay--) {
    $date = date('Y-m-d', strtotime('today - ' . $iDay . 'days'));
    $where = array('created_time >=' => $date . " 00:00:00", 'created_time <=' => $date . " 23:59:59", 'project_status' => 'completed');
    $invoice_result[$date] = count($this->db->where($where)->get('tbl_project')->result());
}

$terget_achievement = $this->db->where(array('goal_type_id' => 12, 'start_date >=' => $last_7_days, 'end_date <=' => $mdate))->get('tbl_goal_tracking')->result();

$total_terget = 0;
if (!empty($terget_achievement)) {
    foreach ($terget_achievement as $v_terget) {
        $total_terget += $v_terget->achievement;
    }
}
$tolal_goal = $all_goal + $bank_goal;
$curency = $this->items_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');

if ($this->session->userdata('user_type') == 1) {
    $margin = 'margin-bottom:30px';
    ?>
    <div class="col-sm-12 bg-white p0" style="<?= $margin ?>">
        <div class="col-md-4">
            <div class="row row-table pv-lg">
                <div class="col-xs-6">
                    <p class="m0 lead"><?= ($tolal_goal) ?></p>
                    <p class="m0">
                        <small><?= lang('achievement') ?></small>
                    </p>
                </div>
                <div class="col-xs-6 ">
                    <p class="m0 lead"><?= ($total_terget) ?></p>
                    <p class="m0">
                        <small><?= lang('last_weeks') . ' ' . lang('created') ?></small>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="row row-table ">
                <div class="col-xs-6">
                    <p class="m0 lead"><?= ($complete_achivement) ?></p>
                    <p class="m0">
                        <small><?= lang('completed') . ' ' . lang('achievements') ?></small>
                    </p>
                </div>
                <div class="col-xs-6 pt">
                    <div data-sparkline="" data-bar-color="#23b7e5" data-height="60" data-bar-width="8"
                         data-bar-spacing="6" data-chart-range-min="0" values="<?php
                    if (!empty($invoice_result)) {
                        foreach ($invoice_result as $v_invoice_result) {
                            echo $v_invoice_result . ',';
                        }
                    }
                    ?>">
                    </div>
                    <p class="m0">
                        <small>
                            <?php
                            if (!empty($invoice_result)) {
                                foreach ($invoice_result as $date => $v_invoice_result) {
                                    echo date('d', strtotime($date)) . ' ';
                                }
                            }
                            ?>
                        </small>
                    </p>

                </div>
            </div>

        </div>
        <div class="col-md-4">
            <div class="row row-table ">
                <div class="col-xs-6">
                    <p class="m0 lead">
                        <?php
                        if ($tolal_goal < $complete_achivement) {
                            $pending_goal = 0;
                        } else {
                            $pending_goal = $tolal_goal - $complete_achivement;
                        } ?>
                        <?= $pending_goal ?></p>
                    <p class="m0">
                        <small><?= lang('pending') . ' ' . lang('achievements') ?></small>
                    </p>
                </div>
                <?php
                if ($tolal_goal <= $complete_achivement) {
                    $total_progress = 100;
                } else {
                    $progress = ($complete_achivement / $tolal_goal) * 100;
                    $total_progress = round($progress);
                }
                ?>
                <div class="col-xs-6 text-center pt">
                    <div class="inline ">
                        <div class="easypiechart text-success"
                             data-percent="<?= $total_progress ?>"
                             data-line-width="5" data-track-Color="#f0f0f0"
                             data-bar-color="#<?php
                             if ($total_progress == 100) {
                                 echo '8ec165';
                             } elseif ($total_progress >= 40 && $total_progress <= 50) {
                                 echo '5d9cec';
                             } elseif ($total_progress >= 51 && $total_progress <= 99) {
                                 echo '7266ba';
                             } else {
                                 echo 'fb6b5b';
                             }
                             ?>" data-rotate="270" data-scale-Color="false"
                             data-size="50"
                             data-animate="2000">
                                                        <span class="small "><?= $total_progress ?>
                                                            %</span>
                            <span class="easypie-text"><strong><?= lang('done') ?></strong></span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
<?php } ?>
<?= message_box('success'); ?>
<?= message_box('error');
$complete = 0;
if (!empty($all_project)):foreach ($all_project as $v_project):
    if ($v_project->project_status == 'completed') {
        $complete += count($v_project->project_id);
    }
endforeach;
endif;
?>
<div class="nav-tabs-custom">
    <!-- Tabs within a box -->
    <ul class="nav nav-tabs">
        <li class="<?= $active == 1 ? 'active' : ''; ?>"><a href="#manage"
                                                            data-toggle="tab"><?= lang('all_project') ?></a></li>

        <li class="<?= $active == 2 ? 'active' : ''; ?>"><a href="#create"
                                                            data-toggle="tab"><?= lang('new_project') ?></a></li>
        <li><a style="background-color: #1797be;color: #ffffff"
               href="<?= base_url() ?>admin/project/import"><?= lang('import') . ' ' . lang('project') ?></a>
        </li>
        <li class="pull-right <?= $active == 3 ? 'active' : ''; ?>"><a href="#archived"
                                                                       data-toggle="tab"><?= lang('archived') ?>
                <small class="label label-danger"
                       style="top: 11%;position: absolute;right: 5%;}"><?php if ($complete != 0) {
                        echo $complete;
                    } ?></small>
            </a>
        </li>
    </ul>
    <div class="tab-content bg-white">
        <!-- ************** general *************-->
        <div class="tab-pane <?= $active == 1 ? 'active' : ''; ?>" id="manage">

            <div class="table-responsive">
                <table class="table table-striped DataTables " id="DataTables" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th><?= lang('project_name') ?></th>
                        <th><?= lang('client') ?></th>
                        <th><?= lang('end_date') ?></th>
                        <th><?= lang('assigned_to') ?></th>
                        <th><?= lang('status') ?></th>
                        <th class="col-options no-sort"><?= lang('action') ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php

                    if (!empty($all_project)):foreach ($all_project as $v_project):
                        if ($v_project->project_status != 'completed') {
                            $can_edit = $this->items_model->can_action('tbl_project', 'edit', array('project_id' => $v_project->project_id));
                            $can_delete = $this->items_model->can_action('tbl_project', 'delete', array('project_id' => $v_project->project_id));
                            ?>
                            <tr>
                                <?php
                                $client_info = $this->db->where('client_id', $v_project->client_id)->get('tbl_client')->row();
                                if (!empty($client_info)) {
                                    if ($client_info->client_status == 1) {
                                        $status = lang('person');
                                    } else {
                                        $status =lang('company');
                                    }
                                    $name = $client_info->name . ' (' . $status . ')';
                                } else {
                                    $name = '-';
                                }
                                ?>
                                <td>
                                    <a class="text-info"
                                       href="<?= base_url() ?>admin/project/project_details/<?= $v_project->project_id ?>"><?= $v_project->project_name ?></a>
                                    <?php if (time() > strtotime($v_project->end_date) AND $v_project->progress < 100) { ?>
                                        <span class="label label-danger pull-right"><?= lang('overdue') ?></span>
                                    <?php } ?>

                                    <div class="progress progress-xs progress-striped active">
                                        <div
                                            class="progress-bar progress-bar-<?php echo ($v_project->progress >= 100) ? 'success' : 'primary'; ?>"
                                            data-toggle="tooltip" data-original-title="<?= $v_project->progress ?>%"
                                            style="width: <?= $v_project->progress; ?>%"></div>
                                    </div>

                                </td>
                                <td><?= $name ?></td>
                                <td><?= strftime(config_item('date_format'), strtotime($v_project->end_date)) ?></td>
                                <td>
                                    <?php
                                    if ($v_project->permission != 'all') {
                                        $get_permission = json_decode($v_project->permission);
                                        if (!empty($get_permission)) :
                                            foreach ($get_permission as $permission => $v_permission) :
                                                $user_info = $this->db->where(array('user_id' => $permission))->get('tbl_users')->row();
                                                if (!empty($user_info)) {
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
                                                }
                                            endforeach;
                                        endif;
                                    } else { ?>
                                        <strong><?= lang('everyone') ?></strong>
                                        <i
                                            title="<?= lang('permission_for_all') ?>"
                                            class="fa fa-question-circle" data-toggle="tooltip"
                                            data-placement="top"></i>
                                        <?php
                                    }
                                    ?>
                                    <?php if (!empty($can_edit)) { ?>
                                        <span data-placement="top" data-toggle="tooltip"
                                              title="<?= lang('add_more') ?>">
                                            <a data-toggle="modal" data-target="#myModal"
                                               href="<?= base_url() ?>admin/project/update_users/<?= $v_project->project_id ?>"
                                               class="text-default ml"><i class="fa fa-plus"></i></a>
                                                </span>
                                    <?php } ?>
                                </td>
                                <td><?php
                                    if (!empty($v_project->project_status)) {
                                        if ($v_project->project_status == 'completed') {
                                            $status = "<span class='label label-success'>" . lang($v_project->project_status) . "</span>";
                                        } elseif ($v_project->project_status == 'in_progress') {
                                            $status = "<span class='label label-primary'>" . lang($v_project->project_status) . "</span>";
                                        } elseif ($v_project->project_status == 'cancel') {
                                            $status = "<span class='label label-danger'>" . lang($v_project->project_status) . "</span>";
                                        } else {
                                            $status = "<span class='label label-warning'>" . lang($v_project->project_status) . "</span>";
                                        }
                                        echo $status;
                                    }
                                    ?>      </td>
                                <td>
                                    <?= btn_view('admin/project/project_details/' . $v_project->project_id) ?>
                                    <?php if (!empty($can_edit)) { ?>
                                        <?= btn_edit('admin/project/index/' . $v_project->project_id) ?>
                                    <?php }
                                    if (!empty($can_delete)) { ?>
                                        <?= btn_delete('admin/project/delete_project/' . $v_project->project_id) ?>
                                    <?php } ?>
                                    <?php if (!empty($can_edit)) { ?>
                                        <div class="btn-group">
                                            <button class="btn btn-xs btn-success dropdown-toggle"
                                                    data-toggle="dropdown">
                                                <?= lang('change_status') ?>
                                                <span class="caret"></span></button>
                                            <ul class="dropdown-menu animated zoomIn">
                                                <li>
                                                    <a href="<?= base_url() ?>admin/project/change_status/<?= $v_project->project_id . '/started' ?>"><?= lang('started') ?></a>
                                                </li>
                                                <li>
                                                    <a href="<?= base_url() ?>admin/project/change_status/<?= $v_project->project_id . '/in_progress' ?>"><?= lang('in_progress') ?></a>
                                                </li>
                                                <li>
                                                    <a href="<?= base_url() ?>admin/project/change_status/<?= $v_project->project_id . '/cancel' ?>"><?= lang('cancel') ?></a>
                                                </li>
                                                <li>
                                                    <a href="<?= base_url() ?>admin/project/change_status/<?= $v_project->project_id . '/completed' ?>"><?= lang('completed') ?></a>
                                                </li>
                                            </ul>
                                        </div>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php
                        }
                    endforeach;
                    endif;
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="tab-pane <?= $active == 2 ? 'active' : ''; ?>" id="create">
            <form role="form" enctype="multipart/form-data" id="form"
                  action="<?php echo base_url(); ?>admin/project/saved_project/<?php
                  if (!empty($project_info)) {
                      echo $project_info->project_id;
                  }
                  ?>" method="post" class="form-horizontal  ">
                <div class="panel-body">
                    <?php if (!empty($project_info)) { ?>
                        <div class="form-group">
                            <label class="col-lg-2 control-label"><?= lang('change_status') ?> <span
                                    class="text-danger">*</span></label>
                            <div class="col-lg-5">
                                <select name="project_status" class="form-control select_box" style="width: 100%"
                                        required="">
                                    <option <?= $project_info->project_status == 'started' ? 'selected' : null ?>
                                        value="started"><?= lang('started') ?></option>
                                    <option <?= $project_info->project_status == 'in_progress' ? 'selected' : null ?>
                                        value="in_progress"><?= lang('in_progress') ?></option>
                                    <option <?= $project_info->project_status == 'cancel' ? 'selected' : null ?>
                                        value="cancel"><?= lang('cancel') ?></option>
                                    <option <?= $project_info->project_status == 'completed' ? 'selected' : null ?>
                                        value="completed"><?= lang('completed') ?></option>
                                </select>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="form-group">
                        <label class="col-lg-2 control-label"><?= lang('project_name') ?> <span
                                class="text-danger">*</span></label>
                        <div class="col-lg-5">
                            <input type="text" class="form-control" value="<?php
                            if (!empty($project_info)) {
                                echo $project_info->project_name;
                            }
                            ?>" name="project_name" required="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-2 control-label"><?= lang('select_client') ?> <span
                                class="text-danger">*</span></label>
                        <div class="col-lg-5">
                            <select name="client_id" class="form-control select_box" style="width: 100%" required="">
                                <option value=""><?= lang('select_client') ?></option>
                                <?php
                                $all_client = $this->db->get('tbl_client')->result();
                                if (!empty($all_client)) {
                                    foreach ($all_client as $v_client) {
                                        ?>
                                        <option value="<?= $v_client->client_id ?>" <?php
                                        if (!empty($project_info) && $project_info->client_id == $v_client->client_id) {
                                            echo 'selected';
                                        }
                                        ?>><?= $v_client->name ?></option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-2 control-label"><?= lang('progress') ?></label>
                        <div class="col-lg-5">
                            <input name="progress" data-ui-slider="" type="text"
                                   value="<?php if (!empty($project_info->progress)) echo $project_info->progress; ?>"
                                   data-slider-min="0" data-slider-max="100" data-slider-step="1"
                                   data-slider-value="<?php if (!empty($project_info->progress)) echo $project_info->progress; ?>"
                                   data-slider-orientation="horizontal" class="slider slider-horizontal"
                                   data-slider-id="red">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-2 control-label"><?= lang('start_date') ?> <span
                                class="text-danger">*</span></label>
                        <div class="col-lg-5">
                            <div class="input-group">
                                <input type="text" name="start_date" class="form-control datepicker" value="<?php
                                if (!empty($project_info->start_date)) {
                                    echo date('Y-m-d', strtotime($project_info->start_date));
                                }
                                ?>" data-date-format="<?= config_item('date_picker_format'); ?>">
                                <div class="input-group-addon">
                                    <a href="#"><i class="fa fa-calendar"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-2 control-label"><?= lang('end_date') ?> <span class="text-danger">*</span></label>
                        <div class="col-lg-5">
                            <div class="input-group">
                                <input type="text" name="end_date" class="form-control datepicker" value="<?php
                                if (!empty($project_info->end_date)) {
                                    echo date('Y-m-d', strtotime($project_info->end_date));
                                }
                                ?>" data-date-format="<?= config_item('date_picker_format'); ?>">
                                <div class="input-group-addon">
                                    <a href="#"><i class="fa fa-calendar"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-2 control-label"><?= lang('project_cost') ?></label>
                        <div class="col-lg-3">
                            <input type="text" value="<?php
                            if (!empty($project_info->project_cost)) {
                                echo $project_info->project_cost;
                            }
                            ?>" class="form-control" placeholder="100" name="project_cost">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-2 control-label"><?= lang('demo_url') ?></label>
                        <div class="col-lg-5">
                            <input type="text" value="<?php
                            if (!empty($project_info->demo_url)) {
                                echo $project_info->demo_url;
                            }
                            ?>" class="form-control" placeholder="http://www.demourl.com" name="demo_url">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-2 control-label"><?= lang('description') ?> <span
                                class="text-danger">*</span></label>
                        <div class="col-lg-8">
                            <textarea style="" name="description" class="form-control textarea" rows="5"
                                      placeholder="<?= lang('description') ?>"><?php
                                if (!empty($project_info->description)) {
                                    echo $project_info->description;
                                }
                                ?></textarea>
                        </div>
                    </div>
                    <?php
                    if (!empty($project_info)) {
                        $project_id = $project_info->project_id;
                    } else {
                        $project_id = null;
                    }
                    ?>
                    <?= custom_form_Fields(4, $project_id, true); ?>
                    <div class="form-group" id="border-none">
                        <label for="field-1" class="col-sm-2 control-label"><?= lang('assined_to') ?> <span
                                class="required">*</span></label>
                        <div class="col-sm-9">
                            <div class="checkbox c-radio needsclick">
                                <label class="needsclick">
                                    <input id="" <?php
                                    if (!empty($project_info->permission) && $project_info->permission == 'all') {
                                        echo 'checked';
                                    } elseif (empty($project_info)) {
                                        echo 'checked';
                                    }
                                    ?> type="radio" name="permission" value="everyone">
                                    <span class="fa fa-circle"></span><?= lang('everyone') ?>
                                    <i title="<?= lang('permission_for_all') ?>"
                                       class="fa fa-question-circle" data-toggle="tooltip"
                                       data-placement="top"></i>
                                </label>
                            </div>
                            <div class="checkbox c-radio needsclick">
                                <label class="needsclick">
                                    <input id="" <?php
                                    if (!empty($project_info->permission) && $project_info->permission != 'all') {
                                        echo 'checked';
                                    }
                                    ?> type="radio" name="permission" value="custom_permission"
                                    >
                                    <span class="fa fa-circle"></span><?= lang('custom_permission') ?> <i
                                        title="<?= lang('permission_for_customization') ?>"
                                        class="fa fa-question-circle" data-toggle="tooltip"
                                        data-placement="top"></i>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group <?php
                    if (!empty($project_info->permission) && $project_info->permission != 'all') {
                        echo 'show';
                    }
                    ?>" id="permission_user_1">
                        <label for="field-1"
                               class="col-sm-2 control-label"><?= lang('select') . ' ' . lang('users') ?>
                            <span
                                class="required">*</span></label>
                        <div class="col-sm-9">
                            <?php
                            if (!empty($assign_user)) {
                                foreach ($assign_user as $key => $v_user) {

                                    if ($v_user->role_id == 1) {
                                        $disable = true;
                                        $role = '<strong class="badge btn-danger">' . lang('admin') . '</strong>';
                                    } else {
                                        $disable = false;
                                        $role = '<strong class="badge btn-primary">' . lang('staff') . '</strong>';
                                    }

                                    ?>
                                    <div class="checkbox c-checkbox needsclick">
                                        <label class="needsclick">
                                            <input type="checkbox"
                                                <?php
                                                if (!empty($project_info->permission) && $project_info->permission != 'all') {
                                                    $get_permission = json_decode($project_info->permission);
                                                    foreach ($get_permission as $user_id => $v_permission) {
                                                        if ($user_id == $v_user->user_id) {
                                                            echo 'checked';
                                                        }
                                                    }

                                                }
                                                ?>
                                                   value="<?= $v_user->user_id ?>"
                                                   name="assigned_to[]"
                                                   class="needsclick">
                                                        <span
                                                            class="fa fa-check"></span><?= $v_user->username . ' ' . $role ?>
                                        </label>

                                    </div>
                                    <div class="action_1 p
                                                <?php

                                    if (!empty($project_info->permission) && $project_info->permission != 'all') {
                                        $get_permission = json_decode($project_info->permission);

                                        foreach ($get_permission as $user_id => $v_permission) {
                                            if ($user_id == $v_user->user_id) {
                                                echo 'show';
                                            }
                                        }

                                    }
                                    ?>
                                                " id="action_1<?= $v_user->user_id ?>">
                                        <label class="checkbox-inline c-checkbox">
                                            <input id="<?= $v_user->user_id ?>" checked type="checkbox"
                                                   name="action_1<?= $v_user->user_id ?>[]"
                                                   disabled
                                                   value="view">
                                                        <span
                                                            class="fa fa-check"></span><?= lang('can') . ' ' . lang('view') ?>
                                        </label>
                                        <label class="checkbox-inline c-checkbox">
                                            <input <?php if (!empty($disable)) {
                                                echo 'disabled' . ' ' . 'checked';
                                            } ?> id="<?= $v_user->user_id ?>"
                                                <?php

                                                if (!empty($project_info->permission) && $project_info->permission != 'all') {
                                                    $get_permission = json_decode($project_info->permission);

                                                    foreach ($get_permission as $user_id => $v_permission) {
                                                        if ($user_id == $v_user->user_id) {
                                                            if (in_array('edit', $v_permission)) {
                                                                echo 'checked';
                                                            };

                                                        }
                                                    }

                                                }
                                                ?>
                                                 type="checkbox"
                                                 value="edit" name="action_<?= $v_user->user_id ?>[]">
                                                        <span
                                                            class="fa fa-check"></span><?= lang('can') . ' ' . lang('edit') ?>
                                        </label>
                                        <label class="checkbox-inline c-checkbox">
                                            <input <?php if (!empty($disable)) {
                                                echo 'disabled' . ' ' . 'checked';
                                            } ?> id="<?= $v_user->user_id ?>"
                                                <?php

                                                if (!empty($project_info->permission) && $project_info->permission != 'all') {
                                                    $get_permission = json_decode($project_info->permission);
                                                    foreach ($get_permission as $user_id => $v_permission) {
                                                        if ($user_id == $v_user->user_id) {
                                                            if (in_array('delete', $v_permission)) {
                                                                echo 'checked';
                                                            };
                                                        }
                                                    }

                                                }
                                                ?>
                                                 name="action_<?= $v_user->user_id ?>[]"
                                                 type="checkbox"
                                                 value="delete">
                                                        <span
                                                            class="fa fa-check"></span><?= lang('can') . ' ' . lang('delete') ?>
                                        </label>
                                        <input id="<?= $v_user->user_id ?>" type="hidden"
                                               name="action_<?= $v_user->user_id ?>[]" value="view">

                                    </div>


                                    <?php
                                }
                            }
                            ?>


                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-2 control-label"></label>
                        <div class="col-lg-5">
                            <button type="submit" class="btn btn-sm btn-primary"><?= lang('updates') ?></button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="tab-pane <?= $active == 3 ? 'active' : ''; ?>" id="archived">

            <div class="table-responsive">
                <table class="table table-striped DataTables " id="DataTables" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th><?= lang('project_name') ?></th>
                        <th><?= lang('client') ?></th>
                        <th><?= lang('start_date') ?></th>
                        <th><?= lang('end_date') ?></th>
                        <th><?= lang('status') ?></th>
                        <th class="col-options no-sort"><?= lang('action') ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if (!empty($all_project)):foreach ($all_project as $v_project):
                        if ($v_project->project_status == 'completed') {
                            $can_edit = $this->items_model->can_action('tbl_project', 'edit', array('project_id' => $v_project->project_id));
                            $can_delete = $this->items_model->can_action('tbl_project', 'delete', array('project_id' => $v_project->project_id));
                            ?>
                            <tr>
                                <?php
                                $client_info = $this->db->where('client_id', $v_project->client_id)->get('tbl_client')->row();
                                if (!empty($client_info)) {
                                    if ($client_info->client_status == 1) {
                                        $status = lang('person');
                                    } else {
                                        $status =lang('company');
                                    }
                                    $name = $client_info->name . ' (' . $status . ')';
                                } else {
                                    $name = '-';
                                }
                                ?>
                                <td>
                                    <a class="text-info"
                                       href="<?= base_url() ?>admin/project/project_details/<?= $v_project->project_id ?>"><?= $v_project->project_name ?></a>
                                    <?php if (time() > strtotime($v_project->end_date) AND $v_project->progress < 100) { ?>
                                        <span class="label label-danger pull-right"><?= lang('overdue') ?></span>
                                    <?php } ?>

                                    <div class="progress progress-xs progress-striped active">
                                        <div
                                            class="progress-bar progress-bar-<?php echo ($v_project->progress >= 100) ? 'success' : 'primary'; ?>"
                                            data-toggle="tooltip" data-original-title="<?= $v_project->progress ?>%"
                                            style="width: <?= $v_project->progress; ?>%"></div>
                                    </div>

                                </td>
                                <td><?= $name ?></td>

                                <td><?= strftime(config_item('date_format'), strtotime($v_project->start_date)) ?></td>
                                <td><?= strftime(config_item('date_format'), strtotime($v_project->end_date)) ?></td>

                                <td><?php
                                    if (!empty($v_project->project_status)) {
                                        if ($v_project->project_status == 'completed') {
                                            $status = "<span class='label label-success'>" . lang($v_project->project_status) . "</span>";
                                        } elseif ($v_project->project_status == 'in_progress') {
                                            $status = "<span class='label label-primary'>" . lang($v_project->project_status) . "</span>";
                                        } elseif ($v_project->project_status == 'cancel') {
                                            $status = "<span class='label label-danger'>" . lang($v_project->project_status) . "</span>";
                                        } else {
                                            $status = "<span class='label label-warning'>" . lang($v_project->project_status) . "</span>";
                                        }
                                        echo $status;
                                    }
                                    ?>      </td>
                                <td>
                                    <?= btn_view('admin/project/project_details/' . $v_project->project_id) ?>
                                    <?php if (!empty($can_edit)) { ?>
                                        <?= btn_edit('admin/project/index/' . $v_project->project_id) ?>
                                    <?php }
                                    if (!empty($can_delete)) { ?>
                                        <?= btn_delete('admin/project/delete_project/' . $v_project->project_id) ?>
                                    <?php } ?>
                                    <?php if (!empty($can_edit)) { ?>
                                        <div class="btn-group">
                                            <button class="btn btn-xs btn-success dropdown-toggle"
                                                    data-toggle="dropdown">
                                                <?= lang('change_status') ?>
                                                <span class="caret"></span></button>
                                            <ul class="dropdown-menu animated zoomIn">
                                                <li>
                                                    <a href="<?= base_url() ?>admin/project/change_status/<?= $v_project->project_id . '/started' ?>"><?= lang('started') ?></a>
                                                </li>
                                                <li>
                                                    <a href="<?= base_url() ?>admin/project/change_status/<?= $v_project->project_id . '/in_progress' ?>"><?= lang('in_progress') ?></a>
                                                </li>
                                                <li>
                                                    <a href="<?= base_url() ?>admin/project/change_status/<?= $v_project->project_id . '/cancel' ?>"><?= lang('cancel') ?></a>
                                                </li>
                                                <li>
                                                    <a href="<?= base_url() ?>admin/project/change_status/<?= $v_project->project_id . '/completed' ?>"><?= lang('completed') ?></a>
                                                </li>
                                            </ul>
                                        </div>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php
                        }
                    endforeach;
                    endif;
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>