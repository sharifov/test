<?php

use modules\quoteAward\src\dictionary\AwardProgramDictionary;
use modules\quoteAward\src\forms\AwardQuoteForm;
use yii\helpers\Url;

/** @var \common\models\Lead $lead */

$awardType = AwardProgramDictionary::AWARD_MILE;
$flightUrl = Url::to(['quote-award/update', 'leadId' => $lead->id, 'type' => AwardQuoteForm::REQUEST_FLIGHT]);
$segmentUrl = Url::to(['quote-award/update', 'leadId' => $lead->id, 'type' => AwardQuoteForm::REQUEST_SEGMENT]);
$updateUrl = Url::to(['quote-award/update', 'leadId' => $lead->id]);
$quotePriceUrl = Url::to(['quote-award/calc-price', 'leadId' => $lead->id]);

$js = <<<JS

    var leadId = '$lead->id';
    var flightUrl = '{$flightUrl}';
    var segmentUrl = '{$segmentUrl}';
    var awardType = '{$awardType}';
    var updateUrl = '{$updateUrl}';
   
   
    $(document).on('click', '#js-add-flight-award', function(event){
        var AwardForm = $('#alt-award-quote-info-form');
         loadingBtn($(this), true);       
        $.ajax({
            url: flightUrl,
            type: 'POST',
            data: AwardForm.serialize(),
             success: function (data) {
                let modal = $('#modal-lg');
                modal.find('.js-update-ajax').html(data);
                loadingBtn($('#js-add-flight-award'), false);
            }
        }) .fail(function(error) {
            loadingBtn($('#js-add-flight-award'), false);
            console.log(error);
        })
    });
    
     $(document).on('click', '.js-remove-flight-award', function(event){
        var AwardForm = $('#alt-award-quote-info-form');
         loadingBtn($(this), true);       
         let id = $(this).data('id')
        $.ajax({
            url: flightUrl,
            type: 'POST',
            data: AwardForm.serialize()+'&index='+id,
             success: function (data) {
                let modal = $('#modal-lg');
                modal.find('.js-update-ajax').html(data);
            }
        }) .fail(function(error) {
            console.log(error);
        })
    });
     
     $(document).on('click', '#js-add-segment-award', function(event){
        var AwardForm = $('#alt-award-quote-info-form');
         loadingBtn($(this), true);       
        $.ajax({
            url: segmentUrl,
            type: 'POST',
            data: AwardForm.serialize(),
             success: function (data) {
                let modal = $('#modal-lg');
                modal.find('.js-update-ajax').html(data);
                loadingBtn($('#js-add-segment-award'), false);
            }
        }) .fail(function(error) {
            loadingBtn($('#js-add-segment-award'), false);
            console.log(error);
        })
    });
     
    $(document).on('change', '.js-flight-quote-program', function(event){
        let id = $(this).data('id');
        if($(this).val() === awardType){
            $('.js-display-quote-program[data-id="'+id+'"]').removeClass('d-none')
        }else {
            $('.js-display-quote-program[data-id="'+id+'"]').addClass('d-none')
        }
    });
    
     $(document).on('change', '.js-pax-award', function(event){
       
        var AwardForm = $('#alt-award-quote-info-form');
        var formDataAward = AwardForm.serialize()
          $(this).prop( "disabled", true);
          $.ajax({
            url: updateUrl,
            type: 'POST',
            data: formDataAward,
             success: function (data) {
                let modal = $('#modal-lg');
                modal.find('.js-update-ajax').html(data);
                $(this).prop( "disabled", false);
            }
        }) .fail(function(error) {
            console.log(error);
            $(this).prop( "disabled", false);
        })
    });
     
     
    
     $(document).on('click', '.js-remove-segment-award', function(event){
        var AwardForm = $('#alt-award-quote-info-form');
         loadingBtn($(this), true);       
         let id = $(this).data('id')
        $.ajax({
            url: segmentUrl,
            type: 'POST',
            data: AwardForm.serialize()+'&index='+id,
             success: function (data) {
                let modal = $('#modal-lg');
                modal.find('.js-update-ajax').html(data);
            }
        }) .fail(function(error) {
            console.log(error);
        })
    });
    
     function loadingBtn(btnObj, loading)
    {
        if (loading === true) {
            btnObj.removeClass()
                .addClass('btn btn-default')
                .html('<span class="spinner-border spinner-border-sm"></span> Loading')
                .prop("disabled", true);
        } else {
            let origClass = btnObj.data('class');
            let origInner = btnObj.data('inner');
            btnObj.removeClass()
                .addClass(origClass)
                .html(origInner)
                .prop("disabled", false);
        }  
    }
    
     $(document).on('keyup', '.alt-award-quote-price', function(event){
        var key = event.keyCode ? event.keyCode : event.which;
        validatePriceField($(this), key);
    });

    $(document).on('change', '.alt-award-quote-price', function(event){
          $('.alt-award-quote-price').prop('readonly', true);
           let quotePriceUrl = '{$quotePriceUrl}';
           var AwardForm = $('#alt-award-quote-info-form');
           
            $.ajax({
            type: 'post',
            url: quotePriceUrl,
            data: AwardForm.serialize(),
            success: function (data) {
                $.each(data, function( index, value ) {
                    $('#'+index).val(value);
                });
                $('.alt-award-quote-price').prop('readonly', false);
            },
            error: function (error) {
                console.log('Error: ' + error);
                $('.alt-award-quote-price').prop('readonly', false);
            }
        });
    });
    
     $(document).on('change', '.js-award-program', function(){
        let ppm = $(this).find(':selected').data('ppm');
        let ppmInput = $(this).closest('.js-flight-price').find('.js-award-ppm');
        ppmInput.val(ppm);
        ppmInput.trigger('change')
        
    });
JS;
$this->registerJs($js);
