<?php

namespace sales\model\call\entity\callCommand\behaviors;

use sales\model\call\entity\callCommand\CallCommand;
use sales\model\call\services\CommandListService;
use yii\base\Behavior;
use yii\db\ActiveRecord;

/**
 * Class SyncSortToJsonBehavior
 *
 */
class SyncSortToJsonBehavior extends Behavior
{
    /**
     * @return array
     */
    public function events(): array
    {
        return [
            ActiveRecord::EVENT_BEFORE_UPDATE => 'syncSortToJson',
            ActiveRecord::EVENT_BEFORE_INSERT => 'syncSortToJson',
        ];
    }

    public function syncSortToJson(): void
    {
        /** @var CallCommand $this->owner */
        if ((int) $this->owner->ccom_type_id !== CallCommand::TYPE_COMMAND_LIST) {
            $paramsJson = $this->owner->ccom_params_json;
            $paramsJson['sort'] = $this->owner->ccom_sort_order;
            $this->owner->ccom_params_json = $paramsJson;
        }
    }
}
