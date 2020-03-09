<?php

use sales\model\lead\useCases\lead\import\LeadImportUploadForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var array $log */
/** @var LeadImportUploadForm $model */

$this->title = 'Lead Import';

$this->params['breadcrumbs'][] = $this->title;

?>

    <h1><?= $this->title ?></h1>

    <?php if ($log): ?>

        <div class="card import-summary" style="margin-bottom: 10px;">
            <div class="card-header">
                <span class="pull-right clickable close-icon"><i class="fa fa-times"></i></span>
                Processing result log:
            </div>
            <div class="card-body">
                <ul>
                    <?php foreach ($log as $message): ?>
                        <li><?= $message ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

    <?php endif; ?>

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

    <?= $form->field($model, 'file')->fileInput() ?>

    <?= Html::submitButton('Submit') ?>

<?php

ActiveForm::end();


$js = <<<JS
    $('.close-icon').on('click', function(){    
        $('.import-summary').slideUp();
    })
JS;
$this->registerJs($js);
