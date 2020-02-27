<?php

use common\models\Employee;
use sales\helpers\lead\LeadPreferencesHelper;
use sales\model\lead\useCases\lead\create\LeadCreateForm;
use sales\model\lead\useCases\lead\create\LeadManageForm;
use yii\widgets\ActiveForm;

/**
 * @var $leadForm LeadManageForm
 * @var $form ActiveForm
 */

?>

<?php //= $form->field($leadForm, 'sourceId')->dropDownList($leadForm->listSourceId(), ['prompt' => '---']) ?>

<div class="row">
	<div class="col-md-6">
		<?= $form->field($leadForm, 'source')->widget(\kartik\select2\Select2::class, [
			'data' => $leadForm->listSources(),
			'size' => \kartik\select2\Select2::SMALL,
			'options' => ['placeholder' => 'Select market', 'multiple' => false],
			'pluginOptions' => ['allowClear' => true],
		]) ?>
	</div>
</div>