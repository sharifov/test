<?php
use yii\bootstrap\Modal;
use yii\helpers\Url;

/**
 * @var $result []
 * @var $airlines []
 * @var $locations []
 * @var $leadId int
 * @var $gds string
 */

$url = Url::to(['quote/create-quote-from-search', 'leadId' => $leadId]);
$this->registerCssFile('//cdnjs.cloudflare.com/ajax/libs/noUiSlider/11.1.0/nouislider.min.css');
$js = <<<JS
    $(document).on('click','.search_details__btn', function (e) {
        e.preventDefault();
        var modal = $('#flight-details__modal');
        modal.find('.modal-header h2').html($(this).data('title'));
        var target = $($(this).data('target')).html();
        modal.find('.modal-body').html(target);
        modal.modal('show');
    });

    $('.create_quote__btn').click(function (e) {
        e.preventDefault();
        var key = $(this).data('key');
        $('#preloader').removeClass('hidden');
        $.ajax({
            url: '$url',
            type: 'post',
            data: {'key': key, 'gds': '$gds'},
            success: function (data) {
                $('#preloader').addClass('hidden');
                if(data.status == true){
                    $('#search-results__modal').modal('hide');
                    $('#flight-details__modal').modal('hide');
                    $.pjax.reload({container: '#quotes_list', async: false});
                }else{
                    alert('Some errors was happened during create quote. Please try again later.');
                }
            },
            error: function (error) {
                console.log('Error: ' + error);
            }
        });
    });
JS;
$this->registerJs($js);
?>
<?php if($result || (isset($result['count']) && $result['count'] > 0)):?>
<div class="filters-panel">
    <div class="filters-aux">
        <div class="filters-total"><strong><?= $result['count']?> res</strong></div>
        <div class="filters-sort">
            <label for="sort" class="control-label">
                <i class="fa fa-sort"></i>
                Sort by</label>

        </div>
    </div>
</div>

<?php foreach ($result['results'] as $key => $resultItem):?>
	<?= $this->render('_search_result_item', ['resultKey' => $key,'result' => $resultItem,'locations' => $locations,'airlines' => $airlines]);?>
<?php endforeach;?>
<?php else:?>
	<p>No search results</p>
<?php endif;?>