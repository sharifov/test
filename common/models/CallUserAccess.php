<?php

namespace common\models;

use common\components\purifier\Purifier;
use common\models\query\CallUserAccessQuery;
use frontend\widgets\newWebPhone\call\socket\RemoveIncomingRequestMessage;
use modules\notification\src\abac\dto\NotificationAbacDto;
use modules\notification\src\abac\NotificationAbacObject;
use src\helpers\app\AppHelper;
use src\helpers\setting\SettingHelper;
use src\model\call\helper\CallHelper;
use frontend\widgets\notification\NotificationMessage;
use src\dispatchers\NativeEventDispatcher;
use src\model\call\entity\callUserAccess\events\CallUserAccessEvents;
use src\model\phoneList\entity\PhoneList;
use src\model\voip\phoneDevice\device\ReadyVoipDevice;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "call_user_access".
 *
 * @property int $cua_call_id
 * @property int $cua_user_id
 * @property int $cua_status_id
 * @property string $cua_created_dt
 * @property string $cua_updated_dt
 * @property string $cua_priority
 *
 * @property Call $cuaCall
 * @property Employee $cuaUser
 */
class CallUserAccess extends \yii\db\ActiveRecord
{
    public const STATUS_TYPE_PENDING = 1;
    public const STATUS_TYPE_ACCEPT = 2;
    public const STATUS_TYPE_SKIP = 3;
    public const STATUS_TYPE_BUSY = 4;
    public const STATUS_TYPE_NO_ANSWERED = 5;
    public const STATUS_TYPE_WARM_TRANSFER = 6;

    public const STATUS_TYPE_LIST = [
        self::STATUS_TYPE_PENDING       => 'Pending',
        self::STATUS_TYPE_ACCEPT        => 'Accept',
        self::STATUS_TYPE_SKIP          => 'Skip',
        self::STATUS_TYPE_BUSY          => 'Busy',
        self::STATUS_TYPE_NO_ANSWERED   => 'No Answered',
        self::STATUS_TYPE_WARM_TRANSFER => 'Warm transfer',
    ];

