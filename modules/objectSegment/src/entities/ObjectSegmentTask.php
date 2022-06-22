<?php

namespace modules\objectSegment\src\entities;

use common\models\Employee;
use modules\objectSegment\src\repositories\ObjectSegmentTaskRepository;
use modules\taskList\src\entities\taskList\TaskList;
use modules\taskList\src\services\TaskListService;
use Yii;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "object_segment_task".
 *
 * @property int $ostl_osl_id
 * @property int $ostl_tl_id
 * @property string|null $ostl_created_dt
 * @property int|null $ostl_created_user_id
 *
 * @property Employee $ostlCreatedUser
 * @property ObjectSegmentList $objectSegmentList
 * @property TaskList $taskList
 */
class ObjectSegmentTask extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'object_segment_task';
    }

    public function behaviors(): array
    {
        return array_merge(parent::behaviors(), [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['ostl_created_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
            'attribute' => [
                'class'  => AttributeBehavior::class,
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['ostl_created_user_id'],
                ],
                'value' => Yii::$app->user->id ?? null,
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['ostl_osl_id', 'ostl_tl_id'], 'required'],
            [['ostl_osl_id', 'ostl_tl_id', 'ostl_created_user_id'], 'integer'],
            [['ostl_created_dt'], 'safe'],
            [['ostl_osl_id', 'ostl_tl_id'], 'unique', 'targetAttribute' => ['ostl_osl_id', 'ostl_tl_id']],
            [['ostl_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ostl_created_user_id' => 'id']],
            [['ostl_osl_id'], 'exist', 'skipOnError' => true, 'targetClass' => ObjectSegmentList::class, 'targetAttribute' => ['ostl_osl_id' => 'osl_id']],
            [['ostl_tl_id'], 'exist', 'skipOnError' => true, 'targetClass' => TaskList::class, 'targetAttribute' => ['ostl_tl_id' => 'tl_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'ostl_osl_id' => 'Object segment list',
            'ostl_tl_id' => 'Task list',
            'ostl_created_dt' => 'Created Dt',
            'ostl_created_user_id' => 'Created User',
        ];
    }

    public static function find(): ObjectSegmentTaskScope
    {
        return new ObjectSegmentTaskScope(static::class);
    }

    /**
     * Gets query for [[OstlCreatedUser]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOstlCreatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'ostl_created_user_id']);
    }

    /**
     * Gets query for [[OstlOsl]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getObjectSegmentList()
    {
        return $this->hasOne(ObjectSegmentList::class, ['osl_id' => 'ostl_osl_id']);
    }

    /**
     * Gets query for [[OstlTl]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTaskList()
    {
        return $this->hasOne(TaskList::class, ['tl_id' => 'ostl_tl_id']);
    }

    public function getObjectList(): array
    {
        return Yii::$app->objectSegment->getObjectList();
    }

    public function getTaskListAsKeyValue(): array
    {
        return TaskListService::getTaskObjectList();
    }

    public static function getAssignedTaskIds(int $id): array
    {
        return self::find()->select(['ostl_tl_id'])->where(['ostl_osl_id' => $id])->column();
    }

    public static function getAssignedObjectSegmentIdsByTaskId(int $id): array
    {
        return self::find()->select(['ostl_osl_id'])->where(['ostl_tl_id' => $id])->column();
    }

    public static function create(int $objectSegmentListId, int $taskListId): self
    {
        $self = new self();
        $self->ostl_osl_id = $objectSegmentListId;
        $self->ostl_tl_id = $taskListId;

        return $self;
    }

    public static function deleteOrAddTasks(int $id, array $taskIds = []): bool
    {
        $currentItems = self::getAssignedTaskIds($id);
        $addList = [];
        $removeList = [];

        if (empty($taskIds) && !empty($currentItems)) {
            $removeList = $currentItems;
        } else {
            $removeList = array_diff($currentItems, $taskIds);
            $addList = array_diff($taskIds, $currentItems);
        }

        if (!empty($removeList)) {
            self::deleteAll(['AND', ['ostl_osl_id' => $id], ['IN', 'ostl_tl_id', $removeList]]);
        }

        if (!empty($addList)) {
            foreach ($addList as $item) {
                $objectSegmentTask = self::create($id, $item);
                (new ObjectSegmentTaskRepository($objectSegmentTask))->save();
            }
        }

        return true;
    }

    public static function deleteOrAddObjectSegments(int $id, array $segmentIds = []): bool
    {
        $currentItems = self::getAssignedObjectSegmentIdsByTaskId($id);
        $addList = [];
        $removeList = [];

        if (empty($segmentIds) && !empty($currentItems)) {
            $removeList = $currentItems;
        } else {
            $removeList = array_diff($currentItems, $segmentIds);
            $addList = array_diff($segmentIds, $currentItems);
        }

        if (!empty($removeList)) {
            self::deleteAll(['AND', ['ostl_tl_id' => $id], ['IN', 'ostl_osl_id', $removeList]]);
        }

        if (!empty($addList)) {
            foreach ($addList as $item) {
                $objectSegmentTask = self::create($item, $id);
                (new ObjectSegmentTaskRepository($objectSegmentTask))->save();
            }
        }

        return true;
    }
}
