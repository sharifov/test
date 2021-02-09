<?php

use common\models\Employee;
use common\models\Setting;
use common\models\SettingCategory;
use console\migrations\RbacMigrationService;
use sales\rbac\rules\globalRules\clientChat\ClientChatHoldOwnerRule;
use sales\rbac\rules\globalRules\clientChat\ClientChatHoldRule;
use yii\db\Migration;
use yii\helpers\Json;

/**
 * Class m201001_112505_add_settings_cc_hold
 */
class m201001_112505_add_settings_and_permissions_to_cc_hold extends Migration
{
    private array $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
    ];
    private string $categoryName = 'Client Chat';
    private string $holdPermissionName = 'global/client-chat/hold';
    private string $holdOwnerPermissionName = 'global/client-chat/hold-owner';
    private string $holdAccessPermissionName = 'client-chat/hold/access';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $settingCategory = SettingCategory::getOrCreateByName($this->categoryName);

        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'client_chat_hold_deadline_options',
                's_name' => 'Client Chat Hold Deadline options',
                's_type' => Setting::TYPE_ARRAY,
                's_value' => Json::encode([
                    '5' => '5',
                    '10' => '10',
                    '15' => '15',
                    '30' => '30',
                    '60' => '60'
                ]),
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->delete('{{%setting}}', ['IN', 's_key', [
            'client_chat_hold_deadline_options',
        ]]);
    }
}
