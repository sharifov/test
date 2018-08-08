<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "project_employee_access".
 *
 * @property int $employee_id
 * @property int $project_id
 * @property string $created
 *
 * @property Employee $employee
 * @property Project $project
 */
class ProjectEmployeeAccess extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'project_employee_access';
    }

    public static function getAllSourceByEmployee()
    {
        /**
         * @var $projects Project[]
         */

        $employeeId = Yii::$app->user->identity->getId();
        $options = [];
        if (Yii::$app->user->identity->role != 'admin') {
            $access = ArrayHelper::map(self::find()->where(['employee_id' => $employeeId])->asArray()->all(), 'project_id', 'project_id');
            $projects = Project::find()->where([
                'id' => $access,
                'closed' => false
            ])->all();
        } else {
            $projects = Project::find()->where([
                'closed' => false
            ])->all();
        }
        foreach ($projects as $id => $project) {
            $sources = $project->sources;
            $child_options = [];
            foreach ($sources as $source) {
                $child_options[$source->id] = $source->name;
            }
            $options[$project->name] = $child_options;
        }
        return $options;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['employee_id', 'project_id'], 'integer'],
            [['created'], 'safe'],
            [['employee_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::className(), 'targetAttribute' => ['employee_id' => 'id']],
            [['project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::className(), 'targetAttribute' => ['project_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'employee_id' => 'Employee ID',
            'project_id' => 'Project ID',
            'created' => 'Created',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmployee()
    {
        return $this->hasOne(Employee::className(), ['id' => 'employee_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Project::className(), ['id' => 'project_id']);
    }
}
