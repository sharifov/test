<?php

/**
 * @var Email $email
 * @var $this yii\web\View
 */

use yii\widgets\DetailView;
use src\entities\email\Email;

?>
<?= DetailView::widget([
      'model' => $email,
      'attributes' => [
          'e_id',
          'reply.e_id',
          'leads:leads',
          'cases:cases',
          'e_project_id:projectName',
          'emailFrom:email',
          'emalFromName',
          'emailTo:email',
          'emailToName',
          'e_email_cc:email', //TODO: fill properly
          'e_email_bc:email', //TODO: fill properly
          'emailSubject',
          'emailData:array',
          'typeName',
          'templateTypeId',
          'languageId',
          'communicationId',
          'e_is_deleted',
          'emailLog.el_is_new',
          'params.ep_priority',
          'statusName',
          'statusDoneDt',
          'emailLog.el_read_dt',
          'emailLog.el_error_message',
          'e_created_user_id:username',
          'e_updated_user_id:username',
          'e_created_dt',
          'e_updated_dt',
          'messageId',
          'emailLog.el_ref_message_id:ntext',
          'emailLog.el_inbox_created_dt',
          'emailLog.el_inbox_email_id:email',
      ],
  ]);
