<?php

namespace modules\qaTask\src\useCases\qaTask\action\takeOver;

use modules\qaTask\src\entities\qaTask\QaTask;
use modules\qaTask\src\entities\qaTaskStatusReason\QaTaskStatusReasonQuery;
use sales\yii\validators\IsNotArrayValidator;
use yii\base\Model;

/**
 * Class QaTaskActionTakeOverForm
 *
 * @property int $reasonId
 * @property string|null $description
 * @property QaTask $task
 * @property array $reasons
 */
class QaTaskActionTakeOverForm extends Model
{
    public $reasonId;
    public $description;

    private $task;
    private $reasons;

    public function __construct(QaTask $task, $config = [])
    {
        $this->task = $task;
        $this->reasons = [];
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            ['reasonId', 'required'],
            ['reasonId', 'integer'],
            ['reasonId', 'in', 'range' => array_keys($this->reasons)],

            ['description', 'string'],
            ['description', IsNotArrayValidator::class],
        ];
    }
}
