<?php

namespace modules\qaTask\src\widgets\objectMenu;

use modules\qaTask\src\entities\qaTask\QaTask;
use modules\qaTask\src\entities\qaTask\QaTaskObjectType;
use Webmozart\Assert\Assert;
use yii\base\Widget;
use yii\helpers\Url;

/**
 * Class QaTaskObjectMenuWidget
 *
 * @property int $objectType
 * @property int $objectId
 */
class QaTaskObjectMenuWidget extends Widget
{
    public $objectType;
    public $objectId;

    public function init()
    {
        Assert::notNull($this->objectType);
        Assert::integer($this->objectType);
        Assert::oneOf($this->objectType, array_keys(QaTaskObjectType::getList()));

        Assert::notNull($this->objectId);
        Assert::integer($this->objectId);

        parent::init();
    }

    public function run(): string
    {
        return $this->render('qa-task-object-menu', [
            'viewUrl' => Url::to(['/qa-task/qa-task/view-tasks', 'typeId' => $this->objectType, 'id' => $this->objectId]),
            'addUrl' => Url::to(['/qa-task/qa-task-create/' . strtolower(QaTaskObjectType::getName($this->objectType)), 'objectId' => $this->objectId]),
            'active' => $this->countActive(),
            'total' => $this->countTotal(),
        ]);
    }

    private function countActive(): int
    {
        return QaTask::find()->byObjectType($this->objectType)->byObjectId($this->objectId)->active()->count();
    }

    private function countTotal(): int
    {
        return QaTask::find()->byObjectType($this->objectType)->byObjectId($this->objectId)->count();
    }
}
