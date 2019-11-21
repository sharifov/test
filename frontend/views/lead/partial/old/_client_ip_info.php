<?php

use common\models\Lead;
use common\models\search\lead\LeadSearchByIp;
use yii\helpers\Html;
use yii\bootstrap4\Modal;
use yii\helpers\Url;
use yii\web\View;

/** @var Lead $lead */
/** @var View $this */

if (($count = LeadSearchByIp::count($lead->request_ip, Yii::$app->user->id)) > 1) {

    Modal::begin([
        'title' => '',
        'id' => 'modal-ip-cnt-ip',
        'size' => 'modal-lg',
        'clientOptions' => ['backdrop' => 'static'],
        'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">Close</a>'
    ]);
    Modal::end();

    echo Html::button('<i class="fa fa-globe"></i> IP: ' . $lead->request_ip . ' - ' . $count . ' <i class="fa fa-clone"></i>', [
        'id' => 'button-cnt-ip',
        'data-lead_id' => 'ip-cnt-ip',
        'title' => $lead->request_ip,
        'class' => 'btn btn-default',
    ]);

    $url = Url::to(['/lead-view/search-leads-by-ip']);
    $gid = $lead->gid;

    $js = <<<JS
    $(document).on('click', '#button-cnt-ip', function(e) {
        e.preventDefault();
        $('#modal-ip-cnt-ip .modal-body').html('<div style="text-align:center;font-size: 60px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');
        $('#modal-ip-cnt-ip .modal-header').html('<h2>Leads</h2>');
        $('#modal-ip-cnt-ip').modal();
        $.get('$url', {gid:'$gid'}, function (data) {
                $('#modal-ip-cnt-ip .modal-body').html(data);
            }
        );
    }); 
JS;

    $this->registerJs($js);

} else {

    $dataContent = '';
    $ipData = @json_decode($lead->request_ip_detail, true);

    if ($ipData) {

        $str = '<table class="table table-bordered">';
        $content = '';
        foreach ($ipData as $key => $val) {
            if (is_array($val)) {
                continue;
            }
            $content .= '<tr><th>' . $key . '</th><td>' . $val . '</td></tr>';
        }
        if ($content) {
            $dataContent = $str . $content . '</table>';
        }

    }
    echo Html::button('<i class="fa fa-globe"></i> IP: ' . $lead->request_ip , [
        'data-toggle' => 'popover',
        'data-placement' => 'bottom',
        'data-content' => $dataContent,
        'class' => 'btn btn-default client-comment-phone-button',
    ]);
}
