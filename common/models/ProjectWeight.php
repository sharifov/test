<?php

namespace common\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%project_weight}}".
 *
 * @property int $pw_project_id
 * @property int $pw_weight
 *
 * @property Project $project
 */
class ProjectWeight extends ActiveRecord
{

    public const SCENARIO_INSERT = 'insert';

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%project_weight}}';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['pw_project_id', 'required'],
            ['pw_project_id', 'unique', 'on' => self::SCENARIO_INSERT],
            ['pw_project_id', 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['pw_project_id' => 'id']],
            ['pw_project_id', 'integer'],

            ['pw_weight', 'required'],
            ['pw_weight', 'integer'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'pw_project_id' => 'Project',
            'pw_weight' => 'Weight',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getProject(): ActiveQuery
    {
        return $this->hasOne(Project::class, ['id' => 'pw_project_id']);
    }

    /**
     * @return ProjectWeightQuery
     */
    public static function find(): ProjectWeightQuery
    {
        return new ProjectWeightQuery(get_called_class());
    }
}
