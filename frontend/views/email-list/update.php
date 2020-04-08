<?php

use sales\model\emailList\entity\EmailList;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model EmailList */

$this->title = 'Update Email List: ' . $model->el_id;
$this->params['breadcrumbs'][] = ['label' => 'Email Lists', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->el_id, 'url' => ['view', 'id' => $model->el_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="email-list-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
