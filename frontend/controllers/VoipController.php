<?php

namespace frontend\controllers;

use common\models\Call;
use common\models\Client;
use common\models\Employee;
use common\models\Lead;
use common\models\UserCallStatus;
use sales\auth\Auth;
use sales\entities\cases\Cases;
use sales\helpers\ErrorsToStringHelper;
use sales\helpers\setting\SettingHelper;
use sales\helpers\UserCallIdentity;
use sales\model\call\services\currentQueueCalls\CurrentQueueCallsService;
use sales\model\call\services\FriendlyName;
use sales\model\call\services\RecordManager;
use sales\model\call\useCase\createCall\CreateCallForm;
use sales\model\callLog\entity\callLog\CallLog;
use sales\model\phone\AvailablePhoneList;
use sales\model\user\entity\userStatus\UserStatus;
use yii\web\Controller;

/**
 * Class VoipController
 *
 * @property CurrentQueueCallsService $currentQueueCallsService
 */
class VoipController extends Controller
{
    private CurrentQueueCallsService $currentQueueCallsService;

    public function __construct($id, $module, CurrentQueueCallsService $currentQueueCallsService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->currentQueueCallsService = $currentQueueCallsService;
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionCreateCall()
    {
        $createdUser = Auth::user();

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
                \Yii::error([
                    'message' => 'User wanted to make a call with active calls',
                    'userId' => $createdUser->id,
                    'calls' => $calls->toArray(),
                ], 'UserIsOnCall');
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
            \Yii::error([
                'message' => 'Was wrong value(is_on_call = true) in DB',
                'userId' => $createdUser->id,
            ], 'UserIsOnCall');
            UserStatus::isOnCallOff($createdUser->id);
        }

        $form = new CreateCallForm($createdUser->id);

        if ($form->load(\Yii::$app->request->post()) && $form->validate()) {
            if ($form->isInternalCall()) {
                $result = $this->createInternalCall($createdUser, $form->toUserId);
                return $this->asJson($result);
            }
            if ($form->fromHistoryCall()) {
                $result = $this->createCallFromHistory($form);
                return $this->asJson($result);
            }
            if ($form->isFromCase()) {
                $result = $this->createCallFromCase($form);
                return $this->asJson($result);
            }
            if ($form->isFromLead()) {
                $result = $this->createCallFromLead($form);
                return $this->asJson($result);
            }
            if ($form->isFromContacts()) {
                $result = $this->createCallFromContacts($form);
                return $this->asJson($result);
            }
            $result = $this->createSimpleCall($form);
            return $this->asJson($result);
        }

        return $this->asJson([
            'error' => $form->hasErrors(),
            'message' => $form->hasErrors() ? ErrorsToStringHelper::extractFromModel($form) : null,
        ]);
    }

    private function createSimpleCall(CreateCallForm $form): array
    {
        try {
            $recordDisabled = (RecordManager::createCall(
                Auth::id(),
                null, //todo
                null,
                $form->from,
                null,
            ))->isDisabledRecord();

            $result = \Yii::$app->communication->createCall(
                new \sales\model\call\useCase\conference\create\CreateCallForm([
                    'user_identity' => UserCallIdentity::getClientId($form->getCreatedUserId()),
                    'user_id' => $form->getCreatedUserId(),
                    'to_number' => $form->to,
                    'from_number' => $form->from,
                    'phone_list_id' => $form->getPhoneListId(),
                    'project_id' => null, //todo
                    'call_recording_disabled' => $recordDisabled,
                    'friendly_name' => FriendlyName::next(),
                ])
            );
        } catch (\Throwable $e) {
            $result = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }

        return $result;
    }

    private function createCallFromContacts(CreateCallForm $form): array
    {
        try {
            if (!$client = Client::findOne(['id' => $form->clientId])) {
                throw new \DomainException('Not found Client. ID: ' . $form->clientId);
            }

            $recordDisabled = (RecordManager::createCall(
                Auth::id(),
                null, //todo
                null,
                $form->from,
                $client->id
            ))->isDisabledRecord();

            $result = \Yii::$app->communication->createCall(
                new \sales\model\call\useCase\conference\create\CreateCallForm([
                    'user_identity' => UserCallIdentity::getClientId($form->getCreatedUserId()),
                    'user_id' => $form->getCreatedUserId(),
                    'to_number' => $form->to,
                    'from_number' => $form->from,
                    'phone_list_id' => $form->getPhoneListId(),
                    'project_id' => null, //todo
                    'client_id' => $client->id,
                    'call_recording_disabled' => $recordDisabled,
                    'friendly_name' => FriendlyName::next(),
                ])
            );
        } catch (\Throwable $e) {
            $result = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }

        return $result;
    }

