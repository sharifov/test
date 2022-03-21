<?php

namespace common\components\bootstrap4\activeForm;

/**
 * Class ClientBeforeSubmit
 *
 * @property string $header
 * @property string $modalId
 * @property string $doneSuccessScript
 * @property string $doneErrorScript
 * @property string $doneAlwaysScript
 * @property string $submitBtnId
 * @property bool $notify
 *
 * ActiveForm
      $form = ActiveForm::begin([
          'id' => ...,
          'action' => ....,
          'clientBeforeSubmit' => new ClientBeforeSubmit(
              'Model update',
              true,
              'modal-sm',
              '$.pjax.reload({container: \'#pjax-container-id\'}); ',
              null,
              null
          ),
     ]);
 */
class ClientBeforeSubmit
{
    public $header;
    public $modalId;

    public $doneSuccessScript;
    public $doneErrorScript;
    public $doneAlwaysScript;
    public $submitBtnId;

    public $notify = true;

    public function __construct(
        string $header,
        bool $notify,
        ?string $modalId,
        ?string $doneSuccessScript,
        ?string $doneErrorScript,
        ?string $doneAlwaysScript,
        string $submitBtnId = 'client_submit_btn'
    ) {
        $this->header = $header;
        $this->notify = $notify ? 1 : 0;
        $this->modalId = $modalId;
        $this->doneSuccessScript = $doneSuccessScript;
        $this->doneErrorScript = $doneErrorScript;
        $this->doneAlwaysScript = $doneAlwaysScript;
        $this->submitBtnId = $submitBtnId;
    }

    /**
     * @param $widgetId
     * @return string
     *
     * response from server ['success' => true, 'message' => 'Model updated']
     */
    public function getJs($widgetId): string
    {
        $modalToggle = $this->modalId ? '$(\'#' . $this->modalId . '\').modal(\'toggle\');' : '';
        $js = <<< JS
$('#{$widgetId}').on('beforeSubmit', function (e) {
        e.preventDefault();
        let btn = document.getElementById("{$this->submitBtnId}")        
        if (btn.disabled === false){
            btn.disabled = true
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Processing'  
        $.ajax({
           type: $(this).attr('method'),
           url: $(this).attr('action'),
           data: $(this).serializeArray(),
           dataType: 'json'
        })
        .done(function(data) {
            let message = '';
            if (data.success) {
                {$this->doneSuccessScript};
                if ({$this->notify}) {
                    message = 'Success';
                    if (data.message) {
                        message = data.message;
                    }
                    createNotifyByObject({title: '{$this->header}', text: message, type: 'info'});
                }
            } else {
                {$this->doneErrorScript};
                if ({$this->notify}) {
                    message = 'Error. Try again later.';
                    if (data.message) {
                        message = data.message;
                    }
                    createNotifyByObject({title: '{$this->header}', text: message, type: 'error'});
                }
            }
            {$this->doneAlwaysScript};
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            if ({$this->notify}) {
               createNotifyByObject({title: '{$this->header}', text: "Request failed: " + textStatus, type: 'error'});
           }
        })
        .always(function() {
           {$modalToggle};
        });
        }
        return false;
    }); 
JS;
        return $js;
    }
}
