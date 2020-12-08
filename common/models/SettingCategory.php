<?php

namespace common\models;

use yii\behaviors\AttributeBehavior;
use yii\db\ActiveRecord;
use common\models\query\SettingCategoryQuery;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "setting_category".
 *
 * @property int $sc_id
 * @property string|null $sc_name
 * @property int|null $sc_enabled
 * @property string|null $sc_created_dt
 * @property string|null $sc_updated_dt
 *
 * @property Setting[] $settings
 */
class SettingCategory extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'setting_category';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['sc_enabled'], 'boolean'],
            [['sc_created_dt', 'sc_updated_dt'], 'safe'],
            [['sc_name'], 'trim'],
            [['sc_name'], 'unique'],
            [['sc_name'], 'string', 'min' => 1, 'max' => 50],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'sc_id' => 'ID',
            'sc_name' => 'Name',
            'sc_enabled' => 'Enabled',
            'sc_created_dt' => 'Created',
            'sc_updated_dt' => 'Updated',
        ];
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['sc_created_dt', 'sc_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['sc_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ]
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSettings()
    {
        return $this->hasMany(Setting::class, ['category_id' => 'sc_id']);
    }

    /**
     * {@inheritdoc}
     * @return SettingCategoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SettingCategoryQuery(get_called_class());
    }

    /**
     * @return array
     */
    public static function getList(): array
    {
        $data = self::find()->active()->orderBy(['sc_name' => SORT_ASC])->asArray()->all();
        return ArrayHelper::map($data, 'sc_id', 'sc_name');
    }

    /**
     * @param string $name
     * @return SettingCategory
     */
    public static function getOrCreateByName(string $name): SettingCategory
    {
        if (!$settingCategory = self::findOne(['sc_name' => $name])) {
            $settingCategory = new self();
            $settingCategory->sc_name = $name;
            $settingCategory->save();
        }
        return $settingCategory;
    }
}
