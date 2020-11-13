<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\conference\entity\conferenceParticipantStats\ConferenceParticipantStats */

$this->title = $model->cps_id;
$this->params['breadcrumbs'][] = ['label' => 'Conference Participant Stats', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="conference-participant-stats-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->cps_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->cps_id], [
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
                'cps_id',
                'cps_cf_id',
                'cps_cf_sid',
                'cps_participant_identity',
                'user:UserName',
                'cps_created_dt:byUserDatetime',
                'cps_duration:duration',
                'cps_talk_time:duration',
                'cps_hold_time:duration',
            ],
        ]) ?>

    </div>

</div>
