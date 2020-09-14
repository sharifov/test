<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ConferenceParticipant */

$this->title = $model->cp_id;
$this->params['breadcrumbs'][] = ['label' => 'Conference Participants', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="conference-participant-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->cp_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->cp_id], [
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
            'cp_id',
            'cp_identity',
            'cp_type_id:conferenceParticipantType',
            'cp_cf_id',
            'cp_cf_sid',
            'cp_call_sid',
            'cp_call_id',
            'user:userName',
            'cp_status_id:conferenceParticipantStatus',
            'cp_join_dt:byUserDateTime',
            'cp_leave_dt:byUserDateTime',
            'cp_hold_dt:byUserDateTime',
        ],
    ]) ?>

</div>
