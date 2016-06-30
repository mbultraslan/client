<?php

/**
 * Description of Project_Model
 *
 * @author NaYeM
 */
class Items_Model extends MY_Model
{

    public $_table_name;
    public $_order_by;
    public $_primary_key;

    function calculate_milestone_progress($milestones_id)
    {
        $all_milestone_tasks = $this->db->where('milestones_id', $milestones_id)->get('tbl_task')->num_rows();
        $complete_milestone_tasks = $this->db->where(
            array('task_progress' => '100',
                'milestones_id' => $milestones_id
            ))->get('tbl_task')->num_rows();
        if ($all_milestone_tasks > 0) {
            return round(($complete_milestone_tasks / $all_milestone_tasks) * 100);
        } else {
            return 0;
        }
    }



}
