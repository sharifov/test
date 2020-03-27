<?php

use yii\db\Migration;

/**
 * Class m200327_095501_add_cases_q_hot_and_indexes
 */
class m200327_095501_add_cases_q_hot_and_indexes extends Migration
{
    /**
     * @return bool|void
     * @throws \yii\base\Exception
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $permissions = ['/cases-q/hot'];
        foreach ($permissions as $per) {
            $permission = $auth->createPermission($per);
            $auth->add($permission);
        }
        foreach (['admin', 'sup_agent', 'sup_super', 'ex_agent', 'ex_super'] as $item) {
            $role = $auth->getRole($item);
            foreach ($permissions as $per) {
                $permission = $auth->getPermission($per);
                $auth->addChild($role, $permission);
            }
        }

        $this->createIndex('IND-case_sale-css_in_date', '{{%case_sale}}', ['css_in_date']);
        $this->createIndex('IND-case_sale-css_out_date', '{{%case_sale}}', ['css_out_date']);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
        Yii::$app->db->getSchema()->refreshTableSchema('{{%case_sale}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        $permissions = ['/cases-q/hot'];
        foreach ($permissions as $item) {
            $permission = $auth->getPermission($item);
            $auth->remove($permission);
        }

        $this->dropIndex('IND-case_sale-css_in_date', '{{%case_sale}}');
        $this->dropIndex('IND-case_sale-css_out_date', '{{%case_sale}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
        Yii::$app->db->getSchema()->refreshTableSchema('{{%case_sale}}');
    }
}
