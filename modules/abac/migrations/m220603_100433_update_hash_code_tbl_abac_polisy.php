<?php

namespace modules\abac\migrations;

use modules\abac\src\entities\AbacPolicy;
use yii\db\Migration;

/**
 * Class m220603_100433_update_hash_code_tbl_abac_polisy
 */
class m220603_100433_update_hash_code_tbl_abac_polisy extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $list = AbacPolicy::find()->all();
        if ($list) {
            foreach ($list as $item) {
                $item->update();
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
