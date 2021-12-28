<?php

use sales\model\smsSubscribe\entity\SmsSubscribe;
use sales\model\smsSubscribe\entity\SmsSubscribeStatus;
use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\smsSubscribe\entity\SmsSubscribe */

$this->title = $model->ss_id;
$this->params['breadcrumbs'][] = ['label' => 'Sms Subscribes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sms-subscribe-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->ss_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->ss_id], [
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
                'ss_id',
                'ss_cpl_id',
                'ss_sms_id',
                'ss_project_id:projectName',
                [
                    'attribute' => 'ss_status_id',
                    'value' => static function (SmsSubscribe $model) {
                        return SmsSubscribeStatus::getStatusName($model->ss_status_id);
                    },
                    'format' => 'raw',
                ],
                'ss_created_dt:byUserDateTime',
                'ss_updated_dt:byUserDateTime',
                'ss_deadline_dt:byUserDateTime',
                'ss_created_user_id:username',
                'ss_updated_user_id:username',
            ],
        ]) ?>

    </div>

</div>
