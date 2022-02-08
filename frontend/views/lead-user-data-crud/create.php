<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model src\model\leadUserData\entity\LeadUserData */

$this->title = 'Create Lead User Data';
$this->params['breadcrumbs'][] = ['label' => 'Lead User Datas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-user-data-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
