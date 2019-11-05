<?php

use common\models\Lead;
use frontend\widgets\redial\LeadRedialViewWidget;

/** @var Lead  $lead*/

echo LeadRedialViewWidget::widget(['lead' => $lead]);
