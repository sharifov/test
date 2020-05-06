<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\userVoiceMail\entity\UserVoiceMail */

$this->title = $model->uvmUser->username;
$this->params['breadcrumbs'][] = ['label' => 'User Voice Mails', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-voice-mail-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->uvm_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->uvm_id], [
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
                'uvm_id',
                'uvm_user_id:username',
                'uvm_name',
                'uvm_say_text_message:ntext',
                'uvm_say_language',
                'uvm_say_voice',
                'uvm_voice_file_message',
                'uvm_record_enable:BooleanByLabel',
                'uvm_max_recording_time:datetime',
                'uvm_transcribe_enable:BooleanByLabel',
                'uvm_enabled:BooleanByLabel',
                'uvm_created_dt:byUserDateTime',
                'uvm_updated_dt:byUserDateTime',
                'uvm_created_user_id:username',
                'uvm_updated_user_id:username',
            ],
        ]) ?>

    </div>

</div>
