<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model src\model\userData\entity\UserData */

$this->title = 'Create User Data';
$this->params['breadcrumbs'][] = ['label' => 'User Data', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-data-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
