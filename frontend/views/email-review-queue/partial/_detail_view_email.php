<?php

/**
* @var Email $email
* @var $this yii\web\View
*/

use yii\widgets\DetailView;
use common\models\Email;

?>
<?= DetailView::widget([
      'model' => $email,
      'attributes' => [
          'e_id',
          'e_reply_id',
          'lead:lead',
          'case:case',
          'e_project_id:projectName',
          'emailFrom:email',
          'emalFromName',
          'emailTo:email',
          'emailToName',
          'e_email_cc:email',
          'e_email_bc:email',
          'e_email_subject:email',
          'e_email_data:ntext',
          'typeName',
          'e_template_type_id',
          'e_language_id',
          'e_communication_id',
          'e_is_deleted',
          'e_is_new',
          'e_delay',
          'e_priority',
          'statusName',
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
