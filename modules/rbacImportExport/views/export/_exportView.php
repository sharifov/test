<?php
/**
 * @var ExportForm $model
 */

use kartik\select2\Select2;
use modules\rbacImportExport\src\entity\AuthImportExport;
use modules\rbacImportExport\src\forms\ExportForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\YiiAsset;

$this->title = 'RBAC Export';
$this->params['breadcrumbs'][] = ['label' => 'RBAC Import Export', 'url' => ['/rbac-import-export/log/index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<h1><?= Html::encode($this->title) ?></h1>

<div class="row">
	<div class="col-md-6">

        <?php $form = ActiveForm::begin(['options' => ['data-pjax' => 0]]) ?>

            <?= $form->errorSummary($model) ?>

            <?= $form->field($model, 'roles')->widget(Select2::class, [
				'options' => [
					'multiple' => true,
					'placeholder' => 'Choose roles for export',
					'prompt' => null
				],
				'size' => Select2::SMALL,
            ])->label(true, ['class' => 'control-label']) ?>

            <?= $form->field($model, 'section')->checkboxList(AuthImportExport::getSectionList(), ['itemOptions' => []]) ?>

            <div class="text-center" style="margin-top: 20px;">
                <?= Html::submitButton('<i class="fas fa-file-export"></i> Export', ['class' => 'btn btn-success get-export']) ?>
            </div>

        <?php ActiveForm::end(); ?>

	</div>
</div>