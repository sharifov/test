<?php

use src\entities\cases\CaseCategory;
use src\helpers\app\DBHelper;
use yii\db\Migration;

/**
 * Class m220830_090122_add_columns_for_nested_sets_in_tbl_case_category
 */
class m220830_090122_add_columns_for_nested_sets_in_tbl_case_category extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if (!DBHelper::isColumnExist('case_category', 'cc_lft')) {
            $this->addColumn('{{%case_category}}', 'cc_lft', $this->integer()->notNull());
        }
        if (!DBHelper::isColumnExist('case_category', 'cc_rgt')) {
            $this->addColumn('{{%case_category}}', 'cc_rgt', $this->integer()->notNull());
        }
        if (!DBHelper::isColumnExist('case_category', 'cc_depth')) {
            $this->addColumn('{{%case_category}}', 'cc_depth', $this->integer()->notNull());
        }
        if (!DBHelper::isColumnExist('case_category', 'cc_tree')) {
            $this->addColumn('{{%case_category}}', 'cc_tree', $this->integer()->notNull());
        }
        if (!DBHelper::isColumnExist('case_category', 'cc_allow_to_select')) {
            $this->addColumn('{{%case_category}}', 'cc_allow_to_select', $this->tinyInteger(1));
        }

        $this->initExistingModels();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        if (DBHelper::isColumnExist('case_category', 'cc_lft')) {
            $this->dropColumn('{{%case_category}}', 'cc_lft');
        }
        if (DBHelper::isColumnExist('case_category', 'cc_rgt')) {
            $this->dropColumn('{{%case_category}}', 'cc_rgt');
        }
        if (DBHelper::isColumnExist('case_category', 'cc_depth')) {
            $this->dropColumn('{{%case_category}}', 'cc_depth');
        }
        if (DBHelper::isColumnExist('case_category', 'cc_tree')) {
            $this->dropColumn('{{%case_category}}', 'cc_tree');
        }
        if (DBHelper::isColumnExist('case_category', 'cc_allow_to_select')) {
            $this->dropColumn('{{%case_category}}', 'cc_allow_to_select');
        }
    }

    /**
     * Init logic for preparing existing case categories
     * @return void
     * @see \creocoder\nestedsets\NestedSetsBehavior::beforeInsertRootNode
     */
    private function initExistingModels(): void
    {
        $models = CaseCategory::find()->all();
        foreach ($models as $model) {
            try {
                $model->setAttribute('cc_tree', $model->getPrimaryKey());
                $model->setAttribute('cc_lft', 1);
                $model->setAttribute('cc_rgt', 2);
                $model->setAttribute('cc_depth', 0);
                $model->update(false);
            } catch (\Throwable $throwable) {
                echo $throwable->getMessage();
            }
        }
    }
}
