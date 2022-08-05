<?php

namespace common\components;

use common\components\CommunicationService;
use common\models\DepartmentEmailProject;
use common\models\Notifications;
use common\models\UserProjectParams;
use frontend\widgets\notification\NotificationMessage;
use src\dto\email\EmailDTO;
use src\exception\CreateModelException;
use src\repositories\email\EmailRepositoryFactory;
use src\services\email\EmailMainService;
use src\services\email\EmailService;
use Yii;
use yii\base\BaseObject;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\VarDumper;

/**
 * Class ReceiveEmailsJob
 * @package common\components
 *
 * @property EmailMainService
 */
class ReceiveEmailsJob extends BaseObject implements \yii\queue\JobInterface
{
    /**
     * @var EmailMainService
     */
    private $emailService;

    public $last_email_id = 0;

    public $request_data = [];

    /**
     * @param \yii\queue\Queue $queue
     * @return bool
     * @throws \yii\httpclient\Exception
     */
    public function execute($queue): bool
    {
        $debug = true;
        $filter = [];

        $accessEmailRequest = true;
        $cicleCount = 1;
        $countTotal = 0;

//        Yii::info(VarDumper::dumpAsString(['last_email_id' => $this->last_email_id, 'request_data' => $this->request_data]), 'info\JOB:ReceiveEmailsJob');

        try {
            $this->emailService = EmailMainService::newInstance();

            if ((int)$this->last_email_id < 1) {
                \Yii::error('Not found last_email_id (' . $this->last_email_id . ')', 'ReceiveEmailsJob:execute');
                return true;
            }

            if (!count($this->request_data)) {
                \Yii::error('Error request_data  (' . print_r($this->request_data, true) . ')', 'ReceiveEmailsJob:execute');
                return true;
            }

            $filter['last_id'] = (int)$this->last_email_id;

            if (isset($this->request_data['limit'])) {
                $filter['limit'] = (int)$this->request_data['limit'];
            } else {
                $filter['limit'] = 20;
            }

//            if (isset($this->request_data['email_list'])) {
//                $filter['email_list'] = $this->request_data['email_list'];
//            } else {
//                $filter['email_list'] = [];
//            }
            $filter['email_list'] = Json::encode(['list' => $this->getEmailsForReceivedMessages()]);

            /** @var CommunicationService $communication */
            $communication = Yii::$app->comms;

            $notify = [];

            while ($accessEmailRequest && $cicleCount < 100) {
                if ($debug) {
                    echo "Cicle #" . $cicleCount . PHP_EOL;
                }

                $res = $communication->mailGetMessages($filter);

                if (isset($res['error']) && $res['error']) {
                    $response['error'] = 'Error mailGetMessages';
                    $response['error_code'] = 13;
                    \Yii::error(VarDumper::dumpAsString($res['error']), 'ReceiveEmailsJob:execute');
                    $cicleCount--;
                } elseif (
                    isset($res['data']['emails']) &&
                    $res['data']['emails'] &&
                    \is_array($res['data']['emails']) &&
                    isset($res['data']['emails'][0]) &&
                    $res['data']['emails'][0]
                ) {
                    foreach ($res['data']['emails'] as $mail) {
                        if ($debug) {
                            echo '.';
                        }

                        $filter['last_id'] = $mail['ei_id'] + 1;

                        $emailRepository = EmailRepositoryFactory::getRepository();
                        $find = $emailRepository->findReceived($mail['ei_message_id'], $mail['ei_email_to'])->one();
                        if ($find) {
                            $emailRepository->saveInboxId($find, $mail['ei_id']);
                            continue;
                        }

                        try {
                            $emailDTO = EmailDTO::newInstance()->fillFromCommunication($mail);
                            $notifications = $this->emailService->receiveEmail($emailDTO);
                            if (!empty($notifications)) {
                                $notify = ArrayHelper::merge($notify, $notifications);
                            }
                        } catch (CreateModelException $e) {
                            \Yii::error(VarDumper::dumpAsString([
                                'communicationId' => $emailDTO->inboxEmailId,
                                'error' => $e->getErrors(),
                            ]), 'ReceiveEmailsJob:execute:CreateModelException');
                        } catch (\Throwable $e) {
                            \Yii::error(VarDumper::dumpAsString([
                                'communicationId' => $emailDTO->inboxEmailId,
                                'error' => $e->getMessage(),
                            ]), 'ReceiveEmailsJob:execute');
                        }

                        $countTotal++;
                    }

                    if (isset($res['data']['pagination'], $res['data']['pagination']['count'])) {
                        if ($res['data']['pagination']['count'] < 1) {
                            break;
                        }
                    }
                } else {
                    $cicleCount--;
                    $accessEmailRequest = false;
                    if ($debug) {
                        echo 'Cicle finish' . PHP_EOL;
                    }
                }
                $cicleCount++;
            }

            if ($notify) {
                foreach ($notify as $data) {
                    if ($ntf = Notifications::create($data['user'], $data['title'], $data['message'], Notifications::TYPE_INFO, true)) {
                        $dataNotification = (Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
                        Notifications::publish('getNewNotification', ['user_id' => $data['user']], $dataNotification);
                    }
                }
            }
        } catch (\Throwable $e) {
            if ($debug) {
                echo "error: " . VarDumper::dumpAsString($e);
            }
            \Yii::error($e, 'ReceiveEmailsJob:execute');
        }
        if ($debug) {
            echo "cicleCount:" . $cicleCount . " countTotal:" . $countTotal . PHP_EOL;
        }
        return true;
    }

    private function getEmailsForReceivedMessages(): array
    {
//        $mailsUpp = UserProjectParams::find()->select(['DISTINCT(upp_email)'])->andWhere(['!=', 'upp_email', ''])->column();
        $mailsUpp = UserProjectParams::find()->select('el_email')->distinct()->joinWith('emailList', false, 'INNER JOIN')->column();
//        $mailsDep = DepartmentEmailProject::find()->select(['DISTINCT(dep_email)'])->andWhere(['!=', 'dep_email', ''])->column();
        $mailsDep = DepartmentEmailProject::find()->select(['el_email'])->distinct()->joinWith('emailList', false, 'INNER JOIN')->column();
        $list = array_merge($mailsUpp, $mailsDep);
        return $list;
    }
}
