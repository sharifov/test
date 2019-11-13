<?php

namespace frontend\widgets\redial;

use common\models\Lead;
use yii\base\Widget;

/**
 * Class LeadRedialViewWidget
 *
 * @property Lead $lead
 */
class LeadRedialViewWidget extends Widget
{

    public $lead;

    public function init(): void
    {
        parent::init();
        if (!$this->lead instanceof Lead) {
            throw new \InvalidArgumentException('lead property must be Lead');
        }
    }

    /**
     * @return string
     */
    public function run(): string
    {
        return $this->render('lead_redial_view', ['lead' => $this->lead]);
    }

}
