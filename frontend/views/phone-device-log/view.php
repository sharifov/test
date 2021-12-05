<?php

use sales\model\voip\phoneDevice\PhoneDeviceLog;
use yii\bootstrap4\Html;
use yii\helpers\VarDumper;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\voip\phoneDevice\PhoneDeviceLog */

$this->title = $model->pdl_id;
$this->params['breadcrumbs'][] = ['label' => 'Phone Device Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="phone-device-log-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-8">

        <p>
            <?= Html::a('Delete', ['delete', 'id' => $model->pdl_id], [
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
                'pdl_id',
                'pdl_user_id:userNameWithId',
                'pdl_device_id',
                'pdl_level:phoneDeviceLogLevel',
                'pdl_message',
                [
                    'attribute' => 'pdl_error',
                    'value' => static function (PhoneDeviceLog $log) {
                        if (!$log->pdl_error) {
                            return null;
                        }
                        return '<pre><small>' . VarDumper::dumpAsString($log->pdl_error, 10, false) . '</small></pre>';
                    },
                    'format' => 'raw',
                ],
                'pdl_stacktrace',
                'pdl_timestamp_dt',
                'pdl_created_dt:byUserDatetimeWithSeconds',
            ],
        ]) ?>

    </div>

</div>
