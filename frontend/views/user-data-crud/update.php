<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model src\model\userData\entity\UserData */

$this->title = 'Update User Data: ' . $model->ud_user_id;
$this->params['breadcrumbs'][] = ['label' => 'User Data
', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ud_user_id, 'url' => ['view', 'ud_user_id' => $model->ud_user_id, 'ud_key' => $model->ud_key]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-data-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
