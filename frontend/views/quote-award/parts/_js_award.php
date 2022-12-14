<?php

use modules\quoteAward\src\dictionary\AwardProgramDictionary;
use modules\quoteAward\src\forms\AwardQuoteForm;
use yii\helpers\Url;

/** @var \common\models\Lead $lead */

$awardType = AwardProgramDictionary::AWARD_MILE;
$flightUrl = Url::to(['quote-award/update', 'leadId' => $lead->id, 'type' => AwardQuoteForm::REQUEST_FLIGHT]);
$segmentUrl = Url::to(['quote-award/update', 'leadId' => $lead->id, 'type' => AwardQuoteForm::REQUEST_SEGMENT]);
$tripUrl = Url::to(['quote-award/update', 'leadId' => $lead->id, 'type' => AwardQuoteForm::REQUEST_TRIP]);
$updateUrl = Url::to(['quote-award/update', 'leadId' => $lead->id]);
$quotePriceUrl = Url::to(['quote-award/calc-price', 'leadId' => $lead->id]);
$importDumpUrl = Url::to(['quote-award/import-gds-dump', 'leadId' => $lead->id]);

$js = <<<JS

    var leadId = '$lead->id';
    var flightUrl = '{$flightUrl}';
    var segmentUrl = '{$segmentUrl}';
    var tripUrl = '{$tripUrl}';
    var awardType = '{$awardType}';
    var updateUrl = '{$updateUrl}';
    var importDumpUrl = '{$importDumpUrl}';
   
   
    $(document).on('click', '#js-add-flight-award', function(event){
        var AwardForm = $('#alt-award-quote-info-form');
        let tabActive =  $('.js-flight-tab.active').data('id');
         loadingBtn($(this), true);       
        $.ajax({
            url: flightUrl,
            type: 'POST',
            data: AwardForm.serialize()+'&tab='+tabActive,
             success: function (data) {
               $('.js-update-ajax').html(data);
                loadingBtn($('#js-add-flight-award'), false);
            }
        }) .fail(function(error) {
            loadingBtn($('#js-add-flight-award'), false);
            console.log(error);
        })
    });
    
     $(document).on('click', '.js-remove-flight-award', function(event){
        if (confirm('Are you sure you want to delete this item?')) {
            var AwardForm = $('#alt-award-quote-info-form');
            loadingBtn($(this), true, '', '');       
            let id = $(this).data('id')
            $.ajax({
              url: flightUrl,
              type: 'POST',
              data: AwardForm.serialize()+'&index='+id,
              success: function (data) {
               $('.js-update-ajax').html(data);
             }
            }) .fail(function(error) {
            console.log(error);
         })
        }
    });
     
     $(document).on('click', '#js-add-segment-award', function(event){
        var AwardForm = $('#alt-award-quote-info-form');
        loadingBtn($(this), true, 'btn');
        let tripId = $(this).data('trip');
        let tabActive =  $('.js-flight-tab.active').data('id');
        $.ajax({
            url: segmentUrl,
            type: 'POST',
            data: AwardForm.serialize()+'&tripId='+tripId+'&tab='+tabActive,
             success: function (data) {
               $('.js-update-ajax').html(data);
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
         let tabActive =  $('.js-flight-tab.active').data('id');
        var formDataAward = AwardForm.serialize()
          $(this).prop( "disabled", true);
          $.ajax({
            url: updateUrl,
            type: 'POST',
            data: formDataAward+'&tab='+tabActive,
             success: function (data) {
               $('.js-update-ajax').html(data);
                $(this).prop( "disabled", false);
            }
        }) .fail(function(error) {
            console.log(error);
            $(this).prop( "disabled", false);
        })
    });
     
     
    
     $(document).on('click', '.js-remove-segment-award', function(event){
        var AwardForm = $('#alt-award-quote-info-form');
         loadingBtn($(this), true, 'btn btn-default', '');   
          let tabActive =  $('.js-flight-tab.active').data('id');
         let id = $(this).data('id');
        $.ajax({
            url: segmentUrl,
            type: 'POST',
            data: AwardForm.serialize()+'&index='+id+'&tab='+tabActive,
             success: function (data) {
               $('.js-update-ajax').html(data);
            }
        }) .fail(function(error) {
            console.log(error);
        })
    });
     
     
       $(document).on('click', '.js-remove-trip-award', function(event){
           if (confirm('Are you sure you want to delete this item?')) {
            var AwardForm = $('#alt-award-quote-info-form');
            loadingBtn($(this), true, '', '');   
            let tabActive =  $('.js-flight-tab.active').data('id');
            let id = $(this).data('id');
            $.ajax({
                url: tripUrl,
                type: 'POST',
                data: AwardForm.serialize()+'&index='+id+'&tab='+tabActive,
                 success: function (data) {
                    $('.js-update-ajax').html(data);
                }
            }) .fail(function(error) {
                console.log(error);
            })
        }
    });
    
     function loadingBtn(btnObj, loading, btnClass = 'btn btn-default', textLoading = 'Loading')
    {
        if (loading === true) {
            btnObj.removeClass()
                .addClass(btnClass)
                .html('<span class="spinner-border spinner-border-sm"></span> '+textLoading)
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
           
             if ($(this).hasClass('js-check-payment')) {
            quotePriceUrl = quotePriceUrl + '&refresh=1'
        }
           
            $.ajax({
            type: 'post',
            url: quotePriceUrl,
            data: AwardForm.serialize(),
            success: function (data) {
                $.each(data, function( index, value ) {
                    $('#'+index).val(value);
                     $('#'+index).html(value);
                    $('#'+index).closest('div.form-group').removeClass('has-error');
                    $('#'+index).closest('div.form-group').find('p').html('');
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
        let ppmInput = $(this).closest('.js-flight-wrap').find('.js-award-ppm');
        ppmInput.val(ppm);
        ppmInput.trigger('change')
        
    });
     
       $(document).on('beforeSubmit', '#alt-award-quote-info-form', function (e) {
         e.preventDefault();
         var AwardForm = $(this);
                 $.ajax({
             url: AwardForm.attr("action"),
             type: AwardForm.attr("method"),
             data: AwardForm.serialize(),
             success: function (data) {
                 if(data.success == 'true'){
                 }else {
                     $.each(data, function(key, val){
                          AwardForm.yiiActiveForm('updateAttribute', key, [val]) 
                     })
                   
                 }
             },
             error: function (error) {
                 console.log('Error: ' + error);
             }
         });
       return false;
     });
      
      $(document).on('click','.js-dump-gds', function (e) {
        e.preventDefault();
        let modal = $('#modal-df');
        let tripId = $(this).data('trip');
        modal.find('.modal-title').html('GDS dump');
        modal.find('.modal-body').html('');
        modal.find('.modal-body').load(importDumpUrl+'&tripId='+tripId, function( response, status, xhr ) {
            modal.modal({
              // backdrop: 'static',
              show: true
            });
        });
    });
      
      
      $(document).on('submit', '#award-import-gds', function (event) {
          event.preventDefault();
          let form = $(this);
          var AwardForm = $('#alt-award-quote-info-form');
          loadingBtn($('#btn-award-import-gds'), true);
          let tabActive =  $('.js-flight-tab.active').data('id');
            $.ajax({
            url: importDumpUrl,
            type: form.attr("method"),
            data: form.serialize()+'&'+AwardForm.serialize()+'&tab='+tabActive,
            success: function (dataResponse) {
                loadingBtn($('#btn-award-import-gds'), false);
                if (dataResponse.status === 'success') {
                     let modal = $('#modal-df');
                     modal.modal('hide');
                       $('#preloader').removeClass('d-none');
                     $('.js-update-ajax').html(dataResponse.flight);
                      $('#preloader').addClass('d-none');
                } else {
                    if (dataResponse.message.length) {
                        createNotifyByObject({
                            title: "Error",
                            type: "error",
                            text: dataResponse.message,
                            hide: true
                        }); 
                    }    
                    $('#save_dump_btn').hide(500);
                }
            },
            error: function (error) {
                loadingBtn($('#btn-award-import-gds'), false);
                console.log(error);
            }
            });
            setTimeout(loadingBtn, 4000, $('#btn-award-import-gds'), false);
            });
JS;
$this->registerJs($js);
