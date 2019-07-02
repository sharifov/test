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
        if (!Yii::$app->user->identity->canRole('admin')) {
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
                if($source->hidden) {
                    continue;
                }
                $child_options[$source->id] = $source->name;
            }

            if($child_options) {
                $options[$project->name] = $child_options;
            }
        }
        return $options;
    }


    /**
     * @param int|null $user_id
     * @return array
     */
    public static function getProjectsByEmployee(int $user_id = null)
    {
        /**
         * @var $projects Project[]
         */

        if(!$user_id) {
            $user_id = Yii::$app->user->id;
        }

        if (!Yii::$app->user->identity->canRole('admin') && !Yii::$app->user->identity->canRole('qa')) {
            $subQuery = self::find()->select(['project_id'])->where(['employee_id' => $user_id]);
            $projects = Project::find()->select(['id', 'name'])->where(['closed' => false])->andWhere(['IN', 'id', $subQuery])->asArray()->all();
        } else {
            $projects = Project::find()->select(['id', 'name'])->where(['closed' => false])->asArray()->all();
        }

        return ArrayHelper::map($projects, 'id', 'name');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['employee_id', 'project_id'], 'integer'],
            [['created'], 'safe'],
            [['employee_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['employee_id' => 'id']],
            [['project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['project_id' => 'id']],
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
        return $this->hasOne(Employee::class, ['id' => 'employee_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Project::class, ['id' => 'project_id']);
    }
}
