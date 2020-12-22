<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\project\entity\projectLocale\ProjectLocale */
/* @var $copyModel sales\model\project\entity\projectLocale\ProjectLocale */

$this->title = ($copyModel ? 'Copy' : 'Create' ) . ' Project Locale';
$this->params['breadcrumbs'][] = ['label' => 'Project Locales', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-locale-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'copyModel' => $copyModel,
    ]) ?>

</div>
