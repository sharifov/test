<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "sources".
 *
 * @property int $id
 * @property int $project_id
 * @property string $name
 * @property string $cid
 * @property string $last_update
 *
 * @property Project $project
 */
class Source extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sources';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['project_id'], 'integer'],
            [['last_update'], 'safe'],
            [['name', 'cid'], 'string', 'max' => 255],
            [['project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['project_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'project_id' => 'Project ID',
            'name' => 'Name',
            'cid' => 'Cid',
            'last_update' => 'Last Update',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Project::class, ['id' => 'project_id']);
    }

    /**
     * @return array
     */
    public static function getList(): array
    {
        $data = self::find()->orderBy(['name' => SORT_ASC])->asArray()->all();
        return ArrayHelper::map($data, 'id', 'name', 'project_id');
    }

    public static function getGroupList()
    {
        /**
         * @var $projects Project[]
         */
        $map = [];
        $projects = Project::findAll([
            'id' => array_keys(ProjectEmployeeAccess::getProjectsByEmployee())
        ]);
        foreach ($projects as $project) {
            $child_map = [];
            foreach ($project->sources as $source) {
                $child_map[$source->id] = sprintf('%s', $source->name);
            }
            $map[$project->name] = $child_map;
        }
        return $map;
    }
}
