<?php

/**
 * @var \yii\web\View $this
 * @var \sales\viewModel\chat\ViewModelSearchQuotes $viewModel
 */

use frontend\themes\gentelella_v2\widgets\FlashAlert;

?>

<?= FlashAlert::widget() ?>

<?= $this->render('partial/_flight_request_form', [
    'itineraryForm' => $viewModel->itineraryForm,
    'chatId' => $viewModel->chatId
]) ?>

<?= $this->render('partial/_quote_search_result', [
    'quotes' => $viewModel->quotes,
    'leadId' => $viewModel->lead->id,
    'gds' => '',
    'lead' => $viewModel->lead,
    'dataProvider' => $viewModel->dataProvider,
    'searchForm' => $viewModel->flightQuoteSearchForm,
    'keyCache' => $viewModel->keyCache,
    'searchServiceQuoteDto' => $viewModel->searchServiceDto,
    'airlines' => $viewModel->airlines,
    'locations' => $viewModel->locations,
    'viewModel' => $viewModel
]); ?>

<?php

if ($viewModel->leadCreated) {
    $js = <<<JS
        refreshChatInfo('{$viewModel->chatId}');
    JS;
    $this->registerJs($js);
}