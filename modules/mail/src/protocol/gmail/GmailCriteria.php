<?php

namespace modules\mail\src\gmail;

/**
 * Class GmailCriteria
 *
 * @property array $labelIds
 * @property int $maxResults
 * @property string|null $q
 */
class GmailCriteria
{
    private const MAX_RESULT = 400;

    private $labelIds;
    private $maxResults;
    private $q;

    public function __construct(?int $dayTo, int $maxResults, ?int $dayFrom, array $labelIds = ['INBOX', 'UNREAD'])
    {
        if ($maxResults > self::MAX_RESULT) {
            $this->maxResults = self::MAX_RESULT;
        } else {
            $this->maxResults = $maxResults;
        }

        if ($dayFrom && !$dayTo) {
            $dateEnd = (new \DateTime('now'))->modify('-' . $dayFrom . ' day')->format('Y-m-d');
            $this->q = 'before:' . $dateEnd;
        } elseif (!$dayFrom && $dayTo) {
            $dateStart = (new \DateTime('now'))->modify('-' . $dayTo . ' day')->format('Y-m-d');
            $this->q = 'after:' . $dateStart;
        } elseif ($dayFrom && $dayTo) {
            $dateEnd = (new \DateTime('now'))->modify('-' . $dayFrom . ' day')->format('Y-m-d');
            $dateStart = (new \DateTime('now'))->modify('-' . $dayTo . ' day')->format('Y-m-d');
            $this->q = 'after:' . $dateStart . ' ' . 'before:' . $dateEnd;
        }

        $this->labelIds = $labelIds;
    }

    public function getOptParams(): array
    {
        $optParams = [
            'maxResults' => $this->maxResults,
            'labelIds' => $this->labelIds,
        ];
        if ($this->q) {
            $optParams['q'] = $this->q;
        }
        return $optParams;
    }

    public function toString(): string
    {
        return 'labelIds: (' . implode(', ', $this->labelIds) . ') maxResults: (' . $this->maxResults . ') q: (' . $this->q . ')';
    }
}
