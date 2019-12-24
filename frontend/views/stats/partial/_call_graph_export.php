<?php

use kartik\export\ExportMenu;
use sales\viewModel\call\ViewModelTotalCallGraph;

/**
 * @var ViewModelTotalCallGraph $viewModel
 */
?>

<div class="d-flex">
	<?php echo ExportMenu::widget([
		'dataProvider' => $viewModel->dataProvider,
		'columns' => $viewModel->gridColumns,
		'target' => \kartik\export\ExportMenu::TARGET_BLANK,
		'fontAwesome' => true,
		'dropdownOptions' => [
			'label' => 'Full Export'
		],
		'columnSelectorOptions' => [
			'label' => 'Export Fields'
		],
        'showConfirmAlert' => false
	]); ?>
</div>
