<?php

namespace modules\qaTask\src\useCases\qaTask\close;

use modules\qaTask\src\entities\qaTask\QaTask;
use modules\qaTask\src\entities\qaTask\QaTaskRating;
use yii\base\Model;

/**
 * Class QaTaskCloseForm
 *
 * @property int $decisionId
 * @property string|null $description
 * @property int|null $rating
 * @property QaTask $task
 */
class QaTaskCloseForm extends Model
{
    public const NO_LEAD_ACTION = 1;
    public const SEND_LEAD_TO_REDIAL_QUEUE = 2;
    public const REASSIGN_LEAD = 3;

    public const LEAD_DECISIONS = [
        self::NO_LEAD_ACTION => 'No Lead action',
        self::SEND_LEAD_TO_REDIAL_QUEUE => 'Send Lead to Redial Queue',
        self::REASSIGN_LEAD => 'Re-assign Lead',
    ];

    public $decisionId;
    public $description;
    public $rating;

    private $task;

    public function __construct(QaTask $task, $config = [])
    {
        $this->task = $task;
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            ['decisionId', 'integer'],
            ['decisionId', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['decisionId', 'in', 'range' => array_keys(self::LEAD_DECISIONS)],

            ['description', 'string', 'max' => 255],
            ['description', 'required'],

            ['rating', 'integer'],
            ['rating', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['rating', 'in', 'range' => array_keys(QaTaskRating::getList())],
        ];
    }

    public function getTaskId(): int
    {
        return $this->task->t_id;
    }
}
