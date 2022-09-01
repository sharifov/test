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
          'emailsCC:list',
          'emailsBCC:list',
          'emailSubject',
          'emailData:array',
          'typeName',
          'templateTypeId',
          'languageId',
          'communicationId',
          'e_is_deleted:boolean',
          'emailLog.el_is_new:boolean',
          'params.ep_priority',
          'statusName',
          'statusDoneDt',
          'emailLog.el_read_dt',
          'errorMessage',
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
