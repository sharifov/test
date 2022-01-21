<?php

namespace common\models\search\employee;

use common\models\query\EmployeeQuery;
use src\model\clientChatUserAccess\entity\ClientChatUserAccess;
use yii\base\Model;

/**
 * Class PastAcceptedChatsNumber
 * @package common\models\search\employee
 *
 * @property int $pastMinutes
 * @property int $sortPriority
 * @property bool $enabled
 */
class PastAcceptedChatsNumber extends Model implements SortParameter
{
    public $pastMinutes;
    public $sortPriority;
    public $enabled;

    public function rules(): array
    {
        return [
            [['pastMinutes', 'sortPriority'], 'required'],
            [['pastMinutes', 'sortPriority'], 'integer'],
            [['pastMinutes', 'sortPriority'], 'filter', 'filter' => 'intval'],
            [['enabled'], 'boolean']
        ];
    }

    public function apply(EmployeeQuery $query): void
    {
        if ($this->pastMinutes && $this->enabled) {
            $time = time() - ($this->pastMinutes * 60);
            $acceptedChats = ClientChatUserAccess::find()->select(['ccua_user_id as uid', 'count(ccua_id) as cnt_accepted_chats'])
                ->where(['IN', 'ccua_status_id', [ClientChatUserAccess::STATUS_TRANSFER_ACCEPT, ClientChatUserAccess::STATUS_ACCEPT, ClientChatUserAccess::STATUS_TAKE]])
                ->andWhere(['>=', 'ccua_updated_dt', date('Y-m-d H:i:s', $time)])
                ->groupBy(['ccua_user_id']);

            $query->leftJoin('(' . $acceptedChats->createCommand()->rawSql . ') acceptedChats ', 'acceptedChats.uid = employees.id');
            $query->addOrderBy(['acceptedChats.cnt_accepted_chats' => SORT_ASC]);
        }
    }

    public function getSortPriority(): int
    {
        return (int)$this->sortPriority;
    }

    public function formName(): string
    {
        return '';
    }
}
