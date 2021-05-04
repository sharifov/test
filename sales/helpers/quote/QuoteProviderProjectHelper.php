<?php

namespace sales\helpers\quote;

use common\models\Quote;

/**
 * Class QuoteProviderProjectHelper
 */
class QuoteProviderProjectHelper
{
    public static function getProviderProjects(Quote $quote): array
    {
        $result = [];
        if (!$quote->providerProject) {
            return $result;
        }

        $result[] = $quote->providerProject->project_key;
        if ($quote->providerProject->projectRelations) {
            foreach ($quote->providerProject->projectRelations as $projectRelation) {
                $result[] = $projectRelation->prlRelatedProject->project_key;
            }
        }
        return $result;
    }
}
