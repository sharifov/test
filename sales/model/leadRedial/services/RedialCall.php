<?php

namespace sales\model\leadRedial\services;

/**
 * Class RedialCall
 *
 * @property string $phoneFrom
 * @property int $phoneListId
 * @property string $phoneTo
 * @property int $projectId
 * @property int $leadId
 */
class RedialCall
{
    public string $phoneFrom;
    public int $phoneListId;
    public string $phoneTo;
    public int $projectId;
    public int $leadId;

    public function __construct(string $phoneFrom, int $phoneListId, string $phoneTo, int $projectId, int $leadId)
    {
        $this->phoneFrom = $phoneFrom;
        $this->phoneListId = $phoneListId;
        $this->phoneTo = $phoneTo;
        $this->projectId = $projectId;
        $this->leadId = $leadId;
    }

    public function toArray(): array
    {
        return [
            'phoneFrom' => $this->phoneFrom,
            'phoneListId' => $this->phoneListId,
            'phoneTo' => $this->phoneTo,
            'projectId' => $this->projectId,
            'leadId' => $this->leadId,
        ];
    }
}
