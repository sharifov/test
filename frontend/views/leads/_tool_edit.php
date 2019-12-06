<?php

use common\models\Lead;
use frontend\widgets\lead\editTool\Widget;

/** @var Lead $lead */

echo Widget::widget(['lead' => $lead, 'modalId' => 'modal-df']);
