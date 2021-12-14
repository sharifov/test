<?php

/**
 * @var EmailReviewQueue $model
 * @var Email $email
 * @var \sales\forms\emailReviewQueue\EmailReviewQueueForm $previewForm
 * @var $this yii\web\View
 * @var $displayActionBtns bool
 */

use common\models\Email;
use modules\email\src\abac\EmailAbacObject;
use sales\helpers\email\MaskEmailHelper;
use sales\model\emailReviewQueue\entity\EmailReviewQueue;
use sales\model\emailReviewQueue\entity\EmailReviewQueueStatus;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

$this->title = $model->erqEmail->e_email_subject . ' (' . $model->erq_email_id . ')';
$this->params['breadcrumbs'][] = ['label' => 'Email Review Queues', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

/** @abac null, EmailAbacObject::OBJ_REVIEW_EMAIL, EmailAbacObject::ACTION_MANAGE_REVIEW_FORM, Restrict access view review form on review single email page */
$canManageForm = Yii::$app->abac->can(null, EmailAbacObject::OBJ_REVIEW_EMAIL, EmailAbacObject::ACTION_MANAGE_REVIEW_FORM);
/** @abac null, EmailAbacObject::OBJ_REVIEW_EMAIL, EmailAbacObject::ACTION_VIEW_REVIEW_DATA, Restrict access view review data */
$canViewForReviewData = Yii::$app->abac->can(null, EmailAbacObject::OBJ_REVIEW_EMAIL, EmailAbacObject::ACTION_VIEW_REVIEW_DATA);
/** @abac null, EmailAbacObject::OBJ_REVIEW_EMAIL, EmailAbacObject::ACTION_VIEW_REVIEW_EMAIL_DATA, Restrict access view email data */
$canViewForReviewEmailData = Yii::$app->abac->can(null, EmailAbacObject::OBJ_REVIEW_EMAIL, EmailAbacObject::ACTION_VIEW_REVIEW_EMAIL_DATA);
?>
<div class="row">
  <div class="col-md-12">
    <h1><i class="fa fa-envelope"></i> Email (<?= $email->e_id ?>)</h1>
    <div>

    </div>
  </div>
</div>
<div class="row">

  <?php if ($canManageForm) : ?>
  <div class="col-md-7">
        <?= $this->render('partial/_preview_email', [
          'previewForm' => $previewForm,
          'displayActionBtns' => $displayActionBtns
      ]) ?>
  </div>
  <?php endif; ?>
  <div class="col-md-5">
      <?php if ($canViewForReviewData) : ?>
          <div>
            <h4>Email Review Queue Data</h4>
          </div>
            <?= DetailView::widget([
              'model' => $model,
              'attributes' => [
                  'erq_id',
                  [
                      'attribute' => 'erq_email_id',
                      'value' => static function (EmailReviewQueue $model) {
                          return Html::a(
                              '<i class="fa fa-link"></i> ' . $model->erq_email_id,
                              ['/email/view', 'id' => $model->erq_email_id],
                              ['target' => '_blank']
                          );
                      },
                      'format' => 'raw'
                  ],
                  'erq_project_id:projectName',
                  'erq_department_id:department',
                  'erq_owner_id:userName',
                  [
                      'attribute' => 'erq_status_id',
                      'value' => static function (EmailReviewQueue $model) {
                          return EmailReviewQueueStatus::asFormat($model->erq_status_id);
                      },
                      'format' => 'raw',
                  ],
                  'erq_user_reviewer_id:userName',
                  'erq_created_dt:byUserDateTime',
                  'erq_updated_dt:byUserDateTime',
              ],
          ]) ?>
      <?php endif; ?>
      <?php if ($canViewForReviewEmailData) : ?>
      <div>
        <h4>Email Data</h4>
      </div>
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
                    if ($model->e_type_id == $model::TYPE_INBOX) {
                        return MaskEmailHelper::masking($model->e_email_from);
                    }
                      return $model->e_email_from;
                  },
                  'format' => 'email'
              ],
              'e_email_from_name',
              [
                  'attribute' => 'e_email_to',
                  'value' => static function (Email $model) {
                    if ($model->e_type_id == $model::TYPE_OUTBOX) {
                        return MaskEmailHelper::masking($model->e_email_to);
                    }
                      return $model->e_email_to;
                  },
                  'format' => 'email'
              ],
              'e_email_to_name',
              'e_email_cc:email',
              'e_email_bc:email',
              'e_email_subject:email',
              'e_attach',
              'e_email_data:ntext',
              [
                  'attribute' => 'e_type_id',
                  'value' => static function (Email $model) {
                      return $model->getTypeName();
                  }
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
                      return $model->getStatusName();
                  }
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
      ]) ?>
      <?php endif; ?>
  </div>
</div>