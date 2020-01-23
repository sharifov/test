<?php

use common\models\Employee;
use common\models\Setting;
use common\models\SettingCategory;
use console\migrations\RbacMigrationService;
use yii\db\Migration;
use yii\db\Query;

/**
 * Handles the creation of table `{{%setting_category}}`.
 */
class m200109_095057_create_setting_category_table extends Migration
{
    public $routes = [
        '/setting-category/*',
    ];

    public $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
    ];

    private $_columnName = 's_category_id';
    private $_fkName = 'FK-setting-setting_category';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%setting_category}}', [
            'sc_id' => $this->primaryKey(),
            'sc_name' => $this->string(50)->unique()->notNull(),
            'sc_enabled' => $this->boolean()->defaultValue(true),
            'sc_created_dt' => $this->dateTime(),
            'sc_updated_dt' => $this->dateTime(),
        ]);

        $this->addColumn('{{%setting}}', $this->_columnName, $this->integer());
        $this->addForeignKey(
            $this->_fkName,
            '{{%setting}}',
            $this->_columnName,
            '{{%setting_category}}',
            'sc_id',
            'SET NULL',
            'CASCADE'
        );

        (new RbacMigrationService())->up($this->routes, $this->roles);

        $this->_addCategory();

        $this->_flush();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        (new RbacMigrationService())->down($this->routes, $this->roles);

        $this->dropForeignKey($this->_fkName, '{{%setting}}');
        $this->dropColumn('{{%setting}}', $this->_columnName);

        $this->dropTable('{{%setting_category}}');

        $this->_flush();
    }

    /**
     * @return array
     */
    private function _getCategory()
    {
        $category = [];

        $s_keys = (new Query())
            ->select(['s_key'])
            ->from('{{%setting}}')
            ->orderBy('s_key')
            ->all();

        foreach ($s_keys as $value) {
            $prefix = explode('_', $value['s_key']);
            $category[$prefix[0]] = ucfirst($prefix[0]);
        }

        return $category;
    }

    private function _addCategory(): void
    {
        foreach ($this->_getCategory() as $key => $category) {
            $settingCategory = new SettingCategory();
            $settingCategory->sc_name = $category;
            $settingCategory->save();

            Setting::updateAll(['s_category_id' => $settingCategory->sc_id], ['like', 's_key', $key . '_%', false]);
        }
    }

    private function _flush(): void
    {
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
