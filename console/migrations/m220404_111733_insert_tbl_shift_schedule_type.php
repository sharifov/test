<?php

use yii\db\Migration;

/**
 * Class m220404_111733_insert_tbl_shift_schedule_type
 */
class m220404_111733_insert_tbl_shift_schedule_type extends Migration
{
    private array $data = [
        [
            'sst_key' => 'WT',
            'sst_name' => 'Work Time',
            'sst_work_time' => 1,
            'sst_color' => '#2a3f54',
            'sst_icon_class' => 'fa fa-building-o',
            'sst_sort_order' => 2,
        ],
        [
            'sst_key' => 'WTR',
            'sst_name' => 'Work Time Remotely',
            'sst_work_time' => 1,
            'sst_color' => '#586d83',
            'sst_icon_class' => 'fa fa-building',
            'sst_sort_order' => 4,
        ],
        [
            'sst_key' => 'EDO',
            'sst_name' => 'Emergency DayOff',
            'sst_work_time' => 0,
            'sst_color' => '#dc3545',
            'sst_icon_class' => 'fa fa-home',
            'sst_sort_order' => 6,
        ],
        [
            'sst_key' => 'SL',
            'sst_name' => 'Sick Leave Request',
            'sst_work_time' => 0,
            'sst_color' => '#fd7e14',
            'sst_icon_class' => 'fa fa-plus-square',
            'sst_sort_order' => 8,
        ],
        [
            'sst_key' => 'VAC',
            'sst_name' => 'Vacation Request',
            'sst_work_time' => 0,
            'sst_color' => '#28a745',
            'sst_icon_class' => 'fa fa-smile-o',
            'sst_sort_order' => 10,
        ],
        [
            'sst_key' => 'INC',
            'sst_name' => 'Incentive',
            'sst_work_time' => 1,
            'sst_color' => '#6610f2',
            'sst_icon_class' => 'fa fa-gift',
            'sst_sort_order' => 12,
        ],
        [
            'sst_key' => 'EXTRA',
            'sst_name' => 'Extra Day',
            'sst_work_time' => 1,
            'sst_color' => '#2a3f54',
            'sst_icon_class' => 'fa fa-retweet',
            'sst_sort_order' => 14,
        ],
        [
            'sst_key' => 'UNV',
            'sst_name' => 'Unpaid Vacation',
            'sst_work_time' => 0,
            'sst_color' => '#6c757d',
            'sst_icon_class' => 'fa fa-pause',
            'sst_sort_order' => 16,
        ],

    ];

    /**
     * @return void
     */
    public function safeUp()
    {

        foreach ($this->data as $row) {
            $row['sst_updated_dt'] = date('Y-m-d H:i:s');
            $row['sst_enabled'] = true;
            $row['sst_readonly'] = false;
            $row['sst_title'] = $row['sst_key'] . ' - ' . $row['sst_name'];

            $this->insert('{{%shift_schedule_type}}', $row);
        }
    }

    /**
     * @return void
     */
    public function safeDown()
    {
        $this->delete('{{%shift_schedule_type}}');
    }
}
