<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\cruise\src\entity\cruiseCabin\CruiseCabin */

$this->title = 'Create Cruise Cabin';
$this->params['breadcrumbs'][] = ['label' => 'Cruise Cabins', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cruise-cabin-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
