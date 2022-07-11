<?php

/**
* @var Email $email
* @var $this yii\web\View
*/

use yii\widgets\DetailView;
use common\models\Email;
use src\entities\email\helpers\EmailType;
use src\entities\email\helpers\EmailStatus;

?>
<?= DetailView::widget([
      'model' => $email,
      'attributes' => [
          'e_id',
          'e_reply_id',
          'eLead:lead',
          'eCase:case',
          'e_project_id:projectName',
          [
              'attribute' => 'e_email_from',
              'value' => static function (Email $model) {
                  return $model->emailFrom;
              },
              'format' => 'email'
          ],
          'e_email_from_name',
          [
              'attribute' => 'e_email_to',
              'value' => static function (Email $model) {
                  return $model->emailTo;
              },
              'format' => 'email'
          ],
          'e_email_to_name',
          'e_email_cc:email',
          'e_email_bc:email',
          'e_email_subject:email',
          'e_email_data:ntext',
          [
              'attribute' => 'e_type_id',
              'value' => static function (Email $model) {
                return EmailType::getName($model->e_type_id);
              },
          ],
          'e_template_type_id',
          'e_language_id',
          'e_communication_id',
          'e_is_deleted',
          'e_is_new',
          'e_delay',
          'e_priority',
          [
              'attribute' => 'e_status_id',
              'value' => static function (Email $model) {
                return EmailStatus::getName($model->e_status_id);
              },
          ],
          'e_status_done_dt',
          'e_read_dt',
          'e_error_message',
          'e_created_user_id:username',
          'e_updated_user_id:username',
          'e_created_dt',
          'e_updated_dt',
          'e_message_id',
          'e_ref_message_id:ntext',
          'e_inbox_created_dt',
          'e_inbox_email_id:email',
      ],
  ]);
