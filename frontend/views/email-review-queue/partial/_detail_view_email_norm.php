<?php

/**
 * @var Email $email
 * @var $this yii\web\View
 */

use yii\widgets\DetailView;
use src\entities\email\Email;
use src\entities\email\helpers\EmailType;
use src\entities\email\helpers\EmailStatus;

?>
<?= DetailView::widget([
      'model' => $email,
      'attributes' => [
          'e_id',
          'reply.e_id',
          'leads:leads',
          'cases:cases',
          'e_project_id:projectName',
          [
              'attribute' => 'e_email_from',
              'value' => static function (Email $model) {
                  return $model->emailFrom;
              },
              'format' => 'email'
          ],
          'contactFrom.ea_name',
          [
              'attribute' => 'e_email_to',
              'value' => static function (Email $model) {
                  return $model->emailTo;
              },
              'format' => 'email'
          ],
          'contactTo.ea_name',
          'e_email_cc:email', //TODO: fill properly
          'e_email_bc:email', //TODO: fill properly
          'emailBody.embd_email_subject',
          'emailBody.embd_email_data:ntext',
          [
              'attribute' => 'e_type_id',
              'value' => static function (Email $model) {
                return EmailType::getName($model->e_type_id);
              },
          ],
          'params.ep_template_type_id',
          'params.ep_language_id',
          'emailLog.el_communication_id',
          'e_is_deleted',
          'emailLog.el_is_new',
          'params.ep_priority',
          [
              'attribute' => 'e_status_id',
              'value' => static function (Email $model) {
                return EmailStatus::getName($model->e_status_id);
              },
          ],
          'emailLog.el_status_done_dt',
          'emailLog.el_read_dt',
          'emailLog.el_error_message',
          'e_created_user_id:username',
          'e_created_dt',
          'emailLog.el_message_id',
          'emailLog.el_ref_message_id:ntext',
          'emailLog.el_inbox_created_dt',
          'emailLog.el_inbox_email_id:email',
      ],
  ]);
