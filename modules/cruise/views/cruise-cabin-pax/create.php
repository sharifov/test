<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\cruise\src\entity\cruiseCabinPax\CruiseCabinPax */

$this->title = 'Create Cruise Cabin Pax';
$this->params['breadcrumbs'][] = ['label' => 'Cruise Cabin Paxes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cruise-cabin-pax-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
