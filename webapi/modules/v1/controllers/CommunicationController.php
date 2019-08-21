<?php
namespace webapi\modules\v1\controllers;

use common\components\jobs\CallQueueJob;
use common\models\Call;
use common\models\CallSession;
use common\models\ClientPhone;
use common\models\Department;
use common\models\DepartmentPhoneProject;
use common\models\Email;
use common\models\Employee;
use common\models\Lead;
use common\models\Lead2;
use common\models\Notifications;
use common\models\Project;
use common\models\Sms;
use common\models\Sources;
use common\models\UserProfile;
use common\models\UserProjectParams;
use sales\services\api\communication\CommunicationService;
use Twilio\TwiML\VoiceResponse;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UnprocessableEntityHttpException;
use common\components\ReceiveEmailsJob;
use yii\queue\Queue;
use common\models\ProjectEmployeeAccess;

/**
 * Class CommunicationController
 * @property CommunicationService $communicationService
 */
class CommunicationController extends ApiBaseController
{

    public const ACTION_GET     = 'get';
    public const ACTION_sET     = 'set';
    public const ACTION_READ    = 'read';
    public const ACTION_UPDATE  = 'update';
    public const ACTION_CREATE  = 'create';
    public const ACTION_DELETE  = 'delete';

    public const TYPE_VOIP_RECORD       = 'voip_record';
    public const TYPE_VOIP_INCOMING     = 'voip_incoming';
    public const TYPE_VOIP_GATHER       = 'voip_gather';
    public const TYPE_VOIP              = 'voip';
    public const TYPE_VOIP_CLIENT       = 'voip_client';


    public const TYPE_UPDATE_EMAIL_STATUS = 'update_email_status';
    public const TYPE_UPDATE_SMS_STATUS = 'update_sms_status';

    public const TYPE_NEW_EMAIL_MESSAGES_RECEIVED = 'new_email_messages_received';
    public const TYPE_NEW_SMS_MESSAGES_RECEIVED = 'new_sms_messages_received';

    public const TYPE_VOIP_FINISH       = 'voip_finish';
    public const TYPE_SMS_FINISH        = 'sms_finish';

    private $communicationService;

    public function __construct($id, $module, CommunicationService $communicationService, $config = [])
    {
        $this->communicationService = $communicationService;
        parent::__construct($id, $module, $config);
    }

    /**
     * @api {post} /v1/communication/email Communication Email
     * @apiVersion 0.1.0
     * @apiName CommunicationEmail
     * @apiGroup Communication
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization    Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiSuccessExample Success-Response:
     *  HTTP/1.1 200 OK
     *  {
     *      "type": "update_email_status",
     *      "eq_id": 127,
     *      "eq_status_id": 5,
     *  }
     *
     *
     * @return array
     * @throws NotFoundHttpException
     * @throws UnprocessableEntityHttpException
     * @throws \yii\web\BadRequestHttpException
     */

    public function actionEmail(): array
    {
        $this->checkPost();
        $apiLog = $this->startApiLog($this->action->uniqueId);

        //$action = Yii::$app->request->post('action');
        $type = Yii::$app->request->post('type');
        $last_id = Yii::$app->request->post('last_email_id', NULL);
        /*if(!$action) {
            throw new NotFoundHttpException('Not found action', 1);
        }*/

        if(!$type) {
            throw new NotFoundHttpException('Not found Email type', 1);
        }


        switch ($type) {
            case self::TYPE_UPDATE_EMAIL_STATUS : $response = $this->updateEmailStatus();
                break;
            case self::TYPE_NEW_EMAIL_MESSAGES_RECEIVED : $response = $this->newEmailMessagesReceived($last_id);
                break;
            default: throw new BadRequestHttpException('Invalid Email type', 2);
        }

        $responseData = [];

        if (isset($response['error']) && $response['error']) {

        } else {
            $responseData = [
                'status'    => 200,
                'name'      => 'Success',
                'code'      => 0,
                'message'   => ''
            ];
        }

        $responseData['data']['response'] = $response;
        // $responseData['data']['request']                = $modelLead;
        $responseData = $apiLog->endApiLog($responseData);

        if (isset($response['error']) && $response['error']) {
            $json = @json_encode($response['error']);
            if (isset($response['error_code']) && $response['error_code']) {
                $error_code = $response['error_code'];
            } else {
                $error_code = 0;
            }
            throw new UnprocessableEntityHttpException($json, $error_code);
        }

        return $responseData;
    }


    /**
     * @api {post} /v1/communication/sms Communication SMS
     * @apiVersion 0.1.0
     * @apiName CommunicationSms
     * @apiGroup Communication
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization    Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiSuccessExample Success-Response:
     *  HTTP/1.1 200 OK
     *  {
     *      "type": "update_sms_status",
     *      "sq_id": 127,
     *      "sq_status_id": 5,
     *  }
     *
     *
     * @return array
     * @throws NotFoundHttpException
     * @throws UnprocessableEntityHttpException
     * @throws \yii\web\BadRequestHttpException
     */

    public function actionSms(): array
    {
        $this->checkPost();
        $apiLog = $this->startApiLog($this->action->uniqueId);

        //$action = Yii::$app->request->post('action');
        $type = Yii::$app->request->post('type');

        /*if(!$action) {
            throw new NotFoundHttpException('Not found action', 1);
        }*/

        if(!$type) {
            throw new NotFoundHttpException('Not found type', 1);
        }


        switch ($type) {
            case self::TYPE_UPDATE_SMS_STATUS : $response = $this->updateSmsStatus();
                break;
            case self::TYPE_NEW_SMS_MESSAGES_RECEIVED : $response = $this->newSmsMessagesReceived();
                break;
            case self::TYPE_SMS_FINISH : $response = $this->smsFinish();
                break;
            default: throw new BadRequestHttpException('Invalid type', 2);
        }

        $responseData = [];

        if (isset($response['error']) && $response['error']) {

        } else {
            $responseData = [
                'status'    => 200,
                'name'      => 'Success',
                'code'      => 0,
                'message'   => ''
            ];
        }

        $responseData['data']['response'] = $response;
        $responseData = $apiLog->endApiLog($responseData);

        if (isset($response['error']) && $response['error']) {
            $json = @json_encode($response['error']);
            if (isset($response['error_code']) && $response['error_code']) {
                $error_code = $response['error_code'];
            } else {
                $error_code = 0;
            }
            throw new UnprocessableEntityHttpException($json, $error_code);
        }

        return $responseData;
    }


    /**
     * @param string $agent_phone_number
     * @param string $client_phone_number
     * @param int|null $call_dep_id
     * @param int $limit
     * @return array
     */
    protected function getDirectAgentsByPhoneNumber(string $agent_phone_number, string $client_phone_number, ?int $call_dep_id, int $limit = 10): array
    {
        $call_employee = [];
        $call_agent_username = [];
        $upp = UserProjectParams::find()->where(['upp_tw_phone_number' => $agent_phone_number])->one();
        $user = null;
        $call_user_id = null;
        $call_project_id = null;

        if ($upp && $user = $upp->uppUser) {
            $call_user_id = (int)$upp->upp_user_id;
            $call_project_id = (int)$upp->upp_project_id;



            if ($user->isOnline()) {
                if ($user->isCallStatusReady()) {
                    if ($user->isCallFree()) {
                        Yii::info('DIRECT - User (' . $user->username . ') Id: ' . $user->id . ', phone: ' . $agent_phone_number, 'info\API:CommunicationController:actionVoice:Direct - 2');
                        if ($user->userProfile && (int) $user->userProfile->up_call_type_id === UserProfile::CALL_TYPE_WEB) {
                            $call_employee[] = $user;
                            $call_agent_username[] = 'seller' . $user->id;
                        }

                    } else {
                        Yii::info('Call Occupied - User (' . $user->username . ') Id: ' . $user->id . ', phone: ' . $agent_phone_number, 'info\API:CommunicationController:actionVoice:isCallFree');
                        Notifications::create($user->id, 'Missing Call [Occupied]', 'Missing Call from ' . $client_phone_number . ' to ' . $agent_phone_number . "\r\n Reason: Agent Occupied", Notifications::TYPE_WARNING, true);
                        Notifications::socket($user->id, null, 'getNewNotification', [], true);
                    }
                } else {
                    Yii::info('Call Status not Ready - User (' . $user->username . ') Id: ' . $user->id . ', phone: ' . $agent_phone_number, 'info\API:CommunicationController:actionVoice:isCallStatusReady');
                    Notifications::create($user->id, 'Missing Call [not Ready]', 'Missing Call from ' . $client_phone_number . ' to ' . $agent_phone_number . "\r\n Reason: Call Status not Ready", Notifications::TYPE_WARNING, true);
                    Notifications::socket($user->id, null, 'getNewNotification', [], true);
                }
            } else {
                Yii::info('Offline - User (' . $user->username . ') Id: ' . $user->id . ', phone: ' . $agent_phone_number, 'info\API:CommunicationController:actionVoice:isOnline');
                Notifications::create($user->id, 'Missing Call [Offline]', 'Missing Call from ' . $client_phone_number . ' to ' . $agent_phone_number . "\r\n Reason: Agent offline", Notifications::TYPE_WARNING, true);
                Notifications::socket($user->id, null, 'getNewNotification', [], true);
            }
        }

        if (!$call_employee && $call_user_id && $call_project_id) {

            Yii::info('isRedirectCall - call_user_id (' . $call_user_id . '), call_project_id: ' . $call_project_id, 'info\API:CommunicationController:actionVoice:Redirect - 3');
            $usersForCall = Employee::getAgentsForCall($call_user_id, $call_project_id);

            Yii::info('Redirect usersForCall: ' . VarDumper::dumpAsString($usersForCall), 'info\API:CommunicationController:actionVoice:getAgentsForCall - 4');

            if ($usersForCall) {
                $cntCallAgents = 1;
                foreach ($usersForCall as $userForCall) {
                    $upp = UserProjectParams::find()->where(['upp_user_id' => $userForCall['tbl_user_id'], 'upp_project_id' => $call_project_id])->one();
                    if ($upp) {
                        $employeeModel = Employee::findOne(['id' => $userForCall['tbl_user_id']]);
                        if ($employeeModel && $employeeModel->userProfile && (int)$employeeModel->userProfile->up_call_type_id === UserProfile::CALL_TYPE_WEB) {
                            if($cntCallAgents > $limit) {
                                break;
                            }
                            $call_employee[] = $employeeModel;
                            $call_agent_username[] = 'seller' . $employeeModel->id;
                            Yii::info('Redirected Call: call_user_id: ' . $call_user_id . ', call: ' . 'seller' . $employeeModel->id . ', agent_phone_number: ' . $agent_phone_number, 'info\API:CommunicationController:actionVoice:UserProjectParams - 5');
                            //break;
                            $cntCallAgents ++;
                        }
                    }
                }
            }
        }

        $result = [
            'call_employee' => $call_employee,
            'call_project_id' => $call_project_id,
            'call_agent_username' => $call_agent_username,
        ];

        return $result;
    }


    /**
     *
     * @api {post} /v1/communication/voice Communication Voice
     * @apiVersion 0.1.0
     * @apiName CommunicationVoice
     * @apiGroup Communication
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization    Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiSuccessExample Success-Response:
     *  HTTP/1.1 200 OK
     *  {
     *      "type": "update_sms_status"
     *  }
     *
     * @return array
     * @throws BadRequestHttpException
     * @throws UnprocessableEntityHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionVoice(): array
    {
        $this->checkPost();


        //$action = Yii::$app->request->post('action');
        $type = Yii::$app->request->post('type');

        $apiLog = $this->startApiLog($this->action->uniqueId . ($type ? '/' . $type : ''));

        /*if(!$action) {
            throw new NotFoundHttpException('Not found action', 1);
        }*/


