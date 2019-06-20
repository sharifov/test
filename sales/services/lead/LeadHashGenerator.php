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
     * @return string
     */
    public function generate($requestIp, $projectId, $adults, $children, $infants, $cabin, $phones, $segments): string
    {
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

        return md5(implode('|', $hashArray));
    }

}
