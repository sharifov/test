<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UserProductType */

$this->title = 'Update User Product Type: ' . $model->upt_user_id;
$this->params['breadcrumbs'][] = ['label' => 'User Product Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->upt_user_id, 'url' => ['view', 'upt_user_id' => $model->upt_user_id, 'upt_product_type_id' => $model->upt_product_type_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-product-type-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
