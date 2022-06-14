<?php

namespace src\model\leadRedial\leadExcluder;

use common\models\Lead;
use src\helpers\setting\SettingHelper;

/**
 * Class LeadExcluder
 *
 * @property LeadExcluderSettings $settings
 */
class LeadExcluder
{
    private LeadExcluderSettings $settings;

    public function __construct()
    {
        $this->settings = LeadExcluderSettings::fromArray(SettingHelper::getRedialLeadExcludeAttributes());
    }

    public function isExclude(Lead $lead): bool
    {
        if ($this->settings->isInvalid()) {
            return false;
        }

        if ($this->settings->inProjects($lead->project->project_key)) {
            return true;
        }

        if ($this->settings->inDepartments($lead->lDep->dep_key ?? '')) {
            return true;
        }

        if ($this->settings->inSources($lead->source->cid ?? '')) {
            return true;
        }

        if ($this->settings->inCabins($lead->cabin ?? '')) {
            return true;
        }

        if ($this->settings->isNoFlightDetailsEnable()) {
            if (!$lead->hasFlightDetails()) {
                return true;
            }
        }

        if ($this->settings->inTest((bool)$lead->l_is_test)) {
            return true;
        }

        return false;
    }
}
