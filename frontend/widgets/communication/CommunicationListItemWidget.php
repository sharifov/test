<?php

namespace frontend\widgets\communication;

use yii\base\Widget;
use src\repositories\email\EmailRepositoryFactory;
use src\entities\email\helpers\EmailType;
use src\entities\email\helpers\EmailStatus;
use src\entities\email\EmailInterface;
use Yii;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use src\entities\email\EmailBody;
use modules\fileStorage\src\widgets\FileStorageEmailSentListWidget;
use modules\email\src\abac\dto\EmailAbacDto;
use modules\email\src\abac\EmailAbacObject;

class CommunicationListItemWidget extends Widget
{
    public $type;

    public $id;

    public $disableMasking = false;

    public function init()
    {
        parent::init();

        if ($this->type === null) {
            throw new \InvalidArgumentException('type property must be set');
        }
        if ($this->id === null) {
            throw new \InvalidArgumentException('id property must be set');
        }
    }

    private function getEmailIcon(EmailInterface $mail): string
    {
        $statusTitle = 'SENT - ComID: ' . $mail->communicationId;
        $statusClass = 'chat__status--sent fa fa-circle';

        if (EmailStatus::isDone($mail->e_status_id)) {
            $doneDate = !empty($mail->statusDoneDt) ? $mail->statusDoneDt : $mail->e_updated_dt;
            $statusTitle = 'DONE - ' . Yii::$app->formatter->asDatetime(strtotime($doneDate));
            $statusClass = 'chat__status--success fa fa-circle';
        } elseif (EmailStatus::isErrorGroup($mail->e_status_id)) {
            $statusTitle = 'ERROR - ' . $mail->errorMessage;
            $statusClass = 'chat__status--error fa fa-circle';
        } elseif (EmailStatus::isReview($mail->e_status_id)) {
            $statusTitle = 'Email on review';
            $statusClass = 'warning fas fa-exclamation-triangle';
        }

        return Html::tag('i', null, [
            'class' => 'chat__status ' . $statusClass,
            'data-toggle' => 'tooltip',
            'title' => $statusTitle,
            'data-placement' => 'right',
            'data-original-title' => $statusTitle
        ]);
    }

    private function renderEmailDetailButton($data): string
    {
        return Html::a('<i class="fa fa-search-plus"></i> Details', '#', [
            'class' => 'chat__details',
            'data-id' => $data['id'],
            'data-subject' => '<b>Subject: </b>' . $data['subject'],
            'data-from' => '<b>Email from:</b> ' . $data['createdUser'] . ' (' . $data['fromName'] . ' &lt;' . $data['from'] . '&gt;)',
            'data-to' => '<b>Email To:</b> ' . $data['toName'] . ' &lt;' . $data['to'] . '&gt;',
            'data-date' => '<b>Date:</b> ' . $data['createdDate'],
            'data-files' => $this->renderFilesData($data['emailData'])
        ]);
    }

    private function renderFilesData($files): string
    {
        if (!isset($files['files']) || !is_array($files['files'])) {
            return '';
        }
        return FileStorageEmailSentListWidget::create($files['files']);
    }

    private function renderItem(string $type, $id)
    {
        if ($type == 'email') {
            try {
                $mail = EmailRepositoryFactory::getRepository()->find($id);
                if (EmailType::isDraftOrOutbox($mail->e_type_id)) {
                    $createdUser = Html::encode(($mail->createdUser->username ?? '-')) . ', ';
                    $unsubscribedEmails = array_column($mail->project ? $mail->project->emailUnsubscribes : [], 'eu_email');
                    $unsubscribed = in_array($mail->getEmailTo(false), $unsubscribedEmails);
                }

                $data = [
                    'class' => EmailType::isInbox($mail->e_type_id) ? 'client' : 'system',
                    'icon' => $this->getEmailIcon($mail),
                    'from' => Html::encode($mail->getEmailFrom(!$this->disableMasking)),
                    'fromName' => Html::encode($mail->emailFromName),
                    'to' => Html::encode($mail->getEmailTo(!$this->disableMasking)),
                    'toName' => Html::encode($mail->emailToName),
                    'language' => $mail->languageId ? '(' . $mail->languageId . ')' : '',
                    'createdDate' => Yii::$app->formatter->asDatetime(strtotime($mail->e_created_dt)),
                    'createdUser' => $createdUser ?? '',
                    'shortSubject' => wordwrap(Html::encode($mail->emailSubject), 60, '<br />', true),
                    'subject' => Html::encode($mail->emailSubject),
                    'body' => StringHelper::truncate(EmailBody::stripHtmlTags($mail->getEmailBodyHtml()), 300, '...', null, true),
                    'id' => $mail->e_id,
                    'emailData' => $mail->emailData,
                    'unsubscribed' => $unsubscribed ?? false,
                ];

                if (Yii::$app->abac->can(new EmailAbacDto($mail), EmailAbacObject::ACT_VIEW, EmailAbacObject::ACTION_ACCESS)) {
                    $data['footer'] = '<div class="chat__message-footer">' . $this->renderEmailDetailButton($data) . '</div>';
                }

                return $this->render('email_item', ['data' => $data]);
            } catch (\Throwable $e) {
                return '';
            }
        }
    }

    /**
     * @return string
     */
    public function run(): string
    {
        return $this->renderItem($this->type, $this->id);
    }
}
