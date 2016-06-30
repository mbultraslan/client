<?= message_box('success'); ?>
<div class="panel panel-custom">
    <header class="panel-heading "><?= lang('department') ?></header>
    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th><?= lang('department_name') ?></th>
                    <th><?= lang('action') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php
                if (!empty($all_dept_info)) {
                    foreach ($all_dept_info as $v_dept_info) {
                        ?>
                        <tr>
                            <td>
                                <?php
                                $id = $this->uri->segment(5);
                                if (!empty($id) && $id == $v_dept_info->departments_id) { ?>
                                <form method="post" action="<?= base_url() ?>admin/settings/department/update_dept/<?php
                                if (!empty($dept_info)) {
                                    echo $dept_info->departments_id;
                                }
                                ?>" class="form-horizontal">
                                    <input type="text" name="deptname" value="<?php
                                    if (!empty($dept_info)) {
                                        echo $dept_info->deptname;
                                    }
                                    ?>" class="form-control" placeholder="<?= lang('department_name') ?>" required>
                                    <?php } else {
                                        echo $v_dept_info->deptname;
                                    }
                                    ?>
                            </td>
                            <td>
                                <?php
                                $id = $this->uri->segment(5);
                                if (!empty($id) && $id == $v_dept_info->departments_id) { ?>
                                    <?= btn_update() ?>
                                    </form>
                                    <?= btn_cancel('admin/settings/department/') ?>
                                <?php } else { ?>
                                    <?= btn_edit('admin/settings/department/edit_dept/' . $v_dept_info->departments_id) ?>
                                    <?= btn_delete('admin/settings/delete_dept/' . $v_dept_info->departments_id) ?>
                                <?php }
                                ?>
                            </td>
                        </tr>
                        <?php
                    }
                }
                ?>
                <form method="post" action="<?= base_url() ?>admin/settings/department/update_dept" class="form-horizontal">
                    <tr>
                        <td><input type="text" name="deptname" class="form-control" placeholder="<?= lang('department_name') ?>" required></td>
                        <td><?= btn_add() ?></td>
                    </tr>
                </form>
                </tbody>
            </table>
        </div>
    </div>
</div>
