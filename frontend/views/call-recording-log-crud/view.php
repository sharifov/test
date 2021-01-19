<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\callRecordingLog\entity\CallRecordingLog */

$this->title = $model->crl_id;
$this->params['breadcrumbs'][] = ['label' => 'Call Recording Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="call-recording-log-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'crl_id' => $model->crl_id, 'crl_year' => $model->crl_year, 'crl_month' => $model->crl_month], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'crl_id' => $model->crl_id, 'crl_year' => $model->crl_year, 'crl_month' => $model->crl_month], [
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
                'crl_id',
                'crl_call_sid',
                'crl_user_id:username',
                'crl_created_dt',
                'crl_year',
                'crl_month',
            ],
        ]) ?>

    </div>

</div>
