<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\userVoiceMail\entity\UserVoiceMail */
/* @var $languageList array */

$this->title = 'Update User Voice Mail: ' . $model->uvm_id;
$this->params['breadcrumbs'][] = ['label' => 'User Voice Mails', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->uvm_id, 'url' => ['view', 'id' => $model->uvm_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-voice-mail-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
		'languageList' => $languageList
	]) ?>

</div>
