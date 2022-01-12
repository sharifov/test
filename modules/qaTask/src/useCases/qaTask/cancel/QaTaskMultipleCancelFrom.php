<?php

namespace modules\qaTask\src\useCases\qaTask\cancel;

use common\components\validators\IsArrayValidator;
use common\models\Employee;
use modules\qaTask\src\entities\qaTaskActionReason\QaTaskActionReason;
use modules\qaTask\src\useCases\qaTask\QaTaskActions;
use modules\qaTask\src\entities\qaTaskStatus\QaTaskStatus;
use src\auth\Auth;
use yii\base\Model;

class QaTaskMultipleCancelFrom extends Model
{
    public $gids;
   // public $userId;
    public $actionId;
    public $comment;

    public $actionReasons;
    public $actionReasonsExists;

    public $status;

    public function __construct($config = [])
    {
        $this->actionReasons = QaTaskActionReason::find()->select(['tar_name', 'tar_id'])->where(['tar_action_id' => QaTaskActions::CANCEL, 'tar_enabled' => 1])->indexBy('tar_id')->asArray()->column();
        $this->actionReasonsExists = (bool)$this->actionReasons;
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            ['gids', 'required'],
            ['gids', IsArrayValidator::class],
            ['gids', 'each', 'rule' => ['string']],

            //['userId', 'safe'],
            /*[['userId', 'actionId'], 'integer'],
            [['userId', 'actionId'], 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['userId', 'exist', 'skipOnEmpty' => false, 'targetClass' => Employee::class, 'targetAttribute' => ['userId' => 'id']],*/

            ['comment', 'string', 'max' => 255],
            ['actionId', 'required', 'when' => function () {
                return $this->actionReasonsExists;
            }],
            ['actionId', function () {
                return $this->actionReasonsExists && array_key_exists($this->actionId, $this->actionReasons);
            }],
            ['comment', 'required', 'when' => function () {
                return QaTaskActionReason::find()->where(['tar_enabled' => 1, 'tar_id' => $this->actionId, 'tar_comment_required' => 1])->exists();
            }],

            [['actionId', 'comment'], 'required', 'skipOnEmpty' => true],

            [['status'], 'default', 'value' => QaTaskStatus::CANCELED],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'userId' => 'Agent',
            'actionId' => 'Reason'
        ];
    }
}
