<div class="col-sm-12 wrap-fpanel">
    <div class="panel panel-default" data-collapsed="0">
        <div class="panel-heading">
            <div class="panel-title">
                <strong><?= lang('user_details') ?></strong>
            </div>
        </div>            
        <div class="panel-body">            
            <div class="row">
                <div class="col-sm-6 form-horizontal">                    
                    <?php
                    $user_info = $this->db->where(array('user_id' => $id))->get('tbl_users')->row();
                    $profile_info = $this->db->where(array('user_id' => $id))->get('tbl_account_details')->row();
                    ?>
                    <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label"><?= lang('full_name') ?></label>

                        <div class="col-sm-8">
                            <span class="form-control"><?php
                                if (!empty($profile_info)) {
                                    echo $profile_info->fullname;
                                }
                                ?></span>

                        </div>
                    </div>
                    <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label"><?= lang('username') ?></label>

                        <div class="col-sm-8">
                            <span class="form-control"><?php
                                if (!empty($user_info)) {
                                    echo $user_info->username;
                                }
                                ?></span>

                        </div>
                    </div>                           
                    <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label"><?= lang('email') ?></label>

                        <div class="col-sm-8">
                            <span class="form-control"><?php
                                if (!empty($user_info)) {
                                    echo $user_info->email;
                                }
                                ?></span>                               
                        </div>
                    </div>                        
                    <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label"><?= lang('company') ?></label>

                        <div class="col-sm-8">
                            <span class="form-control"><?php
                                if (!empty($profile_info)) {
                                    echo $profile_info->company;
                                }
                                ?></span>                               
                        </div>
                    </div>                        
                    <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label"><?= lang('language') ?></label>

                        <div class="col-sm-8">
                            <span class="form-control"><?php
                                if (!empty($profile_info)) {
                                    echo $profile_info->language;
                                }
                                ?></span>                               
                        </div>
                    </div>                        
                    <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label"><?= lang('phone') ?></label>

                        <div class="col-sm-8">
                            <span class="form-control"><?php
                                if (!empty($profile_info)) {
                                    echo $profile_info->phone;
                                }
                                ?></span>                               
                        </div>
                    </div>                        
                    <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label"><?= lang('mobile') ?></label>

                        <div class="col-sm-8">
                            <span class="form-control"><?php
                                if (!empty($profile_info)) {
                                    echo $profile_info->mobile;
                                }
                                ?></span>                               
                        </div>
                    </div>                        
                    <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label"><?= lang('skype_id') ?></label>

                        <div class="col-sm-8">
                            <span class="form-control"><?php
                                if (!empty($profile_info)) {
                                    echo $profile_info->skype;
                                }
                                ?></span>                               
                        </div>
                    </div>                        
                    <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label"><?= lang('profile_photo') ?></label>

                        <div class="col-sm-8">
                            <div class="fileinput-new thumbnail" style="width: 210px;" >
                                <?php
                                if (!empty($profile_info)) :
                                    ?>
                                    <img src="<?php echo base_url() . $profile_info->avatar; ?>" >  
                                <?php else: ?>
                                    <img src="http://placehold.it/350x260" alt="Please Connect Your Internet">     
                                <?php endif; ?>                                 
                            </div>                            
                        </div>
                    </div>                        

                </div>
                <div class="col-sm-6">

                    <div id="roll" class="list-group">
                        <a href="#" class="list-group-item disabled">
                            Menu Permission
                        </a>      
                        <?php
                        if (!empty($user_role)):foreach ($user_role as $v_role):
                                ?>
                                <?php if ($v_role->parent != '0') { ?>                                
                                    <li class="list-group-item" style="padding: 4px 15px;font-size: 12px">
                                        <ul style="margin: 0px;">
                                            <li><?= lang($v_role->label) ?></li>
                                        </ul>
                                    </li>        
                                <?php } else { ?>
                                    <li class="list-group-item" style="padding: 4px 20px;font-size: 12px"><?= lang($v_role->label) ?></li>
                                    <?php }
                                    ?>                       

                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>            
        </div>

    </div>
</div>