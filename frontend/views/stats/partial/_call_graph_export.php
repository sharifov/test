<?php

use kartik\export\ExportMenu;
use src\viewModel\call\ViewModelTotalCallGraph;

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
        'exportConfig' => [
            ExportMenu::FORMAT_PDF => [
                'pdfConfig' => [
                    'mode' => 'c',
                    'format' => 'A4-L',
                ]
            ]
        ],
        'target' => \kartik\export\ExportMenu::TARGET_BLANK,
        'bsVersion' => '3.x',
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
