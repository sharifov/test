<?php

namespace sales\forms\clientChat;

use common\models\Employee;
use sales\access\EmployeeGroupAccess;
use sales\model\clientChat\entity\ClientChat;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * Class MultipleAssignForm
 */
class MultipleAssignForm extends Model
{
    public $chatIds;
    public $assignUserId;

    private array $commonUsers = [];

    /**
     * @param int $userId
     * @param array $config
     */
    public function __construct(int $userId, $config = [])
    {
        $this->setCommonUsers($userId);
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['chatIds', 'assignUserId'], 'required'],
            ['chatIds', 'filter', 'filter' => static function ($value) {
                return Json::decode($value);
            }],
            ['chatIds', 'each', 'rule' => ['filter', 'filter' => 'intval']],
            ['chatIds', 'each', 'rule' => ['exist', 'targetClass' => ClientChat::class, 'targetAttribute' => 'cch_id']],

            ['assignUserId', 'integer'],
            ['assignUserId', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['assignUserId', 'in', 'range' => array_keys($this->getCommonUsers()), 'skipOnEmpty' => true],
        ];
    }

    private function setCommonUsers(int $userId): void
    {
        $commonUserIds = EmployeeGroupAccess::getUsersIdsInCommonGroups($userId);
        if (isset($commonUserIds[$userId])) {
            unset($commonUserIds[$userId]);
        }
        $data = Employee::find()->andWhere(['IN', 'id', $commonUserIds])->orderBy(['username' => SORT_ASC])->asArray()->all();
        $this->commonUsers = ArrayHelper::map($data, 'id', 'username');
    }

    public function getCommonUsers(): array
    {
        return $this->commonUsers;
    }
}
