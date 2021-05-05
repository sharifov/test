<?php

namespace modules\attraction\src\services;

use modules\attraction\models\AttractionQuote;

class RequestPdfDataGenerator
{
    public function generate(AttractionQuote $quote): array
    {
        $order = $quote->atnqProductQuote->pqOrder ?? null;
        if (!$order) {
            throw new \DomainException('Not found relation Order. AttractionQuoteId: ' . $quote->atnq_id);
        }

        $projectData = [];

        if ($order->or_project_id) {
            $project = $order->project;
            $projectContactInfo = [];
            if ($project->contact_info) {
                $projectContactInfo = @json_decode($project->contact_info, true);
            }
            $projectData = [
                'name' => $project->name,
                'url' => $project->link,
                'address' => $projectContactInfo['address'] ?? '',
                'phone' => $projectContactInfo['phone'] ?? '',
                'email' => $projectContactInfo['email'] ?? '',
            ];
        }

        return [
            'project' => $projectData,
            'project_key' => $project->project_key ?? '',
            'attraction' => $quote->atnqProductQuote->serialize(),
            'order' => $order->serialize(),
            'content' => '',
            'subject' => '',
        ];
    }
}