        switch ($type) {
            case self::TYPE_VOIP_INCOMING:
            case self::TYPE_VOIP_GATHER:
                $response = $this->voiceIncoming($type);
//                $response = $this->communicationService->voiceIncoming($type, Yii::$app->request->post());
                break;
            case self::TYPE_VOIP_RECORD:
//                $this->communicationService->voiceRecord(Yii::$app->request->post());
//                $response = [];
                $response = $this->voiceRecord();
                break;
            case self::TYPE_VOIP_FINISH:
//                $this->communicationService->voiceFinish(Yii::$app->request->post());
//                $response = [];
                $response = $this->voiceFinish();
                break;
            case self::TYPE_VOIP_CLIENT:
//                $this->communicationService->voiceClient(Yii::$app->request->post());
//                $response = [];
                $response = $this->voiceClient();
                break;
            default:
//                $response = $this->communicationService->voiceDefault(Yii::$app->request->post());
                $response = $this->voiceDefault();
        }

        $responseData = [];

        if (isset($response['error']) && $response['error']) {

        } else {
            $responseData = [
                'status'    => 200,
                'name'      => 'Success',
                'code'      => 0,
                'message'   => ''
            ];
        }

        $responseData['data']['response'] = $response;
        $responseData = $apiLog->endApiLog($responseData);

        if (isset($response['error']) && $response['error']) {
            $json = @json_encode($response['error']);
            if (isset($response['error_code']) && $response['error_code']) {
                $error_code = $response['error_code'];
            } else {
                $error_code = 0;
            }
            throw new UnprocessableEntityHttpException($json, $error_code);
        }

