<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Call */

$this->title = 'Call Id: ' . $model->c_id . ' ('.$model->c_from.' ... '.$model->c_to.')';
$this->params['breadcrumbs'][] = ['label' => 'Calls', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="call-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->c_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->c_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>


    <div class="col-md-6">
    <?php if($model->c_recording_url):?>
        <audio controls="controls" style="width: 100%;"><source src="<?=$model->c_recording_url?>" type="audio/mpeg"></audio>
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'c_id',
                'c_call_sid',
                'c_account_sid',
                [
                    'attribute' => 'c_call_type_id',
                    'value' => function (\common\models\Call $model) {
                        return $model->getCallTypeName();
                    },
                ],
                'c_from',
                'c_to',
                'c_sip',
                'c_call_status',
                'c_api_version',
                'c_direction',
                'c_forwarded_from',
                'c_caller_name',
                'c_parent_call_sid',
                'c_call_duration',



            ],
        ]) ?>
    <?php endif;?>

    </div>
    <div class="col-md-6">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'c_sip_response_code',
            'c_recording_url:url',
            'c_recording_sid',
            'c_recording_duration',
            'c_timestamp',
            //'c_uri',
            [
                'attribute' => 'c_uri',
                'value' => function (\common\models\Call $model) {
                    return $model->c_uri ? Html::a('Link', 'https://api.twilio.com'.$model->c_uri, ['target' => '_blank']) : '-';
                },
                'format' => 'raw'
            ],
            'c_sequence_number',
            'c_lead_id',

            [
                'attribute' => 'c_created_user_id',
                'value' => function (\common\models\Call $model) {
                    return  $model->cCreatedUser ? '<i class="fa fa-user"></i> ' . Html::encode($model->cCreatedUser->username) : $model->c_created_user_id;
                },
                'format' => 'raw'
            ],
            //'c_created_dt',
            [
                'attribute' => 'c_created_dt',
                'value' => function (\common\models\Call $model) {
                    return $model->c_created_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->c_created_dt)) : '-';
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'c_updated_dt',
                'value' => function (\common\models\Call $model) {
                    return $model->c_updated_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->c_updated_dt)) : '-';
                },
                'format' => 'raw'
            ],


            'c_com_call_id',

            //'c_project_id',
            [
                'attribute' => 'c_project_id',
                'value' => function (\common\models\Call $model) {
                    return $model->cProject ? $model->cProject->name : '-';
                },
                'filter' => \common\models\Project::getList()
            ],
            'c_error_message',
            'c_is_new:boolean',
            'c_is_deleted:boolean',
            [
                'attribute' => 'c_price',
                'value' => function (\common\models\Call $model) {
                    return $model->c_price ? Yii::$app->formatter->asCurrency($model->c_price) : '-';
                },
            ],
        ],
    ]) ?>
    </div>

</div>
