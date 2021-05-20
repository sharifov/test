<?php

namespace sales\services\lead;

class LeadHashGenerator
{
    /**
     * @param $requestIp
     * @param $projectId
     * @param $adults
     * @param $children
     * @param $infants
     * @param $cabin
     * @param $phones
     * @param $segments
     * @param string|null $visitorId
     * @return string
     */
    public function generate(
        $requestIp,
        $projectId,
        $adults,
        $children,
        $infants,
        $cabin,
        $phones,
        $segments,
        ?string $visitorId = null
    ): string {
        $hashArray = [];
        $hashArray[] = $requestIp;
        $hashArray[] = $projectId;
        $hashArray[] = $adults;
        $hashArray[] = $children;
        $hashArray[] = $infants;
        $hashArray[] = $cabin;
        $hashArray[] = date('Y-m-d');

        if ($phones) {
            foreach ($phones as $phone) {
                $hashArray[] = $phone;
            }
        }
        if ($segments) {
            foreach ($segments as $segment) {
                $hashArray[] = $segment['origin'];
                $hashArray[] = $segment['destination'];
                $hashArray[] = $segment['departure'];
            }
        }
        if ($visitorId) {
            $hashArray[] = $visitorId;
        }

        return md5(implode('|', $hashArray));
    }
}
