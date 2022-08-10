<?php

use src\model\emailReviewQueue\entity\EmailReviewQueue;
use src\model\emailReviewQueue\entity\EmailReviewQueueStatus;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model src\model\emailReviewQueue\entity\EmailReviewQueue */

$this->title = $model->emailSubject . ' (' . $model->erq_email_id . ')';
$this->params['breadcrumbs'][] = ['label' => 'Email Review Queues', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="email-review-queue-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->erq_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->erq_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'erq_id',
            [
                'attribute' => 'erq_email_id',
                'value' => static function (EmailReviewQueue $model) {
                    return Html::a('<i class="fa fa-link"></i> ' . $model->erq_email_id, ['/email/view', 'id' => $model->erq_email_id], ['target' => '_blank']);
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

</div>
