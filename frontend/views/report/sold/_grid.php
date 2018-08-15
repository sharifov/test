<?php
/**
 * @var $this View
 * @var $dataProvider ArrayDataProvider
 * @var $model SoldReportForm
 */

use yii\grid\GridView;
use yii\web\View;
use yii\data\ArrayDataProvider;
use frontend\models\SoldReportForm;

$formId = sprintf('%s-Id', $model->formName());

$js = <<<JS
    $('.table-pagination__pagination-wrap a').click(function(e) {
        e.preventDefault();
        var form = $('#$formId'),
            url = $(this).attr('href');
        $('#preloader').removeClass('hidden');
        $.ajax({
            url: url,
            type: 'post',
            data: form.serialize(),
            success: function (data) {
                var tabResult = $('#table-expert-grid-id');
                tabResult.html(data.grid);
                $('#preloader').addClass('hidden');
            },
            error: function (error) {
                console.log('Error: ' + error);
            }
        });
    });

    $('.view-detail-sold').click(function (e) {
        e.preventDefault();
        var url = $(this).data('url');
        var editBlock = $('#modal-report-info');
        editBlock.find('.modal-body').html('');
        editBlock.find('.modal-body').load(url, function( response, status, xhr ) {
            editBlock.modal('show');
        });
    });
JS;

$this->registerJs($js);

$template = <<<HTML
<div class="table-pagination">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <div class="table-pagination__entries-num-text">
                    {summary}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="table-pagination__pagination-wrap">
                    {pager}
                </div>
            </div>
        </div>
    </div>
</div>
{items}
HTML;
?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'tableOptions' => [
        'class' => 'table table-striped table-hover table-bordered table-neutral',
    ],
    'layout' => $template,
    'columns' => $model->getColumns()
])
?>
