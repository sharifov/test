<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\conference\entity\conferenceRecordingLog\ConferenceRecordingLog */

$this->title = $model->cfrl_id;
$this->params['breadcrumbs'][] = ['label' => 'Conference Recording Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="conference-recording-log-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'cfrl_id' => $model->cfrl_id, 'cfrl_year' => $model->cfrl_year, 'cfrl_month' => $model->cfrl_month], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'cfrl_id' => $model->cfrl_id, 'cfrl_year' => $model->cfrl_year, 'cfrl_month' => $model->cfrl_month], [
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
                'cfrl_id',
                'cfrl_conference_sid',
                'cfrl_user_id:username',
                'cfrl_created_dt:byUserDateTime',
                'cfrl_year',
                'cfrl_month',
            ],
        ]) ?>

    </div>

</div>
