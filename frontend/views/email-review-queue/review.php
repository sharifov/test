<?php

/**
 * @var EmailReviewQueue $model
 * @var Email $email
 * @var \src\forms\emailReviewQueue\EmailReviewQueueForm $previewForm
 * @var $this yii\web\View
 * @var $displayActionBtns bool
 */

use common\models\Email;
use frontend\helpers\JsonHelper;
use modules\email\src\abac\EmailAbacObject;
use modules\fileStorage\src\entity\fileStorage\FileStorageQuery;
use modules\fileStorage\src\services\url\QueryParams;
use modules\fileStorage\src\services\url\UrlGenerator;
use src\model\emailReviewQueue\entity\EmailReviewQueue;
use src\model\emailReviewQueue\entity\EmailReviewQueueStatus;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;
use modules\fileStorage\src\services\url\FileInfo;

$this->title = $model->emailSubject . ' (' . $model->erq_email_id . ')';
$this->params['breadcrumbs'][] = ['label' => 'Email Review Queues', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

/** @abac null, EmailAbacObject::OBJ_REVIEW_EMAIL, EmailAbacObject::ACTION_MANAGE_REVIEW_FORM, Restrict access view review form on review single email page */
$canManageForm = Yii::$app->abac->can(null, EmailAbacObject::OBJ_REVIEW_EMAIL, EmailAbacObject::ACTION_MANAGE_REVIEW_FORM);
/** @abac null, EmailAbacObject::OBJ_REVIEW_EMAIL, EmailAbacObject::ACTION_VIEW_REVIEW_DATA, Restrict access view review data */
$canViewForReviewData = Yii::$app->abac->can(null, EmailAbacObject::OBJ_REVIEW_EMAIL, EmailAbacObject::ACTION_VIEW_REVIEW_DATA);
/** @abac null, EmailAbacObject::OBJ_REVIEW_EMAIL, EmailAbacObject::ACTION_VIEW_REVIEW_EMAIL_DATA, Restrict access view email data */
$canViewForReviewEmailData = Yii::$app->abac->can(null, EmailAbacObject::OBJ_REVIEW_EMAIL, EmailAbacObject::ACTION_VIEW_REVIEW_EMAIL_DATA);
/** @abac null, EmailAbacObject::OBJ_REVIEW_EMAIL, EmailAbacObject::ACTION_VIEW_REVIEW_EMAIL_ATTACHED_FILES, Restrict access view email attached files */
$canViewForReviewEmailAttachedFiles = Yii::$app->abac->can(null, EmailAbacObject::OBJ_REVIEW_EMAIL, EmailAbacObject::ACTION_VIEW_REVIEW_EMAIL_ATTACHED_FILES);
$files = JsonHelper::decode($email->e_email_data ?? $email->emailData);
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
          'displayActionBtns' => $displayActionBtns,
          'files' => $files['files'] ?? []
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
                          $url = ($model->erq_email_is_norm) ? '/email-normalized/view' : '/email/view';
                          return Html::a(
                              '<i class="fa fa-link"></i> ' . $model->erq_email_id,
                              [$url, 'id' => $model->erq_email_id],
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
      <?php if ($canViewForReviewEmailAttachedFiles) : ?>
        <div>
            <h4>Attached files</h4>
            <?php
                $urlGenerator = Yii::createObject(UrlGenerator::class);
                $arrayDataProvider = new \yii\data\ArrayDataProvider();
                $arrayDataProvider->setModels($files['files'] ?? []);
            ?>
            <?= GridView::widget([
                'dataProvider' => $arrayDataProvider,
                'columns' => [
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'buttons' => [
                            'view' => static function ($url, $model, $key) use ($urlGenerator, $email) {
                                $file = FileStorageQuery::getByUid($model['uid'] ?? '');
                                if ($file) {
                                    $queryParams = QueryParams::byEmpty();
                                    if ($email->e_lead_id ?? $email->lead) {
                                        $queryParams = QueryParams::byLead();
                                    } else if ($email->e_case_id ?? $email->case) {
                                        $queryParams = QueryParams::byCase();
                                    }
                                    $linkView = $urlGenerator->generate(FileInfo::byFileStorage($file, $queryParams));
                                    return Html::a('<i class="fa fa-eye"></i>', $linkView, ['target' => '_blank']);
                                }
                                return '';
                            }
                        ],
                        'template' => '{view}',
                        'buttonOptions' => [
                            'target' => '_blank'
                        ]
                    ],
                    'name',
                    'title',
                    'uid',
                    'type_id'
                ]
            ]) ?>
        </div>
      <?php endif; ?>
      <?php if ($canViewForReviewEmailData) : ?>
      <div>
        <h4>Email Data</h4>
      </div>
      <div style="overflow: auto; ">
            <?= $this->render('partial/_detail_view_email' . (($model->erq_email_is_norm) ? '_norm' : ''), ['email' => $email]) ?>
      </div>
      <?php endif; ?>
  </div>
</div>