    private function createCallFromCase(CreateCallForm $form): array
    {
        try {
            if (!$case = Cases::findOne(['cs_id' => $form->caseId])) {
                throw new \DomainException('Not found Case. ID: ' . $form->caseId);
            }

            if (!$case->cs_project_id) {
                throw new \DomainException('Not found Project. Case ID: ' . $case->cs_id);
            }

            if (!$case->cs_dep_id) {
                throw new \DomainException('Not found Department. Case ID: ' . $case->cs_id);
            }

            $recordDisabled = (RecordManager::createCall(
                Auth::id(),
                $case->cs_project_id,
                $case->cs_dep_id,
                $form->from,
                $case->cs_client_id
            ))->isDisabledRecord();

            $result = \Yii::$app->communication->createCall(
                new \sales\model\call\useCase\conference\create\CreateCallForm([
                    'user_identity' => UserCallIdentity::getClientId($form->getCreatedUserId()),
                    'user_id' => $form->getCreatedUserId(),
                    'to_number' => $form->to,
                    'from_number' => $form->from,
                    'phone_list_id' => $form->getPhoneListId(),
                    'project_id' => $case->cs_project_id,
                    'department_id' => $case->cs_dep_id,
                    'case_id' => $case->cs_id,
                    'client_id' => $case->cs_client_id,
                    'source_type_id' => Call::SOURCE_CASE,
                    'call_recording_disabled' => $recordDisabled,
                    'friendly_name' => FriendlyName::next(),
                ])
            );
        } catch (\Throwable $e) {
            $result = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }

        return $result;
    }

    private function createCallFromLead(CreateCallForm $form): array
    {
        try {
            if (!$lead = Lead::findOne(['id' => $form->leadId])) {
                throw new \DomainException('Not found Lead. ID: ' . $form->leadId);
            }

            if (!$lead->project_id) {
                throw new \DomainException('Not found Project. Lead ID: ' . $lead->id);
            }

            if (!$lead->l_dep_id) {
                throw new \DomainException('Not found Department. Lead ID: ' . $lead->id);
            }

            $recordDisabled = (RecordManager::createCall(
                Auth::id(),
                $lead->project_id,
                $lead->l_dep_id,
                $form->from,
                $lead->client_id
            ))->isDisabledRecord();

            $result = \Yii::$app->communication->createCall(
                new \sales\model\call\useCase\conference\create\CreateCallForm([
                    'user_identity' => UserCallIdentity::getClientId($form->getCreatedUserId()),
                    'user_id' => $form->getCreatedUserId(),
                    'to_number' => $form->to,
                    'from_number' => $form->from,
                    'phone_list_id' => $form->getPhoneListId(),
                    'project_id' => $lead->project_id,
                    'department_id' => $lead->l_dep_id,
                    'lead_id' => $lead->id,
                    'client_id' => $lead->client_id,
                    'source_type_id' => Call::SOURCE_LEAD,
                    'call_recording_disabled' => $recordDisabled,
                    'friendly_name' => FriendlyName::next(),
                ])
            );
        } catch (\Throwable $e) {
            $result = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }

        return $result;
    }

