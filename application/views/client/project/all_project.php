<?= message_box('success'); ?>
<?= message_box('error'); ?>
<?php if (config_item('allow_client_project') == 'TRUE') { ?>
<div class="nav-tabs-custom">
    <!-- Tabs within a box -->
    <ul class="nav nav-tabs">
        <li class="<?= $active == 1 ? 'active' : ''; ?>"><a href="#manage"
                                                            data-toggle="tab"><?= lang('all_project') ?></a></li>
        <?php if (config_item('allow_client_project') == 'TRUE') { ?>
            <li class="<?= $active == 2 ? 'active' : ''; ?>"><a href="#create"
                                                                data-toggle="tab"><?= lang('new_project') ?></a></li>
        <?php } ?>
    </ul>
    <div class="tab-content bg-white">
        <!-- ************** general *************-->
        <div class="tab-pane <?= $active == 1 ? 'active' : ''; ?>" id="manage">
            <?php }else{ ?>
            <div class="panel panel-custom">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <?= lang('all_project') ?>
                    </h3>
                </div>
                <?php } ?>
                <div class="table-responsive">
                    <table class="table table-striped DataTables " id="DataTables" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th><?= lang('project_name') ?></th>
                            <th><?= lang('start_date') ?></th>
                            <th><?= lang('end_date') ?></th>
                            <th><?= lang('status') ?></th>
                            <th class="col-options no-sort"><?= lang('action') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $client_id = $this->session->userdata('client_id');
                        $all_project = $this->db->where('client_id', $client_id)->get('tbl_project')->result();
                        if (!empty($all_project)):foreach ($all_project as $v_project):
                            ?>
                            <tr>
                                <td><a class="text-info"
                                       href="<?= base_url() ?>client/project/project_details/<?= $v_project->project_id ?>"><?= $v_project->project_name ?></a>
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
                                    <?= btn_view('client/project/project_details/' . $v_project->project_id) ?>
                                </td>
                            </tr>
                            <?php
                        endforeach;
                        endif;
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php if (config_item('allow_client_project') == 'TRUE') { ?>
                <div class="tab-pane <?= $active == 2 ? 'active' : ''; ?>" id="create">
                    <form role="form" enctype="multipart/form-data" id="form"
                          action="<?php echo base_url(); ?>client/project/saved_project/<?php
                          if (!empty($project_info)) {
                              echo $project_info->project_id;
                          }
                          ?>" method="post" class="form-horizontal  ">
                        <div class="panel-body">

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
                                <label class="col-lg-2 control-label"><?= lang('start_date') ?> <span
                                        class="text-danger">*</span></label>
                                <div class="col-lg-5">
                                    <div class="input-group">
                                        <input type="text" name="start_date" class="form-control datepicker"
                                               value="<?php
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
                                <label class="col-lg-2 control-label"><?= lang('end_date') ?> <span
                                        class="text-danger">*</span></label>
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

                            <div class="form-group">
                                <label class="col-lg-2 control-label"></label>
                                <div class="col-lg-5">
                                    <button type="submit" class="btn btn-sm btn-primary"><?= lang('updates') ?></button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            <?php } ?>
        </div>
    </div>
