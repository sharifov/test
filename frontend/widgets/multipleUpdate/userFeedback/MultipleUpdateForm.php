<?php

namespace frontend\widgets\multipleUpdate\userFeedback;

use common\models\Employee;
use frontend\widgets\multipleUpdate\IdsValidator;
use modules\order\src\entities\order\OrderStatus;
use modules\user\userFeedback\entity\UserFeedback;
use yii\base\Model;

/**
 * Class MultipleUpdateForm
 *
 * @property int[] $ids
 * @property int|null $statusId
 *
 * @property Employee $authUser
 */
class MultipleUpdateForm extends Model
{
    public $ids;
    public $statusId;
    public $typeId;

    private $authUser;

    public function __construct(Employee $authUser, $config = [])
    {
        parent::__construct($config);
        $this->authUser = $authUser;
    }

    public function rules(): array
    {
        return [
            ['ids', IdsValidator::class, 'skipOnEmpty' => false],

            ['statusId', 'required'],
            ['statusId', 'integer'],
            ['statusId', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['statusId', 'in', 'range' => array_keys($this->statusList())],

            ['typeId', 'integer'],
            ['typeId', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['typeId', 'in', 'range' => array_keys($this->typeList())],
        ];
    }

    public function statusList(): array
    {
        return UserFeedback::getStatusList();
    }

    public function typeList(): array
    {
        return UserFeedback::getTypeList();
    }

    public function authUserId(): int
    {
        return $this->authUser->id;
    }

    public function attributeLabels(): array
    {
        return [
            'ids' => 'Ids',
            'statusId' => 'Status',
        ];
    }
}
