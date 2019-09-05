<?php

use yii\db\Migration;

/**
 * Class m190903_145755_update_tbl_user_project_param
 */
class m190903_145755_update_tbl_user_project_param extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $upp = \common\models\UserProjectParams::find()->where(['upp_dep_id' => null])->all();

        if ($upp) {
            foreach ($upp as $uppItem) {
                if ($uppItem->uppUser && $uppItem->uppUser->udDeps) {
                    foreach ($uppItem->uppUser->udDeps as $dep) {
                        $uppItem->upp_dep_id = $dep->dep_id;
                        if( !$uppItem->save()) {
                            print_r($uppItem->errors);
                        } else {
                            echo ' - Set department ID: '. $uppItem->upp_dep_id . ', UserId: ' . $uppItem->upp_user_id."\r\n";
                        }
                        break;
                    }
                }
            }
        }

        echo ' - Total UserProjectParams: ' . count($upp) . "\r\n";
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190903_145755_update_tbl_user_project_param cannot be reverted.\n";

        // return false;
    }

}