    public const STATUS_TYPE_LIST_LABEL = [
        self::STATUS_TYPE_PENDING       => 'label-warning',
        self::STATUS_TYPE_ACCEPT        => 'label-success',
        self::STATUS_TYPE_SKIP          => 'label-default',
        self::STATUS_TYPE_BUSY          => 'label-default',
        self::STATUS_TYPE_NO_ANSWERED   => 'label-default',
        self::STATUS_TYPE_WARM_TRANSFER => 'label-default',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'call_user_access';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cua_call_id', 'cua_user_id'], 'required'],
            [['cua_call_id', 'cua_user_id'], 'integer'],
            [['cua_created_dt', 'cua_updated_dt'], 'safe'],
            [['cua_call_id', 'cua_user_id'], 'unique', 'targetAttribute' => ['cua_call_id', 'cua_user_id']],
            [['cua_call_id'], 'exist', 'skipOnError' => true, 'targetClass' => Call::class, 'targetAttribute' => ['cua_call_id' => 'c_id']],
            [['cua_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['cua_user_id' => 'id']],

            ['cua_priority', 'default', 'value' => 0],
            ['cua_priority', 'integer', 'min' => 0, 'max' => 6500],
            ['cua_priority', 'filter', 'filter' => 'intval', 'skipOnError' => true, 'skipOnEmpty' => true],

            ['cua_status_id', 'integer'],
            ['cua_status_id', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true, 'skipOnError' => true],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'cua_call_id' => 'Call ID',
            'cua_user_id' => 'User ID',
            'cua_status_id' => 'Status ID',
            'cua_created_dt' => 'Created Dt',
            'cua_updated_dt' => 'Updated Dt',
            'cua_priority' => 'Priority',
        ];
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['cua_created_dt', 'cua_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['cua_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCuaCall()
    {
        return $this->hasOne(Call::class, ['c_id' => 'cua_call_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCuaUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'cua_user_id']);
    }

    /**
     * {@inheritdoc}
     * @return CallUserAccessQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CallUserAccessQuery(static::class);
    }

    /**
     * @return array
     */
    public static function getStatusTypeList(): array
    {
        return self::STATUS_TYPE_LIST;
    }

    /**
     * @return mixed|string
     */
    public function getStatusTypeName()
    {
        return self::STATUS_TYPE_LIST[$this->cua_status_id] ?? '-';
    }

    public function acceptPending(): void
    {
        $this->cua_status_id = self::STATUS_TYPE_PENDING;
    }

    public function acceptCall(): void
    {
        $this->cua_status_id = self::STATUS_TYPE_ACCEPT;
    }

    public function skipCall(): void
    {
        $this->cua_status_id = self::STATUS_TYPE_SKIP;
    }

    public function busyCall(): void
    {
        $this->cua_status_id = self::STATUS_TYPE_BUSY;
    }

    public function noAnsweredCall(): void
    {
        $this->cua_status_id = self::STATUS_TYPE_NO_ANSWERED;
    }

    public function isPending(): bool
    {
        return $this->cua_status_id === self::STATUS_TYPE_PENDING;
    }

    public function warmTransfer(): void
    {
        $this->cua_status_id = self::STATUS_TYPE_WARM_TRANSFER;
    }

    public function isWarmTransfer(): bool
    {
        return $this->cua_status_id === self::STATUS_TYPE_WARM_TRANSFER;
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        $call = $this->cuaCall;

        // when direct call to online user and original user not answered
        $isRedirectTransferCall = $call->c_source_type_id === Call::SOURCE_DIRECT_CALL && $call->c_created_user_id !== $this->cua_user_id;

        if ($insert) {
            if ($call && !$call->isHold() && !$this->isWarmTransfer()) {
                if (
                    SettingHelper::isGeneralLinePriorityEnable()
                    && ($call->c_source_type_id === Call::SOURCE_GENERAL_LINE || $call->c_source_type_id === Call::SOURCE_REDIRECT_CALL)
                ) {
                    $message = 'New General Line Call';

                    $notification = new Notifications();
                    $notification->n_title = 'New General Line Call';
                    $notification->n_type_id = Notifications::TYPE_SUCCESS;
                    $notification->n_user_id = $this->cua_user_id;

                    $notificationAbacDto = new NotificationAbacDto($notification);

                    if (Yii::$app->abac->can($notificationAbacDto, NotificationAbacObject::OBJ_NOTIFICATION, NotificationAbacObject::ACTION_ACCESS, $this->cuaUser)) {
                        if ($ntf = Notifications::create($this->cua_user_id, 'New General Line Call', $message, Notifications::TYPE_SUCCESS, true)) {
                            $dataNotification = (Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
                            Notifications::publish('getNewNotification', ['user_id' => $this->cua_user_id], $dataNotification);
                        }
                    }
                } else {
                    $message = 'New incoming Call' . ' (' . $this->cua_call_id . ')';
                    if (!$isRedirectTransferCall) {
                        if (isset($call->cLead)) {
                            $message .= '<br> Lead (Id: ' . Purifier::createLeadShortLink($call->cLead) . ')';
                        }
                        if (isset($call->cCase)) {
                            $message .= '<br> Case (Id: ' . Purifier::createCaseShortLink($call->cCase) . ')';
                        }
                    }
                    if ($ntf = Notifications::create($this->cua_user_id, 'New incoming Call (' . $this->cua_call_id . ')', $message, Notifications::TYPE_SUCCESS, true)) {
                        //Notifications::socket($this->cua_user_id, null, 'getNewNotification', [], true);
                        $dataNotification = (Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
                        Notifications::publish('getNewNotification', ['user_id' => $this->cua_user_id], $dataNotification);
                    }
                }
            }

            NativeEventDispatcher::recordEvent(CallUserAccessEvents::class, CallUserAccessEvents::INSERT, [CallUserAccessEvents::class, 'updateUserStatus'], $this);
            NativeEventDispatcher::trigger(CallUserAccessEvents::class, CallUserAccessEvents::INSERT);
        }

        if (($insert || isset($changedAttributes['cua_status_id'])) && $call && ($call->isIn() || $call->isHold() || $this->isWarmTransfer())) {
//        if(($insert || isset($changedAttributes['cua_status_id']))) {
            //Notifications::socket($this->cua_user_id, null, 'updateIncomingCall', $this->attributes);
            if (
                SettingHelper::isGeneralLinePriorityEnable()
                && (($call->c_source_type_id === Call::SOURCE_GENERAL_LINE || $call->c_source_type_id === Call::SOURCE_REDIRECT_CALL) || $isRedirectTransferCall)
                && !$call->isHold()
                && !$this->isWarmTransfer()
            ) {
                if ($this->isPending()) {
                    Notifications::publish(
                        'addPriorityCall',
                        ['user_id' => $this->cua_user_id],
                        [
                            'data' =>
                                array_merge($this->attributes, [
                                    'command' => 'addPriorityCall',
                                    'project' => $call->c_project_id ? $call->cProject->name : '',
                                    'department' => $call->c_dep_id ? Department::getName($call->c_dep_id) : '',
                                ])
                        ]
                    );
                } else {
                    Notifications::publish(
                        'removePriorityCall',
                        ['user_id' => $this->cua_user_id],
                        [
                            'data' =>
                                array_merge($this->attributes, [
                                    'command' => 'removePriorityCall',
                                    'project' => $call->c_project_id ? $call->cProject->name : '',
                                    'department' => $call->c_dep_id ? Department::getName($call->c_dep_id) : '',
                                ])
                        ]
                    );
                }
            } else {
                if ($this->isPending() || $this->isWarmTransfer()) {
                    $client = $this->cuaCall->cClient;

                    $name = '';
                    $phone = '';
                    if ($call->isJoin()) {
                        if (($parent = $call->cParent) && $parent->cCreatedUser) {
                            $name = $parent->cCreatedUser->nickname;
                            $phone = $parent->c_to;
                        }
                    } else {
                        $name = $call->getCallerName($call->c_from);
                        if ($call->isIn()) {
                            $phone = $call->c_from;
                        } elseif ($call->isOut()) {
                            if ($this->isWarmTransfer()) {
                                $phone = $call->c_from;
                            } else {
                                $phone = $call->c_to;
                            }
                        }
                    }

                    $callAntiSpam = [];
                    $callAntiSpamData = [];
                    if ($call->cParent && $call->cParent->c_data_json) {
                        $callAntiSpam = json_decode($call->cParent->c_data_json, true)['callAntiSpamData'] ?? [];
                    }
                    if ($callAntiSpam) {
                        $callAntiSpamData = [
                            'type' => $callAntiSpam['type'] ?? null,
                            'rate' => $callAntiSpam['rate'] ?? 0,
                            'trustPercent' => $callAntiSpam['trustPercent'] ?? null
                        ];
                    }

                    $auth = Yii::$app->authManager;

                    $callInfo = [
                        'id' => $call->c_id,
                        'callSid' => $call->c_call_sid,
                        'conferenceSid' => $call->c_conference_sid,
                        'status' => $call->getStatusName(),
                        'duration' => 0,
                        'leadId' => $call->c_lead_id,
                        'typeId' => $this->isWarmTransfer() ? Call::CALL_TYPE_IN : $call->c_call_type_id,
                        'type' => $this->isWarmTransfer() ? 'Incoming' : CallHelper::getTypeDescription($this->cuaCall),
                        'source_type_id' => $this->isWarmTransfer() ? Call::SOURCE_DIRECT_CALL : $call->c_source_type_id,
                        'fromInternal' => PhoneList::find()->byPhone($this->cuaCall->c_from)->enabled()->exists(),
                        'isHold' => false,
                        'holdDuration' => 0,
                        'isListen' => false,
                        'isCoach' => false,
                        'isBarge' => false,
                        'isMute' => false,
                        'project' => $call->c_project_id ? $call->cProject->name : '',
                        'source' => $this->isWarmTransfer() ? Call::SOURCE_LIST[Call::SOURCE_DIRECT_CALL] : ($call->c_source_type_id ? $call->getSourceName() : ''),
                        'isEnded' => false,
                        'contact' => [
                            'id' => $call->c_client_id,
                            'name' => $name,
                            'phone' => $phone,
                            'company' => '',
                            'isClient' => $call->c_client_id ? $call->cClient->isClient() : false,
                            'canContactDetails' => $auth->checkAccess($this->cua_user_id, '/client/ajax-get-info'),
                            'canCallInfo' => $auth->checkAccess($this->cua_user_id, '/call/ajax-call-info'),
                            'callSid' => $call->c_call_sid,
                        ],
                        'department' => $call->c_dep_id ? Department::getName($call->c_dep_id) : '',
                        'queue' => $this->isWarmTransfer() ? Call::QUEUE_DIRECT : Call::getQueueName($call),
                        'isWarmTransfer' => $this->isWarmTransfer(),
                        'callAntiSpamData' => $callAntiSpamData
                    ];
                }
                Notifications::publish(
                    'updateIncomingCall',
                    ['user_id' => $this->cua_user_id],
                    array_merge($this->attributes, $callInfo ?? ['callSid' => $call->c_call_sid])
                );
            }
        }

        if (isset($changedAttributes['cua_status_id']) && $call && ($call->isIn() || $call->isHold()) && $this->cua_status_id === self::STATUS_TYPE_NO_ANSWERED) {
            Notifications::publish(RemoveIncomingRequestMessage::COMMAND, ['user_id' => $this->cua_user_id], RemoveIncomingRequestMessage::create($call->c_call_sid));
        }
        if (isset($changedAttributes['cua_status_id']) && $changedAttributes['cua_status_id'] == self::STATUS_TYPE_WARM_TRANSFER && $this->cua_status_id === self::STATUS_TYPE_NO_ANSWERED) {
            Notifications::publish(RemoveIncomingRequestMessage::COMMAND, ['user_id' => $this->cua_user_id], RemoveIncomingRequestMessage::create($call->c_call_sid));
        }

        if (!$insert && isset($changedAttributes['cua_status_id'])) {
            NativeEventDispatcher::recordEvent(CallUserAccessEvents::class, CallUserAccessEvents::UPDATE, [CallUserAccessEvents::class, 'updateUserStatus'], $this);
            NativeEventDispatcher::trigger(CallUserAccessEvents::class, CallUserAccessEvents::UPDATE);
        }

        $this->sendFrontendData($insert ? 'insert' : 'update');
//        if ($call) {
//            $call->sendFrontendData();
//        }
    }

    /**
     * @return bool
     */
    public function beforeDelete(): bool
    {
        if (!parent::beforeDelete()) {
            return false;
        }
        $this->sendFrontendData('delete');
        NativeEventDispatcher::recordEvent(CallUserAccessEvents::class, CallUserAccessEvents::DELETE, [CallUserAccessEvents::class, 'resetHasCallAccess'], $this);
        return true;
    }


    /**
     *
     */
    public function afterDelete(): void
    {
        parent::afterDelete();
        NativeEventDispatcher::trigger(CallUserAccessEvents::class, CallUserAccessEvents::DELETE);
    }

    /**
     * @param string $action
     * @return false|mixed
     */
    public function sendFrontendData(string $action = 'update')
    {
        $enabled = !empty(Yii::$app->params['centrifugo']['enabled']);

        if ($enabled) {
            try {
                return Yii::$app->centrifugo->setSafety(false)
                ->publish(
                    Call::CHANNEL_REALTIME_MAP,
                    [
                        'object' => 'callUserAccess',
                        'action' => $action,
                        'data' => [
                            'callUserAccess' => ArrayHelper::toArray($this, [
                                CallUserAccess::class => [
                                    'user_id' => 'cua_user_id',
                                    'cua_call_id',
                                    'cua_user_id',
                                    'cua_status_id',
                                    'cua_created_dt',
                                    'cua_updated_dt',
                                    'cua_priority',
                                    'userDep' => function (CallUserAccess $cua) {
                                        $deps = [];
                                        foreach ($cua->cuaUser->udDeps as $dep) {
                                            if (isset($dep->dep_id)) {
                                                $deps[] = $dep->dep_id;
                                            }
                                        }
                                        return $deps;
                                    }
                                ],
                            ]),
                        ]
                    ]
                );
            } catch (\Throwable $throwable) {
                Yii::error(AppHelper::throwableFormatter($throwable), 'CallUserAccess:sendFrontendData:Throwable');
                return false;
            }
        }
        return false;
    }

    /**
     * @return array
     */
    public static function getStatusTypeListApi(): array
    {
        $data = [];
        if (self::STATUS_TYPE_LIST) {
            foreach (self::STATUS_TYPE_LIST as $id => $name) {
                $data[] = [
                    'id' => $id,
                    'name' => $name,
                ];
            }
        }
        return $data;
    }
}
