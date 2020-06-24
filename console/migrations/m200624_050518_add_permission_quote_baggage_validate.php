<?php

use common\models\Employee;
use common\models\Setting;
use common\models\SettingCategory;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200624_050518_add_permission_quote_baggage_validate
 */
class m200624_050518_add_permission_quote_baggage_validate extends Migration
{
    public $routes = [
        '/quote/segment-baggage-validate',
    ];

    public $roles = [
        Employee::ROLE_ADMIN, Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_AGENT, Employee::ROLE_SUPERVISION,
        Employee::ROLE_EX_AGENT, Employee::ROLE_EX_SUPER,
        Employee::ROLE_SUP_AGENT, Employee::ROLE_SUP_SUPER,
        Employee::ROLE_QA, Employee::ROLE_USER_MANAGER,
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if (!$settingCategory = SettingCategory::findOne(['sc_name' => 'Quote'])) {
            $settingCategory = new SettingCategory();
            $settingCategory->sc_name = 'Quote';
            $settingCategory->save();
        }

        $this->insert('{{%setting}}', [
            's_key' => 'flight_baggage_size_values',
            's_name' => 'Flight baggage size values',
            's_type' => Setting::TYPE_ARRAY,
            's_value' => json_encode(
                [
                    '62/158', '81/208',
                    '59/150', '45/115',
                ],
                JSON_THROW_ON_ERROR
            ),
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_category_id' => $settingCategory ? $settingCategory->sc_id : null,
        ]);

        $this->insert('{{%setting}}', [
            's_key' => 'flight_baggage_weight_values',
            's_name' => 'Flight baggage weight values',
            's_type' => Setting::TYPE_ARRAY,
            's_value' => json_encode(
                [
                    '50/23', '100/45', '70/32', '62/28',
                    '55/25', '44/20', '33/15', '22/10',
                 ],
                JSON_THROW_ON_ERROR
            ),
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_category_id' => $settingCategory ? $settingCategory->sc_id : null,
        ]);

        (new RbacMigrationService())->up($this->routes, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%setting}}', ['IN', 's_key', [
            'flight_baggage_size_values',
            'flight_baggage_weight_values',
        ]]);

        SettingCategory::deleteAll(['sc_name' => 'Quote']);

        (new RbacMigrationService())->down($this->routes, $this->roles);
    }
}
