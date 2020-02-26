<?php

namespace sales\yii\bootstrap4;

/**
 * Class ClientBeforeSubmit
 *
 * @property string $header
 * @property string $modalId
 * @property string $successScript
 * @property string $errorScript
 * @property bool $notify
 */
class ClientBeforeSubmit
{
    public $header;
    public $modalId;

    public $successScript;
    public $errorScript;

    public $notify = true;

    public function __construct(
        string $header,
        bool $notify,
        ?string $modalId,
        ?string $successScript,
        ?string $errorScript
    )
    {
        $this->header = $header;
        $this->notify = $notify;
        $this->modalId = $modalId;
        $this->successScript = $successScript;
        $this->errorScript = $errorScript;
    }

    public function getJs($widgetId): string
    {
        $modalToggle = $this->modalId ? '$(\'#' . $this->modalId . '\').modal(\'toggle\')' : '';
        $js =<<< JS
$('#{$widgetId}').on('beforeSubmit', function (e) {
        e.preventDefault();
        $.ajax({
           type: $(this).attr('method'),
           url: $(this).attr('action'),
           data: $(this).serializeArray(),
           dataType: 'json',
           success: function(data) {
                {$modalToggle}
                let message = '';
                if (data.success) {
                    {$this->successScript};
                    if ({$this->notify}) {
                        message = 'Success';
                        if (data.message) {
                            message = data.message;
                        }
                        new PNotify({title: '{$this->header}', text: message, type: 'info'});
                    }
                } else {
                    {$this->errorScript};
                    if ({$this->notify}) {
                        message = 'Error. Try again later.';
                        if (data.message) {
                            message = data.message;
                        }
                        new PNotify({title: '{$this->header}', text: message, type: 'error'});
                    }
                }
           },
           error: function (error) {
               {$modalToggle}
               if ({$this->notify}) {
                   new PNotify({title: '{$this->header}', text: 'Internal Server Error. Try again later.', type: 'error'});
               }
           }
        })
        return false;
    }); 
JS;
        return $js;
    }
}
