<?php $this->load->view('admin/components/htmlheader');
$opened = $this->session->userdata('opened');
$this->session->unset_userdata('opened');
?>

<body class="<?php if (!empty($opened)) {
    echo 'offsidebar-open';
} ?> <?= config_item('aside-float') . ' ' . config_item('aside-collapsed') . ' ' . config_item('layout-boxed') . ' ' . config_item('layout-fixed') ?>">
<div class="wrapper">
    <!-- top navbar-->
    <?php $this->load->view('admin/components/header'); ?>
    <!-- sidebar-->
    <?php $this->load->view('admin/components/sidebar'); ?>
    <!-- offsidebar-->
    <?php $this->load->view('admin/components/offsidebar'); ?>
    <!-- Main section-->
    <section>
        <!-- Page content-->
        <div class="content-wrapper">
            <div class="content-heading">
                <?php echo $this->breadcrumbs->build_breadcrumbs(); ?>

            </div>

            <div class="row">
                <div class="col-lg-12">
                    <?php echo $subview ?>
                </div>
            </div>
        </div>
    </section>
    <!-- Page footer-->

    <footer>
        <div class="pull-right hidden-xs">
            <b>Version</b> 1.6
        </div>
        <strong>&copy; <a href="#">XAERUS</a>.</strong> All rights reserved.
    </footer>
</div>
<?php $this->load->view('admin/components/footer'); ?>
<script type="text/javascript">

    $(document).ready(function () {
        $('.complete input[type="checkbox"]').change(function () {
            var task_id = $(this).data().id;
            var task_complete = $(this).is(":checked");

            var formData = {
                'task_id': task_id,
                'task_progress': 100,
                'task_status': 'completed'
            };
            $.ajax({
                type: 'POST', // define the type of HTTP verb we want to use (POST for our form)
                url: '<?= base_url()?>admin/tasks/completed_tasks/' + task_id, // the url where we want to POST
                data: formData, // our data object
                dataType: 'json', // what type of data do we expect back from the server
                encode: true,
                success: function (res) {
                    console.log(res);
                    if (res) {
                        location.reload();
                    } else {
                        alert('There was a problem with AJAX');
                    }
                }
            })

        });

    })
    ;
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#permission_user_1').hide();
        $("div.action_1").hide();
        $("input[name$='permission']").click(function () {
            $("#permission_user_1").removeClass('show');
            if ($(this).attr("value") == "custom_permission") {
                $("#permission_user_1").show();
            } else {
                $("#permission_user_1").hide();
            }
        });
        $("input[name$='assigned_to[]']").click(function () {
            var user_id = $(this).val();
            $("#action_1" + user_id).removeClass('show');
            if (this.checked) {
                $("#action_1" + user_id).show();
            } else {
                $("#action_1" + user_id).hide();
            }

        });
    });

</script>
<?php $this->load->view('admin/_layout_modal'); ?>
