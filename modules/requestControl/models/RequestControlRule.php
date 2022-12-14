<?php

namespace modules\requestControl\models;

use modules\requestControl\RequestControlModule;

/**
 * Class Rule
 *
 * @property integer $rcr_id
 * @property string $rcr_type
 * @property string $rcr_subject
 * @property integer $rcr_local
 * @property integer $rcr_global
 *
 */
class RequestControlRule extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @return string
     */
    public static function tableName(): string
    {
        return 'request_control_rule';
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function rules(): array
    {
        return [
            [['rcr_type', 'rcr_subject', 'rcr_local', 'rcr_global'], 'required'],
            ['rcr_type', 'string', 'max' => 50],
            ['rcr_subject', 'string', 'max' => 255],
            ['rcr_local', 'integer'],
            ['rcr_global', 'integer'],
            [['rcr_local', 'rcr_global'], 'default', 'value' => 0]
        ];
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'rcr_type' => 'Rule type',
            'rcr_subject' => 'Rule subject',
            'rcr_local' => 'Local limit',
            'rcr_global' => 'Global limit'
        ];
    }

    /**
     * Solution #1. Fastest solution.
     *
     * Possible solution. #2.
     * The one more abstract level can be added into module. That abstract layer should determine business logic for record creating and updating.
     * This solution give:
     *      1. The controller will be thin;
     *      2. The model won't contain a business logic and will be simple;
     *      3. The business logic will be clean and easy for testing.
     *
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        self::refreshCache();
        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @inheritdoc
     */
    public function afterDelete()
    {
        self::refreshCache();
        parent::afterDelete(); // TODO: Change the autogenerated stub
    }

    /**
     * Refreshing cache by new data
     */
    public static function refreshCache(): void
    {
        \Yii::$app->cache->set(
            RequestControlModule::REQUEST_CONTROL_RULES_CACHE_KEY,
            self::find()->asArray()->all()
        );
    }
}
