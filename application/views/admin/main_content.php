<link href="<?php echo base_url() ?>asset/css/fullcalendar.css" rel="stylesheet" type="text/css">
<style type="text/css">
    .datepicker {
        z-index: 1151 !important;
    }

    .mt-sm {
        font-size: 14px;
    }
</style>
<?php
echo message_box('success');
echo message_box('error');
$curency = $this->admin_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');

?>
<div class="dashboard row">

    <div class="">
        <!--        ******** transactions ************** -->
        <?php if ($this->session->userdata('user_type') == 1) { ?>
            <div class="col-sm-4">
                <div class="panel widget">
                    <div class="row row-table row-flush">
                        <div class="col-xs-6 bb br">
                            <div class="row row-table row-flush">
                                <div class="col-xs-2 text-center text-info">
                                    <em class="fa fa-plus fa-2x"></em>
                                </div>
                                <div class="col-xs-10">
                                    <div class="text-center">
                                        <h4 class="mt-sm mb0"><?php
                                            if (!empty($today_income)) {
                                                $today_income = $today_income;
                                            } else {
                                                $today_income = '0';
                                            }
                                            echo display_money($today_income, $curency->symbol);
                                            ?>
                                        </h4>
                                        <p class="mb0 text-muted"><?= lang('income_today') ?></p>
                                        <small><a href="<?= base_url() ?>admin/transactions/deposit"
                                                  class="mt0 mb0"><?= lang('more_info') ?> <i
                                                    class="fa fa-arrow-circle-right"></i></a></small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-6 bb">
                            <div class="row row-table row-flush">
                                <div class="col-xs-2 text-center text-danger">
                                    <em class="fa fa-minus fa-2x"></em>
                                </div>
                                <div class="col-xs-10">
                                    <div class="text-center">
                                        <h4 class="mt-sm mb0"> <?php
                                            if (!empty($today_expense)) {
                                                $today_expense = $today_expense;
                                            } else {
                                                $today_expense = '0';
                                            }
                                            echo display_money($today_expense, $curency->symbol);
                                            ?></h4>
                                        <p class="mb0 text-muted"><?= lang('expense_today') ?></p>
                                        <small><a href="<?= base_url() ?>admin/transactions/expense"
                                                  class=" small-box-footer"><?= lang('more_info') ?>
                                                <i
                                                    class="fa fa-arrow-circle-right"></i></a></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row row-table row-flush">
                        <div class="col-xs-6 br">
                            <div class="row row-table row-flush">
                                <div class="col-xs-2 text-center text-info">
                                    <em class="fa fa-plus fa-2x"></em>
                                </div>
                                <div class="col-xs-10">
                                    <div class="text-center">
                                        <h4 class="mt-sm mb0"><?php
                                            if (!empty($total_income)) {
                                                $total_income = $total_income;
                                            } else {
                                                $total_income = '0';
                                            }

                                            echo display_money($total_income, $curency->symbol);
                                            ?></h4>
                                        <p class="mb0 text-muted"><?= lang('total_income') ?></p>
                                        <small><a href="<?= base_url() ?>admin/transactions/deposit"
                                                  class="mt0 mb0"><?= lang('more_info') ?> <i
                                                    class="fa fa-arrow-circle-right"></i></a></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-6">
                            <div class="row row-table row-flush">
                                <div class="col-xs-2 text-center text-danger">
                                    <em class="fa fa-minus fa-2x"></em>
                                </div>
                                <div class="col-xs-10">
                                    <div class="text-center">
                                        <h4 class="mt-sm mb0"> <?php
                                            if (!empty($total_expense)) {
                                                $total_expense = $total_expense;
                                            } else {
                                                $total_expense = '0';
                                            }
                                            echo display_money($total_expense, $curency->symbol);
                                            ?></h4>
                                        <p class="mb0 text-muted"><?= lang('total_expense') ?></p>
                                        <small><a href="<?= base_url() ?>admin/transactions/expense"
                                                  class="small-box-footer"><?= lang('more_info') ?>
                                                <i
                                                    class="fa fa-arrow-circle-right"></i></a></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--        ******** Sales ************** -->
            <div class="col-sm-4">
                <div class="panel widget">
                    <div class="row row-table row-flush">
                        <div class="col-xs-6 bb br">
                            <div class="row row-table row-flush">
                                <div class="col-xs-2 text-center ">
                                    <em class="fa fa-shopping-cart fa-2x"></em>
                                </div>
                                <div class="col-xs-10">
                                    <div class="text-center">
                                        <h4 class="mt-sm mb0"><?php
                                            $date = date('Y-m-d');
                                            $all_items = $this->db->get('tbl_items')->result();
                                            $today_invoice = 0;
                                            if (!empty($all_items)) {
                                                foreach ($all_items as $in_items) {
                                                    $invoice_date = date('Y-m-d', strtotime($in_items->date_saved));
                                                    if ($invoice_date == $date) {
                                                        $today_invoice += $in_items->total_cost;
                                                    }
                                                }

                                            }
                                            echo display_money($today_invoice, $curency->symbol);

                                            ?></h4>
                                        <p class="mb0 text-muted"><?= lang('invoice_today') ?></p>
                                        <small><a href="<?= base_url() ?>admin/invoice/manage_invoice"
                                                  class="mt0 mb0"><?= lang('more_info') ?> <i
                                                    class="fa fa-arrow-circle-right"></i></a></small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-6 bb">
                            <div class="row row-table row-flush">
                                <div class="col-xs-2 text-center text-purple">
                                    <em class="fa fa-money fa-2x"></em>
                                </div>
                                <div class="col-xs-10">
                                    <div class="text-center">
                                        <h4 class="mt-sm mb0"> <?php
                                            echo display_money($this->db->select_sum('amount')->where('payment_date', $date)->get('tbl_payments')->row()->amount, $curency->symbol);

                                            ?></h4>
                                        <p class="mb0 text-muted"><?= lang('payment_today') ?></p>
                                        <small><a href="<?= base_url() ?>admin/invoice/all_payments"
                                                  class=" small-box-footer"><?= lang('more_info') ?>
                                                <i
                                                    class="fa fa-arrow-circle-right"></i></a></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row row-table row-flush">
                        <div class="col-xs-6 br">
                            <div class="row row-table row-flush">
                                <div class="col-xs-2 text-center text-purple">
                                    <em class="fa fa-money fa-2x"></em>
                                </div>
                                <div class="col-xs-10">
                                    <div class="text-center">
                                        <h4 class="mt-sm mb0"><?php
                                            if (!empty($invoce_total)) {
                                                if (!empty($invoce_total['paid'])) {
                                                    $paid = 0;
                                                    foreach ($invoce_total['paid'] as $cur => $total) {
                                                        $paid += $total;
                                                    }
                                                    echo display_money($paid, $curency->symbol);
                                                } else {
                                                    echo '0.00';
                                                }
                                            } else {
                                                echo '0.00';
                                            }
                                            ?></h4>
                                        <p class="mb0 text-muted"><?= lang('paid_amount') ?></p>
                                        <small><a href="<?= base_url() ?>admin/invoice/all_payments"
                                                  class="mt0 mb0"><?= lang('more_info') ?> <i
                                                    class="fa fa-arrow-circle-right"></i></a></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-6">
                            <div class="row row-table row-flush">
                                <div class="col-xs-2 text-center text-danger">
                                    <em class="fa fa-gbp fa-2x"></em>
                                </div>
                                <div class="col-xs-10">
                                    <div class="text-center">
                                        <h4 class="mt-sm mb0"> <?php
                                            if (!empty($invoce_total)) {
                                                $total_due = 0;
                                                if (!empty($invoce_total['due'])) {
                                                    foreach ($invoce_total['due'] as $cur => $d_total) {
                                                        $total_due += $d_total;
                                                    }
                                                    echo display_money($total_due, $curency->symbol);
                                                } else {
                                                    echo '0.00';
                                                }
                                            } else {
                                                echo '0.00';
                                            }
                                            ?></h4>
                                        <p class="mb0 text-muted"><?= lang('due_amount') ?></p>
                                        <small><a href="<?= base_url() ?>admin/invoice/manage_invoice"
                                                  class="small-box-footer"><?= lang('more_info') ?>
                                                <i
                                                    class="fa fa-arrow-circle-right"></i></a></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--        ******** Ticket ************** -->
            <div class="col-sm-4">
                <div class="panel widget">
                    <div class="row row-table row-flush">
                        <div class="col-xs-6 bb br">
                            <div class="row row-table row-flush">
                                <div class="col-xs-2 text-center text-danger">
                                    <em class="fa fa-tasks fa-2x"></em>
                                </div>
                                <div class="col-xs-10">
                                    <div class="text-center">
                                        <h4 class="mt-sm mb0"><?php
                                            echo count($this->db->where('task_status', 'in_progress')->get('tbl_task')->result());
                                            ?></h4>
                                        <p class="mb0 text-muted"><?= lang('in_progress') . ' ' . lang('task') ?></p>
                                        <small><a href="<?= base_url() ?>admin/tasks/all_task"
                                                  class="mt0 mb0"><?= lang('more_info') ?> <i
                                                    class="fa fa-arrow-circle-right"></i></a></small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-6 bb">
                            <div class="row row-table row-flush">
                                <div class="col-xs-2 text-center text-danger">
                                    <em class="fa fa-ticket fa-2x"></em>
                                </div>
                                <div class="col-xs-10">
                                    <div class="text-center">
                                        <h4 class="mt-sm mb0"> <?= count($this->db->where('status', 'open')->get('tbl_tickets')->result()); ?></h4>
                                        <p class="mb0 text-muted"><?= lang('open') . ' ' . lang('tickets') ?></p>
                                        <small><a href="<?= base_url() ?>admin/tickets/open"
                                                  class=" small-box-footer"><?= lang('more_info') ?>
                                                <i
                                                    class="fa fa-arrow-circle-right"></i></a></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row row-table row-flush">
                        <div class="col-xs-6 br">
                            <div class="row row-table row-flush">
                                <div class="col-xs-2 text-center text-danger">
                                    <em class="fa fa-bug fa-2x"></em>
                                </div>
                                <div class="col-xs-10">
                                    <div class="text-center">
                                        <h4 class="mt-sm mb0"><?php
                                            echo count($this->db->where('bug_status', 'in_progress')->get('tbl_bug')->result());
                                            ?></h4>
                                        <p class="mb0 text-muted"><?= lang('in_progress') . ' ' . lang('bugs') ?></p>
                                        <small><a href="<?= base_url() ?>admin/bugs"
                                                  class="mt0 mb0"><?= lang('more_info') ?> <i
                                                    class="fa fa-arrow-circle-right"></i></a></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-6">
                            <div class="row row-table row-flush">
                                <div class="col-xs-2 text-center text-danger">
                                    <em class="fa fa-folder-open-o fa-2x"></em>
                                </div>
                                <div class="col-xs-10">
                                    <div class="text-center">
                                        <h4 class="mt-sm mb0"><?php
                                            echo count($this->db->where('project_status', 'in_progress')->get('tbl_project')->result());
                                            ?></h4>
                                        <p class="mb0 text-muted"><?= lang('in_progress') . ' ' . lang('project') ?></p>
                                        <small><a href="<?= base_url() ?>admin/project"
                                                  class="small-box-footer"><?= lang('more_info') ?>
                                                <i
                                                    class="fa fa-arrow-circle-right"></i></a></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>

        <div class="clearfix visible-sm-block "></div>
        <?php
        $all_project = $this->admin_model->get_permission('tbl_project');

        $project_overdue = 0;
        if (!empty($all_project)) {
            foreach ($all_project as $v_project) {
                if (time() > strtotime($v_project->end_date) AND $v_project->progress < 100) {
                    $project_overdue += count($v_project->project_id);
                }
            }
        }
        // tasks
        $task_all_info = $this->admin_model->get_permission('tbl_task');

        $task_overdue = 0;

        if (!empty($task_all_info)):
            foreach ($task_all_info as $v_task_info):
                $due_date = $v_task_info->due_date;
                $due_time = strtotime($due_date);
                $current_time = time();
                if ($current_time > $due_time && $v_task_info->task_progress < 100) {
                    $task_overdue += count($v_task_info->task_id);
                }
            endforeach;
        endif;

        // invoice
        $all_invoices_info = $this->admin_model->get_permission('tbl_invoices');
        $invoice_overdue = 0;
        if (!empty($all_invoices_info)) {
            foreach ($all_invoices_info as $v_invoices) {
                $payment_status = $this->invoice_model->get_payment_status($v_invoices->invoices_id);
                if (strtotime($v_invoices->due_date) < time() AND $payment_status != lang('fully_paid')) {
                    $invoice_overdue += count($v_invoices->invoices_id);
                }
            }
        }
        // estimate
        $all_estimates_info = $this->admin_model->get_permission('tbl_estimates');
        $estimate_overdue = 0;
        if (!empty($all_estimates_info)) {
            foreach ($all_estimates_info as $v_estimates) {
                if (strtotime($v_estimates->due_date) < time() AND $v_estimates->status == 'Pending') {
                    $estimate_overdue += count($v_estimates->estimates_id);
                }
            }
        }
        // bugs
        $all_bugs_info = $this->admin_model->get_permission('tbl_bug');
        $bug_unconfirmed = 0;
        if (!empty($all_bugs_info)):foreach ($all_bugs_info as $key => $v_bugs):
            if ($v_bugs->bug_status == 'unconfirmed') {
                $bug_unconfirmed += count($v_bugs->bug_id);
            }
        endforeach;
        endif;
        $all_opportunity = $this->admin_model->get_permission('tbl_opportunities');
        $opportunity_overdue = 0;
        if (!empty($all_opportunity)) {
            foreach ($all_opportunity as $v_opportunity) {
                if (time() > strtotime($v_opportunity->close_date) AND $v_opportunity->probability < 100) {
                    $opportunity_overdue += count($v_opportunity->opportunities_id);
                }
            }
        }
        ?>
        <div class="col-md-12 mt-lg">
            <section class="panel panel-custom">
                <aside class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class=""><a href="#projects"
                                        data-toggle="tab"><?= lang('overdue') . ' ' . lang('project') ?>
                                <strong class="pull-right ">(<?= $project_overdue ?>)</strong>
                            </a></li>
                        <li class=""><a href="#tasks" data-toggle="tab"><?= lang('overdue') . ' ' . lang('tasks') ?>
                                <strong class="pull-right ">(<?= $task_overdue ?>)</strong>
                            </a></li>
                        <li class=""><a href="#invoice" data-toggle="tab"><?= lang('overdue') . ' ' . lang('invoice') ?>
                                <strong class="pull-right ">(<?= $invoice_overdue ?>)</strong>
                            </a></li>
                        <li class=""><a href="#estimate"
                                        data-toggle="tab"><?= lang('expired') . ' ' . lang('estimate') ?>
                                <strong class="pull-right ">(<?= $estimate_overdue ?>)</strong>
                            </a></li>
                        <li class=""><a href="#bugs"
                                        data-toggle="tab"><?= lang('unconfirmed') . ' ' . lang('bugs') ?>
                                <strong class="pull-right ">(<?= $bug_unconfirmed ?>)</strong>
                            </a></li>
                        <li class=""><a href="#recent_opportunities"
                                        data-toggle="tab"><?= lang('overdue') . ' ' . lang('opportunities') ?>
                                <strong class="pull-right ">(<?= $opportunity_overdue ?>)</strong>
                            </a></li>
                    </ul>
                    <section class="scrollable">
                        <div class="tab-content">
                            <div class="tab-pane " id="projects">
                                <table class="table table-striped m-b-none text-sm" id="DataTables">
                                    <thead>
                                    <tr>
                                        <th><?= lang('project_name') ?></th>
                                        <th><?= lang('client') ?></th>
                                        <th><?= lang('end_date') ?></th>
                                        <th><?= lang('status') ?></th>
                                        <th class="col-options no-sort"><?= lang('action') ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if (!empty($all_project)) {
                                        foreach ($all_project as $v_project):
                                            if (time() > strtotime($v_project->end_date) AND $v_project->progress < 100) {
                                                ?>
                                                <tr>
                                                    <?php
                                                    $client_info = $this->db->where('client_id', $v_project->client_id)->get('tbl_client')->row();
                                                    if (!empty($client_info)) {
                                                        if ($client_info->client_status == 1) {
                                                            $status = lang('person');
                                                        } else {
                                                            $status = lang('company');
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
                                                            <span
                                                                class="label label-danger pull-right"><?= lang('overdue') ?></span>
                                                        <?php } ?>

                                                        <div class="progress progress-xs progress-striped active">
                                                            <div
                                                                class="progress-bar progress-bar-<?php echo ($v_project->progress >= 100) ? 'success' : 'primary'; ?>"
                                                                data-toggle="tooltip"
                                                                data-original-title="<?= $v_project->progress ?>%"
                                                                style="width: <?= $v_project->progress; ?>%"></div>
                                                        </div>

                                                    </td>
                                                    <td><?= $name ?></td>

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
                                                        <?= btn_view(base_url() . 'admin/project/project_details/' . $v_project->project_id) ?>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                        endforeach;
                                    }
                                    ?>

                                    </tbody>
                                </table>
                            </div>
                            <div class="tab-pane" id="tasks">
                                <table class="table table-striped m-b-none text-sm" id="DataTables">
                                    <thead>
                                    <tr>

                                        <th><?= lang('task_name') ?></th>
                                        <th><?= lang('end_date') ?></th>
                                        <th><?= lang('progress') ?></th>
                                        <th class="col-options no-sort col-md-1"><?= lang('action') ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if (!empty($task_all_info)):foreach ($task_all_info as $v_task):
                                        $due_date = $v_task->due_date;
                                        $due_time = strtotime($due_date);
                                        $current_time = time();
                                        if ($current_time > $due_time && $v_task->task_progress < 100) {
                                            ?>
                                            <tr>
                                                <td><a class="text-info" style="<?php
                                                    if ($v_task->task_progress >= 100) {
                                                        echo 'text-decoration: line-through;';
                                                    }
                                                    ?>"
                                                       href="<?= base_url() ?>admin/tasks/view_task_details/<?= $v_task->task_id ?>"><?php echo $v_task->task_name; ?></a>
                                                </td>
                                                <td>
                                                    <?= strftime(config_item('date_format'), strtotime($due_date)) ?>
                                                    <?php if ($current_time > $due_time && $v_task->task_progress < 100) { ?>
                                                        <span class="label label-danger"><?= lang('overdue') ?></span>
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
                                                             data-size="50"
                                                             data-animate="2000">
                                                        <span class="small text-muted"><?= $v_task->task_progress ?>
                                                            %</span>
                                                        </div>
                                                    </div>

                                                </td>

                                                <td><?= btn_view('admin/tasks/view_task_details/' . $v_task->task_id) ?></td>
                                            </tr>
                                            <?php
                                        }
                                    endforeach; ?>
                                    <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="tab-pane" id="invoice">
                                <table class="table table-striped m-b-none text-sm" id="DataTables">
                                    <thead>
                                    <tr>
                                        <th><?= lang('invoice') ?></th>
                                        <th class="col-date"><?= lang('due_date') ?></th>
                                        <th><?= lang('client_name') ?></th>
                                        <th class="col-currency"><?= lang('due_amount') ?></th>
                                        <th><?= lang('status') ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    <?php
                                    if (!empty($all_invoices_info)) {
                                        foreach ($all_invoices_info as $v_invoices) {
                                            $payment_status = $this->invoice_model->get_payment_status($v_invoices->invoices_id);
                                            if (strtotime($v_invoices->due_date) < time() AND $payment_status != lang('fully_paid')) {
                                                if ($payment_status == lang('fully_paid')) {
                                                    $invoice_status = lang('fully_paid');
                                                    $label = "success";
                                                } elseif ($v_invoices->emailed == 'Yes') {
                                                    $invoice_status = lang('sent');
                                                    $label = "info";
                                                } else {
                                                    $invoice_status = lang('draft');
                                                    $label = "default";
                                                }
                                                ?>
                                                <tr>
                                                    <td><a class="text-info"
                                                           href="<?= base_url() ?>admin/invoice/manage_invoice/invoice_details/<?= $v_invoices->invoices_id ?>"><?= $v_invoices->reference_no ?>

                                                        </a>
                                                    </td>
                                                    <td><?= strftime(config_item('date_format'), strtotime($v_invoices->due_date)) ?>
                                                        <span
                                                            class="label label-danger "><?= lang('overdue') ?></span>
                                                    </td>
                                                    <?php
                                                    $client_info = $this->invoice_model->check_by(array('client_id' => $v_invoices->client_id), 'tbl_client');

                                                    if ($client_info->client_status == 1) {
                                                        $status = 'Person';
                                                    } else {
                                                        $status = 'Company';
                                                    }
                                                    ?>
                                                    <td><?= $client_info->name . ' (' . $status . ')'; ?></td>
                                                    <?php $currency = $this->invoice_model->client_currency_sambol($v_invoices->client_id); ?>
                                                    <td><?= display_money($this->invoice_model->calculate_to('invoice_due', $v_invoices->invoices_id), $curency->symbol); ?></td>
                                                    <td><span
                                                            class="label label-<?= $label ?>"><?= $invoice_status ?></span>
                                                        <?php if ($v_invoices->recurring == 'Yes') { ?>
                                                            <span data-toggle="tooltip" data-placement="top"
                                                                  title="<?= lang('recurring') ?>"
                                                                  class="label label-primary"><i
                                                                    class="fa fa-retweet"></i></span>
                                                        <?php } ?>

                                                    </td>

                                                </tr>
                                                <?php
                                            }
                                        }
                                    }
                                    ?>

                                    </tbody>
                                </table>
                            </div>
                            <div class="tab-pane" id="estimate">
                                <table class="table table-striped m-b-none text-sm" id="DataTables">
                                    <thead>
                                    <tr>
                                        <th><?= lang('estimate') ?></th>
                                        <th><?= lang('due_date') ?></th>
                                        <th><?= lang('client_name') ?></th>
                                        <th><?= lang('amount') ?></th>
                                        <th><?= lang('status') ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if (!empty($all_estimates_info)) {
                                        foreach ($all_estimates_info as $v_estimates) {
                                            if (strtotime($v_estimates->due_date) < time() AND $v_estimates->status == 'Pending') {
                                                if ($v_estimates->status == 'Pending') {
                                                    $label = "info";
                                                } elseif ($v_estimates->status == 'Accepted') {
                                                    $label = "success";
                                                } else {
                                                    $label = "danger";
                                                }
                                                ?>
                                                <tr>
                                                    <td>
                                                        <a class="text-info"
                                                           href="<?= base_url() ?>admin/estimates/index/estimates_details/<?= $v_estimates->estimates_id ?>"><?= $v_estimates->reference_no ?></a>
                                                    </td>
                                                    <td><?= strftime(config_item('date_format'), strtotime($v_estimates->due_date)) ?>
                                                        <?php
                                                        if (strtotime($v_estimates->due_date) < time() AND $v_estimates->status == 'Pending') { ?>
                                                            <span
                                                                class="label label-danger "><?= lang('expired') ?></span>
                                                        <?php }
                                                        ?>
                                                    </td>
                                                    <?php
                                                    $client_info = $this->estimates_model->check_by(array('client_id' => $v_estimates->client_id), 'tbl_client');
                                                    if ($client_info->client_status == 1) {
                                                        $status = lang('person');
                                                    } else {
                                                        $status = lang('company');
                                                    }
                                                    ?>
                                                    <td><?= $client_info->name . ' (' . $status . ')'; ?></td>
                                                    <?php $currency = $this->estimates_model->client_currency_sambol($v_estimates->client_id); ?>
                                                    <td><?= display_money($this->estimates_model->estimate_calculation('estimate_amount', $v_estimates->estimates_id), $curency->symbol); ?></td>
                                                    <td><span
                                                            class="label label-<?= $label ?>"><?= lang(strtolower($v_estimates->status)) ?></span>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="tab-pane" id="bugs">
                                <table class="table table-striped m-b-none text-sm" id="DataTables">
                                    <thead>
                                    <tr>
                                        <th><?= lang('bug_title') ?></th>
                                        <th><?= lang('status') ?></th>
                                        <th><?= lang('priority') ?></th>
                                        <?php if ($this->session->userdata('user_type') == '1') { ?>
                                            <th><?= lang('reporter') ?></th>
                                        <?php } ?>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $all_bugs_info = $this->db->get('tbl_bug')->result();
                                    if (!empty($all_bugs_info)):foreach ($all_bugs_info as $key => $v_bugs):
                                        if ($v_bugs->bug_status == 'unconfirmed') {
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
                                                       href="<?= base_url() ?>admin/bugs/view_bug_details/<?= $v_bugs->bug_id ?>"><?php echo $v_bugs->bug_title; ?></a>
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
                                                <td>
                                                    <?php
                                                    if ($v_bugs->priority == 'High') {
                                                        $plabel = 'danger';
                                                    } elseif ($v_bugs->priority == 'Medium') {
                                                        $plabel = 'info';
                                                    } else {
                                                        $plabel = 'primary';
                                                    }
                                                    ?>
                                                    <span
                                                        class="badge btn-<?= $plabel ?>"><?= ucfirst($v_bugs->priority) ?></span>
                                                </td>
                                                <?php if ($this->session->userdata('user_type') == '1') { ?>
                                                    <td>
                                                    <span
                                                        class="badge btn-<?= $badge ?> "><?= $reporter->username ?></span>
                                                    </td>
                                                <?php } ?>
                                            </tr>
                                            <?php
                                        } endforeach; ?>
                                    <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="tab-pane" id="recent_opportunities">
                                <table class="table table-striped m-b-none text-sm" id="DataTables">
                                    <thead>
                                    <tr>
                                        <th><?= lang('opportunity_name') ?></th>
                                        <th><?= lang('state') ?></th>
                                        <th><?= lang('stages') ?></th>
                                        <th><?= lang('expected_revenue') ?></th>
                                        <th><?= lang('next_action') ?></th>
                                        <th><?= lang('next_action_date') ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $all_opportunity = $this->db->get('tbl_opportunities')->result();
                                    if (!empty($all_opportunity)) {
                                        foreach ($all_opportunity as $v_opportunity) {
                                            if (time() > strtotime($v_opportunity->close_date) AND $v_opportunity->probability < 100) {
                                                $opportunities_state_info = $this->db->where('opportunities_state_reason_id', $v_opportunity->opportunities_state_reason_id)->get('tbl_opportunities_state_reason')->row();
                                                if ($opportunities_state_info->opportunities_state == 'open') {
                                                    $label = 'primary';
                                                } elseif ($opportunities_state_info->opportunities_state == 'won') {
                                                    $label = 'success';
                                                } elseif ($opportunities_state_info->opportunities_state == 'suspended') {
                                                    $label = 'info';
                                                } else {
                                                    $label = 'danger';
                                                }
                                                $currency = $this->db->where('code', config_item('default_currency'))->get('tbl_currencies')->row();
                                                ?>
                                                <tr>
                                                    <td>
                                                        <a class="text-info"
                                                           href="<?= base_url() ?>admin/opportunities/opportunity_details/<?= $v_opportunity->opportunities_id ?>"><?= $v_opportunity->opportunity_name ?></a>
                                                        <?php if (time() > strtotime($v_opportunity->close_date) AND $v_opportunity->probability < 100) { ?>
                                                            <span
                                                                class="label label-danger pull-right"><?= lang('overdue') ?></span>
                                                        <?php } ?>
                                                        <div class="progress progress-xs progress-striped active">
                                                            <div
                                                                class="progress-bar progress-bar-<?php echo ($v_opportunity->probability >= 100) ? 'success' : 'primary'; ?>"
                                                                data-toggle="tooltip"
                                                                data-original-title="<?= lang('probability') . ' ' . $v_opportunity->probability ?>%"
                                                                style="width: <?= $v_opportunity->probability ?>%"></div>
                                                        </div>
                                                    </td>
                                                    <td><?= lang($v_opportunity->stages) ?></td>
                                                    <td><span
                                                            class="label label-<?= $label ?>"><?= lang($opportunities_state_info->opportunities_state) ?></span>
                                                    </td>
                                                    <td><?php
                                                        if (!empty($v_opportunity->expected_revenue)) {
                                                            echo display_money($v_opportunity->expected_revenue, $currency->symbol);
                                                        }
                                                        ?></td>
                                                    <td><?= $v_opportunity->next_action ?></td>
                                                    <td><?= strftime(config_item('date_format'), strtotime($v_opportunity->next_action_date)) ?></td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </section>
                </aside>
                <?php if ($this->session->userdata('user_type') == '1') { ?>
                    <footer class="panel-footer bg-white no-padder">
                        <div class="row text-center no-gutter">
                            <div class="col-xs-2 b-r b-light">
                                <span
                                    class="h4 font-bold m-t block"><?= count($this->db->where('project_status', 'completed')->get('tbl_project')->result()) ?>
                                </span>
                                <small class="text-muted m-b block"><?= lang('complete_projects') ?></small>
                            </div>
                            <div class="col-xs-2 b-r b-light">
                                <span
                                    class="h4 font-bold m-t block"><?= count($this->db->where('task_status', 'completed')->get('tbl_task')->result()) ?>
                                </span>
                                <small class="text-muted m-b block"><?= lang('complete_tasks') ?></small>
                            </div>
                            <div class="col-xs-2">
                                <span
                                    class="h4 font-bold m-t block"><?=
                                    display_money($this->db->select_sum('total_cost')->get('tbl_items')->row()->total_cost, $curency->symbol);
                                    ?>
                                </span>
                                <small
                                    class="text-muted m-b block"><?= lang('total') . ' ' . lang('invoice_amount') ?></small>

                            </div>
                            <div class="col-xs-2">
                                <span
                                    class="h4 font-bold m-t block"><?=
                                    display_money($this->db->select_sum('total_cost')->get('tbl_estimate_items')->row()->total_cost, $curency->symbol);
                                    ?>
                                </span>
                                <small
                                    class="text-muted m-b block"><?= lang('total') . ' ' . lang('estimate') ?></small>

                            </div>
                            <div class="col-xs-2">
                                <span
                                    class="h4 font-bold m-t block"><?= count($this->db->where('bug_status', 'resolved')->get('tbl_bug')->result()) ?>
                                </span>
                                <small class="text-muted m-b block"><?= lang('resolved') . ' ' . lang('bugs') ?></small>

                            </div>
                            <div class="col-xs-2">
                                <span
                                    class="h4 font-bold m-t block"><?= count($this->db->where('stages', 'won')->get('tbl_opportunities')->result()) ?>
                                </span>
                                <small
                                    class="text-muted m-b block"><?= lang('won') . ' ' . lang('opportunities') ?></small>

                            </div>
                        </div>
                    </footer>
                <?php } ?>
            </section>
        </div>
        <?php
        $my_project = $this->admin_model->my_permission('tbl_project');
        $my_task = $this->admin_model->my_permission('tbl_task');
        ?>

        <div class="col-md-12" style="margin-top: 20px;">
            <div class="panel panel-custom">
                <header class="panel-heading">
                    <h3 class="panel-title"><?= lang('goal') . ' ' . lang('report') ?></h3>
                </header>
                <div class="panel-body">
                    <p class="text-center">
                    <form role="form" id="form" action="<?php echo base_url(); ?>admin/dashboard/index/goal_month"
                          method="post" class="form-horizontal form-groups-bordered">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Select <?= lang('month') ?><span
                                    class="required">*</span></label>
                            <div class="col-sm-5">
                                <div class="input-group">
                                    <input type="text" name="goal_month" value="<?php
                                    if (!empty($goal_month)) {
                                        echo $goal_month;
                                    }
                                    ?>" class="form-control monthyear"><span class="input-group-addon"><a href="#"><i
                                                class="fa fa-calendar"></i></a></span>
                                </div>
                            </div>
                            <button type="submit" data-toggle="tooltip" data-placement="top" title="Search"
                                    class="btn btn-custom"><i class="fa fa-search"></i></button>
                        </div>
                    </form>
                    </p>
                    <!--End select input year -->
                    <!--Sales Chart Canvas -->
                    <div id="goal_report"></div>
                </div><!-- ./box-body -->

            </div>
        </div>
        <div class="col-md-6" style="margin-top: 20px;">
            <div class="panel panel-custom" style="height: 437px;overflow-y: scroll;">
                <header class="panel-heading mb0">
                    <h3 class="panel-title"><?= lang('my_project') ?></h3>
                </header>
                <div class="">
                    <table class="table table-striped m-b-none text-sm">
                        <thead>
                        <tr>
                            <th><?= lang('project_name') ?></th>
                            <th><?= lang('end_date') ?></th>
                            <th class="col-sm-1"><?= lang('status') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if (!empty($my_project)) {
                            foreach ($my_project as $v_my_project):
                                if ($v_my_project->project_status != 'completed' AND $v_my_project->progress < 100) {
                                    ?>
                                    <tr>

                                        <td>
                                            <a class="text-info"
                                               href="<?= base_url() ?>admin/project/project_details/<?= $v_my_project->project_id ?>"><?= $v_my_project->project_name ?></a>
                                            <?php if (time() > strtotime($v_my_project->end_date) AND $v_my_project->progress < 100) { ?>
                                                <span
                                                    class="label label-danger pull-right"><?= lang('overdue') ?></span>
                                            <?php } ?>

                                            <div class="progress progress-xs progress-striped active">
                                                <div
                                                    class="progress-bar progress-bar-<?php echo ($v_my_project->progress >= 100) ? 'success' : 'primary'; ?>"
                                                    data-toggle="tooltip"
                                                    data-original-title="<?= $v_my_project->progress ?>%"
                                                    style="width: <?= $v_my_project->progress; ?>%"></div>
                                            </div>

                                        </td>
                                        <td><?= strftime(config_item('date_format'), strtotime($v_my_project->end_date)) ?></td>

                                        <td><?php
                                            if (!empty($v_my_project->project_status)) {
                                                if ($v_my_project->project_status == 'completed') {
                                                    $status = "<span class='label label-success'>" . lang($v_my_project->project_status) . "</span>";
                                                } elseif ($v_my_project->project_status == 'in_progress') {
                                                    $status = "<span class='label label-primary'>" . lang($v_my_project->project_status) . "</span>";
                                                } elseif ($v_my_project->project_status == 'cancel') {
                                                    $status = "<span class='label label-danger'>" . lang($v_my_project->project_status) . "</span>";
                                                } else {
                                                    $status = "<span class='label label-warning'>" . lang($v_my_project->project_status) . "</span>";
                                                }
                                                echo $status;
                                            }
                                            ?> </td>
                                    </tr>
                                    <?php
                                }
                            endforeach;
                        }
                        ?>

                        </tbody>
                    </table>
                </div><!-- ./box-body -->

            </div>
        </div>
        <?php include_once 'assets/admin-ajax.php'; ?>
        <div class="col-md-6" style="margin-top: 20px;">

            <div class="panel panel-custom" style="height: 437px;overflow-y: scroll;">
                <header class="panel-heading mb0">
                    <h3 class="panel-title"><?= lang('my_tasks') ?></h3>
                </header>
                <div class="">
                    <table class="table table-striped m-b-none text-sm">
                        <thead>
                        <tr>
                            <th data-check-all>

                            </th>
                            <th><?= lang('task_name') ?></th>
                            <th><?= lang('end_date') ?></th>
                            <th class="col-sm-1"><?= lang('status') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if (!empty($my_task)):foreach ($my_task as $v_my_task):


                            if ($v_my_task->task_status == 'not_started' || $v_my_task->task_status == 'in_progress' || $v_my_task->task_progress < 100) {
                                $due_date = $v_my_task->due_date;
                                $due_time = strtotime($due_date);
                                $current_time = time();
                                ?>
                                <tr>
                                    <td class="col-sm-1">
                                        <div class="complete checkbox c-checkbox">
                                            <label>
                                                <input type="checkbox" data-id="<?= $v_my_task->task_id ?>"
                                                       style="position: absolute;" <?php
                                                if ($v_my_task->task_progress >= 100) {
                                                    echo 'checked';
                                                }
                                                ?>>
                                                <span class="fa fa-check"></span>
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <a class="text-info"
                                           href="<?= base_url() ?>admin/tasks/view_task_details/<?= $v_my_task->task_id ?>">
                                            <?php echo $v_my_task->task_name; ?></a>
                                        <?php if ($current_time > $due_time && $v_my_task->task_progress < 100) { ?>
                                            <span
                                                class="label label-danger pull-right"><?= lang('overdue') ?></span>
                                        <?php } ?>

                                        <div class="progress progress-xs progress-striped active">
                                            <div
                                                class="progress-bar progress-bar-<?php echo ($v_my_task->task_progress >= 100) ? 'success' : 'primary'; ?>"
                                                data-toggle="tooltip"
                                                data-original-title="<?= $v_my_task->task_progress ?>%"
                                                style="width: <?= $v_project->progress; ?>%"></div>
                                        </div>

                                    </td>

                                    <td>
                                        <?= strftime(config_item('date_format'), strtotime($due_date)) ?>
                                    </td>
                                    <td>
                                        <?php
                                        if ($v_my_task->task_status == 'completed') {
                                            $label = 'success';
                                        } elseif ($v_my_task->task_status == 'not_started') {
                                            $label = 'info';
                                        } elseif ($v_my_task->task_status == 'deferred') {
                                            $label = 'danger';
                                        } else {
                                            $label = 'warning';
                                        }
                                        ?>
                                        <span
                                            class="label label-<?= $label ?>"><?= lang($v_my_task->task_status) ?> </span>
                                    </td>

                                </tr>
                                <?php
                            }
                        endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div><!-- ./box-body -->

            </div>
        </div>

        <?php if ($this->session->userdata('user_type') == '1') { ?>
            <div class="col-md-6" style="margin-top: 20px;">
                <div class="panel panel-custom">
                    <header class="panel-heading">
                        <h3 class="panel-title"><?= lang('payments_report') ?></h3>
                    </header>
                    <div class="panel-body">
                        <div class="text-center">
                            <form role="form" id="form" action="<?php echo base_url(); ?>admin/dashboard/index/payments"
                                  method="post" class="form-horizontal form-groups-bordered">
                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><?= lang('select') . ' ' . lang('year') ?>
                                        <span
                                            class="required">*</span></label>
                                    <div class="col-sm-5">
                                        <div class="input-group">
                                            <input type="text" name="yearly" value="<?php
                                            if (!empty($yearly)) {
                                                echo $yearly;
                                            }
                                            ?>" class="form-control years"><span class="input-group-addon"><a
                                                    href="#"><i
                                                        class="fa fa-calendar"></i></a></span>
                                        </div>
                                    </div>
                                    <button type="submit" data-toggle="tooltip" data-placement="top" title="Search"
                                            class="btn btn-custom"><i class="fa fa-search"></i></button>
                                </div>
                            </form>
                        </div>
                        <canvas id="yearly_report" class="col-sm-12" style="max-height:310px;"></canvas>
                    </div><!-- ./box-body -->
                </div>
            </div>
            <div class="col-md-6" style="margin-top: 20px;">
                <!-- DONUT CHART -->
                <div class="panel panel-custom">
                    <header class="panel-heading">
                        <h3 class="panel-title"><?= lang('income_expense') ?></h3>
                    </header>
                    <div class="panel-body">
                        <p class="text-center">
                        <form role="form" id="form" action="<?php echo base_url(); ?>admin/dashboard/index/month"
                              method="post" class="form-horizontal form-groups-bordered">
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?= lang('select') . ' ' . lang('month') ?><span
                                        class="required">*</span></label>
                                <div class="col-sm-5">
                                    <div class="input-group">
                                        <input type="text" name="month" value="<?php
                                        if (!empty($month)) {
                                            echo $month;
                                        }
                                        ?>" class="form-control monthyear"><span class="input-group-addon"><a
                                                href="#"><i
                                                    class="fa fa-calendar"></i></a></span>
                                    </div>
                                </div>
                                <button type="submit" data-toggle="tooltip" data-placement="top" title="Search"
                                        class="btn btn-custom"><i class="fa fa-search"></i></button>
                            </div>
                        </form>
                        </p>
                        <div class="chart" id="sales-chart" style="height: 300px; position: relative;"></div>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div>
            <div class="col-md-6" style="margin-top: 20px;">
                <div class="panel panel-custom">
                    <header class="panel-heading">
                        <h3 class="panel-title"><?= lang('income_report') ?></h3>
                    </header>
                    <div class="panel-body">
                        <p class="text-center">
                        <form role="form" id="form" action="<?php echo base_url(); ?>admin/dashboard/index/Income"
                              method="post" class="form-horizontal form-groups-bordered">
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?= lang('select') . ' ' . lang('year') ?><span
                                        class="required">*</span></label>
                                <div class="col-sm-5">
                                    <div class="input-group">
                                        <input type="text" name="Income" value="<?php
                                        if (!empty($Income)) {
                                            echo $Income;
                                        }
                                        ?>" class="form-control years"><span class="input-group-addon"><a href="#"><i
                                                    class="fa fa-calendar"></i></a></span>
                                    </div>
                                </div>
                                <button type="submit" data-toggle="tooltip" data-placement="top" title="Search"
                                        class="btn btn-custom"><i class="fa fa-search"></i></button>
                            </div>
                        </form>
                        </p>
                        <!--End select input year -->
                        <div class="chart-responsive">
                            <!--Sales Chart Canvas -->
                            <canvas id="income" class="col-sm-12"></canvas>
                        </div><!-- /.chart-responsive -->
                    </div><!-- ./box-body -->

                </div>
            </div>
            <div class="col-md-6" style="margin-top: 20px;">

                <div class="panel panel-custom">
                    <header class="panel-heading">
                        <h3 class="panel-title"><?= lang('expense_report') ?></h3>
                    </header>
                    <div class="panel-body">
                        <p class="text-center">
                        <form role="form" id="form" action="<?php echo base_url(); ?>admin/dashboard" method="post"
                              class="form-horizontal form-groups-bordered">
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?= lang('select') . ' ' . lang('year') ?><span
                                        class="required">*</span></label>
                                <div class="col-sm-5">
                                    <div class="input-group">
                                        <input type="text" name="year" value="<?php
                                        if (!empty($year)) {
                                            echo $year;
                                        }
                                        ?>" class="form-control years"><span class="input-group-addon"><a href="#"><i
                                                    class="fa fa-calendar"></i></a></span>
                                    </div>
                                </div>
                                <button type="submit" data-toggle="tooltip" data-placement="top" title="Search"
                                        class="btn btn-custom"><i class="fa fa-search"></i></button>
                            </div>
                        </form>
                        </p>
                        <!--End select input year -->
                        <div class="chart-responsive">
                            <!--Sales Chart Canvas -->
                            <canvas id="buyers" class="col-sm-12"></canvas>
                        </div><!-- /.chart-responsive -->
                    </div><!-- ./box-body -->

                </div>
            </div>

            <div class="wrap-fpanel col-sm-6 " style="margin-top: 20px;">
                <section class="panel panel-custom">
                    <header class="panel-heading">
                        <h3 class="panel-title"><?= lang('recently_paid_invoices') ?></h3>
                    </header>
                    <div class="panel-body inv-slim-scroll">
                        <div class="list-group bg-white">
                            <?php
                            $recently_paid = $this->db
                                ->order_by('created_date', 'desc')
                                ->get('tbl_payments', 5)
                                ->result();
                            if (!empty($recently_paid)) {
                                foreach ($recently_paid as $key => $v_paid) {

                                    $invoices_info = $this->db->where(array('invoices_id' => $v_paid->invoices_id))->get('tbl_invoices')->row();

                                    $payment_method = $this->db->where(array('payment_methods_id' => $v_paid->payment_method))->get('tbl_payment_methods')->row();

                                    $currency = $this->admin_model->client_currency_sambol($invoices_info->client_id);

                                    if ($v_paid->payment_method == '1') {
                                        $label = 'success';
                                    } elseif ($v_paid->payment_method == '2') {
                                        $label = 'danger';
                                    } else {
                                        $label = 'dark';
                                    }
                                    ?>
                                    <a href="<?= base_url() ?>admin/invoice/manage_invoice/invoice_details/<?= $v_paid->invoices_id ?>"
                                       class="list-group-item">
                                        <?= $invoices_info->reference_no ?> -
                                        <small
                                            class="text-muted"><?= display_money($v_paid->amount, $curency->symbol) ?>
                                            <span
                                                class="label label-<?= $label ?> pull-right"><?= $payment_method->method_name; ?></span>
                                        </small>
                                    </a>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <small><?= lang('total_receipts') ?>: <strong>
                                <?php
                                if (!empty($invoce_total)) {
                                    if (!empty($invoce_total['paid'])) {
                                        foreach ($invoce_total['paid'] as $v_total) {
                                            $total_paid [] = display_money($v_total, $curency->symbol);
                                        }
                                        echo implode(", ", $total_paid);
                                    } else {
                                        echo '0.00';
                                    }
                                } else {
                                    echo '0.00';
                                }
                                ?>
                            </strong></small>
                    </div>
                </section>

            </div>
        <?php } ?>
        <div class="wrap-fpanel col-sm-6 " style="margin-top: 20px;">

            <div class="panel panel-custom">
                <header class="panel-heading">
                    <h3 class="panel-title"><?= lang('recent_activities') ?></h3>
                </header>
                <div class="panel-body">
                    <section class="comment-list block">
                        <section class="slim-scroll" style="height:400px;overflow-x: scroll">
                            <?php
                            $activities = $this->db
                                ->order_by('activity_date', 'desc')
                                ->get('tbl_activities', 10)
                                ->result();
                            if (!empty($activities)) {
                                foreach ($activities as $v_activities) {
                                    $profile_info = $this->db->where(array('user_id' => $v_activities->user))->get('tbl_account_details')->row();
                                    ?>
                                    <article id="comment-id-1" class="comment-item" style="font-size: 11px;">
                                        <div class="pull-left recect_task  ">
                                            <a class="pull-left recect_task  ">
                                                <?php if (!empty($profile_info)) {
                                                    ?>
                                                    <img style="width: 30px;margin-left: 18px;
                                                             height: 29px;
                                                             border: 1px solid #aaa;"
                                                         src="<?= base_url() . $profile_info->avatar ?>"
                                                         class="img-circle">
                                                <?php } ?>
                                            </a>
                                        </div>
                                        <section class="comment-body m-b-lg">
                                            <header class=" ">
                                                <strong>
                                                    <?= $profile_info->fullname ?></strong>
                                                    <span class="text-muted text-xs"> <?php
                                                        $today = time();
                                                        $activity_day = strtotime($v_activities->activity_date);
                                                        echo $this->admin_model->get_time_different($today, $activity_day);
                                                        ?> <?= lang('ago') ?>
                                                    </span>
                                            </header>
                                            <div>
                                                <?= lang($v_activities->activity) ?>
                                                <strong> <?= $v_activities->value1 . ' ' . $v_activities->value2 ?></strong>
                                            </div>
                                            <hr/>
                                        </section>
                                    </article>


                                    <?php
                                }
                            }
                            ?>
                        </section>
                </div>
            </div>
        </div>
    </div>

    <?php
    if (!empty($goal_report)) {
        foreach ($goal_report as $type_id => $v_goal_report) {
            $total_target = 0;
            $total_achievement = 0;
            $goal_type = $this->db->where('goal_type_id', $type_id)->get('tbl_goal_type')->row()->type_name;

            foreach ($v_goal_report['target'] as $v_target) {
                $total_target += $v_target;
            }
            foreach ($v_goal_report['achievement'] as $v_achievement) {
                $total_achievement += $v_achievement['achievement'];
            }


            ?>
        <?php }

    }

    ?>

    <!-- Morris.js charts -->
    <script src="<?php echo base_url() ?>assets/plugins/raphael/raphael.js"></script>
    <script src="<?php echo base_url() ?>assets/plugins/morris/morris.js"></script>

    <!--Calendar-->

    <!-- / Chart.js Script -->
    <script src="<?php echo base_url(); ?>asset/js/chart.min.js" type="text/javascript"></script>
    <script type="text/javascript">
        (function (window, document, $, undefined) {
            $(function () {
                if (typeof Morris === 'undefined') return;
                var chartdata = [
                    <?php
                    if (!empty($goal_report)) {
                    foreach ($goal_report as $type_id => $v_goal_report) {
                    $total_target = 0;
                    $total_achievement = 0;
                    $goal_type = $this->db->where('goal_type_id', $type_id)->get('tbl_goal_type')->row()->type_name;
                    foreach ($v_goal_report['target'] as $v_target) {
                        $total_target += $v_target;
                    }
                    foreach ($v_goal_report['achievement'] as $v_achievement) {
                        $total_achievement += $v_achievement['achievement'];
                    }
                    ?>
                    {y: "<?= lang($goal_type)?>", a: <?= $total_target?>, b: <?= $total_achievement?>},
                    <?php }
                    }?>
                ];
                // Bar Chart
                // -----------------------------------
                new Morris.Bar({
                    element: 'goal_report',
                    data: chartdata,
                    xkey: 'y',
                    ykeys: ["a", "b"],
                    labels: ["<?php echo lang('achievement')?>", "<?php echo lang('achievements')?>"],
                    xLabelMargin: 2,
                    barColors: ['#23b7e5', '#f05050'],
                    resize: true,
                    xLabelAngle: 60,
                    hideHover: 'auto'
                });
            });
        })(window, document, window.jQuery);
    </script>


    <script>
        // line chart data
        var buyerData = {

            labels: [
                <?php
                // yearle result name = month name
                foreach ($all_income as $name => $v_income):
                $month_name = date('F', strtotime($year . '-' . $name)); // get full name of month by date query
                ?>
                "<?php echo $month_name; ?>", // echo the whole month of the year
                <?php endforeach; ?>
            ],
            datasets: [
                {
                    fillColor: "rgba(172,194,132,0.4)",
                    strokeColor: "#ACC26D",
                    pointColor: "#fff",
                    pointStrokeColor: "#9DB86D",
                    data: [
                        <?php
                        // get monthly result report
                        foreach ($all_income as $v_income):
                        ?>
                        "<?php
                            if (!empty($v_income)) { // if the report result is exist
                                $total_income = 0;
                                foreach ($v_income as $income) {
                                    $total_income += $income->amount;
                                }

                                echo $total_income; // view the total report in a  month
                            }
                            ?>",
                        <?php
                        endforeach;
                        ?>
                    ]
                }
            ]
        }

        // get line chart canvas
        var buyers = document.getElementById('income').getContext('2d');
        // draw line chart
        new Chart(buyers).Line(buyerData);</script>
    <script>
        // line chart data
        var buyerData = {

            labels: [
                <?php
                // yearle result name = month name
                foreach ($all_expense as $name => $v_expense):
                $month_name = date('F', strtotime($year . '-' . $name)); // get full name of month by date query
                ?>
                "<?php echo $month_name; ?>", // echo the whole month of the year
                <?php endforeach; ?>
            ],
            datasets: [
                {
                    fillColor: "rgba(172,194,132,0.4)",
                    strokeColor: "#ACC26D",
                    pointColor: "#fff",
                    pointStrokeColor: "#9DB86D",
                    data: [
                        <?php
                        // get monthly result report
                        foreach ($all_expense as $v_expense):
                        ?>
                        "<?php
                            if (!empty($v_expense)) { // if the report result is exist
                                $total_expense = 0;
                                foreach ($v_expense as $exoense) {
                                    $total_expense += $exoense->amount;
                                }
                                echo $total_expense; // view the total report in a  month
                            }
                            ?>",
                        <?php
                        endforeach;
                        ?>
                    ]
                }
            ]
        }

        // get line chart canvas
        var buyers = document.getElementById('buyers').getContext('2d');
        // draw line chart
        new Chart(buyers).Line(buyerData);</script>
    <script>
        // line chart data
        var buyerData = {

            labels: [
                <?php
                // yearle result name = month name
                for ($i = 1; $i <= 12; $i++) {
                $month_name = date('F', strtotime($year . '-' . $i)); // get full name of month by date query
                ?>
                "<?php echo $month_name; ?>", // echo the whole month of the year
                <?php }; ?>
            ],
            datasets: [
                {
                    fillColor: "rgba(172,194,132,0.4)",
                    strokeColor: "#ACC26D",
                    pointColor: "#fff",
                    pointStrokeColor: "#9DB86D",
                    data: [
                        <?php
                        // get monthly result report
                        foreach ($yearly_overview as $v_overview):
                        ?>
                        "<?php
                            echo $v_overview; // view the total report in a  month
                            ?>",
                        <?php
                        endforeach;
                        ?>
                    ]
                }
            ]
        }

        // get line chart canvas
        var buyers = document.getElementById('yearly_report').getContext('2d');
        // draw line chart
        new Chart(buyers).Line(buyerData);</script>
    <script type="text/javascript">
        $(function () {

            "use strict";
            //DONUT CHART
            var donut = new Morris.Donut({
                element: 'sales-chart',
                resize: true,
                colors: ["#00a65a", "#f56954"],
                data: [
                    {
                        label: "<?= lang('Income') ?>", value:
                        <?php
                        $total_vincome = 0;
                        if (!empty($income_expense)):foreach ($income_expense as $v_income_expense):
                        if ($v_income_expense->type == 'Income') {

                        $total_vincome += $v_income_expense->amount;
                        ?>

                        <?php
                        }
                        endforeach;
                        endif;
                        echo $total_vincome;
                        ?>
                    },
                    {
                        label: "<?= lang('Expense') ?>", value: <?php
                        $total_vexpense = 0;
                        if (!empty($income_expense)):foreach ($income_expense as $v_income_expense):
                        if ($v_income_expense->type == 'Expense') {
                        $total_vexpense += $v_income_expense->amount;
                        ?>

                        <?php
                        }
                        endforeach;
                        endif;
                        echo $total_vexpense;
                        ?>},
                ],
                hideHover: 'auto'
            });
        });
    </script>