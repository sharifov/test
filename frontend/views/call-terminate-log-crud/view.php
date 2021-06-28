<?php

use common\models\Call;
use sales\model\callTerminateLog\entity\CallTerminateLog;
use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\callTerminateLog\entity\CallTerminateLog */

$this->title = $model->ctl_id;
$this->params['breadcrumbs'][] = ['label' => 'Call Terminate Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="call-terminate-log-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->ctl_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->ctl_id], [
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
                'ctl_id',
                'ctl_call_phone_number',
                [
                    'attribute' => 'ctl_call_status_id',
                    'value' => static function (CallTerminateLog $model) {
                        return Call::getStatusNameById($model->ctl_call_status_id);
                    },
                ],
                'ctl_project_id:projectName',
                'ctl_created_dt:byUserDateTime',
            ],
        ]) ?>

    </div>

</div>
