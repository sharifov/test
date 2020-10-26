<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var array $logs */

$this->title = 'Import phones';
$this->params['breadcrumbs'][] = ['label' => 'Department Phone Projects', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

\yii\web\YiiAsset::register($this);

?>

<div class="department-phone-project-import">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

    <?= $form->field($model, 'file')->fileInput() ?>

    <?= Html::submitButton('Import', ['class' => 'btn btn-success']) ?>

    <?php ActiveForm::end() ?>

</div>

<?php foreach ($logs as $log): ?>
<pre>
<?php print_r($log) ?>
</pre>
<?php endforeach;?>