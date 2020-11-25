<?php

namespace sales\model\call\entity\callCommand\behaviors;

use sales\model\call\entity\callCommand\CallCommand;
use sales\model\call\services\CommandListService;
use yii\base\Behavior;
use yii\db\ActiveRecord;

/**
 * Class RefreshCommandLineJsonBehavior
 *
 */
class RefreshCommandLineJsonBehavior extends Behavior
{
    /**
     * @return array
     */
    public function events(): array
    {
        return [
            ActiveRecord::EVENT_AFTER_DELETE => 'refreshJson',
            ActiveRecord::EVENT_AFTER_UPDATE => 'refreshJson',
            ActiveRecord::EVENT_AFTER_INSERT => 'refreshJson',
        ];
    }

    public function refreshJson(): void
    {
        /** @var CallCommand $this->owner */
        CommandListService::refreshParentJson($this->owner);
    }
}
