<div class="panel panel-custom">
    <div class="panel-heading">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                class="sr-only">Close</span></button>
        <h4 class="modal-title" style="margin-top: 13px;;" id="myModalLabel"><?= lang('calendar_settings') ?></h4>
    </div>
    <form role="form" id="from_items" action="<?php echo base_url(); ?>admin/calendar/save_settings"
          method="post" class="form-horizontal form-groups-bordered">
        <div class="modal-body">
            <div class="form-group">
                <label class="col-lg-4 control-label"><?= lang('google_api') ?></label>
                <div class="col-lg-8">
                    <input type="text" class="form-control" value="<?= config_item('gcal_api_key') ?>"
                           name="gcal_api_key">
                </div>
            </div>

            <div class="form-group">
                <label class="col-lg-4 control-label"><?= lang('calendar_id') ?></label>
                <div class="col-lg-8">
                    <input type="text" class="form-control" value="<?= config_item('gcal_id') ?>" name="gcal_id">
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label"></label>
                <div class="col-lg-7">
                    <button type="submit" class="btn btn-primary"><?= lang('save') ?></button>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
                </div>
            </div>
        </div>
    </form>
</div>
