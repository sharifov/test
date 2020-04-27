<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\userVoiceMail\entity\UserVoiceMail */
/* @var $languageList array */

$this->title = 'Create User Voice Mail';
$this->params['breadcrumbs'][] = ['label' => 'User Voice Mails', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-voice-mail-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'languageList' => $languageList
    ]) ?>

</div>
