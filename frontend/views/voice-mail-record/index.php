<?php

use common\components\grid\BooleanColumn;
use common\components\grid\call\CallDurationColumn;
use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel sales\model\voiceMailRecord\entity\search\VoiceMailRecordSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Voice Mail Records';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="voice-mail-record-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Voice Mail Record', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'vmr_call_id',
            ['class' => CallDurationColumn::class, 'attributeDuration' => 'vmr_duration', 'attributeSid' => 'vmr_record_sid', 'attributeUrl' => 'recordingUrl'],
            'vmr_client_id:client',
            ['class' => UserSelect2Column::class, 'attribute' => 'vmr_user_id', 'relation' => 'user'],
            ['class' => DateTimeColumn::class, 'attribute' => 'vmr_created_dt'],
            'vmr_duration',
            ['class' => BooleanColumn::class, 'attribute' => 'vmr_new'],
            ['class' => BooleanColumn::class, 'attribute' => 'vmr_deleted'],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
