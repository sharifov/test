<?php

use sales\model\lead\useCases\lead\import\LeadImportUploadForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var array $log */
/** @var LeadImportUploadForm $model */

$this->title = 'Lead Import';

$this->params['breadcrumbs'][] = $this->title;

?>

    <h1><i class="fa fa-upload"></i> <?= $this->title ?></h1>

    <div class="x_panel">
    <div class="x_title">
        <h2>Import form</h2>

        <div class="clearfix"></div>
    </div>
    <div class="x_content">

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
    <div class="col-md-12 col-sm-12">
        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

        <div class="row">
            <div class="col-md-12 col-sm-12 text-center">
                <div class="form-group">
                    <?= $form->field($model, 'file')->fileInput(['multiple' => false, 'accept' => '.csv']) ?>
                </div>
            </div>
        </div>
        <div class="col-md-12 text-center">
            <?= Html::submitButton('<i class="fa fa-upload"></i> Upload CSV file', ['class' => 'btn btn-success']) ?>
        </div>
    </div>
    </div>
    </div>

<?php

ActiveForm::end();


$js = <<<JS
    $('.close-icon').on('click', function(){    
        $('.import-summary').slideUp();
    })
JS;
$this->registerJs($js);