        return $responseData;
    }


    /**
     * @param string $type
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    private function voiceIncoming(string $type): array
    {
        $response = [];
        $post = Yii::$app->request->post();
        $settings = \Yii::$app->params['settings'];

        Yii::info(VarDumper::dumpAsString($post), 'info\API:Communication:voiceIncoming');

        $callSourceTypeId = null;
        $lead2 = null;

        $general_line_call_distribution = \Yii::$app->params['general_line_call_distribution'];

        $use_new_general_line_distribution = $settings['use_general_line_distribution'] ?? $general_line_call_distribution['use_general_line_distribution'];
        $general_line_leads_limit = $settings['general_line_leads_limit'] ?? $general_line_call_distribution['general_line_leads_limit'];
        $general_line_role_priority = $settings['general_line_role_priority'] ?? $general_line_call_distribution['general_line_role_priority'];
        $general_line_last_hours = $settings['general_line_last_hours'] ?? $general_line_call_distribution['general_line_last_hours'];
        $general_line_user_limit = $settings['general_line_user_limit'] ?? $general_line_call_distribution['general_line_user_limit'];
        $direct_agent_user_limit = $settings['direct_agent_user_limit'] ?? $general_line_call_distribution['direct_agent_user_limit'];


        $isError = false;
        $clientPhone = null;
        $generalLineNumber = \Yii::$app->params['global_phone'];
        $voice_gather_configs = \Yii::$app->params['voice_gather'];

        $ivrEnable = (bool) ($settings['call_ivr_enable'] ?? $voice_gather_configs['use_voice_gather']);

        //VarDumper::dump($avrEnable); exit;

        if(isset($post['call']) && $post['call']) {
            $client_phone_number = null;
            $incoming_phone_number = null;

            $callSid = $post['call']['CallSid'] ?? false;
            $parentCallSid = $post['call']['ParentCallSid'] ?? false;


            if (isset($post['call_id'])) {
                $post['call']['c_com_call_id'] = $post['call_id'];
            }


            if (isset($post['call']['From']) && $post['call']['From']) {
                $client_phone_number = $post['call']['From'];
            }

            if (isset($post['call']['To']) && $post['call']['To']) {
                $incoming_phone_number = $post['call']['Called'];
            }

            if (!$client_phone_number) {
                $response['error'] = 'Not found Call From (Client phone number)';
                $response['error_code'] = 10;
            }

            if (!$incoming_phone_number) {
                $response['error'] = 'Not found Call Called (Agent phone number)';
                $response['error_code'] = 11;
            }

            $isOnHold = false;
            $callGeneralNumber = false;
            $call_project_id = null;
            $call_agent_username = [];
            $call_employee = [];

            $project = null;

            $clientPhone = ClientPhone::find()->where(['phone' => $client_phone_number])->orderBy(['id' => SORT_DESC])->limit(1)->one();

            $departmentPhone = DepartmentPhoneProject::find()->where(['dpp_phone_number' => $incoming_phone_number, 'dpp_enable' => true])->limit(1)->one();

            //VarDumper::dump($department); exit;

            $call_dep_id = null;
            $callModel = null;

            if($departmentPhone) {
                $agentDirectCallCheck = false;
                $project = $departmentPhone->dppProject;
                $source = $departmentPhone->dppSource;
                if($project && !$source) {
                    $source = Sources::find()->where(['project_id' => $project->id, 'default' => true])->one();
                    if($source) {
                        $departmentPhone->dpp_source_id = $source->id;
                    }
                }

                $call_project_id = $departmentPhone->dpp_project_id;
                $call_dep_id = $departmentPhone->dpp_dep_id;

                $ivrEnable = (bool) $departmentPhone->dpp_ivr_enable; ////(bool) ($settings['call_avr_enable'] ?? $voice_gather_configs['use_voice_gather']);

                $callModel = $this->findOrCreateCall($callSid, $post['call'], $call_project_id, $call_dep_id);

            } else {

                $agentDirectCallCheck = true;

                $upp = UserProjectParams::find()->where(['upp_tw_phone_number' => $incoming_phone_number])->limit(1)->one();
                if ($upp) {
                    $call_project_id = $upp->upp_project_id;
                    if ($upp->uppUser && $upp->uppUser->userDepartments) {
                        foreach ($upp->uppUser->userDepartments as $userDepartment) {
                            $call_dep_id = $userDepartment->ud_dep_id;
                            break;
                        }
                    }

                }

            }


            if($departmentPhone) {
                $callSourceTypeId = Call::SOURCE_GENERAL_LINE;
                if($ivrEnable) {
                    $ivrSelectedDigit = isset($post['call']['Digits']) ? (int) $post['call']['Digits'] : null;
                    $ivrStep = (int) Yii::$app->request->get('step', 1);
                    return $this->ivrService($callModel, $departmentPhone, $ivrStep, $ivrSelectedDigit);
                }


                if($type === self::TYPE_VOIP_INCOMING) {

                    if ($clientPhone) {
                        $lead2 = Lead2::findLastLeadByClientPhone($client_phone_number, $project->id);
                    }

                    if (!$lead2) {
                        // $sql = Lead2::findLastLeadByClientPhone($client_phone_number, $project->id, true);
                        // Yii::info('phone: '. $client_phone_number.', sql: '. $sql, 'info\API:Communication:findLastLeadByClientPhone');
                        $lead2 = Lead2::createNewLeadByPhone($client_phone_number, $project->id);
                    } else {
                        Yii::info('Find LastLead ('.$lead2->id.') By ClientPhone: ' . $client_phone_number, 'info\API:Communication:voiceIncoming:findLastLeadByClientPhone');
                    }
                }

            } elseif ($agentDirectCallCheck) {

                $agentRes = $this->getDirectAgentsByPhoneNumber($incoming_phone_number, $client_phone_number, $call_dep_id, $direct_agent_user_limit);
                if($agentRes && isset($agentRes['call_employee'], $agentRes['call_agent_username']) && $agentRes['call_employee']) {
                    $isOnHold = false;
                    $callGeneralNumber = false;
                    $call_employee = $agentRes['call_employee'];
                    $call_project_id = $agentRes['call_project_id'] ?? null;
                    $call_agent_username = $agentRes['call_agent_username'];
                } else {
                    if($agentRes && isset($agentRes['call_project_id'])) {
                        $call_project_id = ($agentRes['call_project_id'] > 0) ? $agentRes['call_project_id'] : null;
                        if(NULL === $call_project_id) {
                            $isOnHold = false;
                            $callGeneralNumber = true;
                        } else {
                            $isOnHold = true;
                            $callGeneralNumber = false;
                        }
                    } else {
                        $isOnHold = false;
                        $callGeneralNumber = true;
                    }
                }

                if ($clientPhone) {
                    $lead2 = Lead2::findLastLeadByClientPhone($client_phone_number, $agentRes['call_project_id'] ?? null);
                }

                if (!$lead2) {
                    //$sql = Lead2::findLastLeadByClientPhone($client_phone_number, true);
                    //Yii::info('phone: '. $client_phone_number.', sql: '. $sql, 'info\API:Communication:findLastLeadByClientPhone');
                    if(isset($agentRes['call_project_id']) && $agentRes['call_project_id']) {
                        $lead2 = Lead2::createNewLeadByPhone($client_phone_number, $agentRes['call_project_id']);
                    }
                } /*else {
                            Yii::info('Find LastLead ('.$lead2->id.') By ClientPhone: ' . $client_phone_number, 'info\API:Communication:findLastLeadByClientPhone');
                        }*/



            } else {
                $callGeneralNumber = true;
            }

            // $clientPhone = ClientPhone::find()->where(['phone' => $client_phone_number])->orderBy(['id' => SORT_DESC])->limit(1)->one();
            //$lead = null;

            //if(!$lead) {
            /*if ($clientPhone && $clientPhone->client_id) {
                $lead = Lead::find()->select(['id'])->where(['client_id' => $clientPhone->client_id])->orderBy(['id' => SORT_DESC])->limit(1)->one();
            }*/
            //}

            /*$data = [];
            $data['client_name'] = 'Noname';
            $data['client_id'] = null;
            $data['last_lead_id'] = null;
            $data['client_emails'] = [];
            $data['client_phones'] = [];
            $data['client_count_calls'] = 0;
            $data['client_count_sms'] = 0;
            $data['client_created_date'] = '';
            $data['client_last_activity'] = '';

            if($clientPhone && $client = $clientPhone->client) {
                $data['client_name'] = $client->full_name;
                $data['client_id'] = $clientPhone->client_id;
                $data['client_created_date'] = Yii::$app->formatter->asDate(strtotime($client->created));
                if ($lead2) {
                    $data['last_lead_id'] = $lead2->id;
                    $data['client_last_activity'] = Yii::$app->formatter->asDate(strtotime($client->created));
                }
            }

            $data['client_phone'] = $client_phone_number;
            $data['agent_phone'] = $incoming_phone_number;


            Yii::info(VarDumper::dumpAsString([
                'data' => $data,
                'post' => $post,
                'call_employee' => $call_employee,

            ], 10, false), 'info\API:Communication:voiceIncoming:ParamsToCall');*/

            if (!$isOnHold && !$callGeneralNumber && $call_employee) {

                foreach ($call_employee AS $key => $userCall) {
                    $call = new Call();
                    $call->c_call_sid = $post['call']['CallSid'] ?? null;
                    $call->c_account_sid = $post['call']['AccountSid'] ?? null;
                    $call->c_call_type_id = Call::CALL_TYPE_IN;
                    $call->c_call_status = $post['call']['CallStatus'] ?? Call::CALL_STATUS_RINGING;
                    $call->c_com_call_id = $post['call_id'] ?? null;
                    $call->c_direction = $post['call']['Direction'] ?? null;
                    $call->c_project_id = $call_project_id;
                    $call->c_is_new = true;
                    if($call_dep_id) {
                        $call->c_dep_id = $call_dep_id;
                    }
                    $call->c_api_version = $post['call']['ApiVersion'] ?? null;
                    $call->c_created_dt = date('Y-m-d H:i:s');
                    $call->c_from = $client_phone_number;
                    $call->c_sip = null;
                    $call->c_to = $incoming_phone_number; //$userCall->username ? $userCall->username : null;
                    $call->c_created_user_id = $userCall->id;
                    $call->c_source_type_id = Call::SOURCE_REDIRECT_CALL;
                    if ($lead2) {
                        $call->c_lead_id = $lead2->id;
                    } else {
                        $call->c_lead_id = null;
                    }
                    if (!$call->save()) {
                        Yii::error(VarDumper::dumpAsString($call->errors), 'API:Communication:voiceIncoming:Call:save');
                    }
                    $data['status'] = $call->c_call_status;
                    // Notifications::socket($call->c_created_user_id, $call->c_lead_id, 'incomingCall', $data, true);
                }
            } elseif($isOnHold) {

                /*$callModel = $this->findOrCreateCall($callSid, $post['call'], $call_project_id, $call_dep_id);
                $callModel->c_source_type_id = Call::SOURCE_DIRECT_CALL;
                if (!$callModel->update()) {
                    Yii::error(VarDumper::dumpAsString($callModel->errors), 'API:Communication:Direct:Hold:Call:save');
                }*/

                $call = new Call();
                $call->c_call_sid = $post['call']['CallSid'] ?? null;
                $call->c_account_sid = $post['call']['AccountSid'] ?? null;
                $call->c_call_type_id = Call::CALL_TYPE_IN;
                $call->c_call_status =  Call::CALL_STATUS_IVR;
                $call->c_com_call_id = $post['call_id'] ?? null;
                $call->c_direction = $post['call']['Direction'] ?? null;
                $call->c_project_id = $call_project_id;
                $call->c_is_new = true;
                $call->c_api_version = $post['call']['ApiVersion'] ?? null;
                $call->c_created_dt = date('Y-m-d H:i:s');
                $call->c_from = $client_phone_number;
                $call->c_sip = null;
                $call->c_to = $incoming_phone_number;
                $call->c_created_user_id = null;
                $call->c_source_type_id = Call::SOURCE_DIRECT_CALL;

                if($call_dep_id) {
                    $call->c_dep_id = $call_dep_id;
                }
                if ($lead2) {
                    $call->c_lead_id = $lead2->id;
                }
                if (!$call->save()) {
                    Yii::error(VarDumper::dumpAsString($call->errors), 'API:Communication:Direct:OnHold:save');
                }


                $project = $call->cProject;


                //$url_say_play_hold = '';
                $url_music_play_hold = 'https://talkdeskapp.s3.amazonaws.com/production/audio_messages/folk_hold_music.mp3';

                $responseTwml = new VoiceResponse();

                if($project && $project->custom_data) {
                    $customData = @json_decode($project->custom_data, true);
                    if($customData) {

                        if(isset($customData['pause']) && $customData['pause']) {
                            $responseTwml->pause((int) $customData['pause']);
                        }

                        if(isset($customData['say_hold'], $customData['language'], $customData['voice']) && $customData['say_hold']) {
                            $responseTwml->say($customData['say_hold'], [
                                'language' => $customData['language'],
                                'voice' => $customData['voice'],
                            ]);
                        } elseif (isset($customData['url_say_play_hold']) && $customData['url_say_play_hold']) {
                            $responseTwml->play($customData['url_say_play_hold']);
                        }

                        if(isset($customData['url_music_play_hold']) && $customData['url_music_play_hold']) {
                            $responseTwml->play($customData['url_music_play_hold']);
                        }
                    }
                } else {
                    $responseTwml->play($url_music_play_hold);
                }



                /*if($url_say_play_hold) {
                    $responseTwml->play($url_say_play_hold);
                    if($url_music_play_hold) {
                        $responseTwml->play($url_music_play_hold);
                    }

                } else {

                    $say_params = \Yii::$app->params['voice_gather'];
                    $responseTwml = new VoiceResponse();
                    $responseTwml->pause(['length' => 5]);

                    $company = ' ' . strtolower($project->name);
                    $entry_phrase = str_replace('{{project}}', $company, $say_params['entry_phrase']);
                    $responseTwml->say('    '.$entry_phrase.'  '. $say_params['languages'][1]['hold_voice'], [
                        'language' => $say_params['languages'][1]['language'],
                        'voice' => $say_params['languages'][1]['voice'],
                    ]);
                    $responseTwml->play($say_params['hold_play']);
                    $response['twml'] = (string)$responseTwml;
                }*/


                $response['twml'] = (string) $responseTwml;

                $job = new CallQueueJob();
                $job->call_id = $call->c_id;
                $job->delay = 10;
                $jobId = Yii::$app->queue_job->push($job);

                Yii::info('JobId: '.$jobId.', Call ('.$call->c_id.') add to hold : project_id: '.$call_project_id.', generalLine: '.$generalLineNumber.', TWML: ' . $response['twml'], 'info\API:Communication:Direct:Hold');


            } elseif($callGeneralNumber){
                $call = new Call();
                $call->c_call_sid = $post['call']['CallSid'] ?? null;
                $call->c_account_sid = $post['call']['AccountSid'] ?? null;
                $call->c_call_type_id = Call::CALL_TYPE_IN;
                $call->c_call_status = $post['call']['CallStatus'] ?? Call::CALL_STATUS_RINGING;
                $call->c_com_call_id = $post['call_id'] ?? null;
                $call->c_direction = $post['call']['Direction'] ?? null;
                $call->c_project_id = $call_project_id;
                $call->c_is_new = true;
                $call->c_api_version = $post['call']['ApiVersion'] ?? null;
                $call->c_created_dt = date('Y-m-d H:i:s');
                $call->c_from = $client_phone_number;
                $call->c_sip = null;
                $call->c_to = $generalLineNumber;
                $call->c_created_user_id = null;
                $call->c_source_type_id = $callSourceTypeId;
                if($call_dep_id) {
                    $call->c_dep_id = $call_dep_id;
                }
                if ($lead2) {
                    $call->c_lead_id = $lead2->id;
                }
                if (!$call->save()) {
                    Yii::error(VarDumper::dumpAsString($call->errors), 'API:Communication:voiceIncoming:Call:save:callGeneralNumber');
                }
                Yii::info('Redirected to General Line : call_project_id: '.$call_project_id.', generalLine: '.$generalLineNumber, 'info\API:Communication:voiceIncoming:callGeneralNumber - 6');
            } else {
                if(!$isOnHold && !$callGeneralNumber) {
                    $isError = true;
                    Yii::error('Not found call destination agent, hold or general line for call number:'. $incoming_phone_number, 'API:Communication:voiceIncoming:isOnHold_callGeneralNumber');
                }
            }

            if(!$isError) {
                $response['agent_sip'] = '';
                $response['agent_phone_number'] = $incoming_phone_number;
                $response['client_phone_number'] = $client_phone_number;
                $response['general_phone_number'] = $generalLineNumber;
                $response['agent_username'] = $call_agent_username;
                $response['call_to_hold'] = $isOnHold ? 1 : 0;
                $response['call_to_general'] = $callGeneralNumber ? 1 : 0;
            } else {
                $response['error'] = 'Not found call destination agent, hold or general line';
                $response['error_code'] = 13;
            }

        } else {
            $response['error'] = 'Not found "call" array';
            $response['error_code'] = 12;
        }

        return $response;
    }

    /**
     * @return array
     */
    private function voiceRecord(): array
    {
        $response = [];
        $post = Yii::$app->request->post();
        Yii::info(VarDumper::dumpAsString($post), 'info\API:Communication:voiceRecord');

        if (isset($post['callData']['CallSid']) && $post['callData']['CallSid']) {

            //$call = Call::find()->where(['c_call_sid' => $post['callData']['CallSid']])->one();

            $call = null;
            $is_call_incoming = (isset($post['call'],$post['call']['c_call_type_id']) && (int)$post['call']['c_call_type_id'] === Call::CALL_TYPE_IN);
            if($is_call_incoming) {
                $call = Call::find()->where(['c_call_sid' => $post['callData']['CallSid']])
                    //->andWhere(['c_call_status' => Call::CALL_STATUS_COMPLETED])
                    ->andWhere([ '>', 'c_created_user_id', 0])
                    ->orderBy(['c_updated_dt' => SORT_DESC])->limit(1)->one();
            }

            if(!$call) {
                $call = Call::find()->where(['c_call_sid' => $post['callData']['CallSid']])->orderBy(['c_id' => SORT_ASC])->limit(1)->one();
            }

            if ($call) {

                if($post['callData']['RecordingUrl']) {
                    $call->c_recording_url = $post['callData']['RecordingUrl'];
                    $call->c_recording_duration = $post['callData']['RecordingDuration'];
                    $call->c_recording_sid = $post['callData']['RecordingSid'];
                    $call->c_updated_dt = date('Y-m-d H:i:s');


                    if(!$call->save()) {
                        Yii::error(VarDumper::dumpAsString($call->errors), 'API:Communication:voiceRecord:Call1:save');
                    }
                    if ($call->c_lead_id) {

                        if($call->c_created_user_id) {
                            Notifications::create($call->c_created_user_id, 'Call Recording Completed  from ' . $call->c_from . ' to ' . $call->c_to . ' <br>Lead ID: ' . $call->c_lead_id , Notifications::TYPE_INFO, true);
                        }
                        Notifications::socket(null, $call->c_lead_id, 'recordingUpdate', ['url' => $call->c_recording_url], true);
                    }
                }
            }
        }

        return $response;
    }

    /**
     * @return array
     */
    private function voiceFinish(): array
    {
        $response = [];
        $post = Yii::$app->request->post();

        Yii::info(VarDumper::dumpAsString($post), 'info\API:Communication:voiceFinish');

        if (isset($post['callData']['sid']) && $post['callData']['sid']) {
            //$call = Call::find()->where(['c_call_sid' => $post['callData']['sid']])->limit(1)->one();
            $call = null;
            $is_call_incoming = (isset($post['call'],$post['call']['c_call_type_id']) && (int)$post['call']['c_call_type_id'] === Call::CALL_TYPE_IN);
            if($is_call_incoming) {
                $call = Call::find()->where(['c_call_sid' => $post['callData']['sid']])
                    //->andWhere(['c_call_status' => Call::CALL_STATUS_COMPLETED])
                    ->andWhere([ '>', 'c_created_user_id', 0])
                    ->orderBy(['c_updated_dt' => SORT_DESC])
                    ->limit(1)
                    ->one();
            }

            if(!$call) {
                $call = Call::find()->where(['c_call_sid' => $post['callData']['sid']])->orderBy(['c_id' => SORT_ASC])->limit(1)->one();
            }

            $callData = $post['call'];

            if(!$call) {
                $call = new Call();
                $call->c_call_sid = $callData['c_call_sid'];
                $call->c_account_sid = $callData['c_account_sid'];
                $call->c_call_type_id = $callData['c_call_type_id'];
                $call->c_uri = $callData['c_uri'];

                $call->c_from = $callData['c_from'];
                $call->c_to = $callData['c_to'];

                $call->c_timestamp = $callData['c_timestamp'];
                $call->c_created_dt = $callData['c_created_dt'];
                $call->c_updated_dt = date('Y-m-d H:i:s');

                $call->c_recording_url = $callData['c_recording_url'];
                $call->c_recording_sid = $callData['c_recording_sid'];
                $call->c_recording_duration = $callData['c_recording_duration'];

                $call->c_caller_name = $callData['c_caller_name'];
                $call->c_direction = $callData['c_direction'];
                $call->c_api_version = $callData['c_api_version'];


                //$call->c_call_status = $post['callData']['CallStatus'] ?? '';
                //$call->c_sequence_number = $post['callData']['SequenceNumber'] ?? 0;

                $call->c_sip = $callData['c_sip'];

                if($callData['c_project_id']) {
                    $call->c_project_id = $callData['c_project_id'];
                }



                $upp = null;

                if($call->c_project_id && $call->c_from) {
                    $agentId = (int) str_replace('client:seller', '', $call->c_from);
                    if($agentId) {
                        $upp = UserProjectParams::find()->where(['upp_user_id' => $agentId, 'upp_project_id' => $call->c_project_id])->one();
                    }
                }

                if(!$upp) {
                    $upp = UserProjectParams::find()->where(['upp_phone_number' => $call->c_from])->orWhere(['upp_tw_phone_number' => $call->c_from])->one();
                }

                if(!$upp) {
                    $upp = UserProjectParams::find()->where(['upp_phone_number' => $call->c_to])->orWhere(['upp_tw_phone_number' => $call->c_to])->one();
                }

                $user = null;
                if($upp && $upp->uppUser) {

                    $call->c_created_user_id = $upp->uppUser->id;
                    $call->c_project_id = $upp->upp_project_id;

                    Notifications::create($upp->uppUser->id, 'Call ID-'.$call->c_id.' completed', 'Call ID-'.$call->c_id.' completed. From ' . $call->c_from .' to '.$call->c_to, Notifications::TYPE_INFO, true);
                    Notifications::socket($upp->uppUser->id, null, 'getNewNotification', [], true);
                }

            }



            /*
             *
             *
             *
             *  account_sid: "AC10f3c74efba7b492cbd7dca86077736c"
                annotation: null
                answered_by: null
                api_version: "2010-04-01"
                caller_name: ""
                date_created: "Wed, 06 Feb 2019 15:27:34 +0000"
                date_updated: "Wed, 06 Feb 2019 15:27:53 +0000"
                direction: "outbound-api"
                duration: "15"
                end_time: "Wed, 06 Feb 2019 15:27:53 +0000"
                forwarded_from: null
                from: "onorine.miller"
                from_formatted: "onorine.miller"
                group_sid: null
                parent_call_sid: null
                phone_number_sid: null
                price: "-0.00400"
                price_unit: "USD"
                sid: "CA6359554e2ac4a920427165c4b69288b7"
                start_time: "Wed, 06 Feb 2019 15:27:38 +0000"
                status: "completed"
                subresource_uris: {,…}
                notifications: "/2010-04-01/Accounts/AC10f3c74efba7b492cbd7dca86077736c/Calls/CA6359554e2ac4a920427165c4b69288b7/Notifications.json"
                recordings: "/2010-04-01/Accounts/AC10f3c74efba7b492cbd7dca86077736c/Calls/CA6359554e2ac4a920427165c4b69288b7/Recordings.json"
                to: "sip:onorine.miller@kivork.sip.us1.twilio.com"
                to_formatted: "sip:onorine.miller@kivork.sip.us1.twilio.com"
                uri: "/2010-04-01/Accounts/AC10f3c74efba7b492cbd7dca86077736c/Calls/CA6359554e2ac4a920427165c4b69288b7.json"
             *
             */

            if ($call) {

                if(isset($post['callData']['price']) && $post['callData']['price']) {
                    $call->c_price = abs((float) $post['callData']['price']);
                }

                if(!$call->c_call_status && isset($post['callData']['status'])) {
                    $call->c_call_status = $post['callData']['status'];
                }

                /*if($call->c_call_status && isset($post['callData']['status'])) {
                    if(!in_array($call->c_call_status, [Call::CALL_STATUS_CANCELED, Call::CALL_STATUS_BUSY, Call::CALL_STATUS_NO_ANSWER, Call::CALL_STATUS_FAILED])) {
                        $call->c_call_status = $post['callData']['status'];
                    }
                }*/

                if($call->c_call_status && isset($post['callData']['status'])) {
                    if(!in_array($call->c_call_status, [Call::CALL_STATUS_CANCELED, Call::CALL_STATUS_BUSY, Call::CALL_STATUS_NO_ANSWER])) {
                        $call->c_call_status = $post['callData']['status'];
                    }
                }

                if(isset($post['callData']['duration']) && $post['callData']['duration']) {
                    $call->c_call_duration = (int) $post['callData']['duration'];
                }


                if($call->c_lead_id && $lead = $call->cLead) {

                    if ((int) $lead->status === Lead::STATUS_PENDING && (int) $lead->l_call_status_id === Lead::CALL_STATUS_PROCESS) {

                        $delayTimeMin = $lead->getDelayPendingTime();
                        $lead->l_pending_delay_dt = date('Y-m-d H:i:s', strtotime('+' . $delayTimeMin . ' minutes'));
                        $lead->employee_id = null;
                        $lead->l_call_status_id = Lead::CALL_STATUS_READY;


                        if (!$lead->save()) {
                            Yii::error('lead: ' . $lead->id . ' ' . VarDumper::dumpAsString($lead->errors), 'API:Communication:voiceFinish:Lead:save');
                        }

                        /*if($call->c_created_user_id) {
                            Notifications::create($call->c_created_user_id, 'Lead delayed -' . $lead->id . '', 'Lead ID-' . $lead->id . ' is delayed. (+'.$delayTimeMin.' minutes)' , Notifications::TYPE_INFO, true);
                            Notifications::socket($call->c_created_user_id, null, 'getNewNotification', [], true);
                        }*/

                    }


                    if ((int) $lead->status === Lead::STATUS_PROCESSING) {
                        $lead->l_call_status_id = Lead::CALL_STATUS_DONE;
                        if (!$lead->save()) {
                            Yii::error('lead: ' . $lead->id . ' ' . VarDumper::dumpAsString($lead->errors), 'API:Communication:voiceFinish:Lead:save2');
                        }
                    }
                }


                if(!$call->save()) {
                    Yii::error(VarDumper::dumpAsString($call->errors), 'API:CommunicationController:actionVoice:TYPE_VOIP_FINISH:Call:save');
                }

                if($call->c_created_user_id || $call->c_lead_id) {
                    Notifications::socket($call->c_created_user_id, $call->c_lead_id, 'webCallUpdate', ['status' => $call->c_call_status, 'duration' => $call->c_call_duration, 'debug' => 'TYPE_VOIP_FINISH'], true);
                }

                /*if($post['callData']['RecordingUrl']) {
                    $call->c_recording_url = $post['callData']['RecordingUrl'];
                    $call->c_recording_duration = $post['callData']['RecordingDuration'];
                    $call->c_recording_sid = $post['callData']['RecordingSid'];
                    $call->c_updated_dt = date('Y-m-d H:i:s');


                    if(!$call->save()) {
                        Yii::error(VarDumper::dumpAsString($call->errors), 'API:CommunicationController:actionVoice:TYPE_VOIP_FINISH:Call:save');
                    }

                }*/
            } else {
                Yii::error('Communication Request: Not found Call SID: ' . $post['callData']['sid'], 'API:Communication:voiceFinish:Call:find');
            }
        }
        else {
            Yii::error('Communication Request: Not found post[callData][sid] ' . VarDumper::dumpAsString($post), 'API:Communication:voiceFinish:post');
        }

        return $response;
    }

    /**
     * @return array
     */
    private function voiceClient(): array
    {
        $response = [];
        $post = Yii::$app->request->post();

        Yii::info(VarDumper::dumpAsString($post), 'info\API:Communication:voiceClient');

        $callSid = $post['callData']['sid'] ?? $post['callData']['CallSid'] ?? null;

        if ($callSid) {

            $call = null;
            $is_call_incoming = (isset($post['call'],$post['call']['c_call_type_id']) && (int)$post['call']['c_call_type_id'] === Call::CALL_TYPE_IN);

            if($is_call_incoming) {
                $call = Call::find()->where(['c_call_sid' => $callSid])
                    //->andWhere(['c_call_status' => Call::CALL_STATUS_COMPLETED])
                    ->andWhere([ '>', 'c_created_user_id', 0])
                    ->orderBy(['c_updated_dt' => SORT_DESC])
                    ->limit(1)
                    ->one();
            }

            if(!$call) {
                $call = Call::find()->where(['c_call_sid' => $callSid])->orderBy(['c_id' => SORT_ASC])->limit(1)->one();
            }

            $callData = $post['call'];

            if(!$call) {
                $call = new Call();
                $call->c_call_sid = $callData['c_call_sid'];
                $call->c_account_sid = $callData['c_account_sid'];
                $call->c_call_type_id = $callData['c_call_type_id'];
                $call->c_uri = $callData['c_uri'] ?? null;

                $call->c_from = $callData['c_from'] ?? null;
                $call->c_to = $callData['c_to'] ?? null;

                // $call->c_timestamp = $callData['c_timestamp'] ?? null;
                $call->c_created_dt = $callData['c_created_dt'];
                $call->c_updated_dt = date('Y-m-d H:i:s');

                $call->c_recording_url = $callData['c_recording_url'] ?? null;
                $call->c_recording_sid = $callData['c_recording_sid'] ?? null;
                $call->c_recording_duration = $callData['c_recording_duration'] ?? null;

                $call->c_caller_name = $callData['c_caller_name'] ?? null;
                $call->c_direction = $callData['c_direction'] ?? null;
                $call->c_api_version = $callData['c_api_version'] ?? null;


                //$call->c_call_status = $post['callData']['CallStatus'] ?? '';
                //$call->c_sequence_number = $post['callData']['SequenceNumber'] ?? 0;

                $call->c_sip = $callData['c_sip'] ?? null;

                if(isset($callData['c_project_id']) && $callData['c_project_id']) {
                    $call->c_project_id = $callData['c_project_id'];
                }



                $upp = null;

                if($call->c_project_id && $call->c_from) {
                    $agentId = (int) str_replace('client:seller', '', $call->c_from);
                    if($agentId) {
                        $upp = UserProjectParams::find()->where(['upp_user_id' => $agentId, 'upp_project_id' => $call->c_project_id])->one();
                    }
                }

                if(!$upp) {
                    $upp = UserProjectParams::find()->where(['upp_phone_number' => $call->c_from])->orWhere(['upp_tw_phone_number' => $call->c_from])->one();
                }

                if(!$upp) {
                    $upp = UserProjectParams::find()->where(['upp_phone_number' => $call->c_to])->orWhere(['upp_tw_phone_number' => $call->c_to])->one();
                }

                $user = null;
                if($upp && $upp->uppUser) {

                    $call->c_created_user_id = $upp->uppUser->id;
                    $call->c_project_id = $upp->upp_project_id;

                    //Notifications::create($upp->uppUser->id, 'Call ID-'.$call->c_id.' completed', 'Call ID-'.$call->c_id.' completed. From ' . $call->c_from .' to '.$call->c_to, Notifications::TYPE_INFO, true);
                    //Notifications::socket($upp->uppUser->id, null, 'getNewNotification', [], true);
                }

            }



            /*
             *
             *
             *
             *  account_sid: "AC10f3c74efba7b492cbd7dca86077736c"
                annotation: null
                answered_by: null
                api_version: "2010-04-01"
                caller_name: ""
                date_created: "Wed, 06 Feb 2019 15:27:34 +0000"
                date_updated: "Wed, 06 Feb 2019 15:27:53 +0000"
                direction: "outbound-api"
                duration: "15"
                end_time: "Wed, 06 Feb 2019 15:27:53 +0000"
                forwarded_from: null
                from: "onorine.miller"
                from_formatted: "onorine.miller"
                group_sid: null
                parent_call_sid: null
                phone_number_sid: null
                price: "-0.00400"
                price_unit: "USD"
                sid: "CA6359554e2ac4a920427165c4b69288b7"
                start_time: "Wed, 06 Feb 2019 15:27:38 +0000"
                status: "completed"
                subresource_uris: {,…}
                notifications: "/2010-04-01/Accounts/AC10f3c74efba7b492cbd7dca86077736c/Calls/CA6359554e2ac4a920427165c4b69288b7/Notifications.json"
                recordings: "/2010-04-01/Accounts/AC10f3c74efba7b492cbd7dca86077736c/Calls/CA6359554e2ac4a920427165c4b69288b7/Recordings.json"
                to: "sip:onorine.miller@kivork.sip.us1.twilio.com"
                to_formatted: "sip:onorine.miller@kivork.sip.us1.twilio.com"
                uri: "/2010-04-01/Accounts/AC10f3c74efba7b492cbd7dca86077736c/Calls/CA6359554e2ac4a920427165c4b69288b7.json"
             *
             */

            if ($call) {

                if(isset($post['callData']['price']) && $post['callData']['price']) {
                    $call->c_price = abs((float) $post['callData']['price']);
                }

                if(!$call->c_call_status && isset($post['callData']['status'])) {
                    $call->c_call_status = $post['callData']['status'];
                }

                /*if($call->c_call_status && isset($post['callData']['status'])) {
                    if(!in_array($call->c_call_status, [Call::CALL_STATUS_CANCELED, Call::CALL_STATUS_BUSY, Call::CALL_STATUS_NO_ANSWER, Call::CALL_STATUS_FAILED])) {
                        $call->c_call_status = $post['callData']['status'];
                    }
                }*/

                if($call->c_call_status && isset($post['callData']['status'])) {
                    if(!in_array($call->c_call_status, [Call::CALL_STATUS_CANCELED, Call::CALL_STATUS_BUSY, Call::CALL_STATUS_NO_ANSWER])) {
                        $call->c_call_status = $post['callData']['status'];
                    }
                }

                if(isset($post['callData']['duration']) && $post['callData']['duration']) {
                    $call->c_call_duration = (int) $post['callData']['duration'];
                }


                if($call->c_lead_id && $lead = $call->cLead) {

                    if ((int) $lead->status === Lead::STATUS_PENDING && (int) $lead->l_call_status_id === Lead::CALL_STATUS_PROCESS) {

                        $delayTimeMin = $lead->getDelayPendingTime();
                        $lead->l_pending_delay_dt = date('Y-m-d H:i:s', strtotime('+' . $delayTimeMin . ' minutes'));
                        $lead->employee_id = null;
                        $lead->l_call_status_id = Lead::CALL_STATUS_READY;


                        if (!$lead->save()) {
                            Yii::error('lead: ' . $lead->id . ' ' . VarDumper::dumpAsString($lead->errors), 'API:Communication:voiceClient:Lead:save');
                        }

                        /*if($call->c_created_user_id) {
                            Notifications::create($call->c_created_user_id, 'Lead delayed -' . $lead->id . '', 'Lead ID-' . $lead->id . ' is delayed. (+'.$delayTimeMin.' minutes)' , Notifications::TYPE_INFO, true);
                            Notifications::socket($call->c_created_user_id, null, 'getNewNotification', [], true);
                        }*/

                    }


                    if ((int) $lead->status === Lead::STATUS_PROCESSING) {
                        $lead->l_call_status_id = Lead::CALL_STATUS_DONE;
                        if (!$lead->save()) {
                            Yii::error('lead: ' . $lead->id . ' ' . VarDumper::dumpAsString($lead->errors), 'API:Communication:voiceClient:Lead:save2');
                        }
                    }
                }


                if(!$call->save()) {
                    Yii::error(VarDumper::dumpAsString($call->errors), 'API:Communication:voiceClient:Call:save');
                }

                if($call->c_created_user_id || $call->c_lead_id) {
                    Notifications::socket($call->c_created_user_id, $call->c_lead_id, 'webCallUpdate', ['status' => $call->c_call_status, 'duration' => $call->c_call_duration, 'debug' => 'TYPE_VOIP_FINISH'], true);
                }

                /*if($post['callData']['RecordingUrl']) {
                    $call->c_recording_url = $post['callData']['RecordingUrl'];
                    $call->c_recording_duration = $post['callData']['RecordingDuration'];
                    $call->c_recording_sid = $post['callData']['RecordingSid'];
                    $call->c_updated_dt = date('Y-m-d H:i:s');


                    if(!$call->save()) {
                        Yii::error(VarDumper::dumpAsString($call->errors), 'API:CommunicationController:actionVoice:TYPE_VOIP_FINISH:Call:save');
                    }

                }*/
            } else {
                Yii::error('Communication Request: Not found Call SID: ' . $callSid, 'API:Communication:voiceClient:Call:find');
            }
        }
        else {
            Yii::error('Communication Request: Not found post[callData][sid] / post[callData][CallSid] ' . VarDumper::dumpAsString($post), 'API:Communication:voiceClient:post');
        }

        return $response;
    }

    /**
     * @return array
     */
    private function voiceDefault(): array
    {


        $response = ['trace' => ''];
        $trace = [];
        $post = Yii::$app->request->post();

        Yii::info(VarDumper::dumpAsString($post), 'info\API:Communication:voiceDefault');

        $agentId = null;
        if(isset($post['callData'], $post['call'], $post['callData']['CallSid']) && $post['callData']['CallSid']) {

            if(isset($post['callData']['Called']) && $post['callData']['Called']) {

                //$trace[] = 'pos' . strpos($post['callData']['Called'], 'client:seller');

                if(strpos($post['callData']['Called'], 'client:seller') !== false) {
                    $agentId = (int)str_replace('client:seller', '', $post['callData']['Called']);
                    $trace[] = 'find 1788 $agentId:' . $agentId;
                } else {
                    // if cancel call in first seconds
                    if( !isset($post['callData']['ParentCallSid']) &&  isset($post['callData']['CallStatus']) && in_array($post['callData']['CallStatus'], [Call::CALL_STATUS_CANCELED])) {
                        $callsIfCancel = Call::findAll(['c_call_sid' => $post['callData']['CallSid']]);
                        if($callsIfCancel) {
                            foreach ($callsIfCancel AS $cancelCall) {
                                $cancelCall->c_call_status = $post['callData']['CallStatus'];
                                $cancelCall->save();
                            }
                        }
                        $trace[] = 'if cancel call in first seconds:status' . $post['callData']['CallStatus'];
                    }
                }
            }
            $call = null;

            if(!$agentId) {
                if(isset($post['callData']['c_user_id']) && $post['callData']['c_user_id']) {
                    $agentId = $post['callData']['c_user_id'];
                    $trace[] = 'agent id (1810): ' . $agentId;
                }
            }

            if($agentId) {
                $call = Call::find()->where(['c_call_sid' => $post['callData']['CallSid']])->andWhere(['c_created_user_id' => $agentId])->limit(1)->one();
                //$trace[] = 'call 1812' .  ($call && $call->c_id) ? $call->c_id : 0;
            } else {
                if(isset($post['call'], $post['call']['c_call_type_id']) && $post['call']['c_call_type_id'] && (int)$post['call']['c_call_type_id'] === Call::CALL_TYPE_OUT) {
                    $call = Call::find()->where(['c_call_sid' => $post['callData']['CallSid']])->limit(1)->one();
                    //$trace[] = 'call 1818' .  ($call && $call->c_id) ? $call->c_id : 0;
                }
            }

            //$trace[] = 'call 1823' . ($call && $call->c_id) ? $call->c_id : 0;

            if(isset($post['callData']['ParentCallSid']) && $post['callData']['ParentCallSid']) {
                $childCall = true;
            } else {
                $childCall = false;
            }


            if(!$call) {
                $call = Call::find()->where(['c_call_sid' => $post['callData']['CallSid'], 'c_created_user_id' => null])->one();
                if($call && $agentId) {
                    $call->c_created_user_id = $agentId;
                    //$call->save();
                }
            }


            if($childCall) {
                if(!$call) {
                    $call = Call::find()->where(['c_call_sid' => $post['callData']['ParentCallSid'], 'c_created_user_id' => $agentId])->one();
                }

                if(!$call) {
                    $call = Call::find()->where(['c_call_sid' => $post['callData']['ParentCallSid'], 'c_created_user_id' => null])->one();
                    if($call && $agentId) {
                        $call->c_created_user_id = $agentId;
                        //$call->save();
                    }
                }
            }


            if($call) {

                if(isset($post['callData']['CallStatus']) && $post['callData']['CallStatus']) {
                    if($call->c_call_status && !in_array($call->c_call_status, [Call::CALL_STATUS_NO_ANSWER, Call::CALL_STATUS_BUSY, Call::CALL_STATUS_COMPLETED,  Call::CALL_STATUS_CANCELED])) {
                        $call->c_call_status = $post['callData']['CallStatus'];
                    }

                    if(isset($post['call']) && $post['call']) {
                        if(isset($post['call']['c_call_duration']) && $post['call']['c_call_duration']) {
                            $call->c_call_duration = (int) $post['call']['c_call_duration'];
                        }
                    } else {
                        $call->c_call_duration = 1;
                    }
                    if(!$call->save()) {
                        \Yii::error(VarDumper::dumpAsString($call->errors), 'API:Communication:voiceDefault:Call:save');
                    }
                    Notifications::socket($call->c_created_user_id, $call->c_lead_id, 'webCallUpdate', ['status' => $call->c_call_status, 'duration' => $call->c_call_duration, 'debug' => 'TYPE_VOIP'], true);
                }


                    $call_status = $post['callData']['CallStatus'];
                    $otherCalls = Call::find()->where(['c_call_sid' => $call->c_call_sid])->andWhere(['<>', 'c_id', $call->c_id])->all();
                    $trace[] = '$otherCalls: ' . count($otherCalls);
                    $otherCallArr = [];

                    if($otherCalls && $call_status === Call::CALL_STATUS_IN_PROGRESS) {
                        foreach ($otherCalls as $otherCall) {
                            $otherCallArr[] = $otherCall->attributes;

                            if($otherCall->c_call_status === Call::CALL_STATUS_RINGING) {
                                $otherCall->c_call_status = Call::CALL_STATUS_CANCELED;
                                //$otherCall->c_call_status = Call::CALL_STATUS_NO_ANSWER;
                                $otherCall->c_updated_dt = date('Y-m-d H:i:s');

                                if(!$otherCall->save()) {
                                    Yii::error('Call ID: '. $otherCall->c_id . ' ' . VarDumper::dumpAsString($otherCall->errors), 'API:Communication:voiceDefault:otherCall:save');
                                }
                            }
                        }
                    }

                    //Yii::info($call->c_call_sid . ' ' . VarDumper::dumpAsString($call->attributes) . ' Other Calls: ' . VarDumper::dumpAsString($otherCallArr), 'info\API:Voice:VOIP:CallBack');


                    if($call->c_call_status === Call::CALL_STATUS_NO_ANSWER || $call->c_call_status === Call::CALL_STATUS_BUSY || $call->c_call_status === Call::CALL_STATUS_CANCELED || $call->c_call_status === Call::CALL_STATUS_FAILED) {

                        if ($call->c_lead_id) {
                            $lead = $call->cLead;
                            $lead->l_call_status_id = Lead::CALL_STATUS_CANCEL;
                            if(!$lead->save()) {
                                Yii::error('lead: '. $lead->id . ' ' . VarDumper::dumpAsString($lead->errors), 'API:Communication:voiceDefault:Lead:save');
                            }
                        }

                    } else {

                        if(isset($post['callData']['CallStatus']) && $post['callData']['CallStatus']) {
                            $call->c_call_status = $post['callData']['CallStatus'];
                        }

                    }

                    if(!$childCall) {
                        $call->c_sequence_number = $post['callData']['SequenceNumber'] ?? 0;

                        if (isset($post['callData']['CallDuration'])) {
                            $call->c_call_duration = (int)$post['callData']['CallDuration'];
                        }

                        if (isset($post['call']['c_tw_price']) && $post['call']['c_tw_price']) {
                            $call->c_price = abs((float)$post['call']['c_tw_price']);
                        }
                    }

                    $call->c_updated_dt = date('Y-m-d H:i:s');
                    if(!$call->save()) {
                        Yii::error(VarDumper::dumpAsString($call->errors), 'API:Communication:voiceDefault:Call2:save');
                    }
                    if ($call->c_lead_id) {
                        //Notifications::create($user_id, 'New SMS '.$sms->s_phone_from, 'SMS from ' . $sms->s_phone_from .' ('.$clientName.') to '.$sms->s_phone_to.' <br> '.nl2br(Html::encode($sms->s_sms_text))
                        // . ($lead_id ? '<br>Lead ID: '.$lead_id : ''), Notifications::TYPE_INFO, true);
                        //Notifications::socket(null, $call->c_lead_id, 'callUpdate', ['status' => $call->c_call_status, 'duration' => $call->c_call_duration, 'snr' => $call->c_sequence_number], true);
                    }

                    if($call->c_created_user_id) {
                        //Notifications::socket($call->c_created_user_id, $lead_id = null, 'incomingCall', ['status' => $call->c_call_status, 'duration' => $call->c_call_duration, 'snr' => $call->c_sequence_number], true);
                        Notifications::socket($call->c_created_user_id, null, 'webCallUpdate', ['status' => $call->c_call_status, 'duration' => $call->c_call_duration, 'debug' => 'DEFAULT'], true);
                    }

            } else {
                $trace[] = 'No call find by params';
            }
        }

        $response['trace'] = $trace;
/*
        if (isset($post['callData']['CallSid']) && $post['callData']['CallSid']) {


            //$agentId = null;

            if(isset($post['callData']['c_user_id']) && $post['callData']['c_user_id']) {
                $agentId = $post['callData']['c_user_id'];
            }

            if(!$agentId ?? isset($post['callData']['Called']) && $post['callData']['Called']) {
                if(strpos($post['callData']['Called'], 'client:seller')) {
                    $agentId = (int) str_replace('client:seller', '', $post['callData']['Called']);
                }
            }

            if(isset($post['callData']['ParentCallSid']) && $post['callData']['ParentCallSid']) {
                $childCall = true;
            } else {
                $childCall = false;
            }


            $call = Call::find()->where(['c_call_sid' => $post['callData']['CallSid'], 'c_created_user_id' => $agentId])->one();

            if(!$call) {
                $call = Call::find()->where(['c_call_sid' => $post['callData']['CallSid'], 'c_created_user_id' => null])->one();
                if($call && $agentId) {
                    $call->c_created_user_id = $agentId;
                    //$call->save();
                }
            }


            if($childCall) {
                if(!$call) {
                    $call = Call::find()->where(['c_call_sid' => $post['callData']['ParentCallSid'], 'c_created_user_id' => $agentId])->one();
                }

                if(!$call) {
                    $call = Call::find()->where(['c_call_sid' => $post['callData']['ParentCallSid'], 'c_created_user_id' => null])->one();
                    if($call && $agentId) {
                        $call->c_created_user_id = $agentId;
                        //$call->save();
                    }
                }
            }


            if(isset($post['callData']['CallStatus']) && $post['callData']['CallStatus']) {
                $call_status = $post['callData']['CallStatus'];
            } else {
                $call_status = '';
            }



            if ($call) {


                $otherCalls = Call::find()->where(['c_call_sid' => $call->c_call_sid])->andWhere(['<>', 'c_id', $call->c_id])->all();

                $otherCallArr = [];

                if($otherCalls && $call_status === Call::CALL_STATUS_IN_PROGRESS) {
                    foreach ($otherCalls as $otherCall) {
                        $otherCallArr[] = $otherCall->attributes;

                        if($otherCall->c_call_status === Call::CALL_STATUS_RINGING) {
                            $otherCall->c_call_status = Call::CALL_STATUS_CANCELED;
                            //$otherCall->c_call_status = Call::CALL_STATUS_NO_ANSWER;
                            $otherCall->c_updated_dt = date('Y-m-d H:i:s');

                            if(!$otherCall->save()) {
                                Yii::error('Call ID: '. $otherCall->c_id . ' ' . VarDumper::dumpAsString($otherCall->errors), 'API:Communication:voiceDefault:otherCall:save');
                            }
                        }
                    }
                }

                //Yii::info($call->c_call_sid . ' ' . VarDumper::dumpAsString($call->attributes) . ' Other Calls: ' . VarDumper::dumpAsString($otherCallArr), 'info\API:Voice:VOIP:CallBack');


                if($call->c_call_status === Call::CALL_STATUS_NO_ANSWER || $call->c_call_status === Call::CALL_STATUS_BUSY || $call->c_call_status === Call::CALL_STATUS_CANCELED || $call->c_call_status === Call::CALL_STATUS_FAILED) {

                    if ($call->c_lead_id) {
                        $lead = $call->cLead;
                        $lead->l_call_status_id = Lead::CALL_STATUS_CANCEL;
                        if(!$lead->save()) {
                            Yii::error('lead: '. $lead->id . ' ' . VarDumper::dumpAsString($lead->errors), 'API:Communication:voiceDefault:Lead:save');
                        }
                    }

                } else {

                    if(isset($post['callData']['CallStatus']) && $post['callData']['CallStatus']) {
                        $call->c_call_status = $post['callData']['CallStatus'];
                    }

                }

                if(!$childCall) {
                    $call->c_sequence_number = $post['callData']['SequenceNumber'] ?? 0;

                    if (isset($post['callData']['CallDuration'])) {
                        $call->c_call_duration = (int)$post['callData']['CallDuration'];
                    }

                    if (isset($post['call']['c_tw_price']) && $post['call']['c_tw_price']) {
                        $call->c_price = abs((float)$post['call']['c_tw_price']);
                    }
                }

                $call->c_updated_dt = date('Y-m-d H:i:s');
                if(!$call->save()) {
                    Yii::error(VarDumper::dumpAsString($call->errors), 'API:Communication:voiceDefault:Call2:save');
                }
                if ($call->c_lead_id) {
                    //Notifications::create($user_id, 'New SMS '.$sms->s_phone_from, 'SMS from ' . $sms->s_phone_from .' ('.$clientName.') to '.$sms->s_phone_to.' <br> '.nl2br(Html::encode($sms->s_sms_text))
                       // . ($lead_id ? '<br>Lead ID: '.$lead_id : ''), Notifications::TYPE_INFO, true);
                    //Notifications::socket(null, $call->c_lead_id, 'callUpdate', ['status' => $call->c_call_status, 'duration' => $call->c_call_duration, 'snr' => $call->c_sequence_number], true);
                }

                if($call->c_created_user_id) {
                    //Notifications::socket($call->c_created_user_id, $lead_id = null, 'incomingCall', ['status' => $call->c_call_status, 'duration' => $call->c_call_duration, 'snr' => $call->c_sequence_number], true);
                    Notifications::socket($call->c_created_user_id, null, 'webCallUpdate', ['status' => $call->c_call_status, 'duration' => $call->c_call_duration, 'debug' => 'DEFAULT'], true);
                }

            }
        }
*/
        return $response;
    }



    protected function findOrCreateCall(string $callSid, array $calData, int $call_project_id, ?int $call_dep_id): Call
    {
        $call = null;

        if($callSid) {
            $call = Call::find()->where(['c_call_sid' => $callSid])->limit(1)->one();
        }
        if(!$call) {

            $call = new Call();
            $call->c_call_sid = $calData['CallSid'] ?? null;
            $call->c_account_sid = $calData['AccountSid'] ?? null;
            $call->c_call_type_id = Call::CALL_TYPE_IN;
            $call->c_call_status = $calData['CallStatus'] ?? Call::CALL_STATUS_QUEUE;
            $call->c_com_call_id = $calData['c_com_call_id'] ?? null;
            $call->c_direction = $calData['Direction'] ?? null;
            $call->c_parent_call_sid = $calData['ParentCallSid'] ?? null;

            if($call_project_id) {
                $call->c_project_id = $call_project_id;
            }
            if($call_dep_id) {
                $call->c_dep_id = $call_dep_id;
            }

            $call->c_is_new = true;
            $call->c_api_version = $calData['ApiVersion'] ?? null;
            $call->c_created_dt = date('Y-m-d H:i:s');
            $call->c_from = $calData['From'];
            $call->c_sip = null;
            $call->c_to = $calData['To']; //Called
            $call->c_created_user_id = null;

            if($call->c_dep_id === Department::DEPARTMENT_SALES) {
                $lead = Lead2::findLastLeadByClientPhone($call->c_from, $call->c_project_id);
                if ($lead) {
                    $call->c_lead_id = $lead->id;
                }
            }

            if (!$call->save()) {
                \Yii::error(VarDumper::dumpAsString($call->errors), 'API:CommunicationController:findOrCreateCall:Call:save');
                throw new \Exception('findOrCreateCall: Can not save call in db');
            }
        }

        return $call;
    }


    protected function startCallService(Call $callModel, DepartmentPhoneProject $department, int $ivrSelectedDigit, array $stepParams): array
    {

        if(isset(Department::DEPARTMENT_LIST[$ivrSelectedDigit])) {
            $callModel->c_dep_id = $ivrSelectedDigit;
            if(!$callModel->save()) {
                Yii::error(VarDumper::dumpAsString($callModel->errors), 'API:Communication:startCallService:Call:update');
            }

            $job = new CallQueueJob();
            $job->call_id = $callModel->c_id;
            $job->delay = 7;
            $jobId = Yii::$app->queue_job->push($job);
        }

        $choice = $stepParams['digits'][$ivrSelectedDigit] ?? null;
        $responseTwml = new VoiceResponse();

        if(isset($stepParams['before_say']) && $stepParams['before_say']) {
            $responseTwml->say($stepParams['before_say'], ['language' => $stepParams['language'], 'voice' => $stepParams['voice']]);
        }

        if($choice) {
            if(isset($choice['pause']) && $choice['pause']) {
                $responseTwml->pause(['length' => $choice['pause']]);
            }
            if(isset($choice['say'])) {
                $responseTwml->say($choice['say'], ['language' => $choice['language'], 'voice' => $choice['voice']]);
            }
            $responseTwml->play($choice['play']);
        }

        if(isset($stepParams['after_say']) && $stepParams['after_say']) {
            $responseTwml->say($stepParams['after_say'], ['language' => $stepParams['language'], 'voice' => $stepParams['voice']]);
        }

        $response = [];
        $response['twml'] = (string) $responseTwml;
        $responseData = [
            'status' => 200,
            'name' => 'Success',
            'code' => 0,
            'message' => '',
            'data' => ['response' => $response]
        ];
        return $responseData;
    }




    /**
     * @param Call $callModel
     * @param DepartmentPhoneProject $department
     * @param int $ivrStep
     * @param int|null $ivrSelectedDigit
     * @return array
     */
    protected function ivrService(Call $callModel, DepartmentPhoneProject $department, int $ivrStep, ?int $ivrSelectedDigit): array
    {
        $response = [];


        Yii::info(VarDumper::dumpAsString([
            'callModel' => $callModel->attributes,
            'department' => $department->attributes,
            'ivrSelectedDigit' => $ivrSelectedDigit,
            'ivrStep' => $ivrStep,

        ], 10, false), 'info\API:Communication:ivrService');


        try {
            //$params_voice_gather = \Yii::$app->params['voice_gather'];

            $dParams = @json_decode($department->dpp_params, true);
            $ivrParams = $dParams['ivr'] ?? [];

            $stepParams = [];

            if(isset($ivrParams['steps'][$ivrStep])) {
                $stepParams = $ivrParams['steps'][$ivrStep];
            }


            $company = '';
            if ($callModel->cProject && $callModel->cProject->name) {
                $company = ' ' . strtolower($callModel->cProject->name);
            }


            if($ivrStep === 2) {

                $ivrSelectedDigit = (int) $ivrSelectedDigit;

                if ($ivrSelectedDigit) {
                    return $this->startCallService($callModel, $department, $ivrSelectedDigit, $stepParams);
                }

                $responseTwml = new VoiceResponse();
                $responseTwml->pause(['length' => 2]);
                //$responseTwml->say('Selected number '.$ivrSelectedDigit . '. Goodbye! ');
                //$responseTwml->reject(['reason' => 'busy']);
                $responseTwml->say($ivrParams['error_phrase'], ['language' => $ivrParams['entry_language'], 'voice' => $ivrParams['entry_voice']]);
                $responseTwml->redirect('/v1/twilio/voice-gather/?step=1', ['method' => 'POST']);


                $response['twml'] = (string) $responseTwml;
                $responseData = [
                    'status' => 200,
                    'name' => 'Success',
                    'code' => 0,
                    'message' => '',
                    'data' => ['response' => $response]
                ];


                return $responseData;
            }

            if($callModel && $callModel->c_call_status != Call::CALL_STATUS_IVR) {
                $callModel->c_call_status = Call::CALL_STATUS_IVR;
                $callModel->update();
            }

            $responseTwml = new VoiceResponse();

            if(isset($ivrParams['entry_pause']) && $ivrParams['entry_pause']) {
                $responseTwml->pause(['length' => $ivrParams['entry_pause']]);
            }

            $entry_phrase = isset($ivrParams['entry_phrase']) ? str_replace('{{project}}', $company, $ivrParams['entry_phrase']) : null;

            if($entry_phrase) {
                $responseTwml->say($entry_phrase, ['language' => $ivrParams['entry_language'], 'voice' => $ivrParams['entry_voice']]);
            }


            if(isset($ivrParams['steps'])) {

                $gather = $responseTwml->gather([
                    'action' => '/v1/twilio/voice-gather/?step=2',
                    'method' => 'POST',
                    'numDigits' => 1,
                    'timeout' => 5,
                    //'actionOnEmptyResult' => true,
                ]);


                $stepParams = $ivrParams['steps'][$ivrStep] ?? [];

                if (isset($stepParams['before_say']) && $stepParams['before_say']) {
                    $gather->say($ivrParams['steps'][$ivrStep]['before_say'], ['language' => $stepParams['language'], 'voice' => $stepParams['voice']]);
                }

                $after_say = '';
                if (isset($stepParams['after_say']) && $stepParams['after_say']) {
                    $after_say = $stepParams['after_say'];
                }

                if (isset($stepParams['choice']) && $stepParams['choice']) {
                    foreach ($stepParams['choice'] as $sayItem) {
                        $gather->say($sayItem['say'] . ' ' . $after_say, ['language' => $stepParams['language'], 'voice' => $stepParams['voice']]);
                        if (isset($sayItem['pause']) && $sayItem['pause']) {
                            $gather->pause(['length' => $sayItem['pause']]);
                        }
                    }
                }

                if (isset($stepParams['after_say']) && $stepParams['after_say']) {
                    $gather->say($stepParams['after_say'], ['language' => $stepParams['language'], 'voice' => $stepParams['voice']]);
                }


                $responseTwml->say($ivrParams['error_phrase']);
                $responseTwml->redirect('/v1/twilio/voice-gather/?step=1', ['method' => 'POST']);
            } else {


                if(isset(Department::DEPARTMENT_LIST[$department->dpp_dep_id])) {
                    $callModel->c_dep_id = $department->dpp_dep_id;
                    if(!$callModel->save()) {
                        Yii::error(VarDumper::dumpAsString($callModel->errors), 'API:Communication:startCallService:Call:update2');
                    }

                    $job = new CallQueueJob();
                    $job->call_id = $callModel->c_id;
                    $job->delay = 7;
                    $jobId = Yii::$app->queue_job->push($job);
                }

                if(isset($ivrParams['hold_play']) && $ivrParams['hold_play']) {
                    $responseTwml->play($ivrParams['hold_play']);
                }

            }

            $response['twml'] = (string) $responseTwml;
            $responseData = [
                'status' => 200,
                'name' => 'Success',
                'code' => 0,
                'message' => ''
            ];
            $responseData['data']['response'] = $response;

        } catch (\Throwable $e) {
            $responseTwml = new VoiceResponse();
            $responseTwml->reject(['reason' => 'busy']);
            $response['twml'] = (string) $responseTwml;
            $responseData = [
                'status' => 404,
                'name' => 'Error',
                'code' => 404,
                'message' => 'Sales Communication error: '. $e->getMessage(). "\n" . $e->getFile() . ':' . $e->getLine(),
            ];
            $responseData['data']['response'] = $response;
        }
        return $responseData;
    }


    /**
     * @return mixed
     */
    private function updateEmailStatus()
    {
        $eq_id = (int) Yii::$app->request->post('eq_id');
        $eq_status_id = (int) Yii::$app->request->post('eq_status_id');
        // $eq_project_id = Yii::$app->request->post('eq_project_id');

        try {

            if(!$eq_id) {
                throw new NotFoundHttpException('Not found eq_id', 11);
            }

            if(!$eq_status_id) {
                throw new NotFoundHttpException('Not found eq_status_id', 12);
            }

            $email = Email::findOne(['e_communication_id' => $eq_id]);
            if($email) {

                if($eq_status_id > 0) {
                    $email->e_status_id = $eq_status_id;
                    if($eq_status_id === Email::STATUS_DONE) {
                        $email->e_status_done_dt = date('Y-m-d H:i:s');
                    }

                    if(!$email->save()) {
                        Yii::error(VarDumper::dumpAsString($email->errors), 'API:Communication:updateEmailStatus:Email:save');
                    }
                }

                $response['email'] = $email->e_id;
            } else {
                $response['error'] = 'Not found Communication ID ('.$eq_id.')';
                $response['error_code'] = 13;
            }


        } catch (\Throwable $e) {
            Yii::error($e->getTraceAsString(), 'API:Communication:updateEmailStatus:Email:try');
            $message = $this->debug ? $e->getTraceAsString() : $e->getMessage() . ' (code:' . $e->getCode() . ', line: ' . $e->getLine() . ')';
            $response['error'] = $message;
            $response['error_code'] = 15;
        }

        return $response;
    }


    /**
     * @return mixed
     */
    private function updateSmsStatus()
    {

        /*
         * [
                'sq_id' => '257'
                'sq_status_id' => '5'
                'sq_project_id' => '6'
                'sq_num_segments' => '2'
                'sms' => [
                    'sq_id' => '257'
                    'sq_project_id' => '6'
                    'sq_phone_from' => '+15596489977'
                    'sq_phone_to' => '+37360368365'
                    'sq_sms_text' => 'WOWFARE best price (per adult) to London:'
                    'sq_sms_data' => '{\"project_id\":\"6\"}'
                    'sq_type_id' => '2'
                    'sq_language_id' => 'en-US'
                    'sq_job_id' => '9058'
                    'sq_priority' => '2'
                    'sq_status_id' => '5'
                    'sq_delay' => '0'
                    'sq_status_done_dt' => '2019-02-08 09:25:16'
                    'sq_tw_message_id' => 'SM591824e067f7459e9da3134dd8fe5b77'
                    'sq_tw_num_segments' => '2'
                    'sq_tw_status' => 'queued'
                    'sq_tw_uri' => '/2010-04-01/Accounts/AC10f3c74efba7b492cbd7dca86077736c/Messages/SM591824e067f7459e9da3134dd8fe5b77.json'
                    'sq_created_api_user_id' => '8'
                    'sq_created_dt' => '2019-02-08 09:25:15'
                    'sq_updated_dt' => '2019-02-08 09:25:16'
                ]
                'action' => 'update'
                'type' => 'update_sms_status'
            ]
         */


        $sq_id = (int) Yii::$app->request->post('sq_id');
        $sq_status_id = (int) Yii::$app->request->post('sq_status_id');

        //$sq_price = Yii::$app->request->post('sq_price');

        $smsParams = Yii::$app->request->post('sms');

        // $sq_project_id = Yii::$app->request->post('sq_project_id');

        try {

            // Yii::info(VarDumper::dumpAsString(Yii::$app->request->post()), 'info\updateSmsStatus');

            if(!$sq_id) {
                throw new NotFoundHttpException('Not found sq_id', 11);
            }

            if(!$sq_status_id) {
                throw new NotFoundHttpException('Not found sq_status_id', 12);
            }

            $sid =  $smsParams['sq_tw_message_id'] ?? null;

            $sms = null;

            if($sid) {
                $sms = Sms::findOne(['s_tw_message_sid' => $sid]);
            }

            if(!$sms) {
                $sms = Sms::findOne(['s_communication_id' => $sq_id]);
            }


            if($sms) {

                if($sq_status_id > 0) {
                    $sms->s_status_id = $sq_status_id;
                    if($sq_status_id === Sms::STATUS_DONE) {
                        $sms->s_status_done_dt = date('Y-m-d H:i:s');
                    }

                    if($smsParams) {
                        if(isset($smsParams['sq_tw_price']) && $smsParams['sq_tw_price']) {
                            $sms->s_tw_price = abs((float) $smsParams['sq_tw_price']);
                        }

                        if(isset($smsParams['sq_tw_num_segments']) && $smsParams['sq_tw_num_segments']) {
                            $sms->s_tw_num_segments = (int) $smsParams['sq_tw_num_segments'];
                        }

                        if(isset($smsParams['sq_tw_status']) && $smsParams['sq_tw_status']) {
                            $sms->s_error_message = 'status: ' .  $smsParams['sq_tw_status'];
                        }

                        if(!$sms->s_tw_message_sid && isset($smsParams['sq_tw_message_id']) && $smsParams['sq_tw_message_id']) {
                            $sms->s_tw_message_sid = $smsParams['sq_tw_message_id'];
                        }

                    }

                    if(!$sms->save()) {
                        Yii::error(VarDumper::dumpAsString($sms->errors), 'API:Communication:updateSmsStatus:Sms:save');
                    }
                }

                $response['sms'] = $sms->s_id;
            } else {
                $response['error'] = 'Not found SMS ID ('.$sq_id.')';
                $response['error_code'] = 13;
            }


        } catch (\Throwable $e) {
            Yii::error($e->getTraceAsString(), 'API:Communication:updateSmsStatus:try');
            $message = $this->debug ? $e->getTraceAsString() : $e->getMessage() . ' (code:' . $e->getCode() . ', line: ' . $e->getLine() . ')';
            $response['error'] = $message;
            $response['error_code'] = 15;
        }

        return $response;
    }



    private function smsFinish(): array
    {

        /*
         * account_sid: "AC10f3c74efba7b492cbd7dca86077736c"
            api_version: "2010-04-01"
            body: "WOWFARE best price (per adult) to Kathmandu:
            ↵$ 1905.05 (s short layovers), https://wowfare.com/q/5c5b5180c6d29
            ↵Regards, Nancy"
            date_created: "Wed, 06 Feb 2019 21:30:12 +0000"
            date_sent: "Wed, 06 Feb 2019 21:30:12 +0000"
            date_updated: "Wed, 06 Feb 2019 21:30:12 +0000"
            direction: "outbound-api"
            error_code: null
            error_message: null
            from: "+16692011645"
            messaging_service_sid: null
            num_media: "0"
            num_segments: "2"
            price: "-0.01500"
            price_unit: "USD"
            sid: "SMb40bfd6908184ec0a51e20789979e304"
            status: "delivered"
            subresource_uris: {,…}
            media: "/2010-04-01/Accounts/AC10f3c74efba7b492cbd7dca86077736c/Messages/SMb40bfd6908184ec0a51e20789979e304/Media.json"
            to: "+15122036074"
            uri: "/2010-04-01/Accounts/AC10f3c74efba7b492cbd7dca86077736c/Messages/SMb40bfd6908184ec0a51e20789979e304.json"
         */

        $response = [];

        try {

            $smsData = Yii::$app->request->post('smsData');
            $comId = Yii::$app->request->post('sq_id');

            if(!$smsData) {
                throw new NotFoundHttpException('Not found smsData', 11);
            }

            if(!$smsData['sid']) {
                throw new NotFoundHttpException('Not found smsData[sid]', 12);
            }



            $sms = Sms::findOne(['s_tw_message_sid' => $smsData['sid']]);

            if(!$sms) {
                $sms = Sms::findOne(['s_communication_id' => $comId]);
            }


            if($sms) {

                if(isset($smsData['price'])) {
                    $sms->s_tw_price = abs((float) $smsData['price']);
                }

                if(isset($smsData['num_segments']) && $smsData['num_segments']) {
                    $sms->s_tw_num_segments = (int) $smsData['num_segments'];
                }

                if(isset($smsData['sid']) && $smsData['sid']) {
                    if(!$sms->s_tw_message_sid) {
                        $sms->s_tw_message_sid = $smsData['sid'];
                    }
                }

                if(isset($smsData['account_sid']) && $smsData['account_sid']) {
                    if(!$sms->s_tw_account_sid) {
                        $sms->s_tw_account_sid = $smsData['account_sid'];
                    }
                }


                if(isset($smsData['status'])) {

                    $sms->s_error_message = 'status: ' . $smsData['status'];

                    if($smsData['status'] === 'delivered') {
                        $sms->s_status_id = SMS::STATUS_DONE;
                    }
                }

                if(!$sms->save()) {
                    Yii::error(VarDumper::dumpAsString($sms->errors), 'API:Communication:smsFinish:Sms:save');
                }
                $response['sms'] = $sms->attributes;

            } else {
                $response['error'] = 'Not found SMS message_sid ('.$smsData['sid'].') and not found CommId ('.$comId.')';
                $response['error_code'] = 13;
            }


        } catch (\Throwable $e) {
            Yii::error($e->getTraceAsString(), 'API:Communication:smsFinish:try');
            $message = $this->debug ? $e->getTraceAsString() : $e->getMessage() . ' (code:' . $e->getCode() . ', line: ' . $e->getLine() . ')';
            $response['error'] = $message;
            $response['error_code'] = $e->getCode();
        }

        return $response;
    }


    /**
     * @param null $last_id
     * @return array
     */
    private function newEmailMessagesReceived($last_id = NULl): array
    {
        $response = [];
        try {

            $filter = [];
            $dateTime = null;
            if(NULL === $last_id) {

                $lastEmail = Email::find()->where(['>', 'e_inbox_email_id', 0])->orderBy(['e_inbox_email_id' => SORT_DESC])->limit(1)->one();

                if ($lastEmail) {
                    //$filter['last_dt'] = $lastEmail->e_inbox_created_dt;
                    $filter['last_id'] = $lastEmail->e_inbox_email_id + 1;
                } else {
                    $filter['last_id'] = 18100;
                }
            } else {
                $filter['last_id'] = (int)$last_id;

                $checkLastEmail = Email::find()->where(['e_inbox_email_id' => $filter['last_id']])->limit(1)->one();
                if($checkLastEmail) {
                    $response[] = 'Last ID ' . $filter['last_id'] . ' Exists';
                    return $response;
                }

            }

            $filter['limit'] = 20;

            $mailList = [];
            $mails = UserProjectParams::find()->select(['DISTINCT(upp_email)'])->andWhere(['!=', 'upp_email', ''])->asArray()->all();
            if($mails) {
                $mailList = ArrayHelper::getColumn($mails,'upp_email');
            }
            $filter['mail_list'] = $mailList;

            // push job
            $job = new ReceiveEmailsJob();
            $job->last_email_id = $filter['last_id'];
            $data = [
                'last_email_id' => $filter['last_id'],
                'mail_list' => $filter['mail_list'],
                'limit' => $filter['limit'],
            ];



            $job->request_data = $data;
            /** @var Queue $queue */
            $queue = \Yii::$app->queue_email_job;
            $jobId = $queue->push($job);
            $response = [
                'job_id' => $jobId,
                'last_id' => $filter['last_id'],
            ];

            //Yii::info('JOB (' .VarDumper::dumpAsString($response).') Push ' . VarDumper::dumpAsString($data) . ' last_id: ' . $last_id, 'info\API:newEmailMessagesReceived');

        } catch (\Throwable $e) {
            Yii::error($e->getTraceAsString(), 'API:Communication:newEmailMessagesReceived:Email:try');
            $message = $this->debug ? $e->getTraceAsString() : $e->getMessage() . ' (code:' . $e->getCode() . ', line: ' . $e->getLine() . ')';
            $response['error'] = $message;
            $response['error_code'] = 15;
        }

        return $response;
    }


    /**
     * @return array
     */
    private function newSmsMessagesReceived(): array
    {
        $response = [];

        $smsItem = Yii::$app->request->post();

        if(!\is_array($smsItem)) {
            $response['error'] = 'Sales: Invalid POST request (array)';
            $response['error_code'] = 16;
        }

        if(!isset($smsItem['si_id'])) {
            $response['error'] = 'Sales: Invalid POST request - not found (si_id)';
            $response['error_code'] = 17;
        }

        if(isset($response['error']) && $response['error']) {
            return $response;
        }

        try {

                /*
                 *  * @property int $si_id
                 * @property string $si_phone_to
                 * @property string $si_phone_from
                 * @property string $si_sms_text
                 * @property int $si_project_id
                 * @property bool $si_deleted
                 * @property string $si_sent_dt
                 * @property string $si_created_dt
                 * @property string $si_updated_dt
                 * @property string $si_message_sid
                 * @property int $si_num_segments
                 * @property string $si_to_country
                 * @property string $si_to_state
                 * @property string $si_to_city
                 * @property string $si_to_zip
                 * @property string $si_from_country
                 * @property string $si_from_city
                 * @property string $si_from_state
                 * @property string $si_from_zip
                 */

                    $sms = new Sms();
                    $sms->s_type_id = Sms::TYPE_INBOX;
                    $sms->s_status_id = Sms::STATUS_DONE;
                    $sms->s_is_new = true;

                    $sms->s_status_done_dt = isset($smsItem['si_sent_dt']) ? date('Y-m-d H:i:s', strtotime($smsItem['si_sent_dt'])) : null;

                    //$sms->s_communication_id = $smsItem['si_id'] ?? null;

                    $sms->s_phone_to = $smsItem['si_phone_to'];
                    $sms->s_phone_from = $smsItem['si_phone_from'];
                    $sms->s_project_id = $smsItem['si_project_id'] ?? null;
                    $sms->s_sms_text = $smsItem['si_sms_text'];
                    $sms->s_created_dt = $smsItem['si_created_dt'];

                    $sms->s_tw_message_sid = $smsItem['si_message_sid'] ?? null;
                    $sms->s_tw_num_segments = $smsItem['si_num_segments'] ?? null;

                    $sms->s_tw_to_country = $smsItem['si_to_country'] ?? null;
                    $sms->s_tw_to_state = $smsItem['si_to_state'] ?? null;
                    $sms->s_tw_to_city = $smsItem['si_to_city'] ?? null;
                    $sms->s_tw_to_zip = $smsItem['si_to_zip'] ?? null;

                    $sms->s_tw_from_country = $smsItem['si_from_country'] ?? null;
                    $sms->s_tw_from_city = $smsItem['si_from_city'] ?? null;
                    $sms->s_tw_from_state = $smsItem['si_from_state'] ?? null;
                    $sms->s_tw_from_zip = $smsItem['si_from_zip'] ?? null;


                    $lead_id = $sms->detectLeadId();


                    if($lead_id) {
                        $lead = Lead::findOne($lead_id);
                        if($lead) {
                            $sms->s_project_id = $lead->project_id;
                        }
                        Yii::info('SMS Detected LeadId '.$lead_id.' from '.$sms->s_phone_from, 'info\API:Communication:newSmsMessagesReceived:Sms');
                    }


                    if(!$sms->save()) {
                        Yii::error(VarDumper::dumpAsString($sms->errors), 'API:Communication:newSmsMessagesReceived:Sms:save');
                        $response['error_code'] = 12;
                        throw new \Exception('Error save SMS data ' . VarDumper::dumpAsString($sms->errors));
                    }


                    //Notifications::create(Yii::$app->user->id, 'Test '.date('H:i:s'), 'Test message <h2>asdasdasd</h2>', Notifications::TYPE_SUCCESS, true);


                    $users = $sms->getUsersIdByPhone();

                    $clientPhone = ClientPhone::find()->where(['phone' => $sms->s_phone_from])->orderBy(['id' => SORT_DESC])->limit(1)->one();
                    if($clientPhone) {
                        $clientName = $clientPhone->client ? $clientPhone->client->full_name : '-';
                    } else {
                        $clientName = '-';
                    }

                    $user_id = 0;

                    if($users) {
                        foreach ($users as $user_id) {

                            Notifications::create($user_id, 'New SMS '.$sms->s_phone_from, 'SMS from ' . $sms->s_phone_from .' ('.$clientName.') to '.$sms->s_phone_to.' <br> '.nl2br(Html::encode($sms->s_sms_text))
                            . ($lead_id ? '<br>Lead ID: '.$lead_id : ''), Notifications::TYPE_INFO, true);
                            Notifications::socket($user_id, null, 'getNewNotification', ['sms_id' => $sms->s_id], true);
                        }
                    }

                    if($user_id > 0) {
                        $sms->s_created_user_id = $user_id;
                        $sms->save();
                    }

                    if($lead_id) {
                        Notifications::socket(null, $lead_id, 'updateCommunication', ['sms_id' => $sms->s_id], true);
                    }

                    $response = $sms->attributes;

        } catch (\Throwable $e) {
            Yii::error($e->getTraceAsString(), 'API:Communication:newSmsMessagesReceived:Sms:try');
            $message = $this->debug ? $e->getTraceAsString() : $e->getMessage() . ' (code:' . $e->getCode() . ', line: ' . $e->getLine() . ')';
            $response['error'] = $message;
            if(!isset($response['error_code']) || !$response['error_code']) {
                $response['error_code'] = 15;
            }
        }

        return $response;
    }
}
