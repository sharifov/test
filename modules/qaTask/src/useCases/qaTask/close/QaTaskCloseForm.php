<?php

namespace modules\qaTask\src\useCases\qaTask\close;

use common\models\Employee;
use modules\qaTask\src\entities\qaTask\QaTask;
use modules\qaTask\src\entities\qaTask\QaTaskRating;
use yii\base\Model;

/**
 * Class QaTaskCloseForm
 *
 * @property string|null $description
 * @property int|null $rating
 * @property QaTask $task
 * @property Employee $user
 */
class QaTaskCloseForm extends Model
{
    public $description;
    public $rating;

    private $task;
    private $user;

    public function __construct(QaTask $task, Employee $user, $config = [])
    {
        $this->task = $task;
        $this->user = $user;
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            ['rating', 'required'],
            ['rating', 'integer'],
            ['rating', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['rating', 'in', 'range' => array_keys($this->getRatingList())],

            ['description', 'string', 'max' => 255],
        ];
    }

    public function getUserId(): int
    {
        return $this->user->id;
    }

    public function getRatingList(): array
    {
        return QaTaskRating::getList();
    }

    public function getTaskId(): int
    {
        return $this->task->t_id;
    }

    public function getTaskGid(): string
    {
        return $this->task->t_gid;
    }

    public function attributeLabels(): array
    {
        return [
            'description' => 'Description',
            'rating' => 'Rating',
        ];
    }
}
