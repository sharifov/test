<?php

use yii\db\Migration;

/**
 * Class m210215_090147_update_tbl_product_type_icon_css
 */
class m210215_090147_update_tbl_product_type_icon_css extends Migration
{
    public $data = [
      1 => ['pt_sort_order' => 1, 'pt_icon_class' => 'fa fa-plane'],
      2 => ['pt_sort_order' => 2, 'pt_icon_class' => 'fa fa-hotel'],
      3 => ['pt_sort_order' => 3, 'pt_icon_class' => 'fa fa-car'],
      4 => ['pt_sort_order' => 4, 'pt_icon_class' => 'fa fa-ship'],
      5 => ['pt_sort_order' => 5, 'pt_icon_class' => 'fas fa-archway'],
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        foreach ($this->data as $id => $item) {
            $this->update('{{%product_type}}', $item, ['pt_id' => $id]);
        }

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
