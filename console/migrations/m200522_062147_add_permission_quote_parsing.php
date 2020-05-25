<?php

use common\models\Employee;
use common\models\Setting;
use common\models\SettingCategory;
use yii\db\Migration;

/**
 * Class m200522_062147_add_permission_quote_parsing
 */
class m200522_062147_add_permission_quote_parsing extends Migration
{
    public array $route = [
        '/quote/prepare-dump',
        '/quote/save-from-dump',
    ];

    public array $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN,
        Employee::ROLE_AGENT,
        Employee::ROLE_SUPERVISION,
        Employee::ROLE_QA,
        Employee::ROLE_QA_SUPER,
        Employee::ROLE_USER_MANAGER,
        Employee::ROLE_SUP_AGENT,
        Employee::ROLE_SUP_SUPER,
        Employee::ROLE_EX_AGENT,
        Employee::ROLE_EX_SUPER,
        Employee::ROLE_SALES_SENIOR,
        Employee::ROLE_EXCHANGE_SENIOR,
        Employee::ROLE_SUPPORT_SENIOR,
    ];

	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$category = SettingCategory::findOne(['sc_name' => 'Enable']);

		$this->insert('{{%setting}}', [
            's_key' => 'enable_gds_parsers_for_create_quote',
            's_name' => 'Enable GDS parsers for create Quote',
            's_type' => Setting::TYPE_BOOL,
            's_value' => false,
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_category_id' => $category ? $category->sc_id : null,
        ]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
        Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');

		(new \console\migrations\RbacMigrationService())->up($this->route, $this->roles);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		(new \console\migrations\RbacMigrationService())->down($this->route, $this->roles);

		$this->delete('{{%setting}}', ['IN', 's_key', [
            'enable_gds_parsers_for_create_quote'
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
        Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');
	}
}
