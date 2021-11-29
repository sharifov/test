<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\SmsTemplateType */

$this->title = $model->stp_origin_name;
$this->params['breadcrumbs'][] = ['label' => 'Sms Template Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sms-template-type-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->stp_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->stp_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <div class="row">
        <div class="col-6">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'stp_id',
            'stp_key',
            'stp_origin_name',
            'stp_name',
            'stp_hidden:boolean',
            /*[
                'attribute' => 'stp_dep_id',
                'value' => static function (\common\models\SmsTemplateType $model) {
                    return $model->stpDep ? $model->stpDep->dep_name : '-';
                },
            ],*/
            [
                'label' => 'Departments',
                'value' => static function (\common\models\SmsTemplateType $model) {
                    $valueArr = [];

                    foreach ($model->smsTemplateTypeDepartments as $item) {
                        $valueArr[] = Html::tag('div', Html::encode($item->sttdDepartment->dep_name), ['class' => 'label label-default']);
                    }

                    return $valueArr ?  implode(' ', $valueArr) : '-';
                },
                'format' => 'raw'
            ],
            [
                'label' => 'Projects',
                'value' => static function (\common\models\SmsTemplateType $model) {
                    $valueArr = [];

                    foreach ($model->smsTemplateTypeProjects as $item) {
                        $valueArr[] = Html::tag('div', Html::encode($item->sttpProject->name), ['class' => 'label label-info']);
                    }

                    return $valueArr ?  implode(' ', $valueArr) : '-';
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'stp_updated_user_id',
                'value' => static function (\common\models\SmsTemplateType $model) {
                    return ($model->stpUpdatedUser ? '<i class="fa fa-user"></i> ' . Html::encode($model->stpUpdatedUser->username) : $model->stp_updated_user_id);
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'stp_updated_dt',
                'value' => static function (\common\models\SmsTemplateType $model) {
                    return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->stp_updated_dt));
                },
                'format' => 'raw'
            ],

            [
                'attribute' => 'stp_created_user_id',
                'value' => static function (\common\models\SmsTemplateType $model) {
                    return  ($model->stpCreatedUser ? '<i class="fa fa-user"></i> ' . Html::encode($model->stpCreatedUser->username) : $model->stp_created_user_id);
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'stp_created_dt',
                'value' => static function (\common\models\SmsTemplateType $model) {
                    return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->stp_created_dt));
                },
                'format' => 'raw'
            ],
        ],
    ]) ?>
        </div>
    </div>
</div>
