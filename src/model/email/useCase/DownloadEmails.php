<?php

namespace src\model\email\useCase;

use common\models\DepartmentEmailProject;
use common\models\Email;
use common\models\Notifications;
use common\models\UserProjectParams;
use frontend\widgets\notification\NotificationMessage;
use src\dto\email\EmailDTO;
use src\services\email\EmailMainService;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use src\repositories\email\EmailRepositoryFactory;

/**
 * Class DownloadMessages
 *
 * @property EmailMainService $emailService
 */
class DownloadEmails
{
    private EmailMainService $emailService;

    public function __construct(
        EmailMainService $emailService
    ) {
        $this->emailService = $emailService;
    }

    public function download(bool $debug, int $limit): void
    {
        $newMessages = (bool)Yii::$app->redis->get('new_email_message_received');
        if (!$newMessages) {
            return;
        }
        Yii::$app->redis->set('new_email_message_received', false);

        $accessEmailRequest = true;
        $cycleCount = 1;
        $countTotal = 0;

        $lastEmailId = EmailRepositoryFactory::getRepository()->getLastInboxId() ?? 1;

        $filter = [
            'last_id' => $lastEmailId,
            'limit' => $limit,
            'email_list' => Json::encode(['list' => $this->getEmailsForReceivedMessages()])
        ];

        try {
            $notify = [];

            while ($accessEmailRequest && $cycleCount < 100) {
                if ($debug) {
                    echo "Cycle #" . $cycleCount . PHP_EOL;
                }

                $res = Yii::$app->comms->mailGetMessages($filter);

                if (isset($res['error']) && $res['error']) {
                    $response['error'] = 'Error mailGetMessages';
                    $response['error_code'] = 13;
                    \Yii::error(VarDumper::dumpAsString($res['error']), 'DownloadEmails:getMessages');
                    $cycleCount--;
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
                    $cycleCount--;
                    $accessEmailRequest = false;
                    if ($debug) {
                        echo 'Cycle finish' . PHP_EOL;
                    }
                }
                $cycleCount++;
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
            \Yii::error($e, 'DownloadEmails:throwable');
        }
        if ($debug) {
            echo "cycleCount:" . $cycleCount . " countTotal:" . $countTotal . PHP_EOL;
        }
    }

    private function getEmailsForReceivedMessages(): array
    {
        $mailsUpp = UserProjectParams::find()->select('el_email')->distinct()->joinWith('emailList', false, 'INNER JOIN')->column();
        $mailsDep = DepartmentEmailProject::find()->select(['el_email'])->distinct()->joinWith('emailList', false, 'INNER JOIN')->column();
        return array_merge($mailsUpp, $mailsDep);
    }
}
