<?php

/**
 * @var \yii\web\View $this
 * @var \sales\viewModel\chat\ViewModelSearchQuotes $viewModel
 */

use frontend\themes\gentelella_v2\widgets\FlashAlert;
use yii\helpers\Url;

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

if ($viewModel->lead) {
    $urlCreateQuoteFromSearch = Url::to(['client-chat-flight-quote/create-quote-from-search', 'leadId' => $viewModel->lead->id]);
    $js = <<<JS
    $('body').off('click', '.search_create_quote__btn').on('click', '.search_create_quote__btn', function (e) {
        e.preventDefault();
        let createQuoteBtn = $(this);
        var key = $(this).data('key');
        var gds = $(this).data('gds');
        var keyCache = $(this).data('key-cache');
        var searchResId = $(this).data('result');
        let projectId = createQuoteBtn.data('project');
        let chatId = createQuoteBtn.data('chat-id');
        
        $('#preloader').removeClass('d-none');
        $.ajax({
            url: '$urlCreateQuoteFromSearch',
            type: 'post',
            data: {
                'key': key, 
                'gds': gds, 
                'keyCache': keyCache, 
                'createFromQuoteSearch':1,
                'projectId': projectId,
                'chatId': chatId
            },
            beforeSend: function () {
              $('#'+searchResId).addClass('loading');
            },
            success: function (data) {
                var error = '';
                
                $('#preloader').addClass('d-none');
                if(data.status == true){
                    createQuoteBtn.remove();
                    
                    new PNotify({
                        title: "Create quote - search",
                        type: "success",
                        text: 'Added new quote id: ' + searchResId,
                        hide: true
                    });
                } else {
                    if(data.error) {
                        error = data.error;    
                    } else {
                        error = 'Some errors was happened during create quote. Please try again later';
                    }
                    
                    new PNotify({
                        title: "Error: Create quote - search",
                        type: "error",
                        text: error,
                        hide: true
                    });
                }
            },
            error: function (error) {
                createNotify('Error', error.responseJSON.message, 'error');
            },
            complete: function () {
              $('#'+searchResId).removeClass('loading');
              $('#preloader').addClass('d-none');
            }
        });
    });
    JS;
    $this->registerJs($js);
}
