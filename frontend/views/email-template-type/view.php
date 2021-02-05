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
    <div class="row">
        <div class="col-6">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'etp_id',
                    'etp_key',
                    'etp_origin_name',
                    'etp_name',
                    'etp_hidden:boolean',
                    [
                        'attribute' => 'etp_dep_id',
                        'value' => static function (\common\models\EmailTemplateType $model) {
                            return $model->etpDep ? $model->etpDep->dep_name : '-';
                        },
                    ],
                    'etp_ignore_unsubscribe:boolean',
                    [
                        'attribute' => 'etp_updated_user_id',
                        'value' => static function (\common\models\EmailTemplateType $model) {
                            return ($model->etpUpdatedUser ? '<i class="fa fa-user"></i> ' . Html::encode($model->etpUpdatedUser->username) : $model->etp_updated_user_id);
                        },
                        'format' => 'raw'
                    ],
                    [
                        'attribute' => 'etp_updated_dt',
                        'value' => static function (\common\models\EmailTemplateType $model) {
                            return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->etp_updated_dt));
                        },
                        'format' => 'raw'
                    ],

                    [
                        'attribute' => 'etp_created_user_id',
                        'value' => static function (\common\models\EmailTemplateType $model) {
                            return ($model->etpCreatedUser ? '<i class="fa fa-user"></i> ' . Html::encode($model->etpCreatedUser->username) : $model->etp_created_user_id);
                        },
                        'format' => 'raw'
                    ],
                    [
                        'attribute' => 'etp_created_dt',
                        'value' => static function (\common\models\EmailTemplateType $model) {
                            return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->etp_created_dt));
                        },
                        'format' => 'raw'
                    ],
                ],
            ]) ?>
        </div>
        <div class="col-6">
            <?php
            echo Html::label('<h2>Params:</h2>');
            echo \kdn\yii2\JsonEditor::widget(
                [
                    'clientOptions' => [
                        'modes' => ['view'], // all available modes 'code', 'form', 'preview', 'text', 'tree', 'view'
                        'mode' => 'view', // default mode
                    ],
                    //'collapseAll' => ['view'], // collapse all fields in "view" mode
                    'containerOptions' => ['class' => 'well'], // HTML options for JSON editor container tag
                    'expandAll' => ['view'], // expand all fields in "tree" and "form" modes
                    'name' => 'editor', // hidden input name
                    'options' => ['id' => 'data'], // HTML options for hidden input
                    //'value' => '{"foo": "bar"}', // JSON which should be shown in editor
                    'decodedValue' => $model->etp_params_json
                ]
            );
            ?>
        </div>
    </div>
</div>