    private function createCallFromHistory(CreateCallForm $form): array
    {
        try {
            if (!$call = CallLog::findOne(['cl_call_sid' => $form->historyCallSid])) {
                throw new \DomainException('Not found Call. History Call SID: ' . $form->historyCallSid);
            }

            if (!$call->cl_project_id) {
                throw new \DomainException('Not found Project. History Call SID: ' . $form->historyCallSid);
            }

            if (!$call->cl_department_id) {
                throw new \DomainException('Not found Department. History Call SID: ' . $form->historyCallSid);
            }

            if (!$departmentParams = $call->department->getParams()) {
                throw new \DomainException('Not found Params. History Call SID: ' . $form->historyCallSid . ' Department: ' . $call->department->dep_name);
            }

            $phoneFrom = [
                'phone' => null,
                'phoneListId' => null
            ];

            if ($call->isOut()) {
                if (UserCallIdentity::canParse($call->cl_phone_from)) {
                    $list = new AvailablePhoneList(Auth::id(), $call->cl_project_id, $call->cl_department_id, $departmentParams->defaultPhoneType);
                    $phoneFrom = $list->getFirst();
                } else {
                    $phoneFrom = [
                        'phone' => $call->cl_phone_from,
                        'phoneListId' => $call->cl_phone_list_id,
                    ];
                }
            } elseif ($call->isIn()) {
                $list = new AvailablePhoneList(Auth::id(), $call->cl_project_id, $call->cl_department_id, $departmentParams->defaultPhoneType);
                $phoneFrom = $list->getFirst();
            }

            if (!$phoneFrom['phone']) {
                throw new \DomainException('Phone From not found. History Call SID: ' . $form->historyCallSid);
            }

            $recordDisabled = (RecordManager::createCall(
                Auth::id(),
                $call->cl_project_id,
                $call->cl_department_id,
                $phoneFrom['phone'],
                $call->cl_client_id
            ))->isDisabledRecord();

            $sourceTypeId = null;
            $leadId = null;
            $caseId = null;

            if ($call->isOut()) {
                $sourceTypeId = $call->cl_category_id;
                $leadId = $call->callLogLead->cll_lead_id ?? null;
                $caseId = $call->callLogCase->clc_case_id ?? null;
            } elseif ($call->isIn()) {
                if ($call->cl_department_id) {
                    $departmentParams = $call->department->getParams();
                    if ($departmentParams) {
                        if ($departmentParams->object->type->isLead()) {
                            if (isset($call->callLogLead->cll_lead_id)) {
                                $sourceTypeId = Call::SOURCE_LEAD;
                                $leadId = $call->callLogLead->cll_lead_id;
                            }
                        } elseif ($departmentParams->object->type->isCase()) {
                            if (isset($call->callLogCase->clc_case_id)) {
                                $sourceTypeId = Call::SOURCE_CASE;
                                $caseId = $call->callLogCase->clc_case_id;
                            }
                        }
                    }
                }
            }

            $result = \Yii::$app->communication->createCall(
                new \sales\model\call\useCase\conference\create\CreateCallForm([
                    'user_identity' => UserCallIdentity::getClientId($form->getCreatedUserId()),
                    'user_id' => $form->getCreatedUserId(),
                    'to_number' => $form->to,
                    'from_number' => $phoneFrom['phone'],
                    'phone_list_id' => $phoneFrom['phoneListId'],
                    'project_id' => $call->cl_project_id,
                    'department_id' => $call->cl_department_id,
                    'lead_id' => $leadId,
                    'case_id' => $caseId,
                    'client_id' => $call->cl_client_id,
                    'source_type_id' => $sourceTypeId,
                    'call_recording_disabled' => $recordDisabled,
                    'friendly_name' => FriendlyName::next(),
                ])
            );
        } catch (\Throwable $e) {
            $result = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }

        return $result;
    }

    private function createInternalCall(Employee $createdUser, int $toUserId): array
    {
        try {
            $key = 'call_user_to_user_' . $createdUser->id;

            if ($result = \Yii::$app->cache->get($key)) {
                throw new \DomainException('Please wait ' . abs($result - time()) . ' seconds.');
            }

            $this->guardToUserIsFree($toUserId);

            \Yii::$app->cache->set($key, (time() + 10), 10);

            $recordingManager = RecordManager::toUser($createdUser->id);

            $result = \Yii::$app->communication->callToUser(
                UserCallIdentity::getClientId($createdUser->id),
                UserCallIdentity::getClientId($toUserId),
                $toUserId,
                $createdUser->id,
                [
                    'status' => 'Ringing',
                    'duration' => 0,
                    'typeId' => Call::CALL_TYPE_IN,
                    'type' => 'Incoming',
                    'source_type_id' => Call::SOURCE_INTERNAL,
                    'fromInternal' => 'false',
                    'isInternal' => 'true',
                    'isHold' => 'false',
                    'holdDuration' => 0,
                    'isListen' => 'false',
                    'isCoach' => 'false',
                    'isMute' => 'false',
                    'isBarge' => 'false',
                    'project' => '',
                    'source' => Call::SOURCE_LIST[Call::SOURCE_INTERNAL],
                    'isEnded' => 'false',
                    'contact' => [
                        'name' => $createdUser->nickname ?: $createdUser->username,
                        'phone' => '',
                        'company' => '',
                    ],
                    'department' => '',
                    'queue' => Call::QUEUE_DIRECT,
                    'conference' => [],
                    'isConferenceCreator' => 'false',
                    'recordingDisabled' => $recordingManager->isDisabledRecord(),
                ],
                FriendlyName::next(),
                $recordingManager->isDisabledRecord()
            );
        } catch (\Throwable $e) {
            $result = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }

        return $result;
    }

    private function guardToUserIsFree(int $userId): void
    {
        if (!$user = Employee::findOne(['id' => $userId])) {
            throw new \DomainException('Not found user. Id: ' . $userId);
        }
        if (!$user->isOnline()) {
            throw new \DomainException('User ' . ($user->nickname ?: $user->full_name) . ' is offline');
        }
        if (!$user->isCallFree()) {
            throw new \DomainException('User ' . ($user->nickname ?: $user->full_name) . ' is occupied');
        }
    }
}
