<?php

namespace frontend\controllers;

use common\models\UserCallStatus;
use common\models\UserProfile;
use src\auth\Auth;
use src\helpers\ErrorsToStringHelper;
use src\helpers\setting\SettingHelper;
use src\model\call\services\currentQueueCalls\CurrentQueueCallsService;
use src\model\call\useCase\createCall\CreateCallForm;
use src\model\call\useCase\createCall\fromCase\CreateCallFromCase;
use src\model\call\useCase\createCall\fromContacts\CreateCallFromContacts;
use src\model\call\useCase\createCall\fromHistory\CreateCallFromHistory;
use src\model\call\useCase\createCall\fromLead\CreateCallFromLead;
use src\model\call\useCase\createCall\internalCall\CreateInternalCall;
use src\model\call\useCase\createCall\simpleCall\CreateSimpleCall;
use src\model\leadRedial\assign\LeadRedialUnAssigner;
use src\model\user\entity\userStatus\UserStatus;
use src\model\voip\phoneDevice\log\PhoneDeviceLogger;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

/**
 * Class VoipController
 *
 * @property CurrentQueueCallsService $currentQueueCallsService
 * @property LeadRedialUnAssigner $leadRedialUnAssigner
 * @property PhoneDeviceLogger $phoneDeviceLogger
 */
class VoipController extends FController
{
    public $enableCsrfValidation = false;

    private CurrentQueueCallsService $currentQueueCallsService;
    private LeadRedialUnAssigner $leadRedialUnAssigner;
    private PhoneDeviceLogger $phoneDeviceLogger;

    public function __construct(
        $id,
        $module,
        PhoneDeviceLogger $phoneDeviceLogger,
        CurrentQueueCallsService $currentQueueCallsService,
        LeadRedialUnAssigner $leadRedialUnAssigner,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->currentQueueCallsService = $currentQueueCallsService;
        $this->leadRedialUnAssigner = $leadRedialUnAssigner;
        $this->phoneDeviceLogger = $phoneDeviceLogger;
    }

    public function behaviors(): array
    {
        $behaviors = [
            'access' => [
                'allowActions' => [
                    'create-call',
                    'log',
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function actionLog()
    {
        if (!SettingHelper::phoneDeviceLogsEnabled()) {
            return $this->asJson(['message' => 'log is disabled']);
        }

        $request = null;
        try {
            $request = file_get_contents('php://input');
            $filtered = preg_replace("/\n/", "", $request);
            $logs = json_decode($filtered, true, 512, JSON_THROW_ON_ERROR);
            $this->phoneDeviceLogger->log(Auth::id(), $logs['logs'], new \DateTimeImmutable());
        } catch (\Throwable $e) {
            \Yii::error([
                'logs' => VarDumper::dumpAsString($request),
                'message' => $e->getMessage(),
            ], 'PhoneDevice:Log');
            return $this->asJson(['message' => 'error']);
        }

        return $this->asJson(['message' => 'ok']);
    }

    public function actionCreateCall()
    {
        $createdUser = Auth::user();

        if (!Auth::can('PhoneWidget')) {
            return $this->asJson([
                'error' => true,
                'message' => 'Access denied.',
            ]);
        }

        $userProfile = UserProfile::find()->where(['up_user_id' => $createdUser->id])->limit(1)->one();
        if (!$userProfile || !$userProfile->canWebCall()) {
            return $this->asJson([
                'error' => true,
                'message' => 'Access denied.',
            ]);
        }

        if (!$createdUser->isOnline()) {
            return $this->asJson([
                'error' => true,
                'message' => 'User is offline. Please refresh page',
            ]);
        }

        if (!$createdUser->isCallFree()) {
            $userStatusType = UserCallStatus::find()->select(['us_type_id'])->where(['us_user_id' => $createdUser->id])->orderBy(['us_id' => SORT_DESC])->limit(1)->asArray()->one();
            $calls = $this->currentQueueCallsService->getQueuesCalls($createdUser->id, null, SettingHelper::isGeneralLinePriorityEnable());
            if ($calls->outgoing || $calls->active) {
                \Yii::info([
                    'message' => 'User wanted to make a call with active calls',
                    'userId' => $createdUser->id,
                    'calls' => $calls->toArray(),
                ], 'log\UserIsOnCall');
                return $this->asJson([
                    'error' => true,
                    'message' => 'You have an active call, please refresh the page or contact system administrator if the issue persist.',
                    'is_on_call' => true,
                    'phone_widget_data' => [
                        'calls' => $calls->toArray(),
                        'userStatus' => (int)($userStatusType['us_type_id'] ?? UserCallStatus::STATUS_TYPE_OCCUPIED),
                    ],
                ]);
            }
            \Yii::info([
                'message' => 'Was wrong value(is_on_call = true) in DB',
                'userId' => $createdUser->id,
            ], 'log\UserIsOnCall');
            UserStatus::isOnCallOff($createdUser->id);
        }

        $form = new CreateCallForm($createdUser);

        if ($form->load(\Yii::$app->request->post()) && $form->validate()) {
            $this->leadRedialUnAssigner->createCall($form->getCreatedUserId());

            if ($form->isInternalCall()) {
                return $this->asJson(
                    (new CreateInternalCall())($form)
                );
            }
            if ($form->fromHistoryCall()) {
                return $this->asJson(
                    (new CreateCallFromHistory())($form)
                );
            }
            if ($form->isFromCase()) {
                return $this->asJson(
                    (new CreateCallFromCase())($form)
                );
            }
            if ($form->isFromLead()) {
                return $this->asJson(
                    (new CreateCallFromLead())($form)
                );
            }
            if ($form->isFromContacts()) {
                return $this->asJson(
                    (new CreateCallFromContacts())($form)
                );
            }

            if (!Auth::can('PhoneWidget_Dialpad')) {
                return $this->asJson([
                    'error' => true,
                    'message' => 'Access denied.',
                ]);
            }
            return $this->asJson(
                (new CreateSimpleCall())($form)
            );
        }

        return $this->asJson([
            'error' => $form->hasErrors(),
            'message' => $form->hasErrors() ? ErrorsToStringHelper::extractFromModel($form) : null,
        ]);
    }
}
