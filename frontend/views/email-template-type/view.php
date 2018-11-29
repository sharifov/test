<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\EmailTemplateType */

$this->title = $model->etp_origin_name;
$this->params['breadcrumbs'][] = ['label' => 'Email Template Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="email-template-type-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->etp_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->etp_id], [
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
            'etp_id',
            'etp_key',
            'etp_origin_name',
            'etp_name',
            'etp_hidden:boolean',
            [
                'attribute' => 'etp_updated_user_id',
                'value' => function (\common\models\EmailTemplateType $model) {
                    return ($model->etpUpdatedUser ? '<i class="fa fa-user"></i> ' .Html::encode($model->etpUpdatedUser->username) : $model->etp_updated_user_id);
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'etp_updated_dt',
                'value' => function (\common\models\EmailTemplateType $model) {
                    return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->etp_updated_dt));
                },
                'format' => 'raw'
            ],

            [
                'attribute' => 'etp_created_user_id',
                'value' => function (\common\models\EmailTemplateType $model) {
                    return  ($model->etpCreatedUser ? '<i class="fa fa-user"></i> ' .Html::encode($model->etpCreatedUser->username) : $model->etp_created_user_id);
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'etp_created_dt',
                'value' => function (\common\models\EmailTemplateType $model) {
                    return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->etp_created_dt));
                },
                'format' => 'raw'
            ],
        ],
    ]) ?>

</div>
