<?php

use kartik\export\ExportMenu;
use sales\viewModel\call\ViewModelTotalCallGraph;

/**
 * @var ViewModelTotalCallGraph $viewModel
 */
?>
<script>
    $('a[id^=export-links]').each( function (i, element ) {
        $(document).off("click.exportmenu", "#"+$(element).attr('id'));
    });
</script>

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
        'showConfirmAlert' => false,
        'options' => [
            'id' => 'export-links'
        ],
	]); ?>
</div>
