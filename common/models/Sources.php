<?php

namespace common\models;

use borales\extensions\phoneInput\PhoneInputValidator;
use sales\access\EmployeeProjectAccess;
use sales\entities\AggregateRoot;
use sales\entities\EventTrait;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "sources".
 *
 * @property int $id
 * @property int $project_id
 * @property string $name
 * @property string $cid
 * @property string $last_update
 * @property boolean $default
 * @property boolean $hidden
 *
 *
 * @property Project $project
 */
class Sources extends \yii\db\ActiveRecord implements AggregateRoot
{

    use EventTrait;

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
            [['project_id'], 'required'],
            [['project_id'], 'integer'],
            [['default', 'hidden'], 'boolean'],
            [['last_update'], 'safe'],
            [['name', 'cid'], 'string', 'max' => 255],
            [['phone_number'], 'string', 'max' => 20],
            [['phone_number'], 'default', 'value' => null],
            [['phone_number'], 'unique'],
            [['phone_number'], PhoneInputValidator::class],

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
            'phone_number' => 'Phone Number',
            'default' => 'Default',
            'hidden' => 'Hidden',
        ];
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['last_update'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['last_update'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
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
     * {@inheritdoc}
     * @return SourcesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SourcesQuery(get_called_class());
    }


    /**
     * @param bool $noHidden
     * @return array
     */
    public static function getList(bool $noHidden = false): array
    {
        $query = self::find()->select(['sources_id' => 'sources.id', 'sources_name' => 'sources.name', 'project_name' => 'projects.name'])
            ->innerJoin('projects', 'projects.id = sources.project_id')
            ->orderBy(['project_name' => SORT_ASC, 'sources_name' => SORT_ASC]);

        if($noHidden) {
            $query->andWhere(['sources.hidden' => false]);
        }
        $data = $query->asArray()->all();
        return ArrayHelper::map($data, 'sources_id', 'sources_name', 'project_name');
    }

    public static function getGroupList()
    {
        /**
         * @var $projects Project[]
         */
        $map = [];
        $projects = Project::findAll([
//            'id' => array_keys(ProjectEmployeeAccess::getProjectsByEmployee())
            'id' => array_keys(EmployeeProjectAccess::getProjects())
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
