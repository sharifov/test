<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel sales\model\userVoiceMail\entity\search\UserVoiceMailSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Voice Mails';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-voice-mail-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create User Voice Mail', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'uvm_id',
            'uvm_user_id:username',
            'uvm_name',
            'uvm_say_text_message:ntext',
            'uvm_say_language',
            'uvm_say_voice',
//            'uvm_voice_file_message',
            'uvm_record_enable:BooleanByLabel',
            //'uvm_max_recording_time:datetime',
            'uvm_transcribe_enable:BooleanByLabel',
            'uvm_enabled:BooleanByLabel',
            'uvm_created_dt:byUserDateTime',
            'uvm_updated_dt:byUserDateTime',
            'uvm_created_user_id:username',
            'uvm_updated_user_id:username',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
