<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Call */

$this->title = 'Call Id: ' . $model->c_id;
//$this->params['breadcrumbs'][] = ['label' => 'Calls', 'url' => ['list']];
//$this->params['breadcrumbs'][] = $this->title;
//\yii\web\YiiAsset::register($this);
?>
<div class="ajax-call-view">

    <h2><i class="fa fa-phone-square"></i> <?= Html::encode($this->title) ?> <?=$model->getStatusLabel()?></h2>


    <div class="col-md-6">
        <?php if($model->c_recording_url):?>
            <audio controls="controls" controlsList="nodownload" style="width: 100%;"><source src="<?=$model->c_recording_url?>" type="audio/mpeg"></>
        <?php endif;?>

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                [
                    'attribute' => 'c_id',
                    'value' => static function (\common\models\Call $model) {
                        return Html::a($model->c_id, ['call/view2', 'id' => $model->c_id], ['target' => '_blank', 'data-pjax' => 0]);
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'c_project_id',
                    'value' => static function (\common\models\Call $model) {
                        return $model->cProject ? $model->cProject->name : '-';
                    },
                ],
                'c_call_sid',
                'c_parent_call_sid',
                //'c_call_type_id',
                [
                    'attribute' => 'c_call_type_id',
                    'value' => static function (\common\models\Call $model) {
                        return $model->getCallTypeName();
                    },
                ],
                [
                    'attribute' => 'c_client_id',
                    'value' => static function (\common\models\Call $model) {
                        return  $model->c_client_id ?: '-';
                    },
                ],
                'c_from',
                'c_to',
                'c_call_status',
                //'c_forwarded_from',

                //'c_parent_call_sid',

            ],
        ]) ?>
    </div>

    <div class="col-md-6">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'c_caller_name',
            'c_call_duration',
            //'c_recording_url:url',
            'c_recording_duration',
            //'c_sequence_number',
            'c_lead_id',
            //'c_created_user_id',
            [
                'attribute' => 'c_created_user_id',
                'value' => static function (\common\models\Call $model) {
                    return  $model->cCreatedUser ? '<i class="fa fa-user"></i> ' . Html::encode($model->cCreatedUser->username) : $model->c_created_user_id;
                },
                'format' => 'raw'
            ],
            //'c_created_dt',
            [
                'attribute' => 'c_created_dt',
                'value' => static function (\common\models\Call $model) {
                    return $model->c_created_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->c_created_dt)) : '-';
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'c_updated_dt',
                'value' => static function (\common\models\Call $model) {
                    return $model->c_updated_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->c_updated_dt)) : '-';
                },
                'format' => 'raw'
            ],
            //'c_com_call_id',
            //'c_updated_dt',
            //'c_project_id',
            //'c_error_message',
            'c_is_new:boolean',
            //'c_is_deleted',
        ],
    ]) ?>
    </div>

</div>
