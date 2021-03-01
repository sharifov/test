<?php

namespace modules\order\src\jobs;

use modules\order\src\entities\order\Order;

class EmailConfirmationData
{
    public function generate(Order $order): array
    {
        $projectData = [];
        if ($order->or_lead_id) {
            if ($order->orLead->project_id) {
                $project = $order->orLead->project;
                $projectContactInfo = [];
                if ($project->contact_info) {
                    $projectContactInfo = @json_decode($project->contact_info, true);
                }
                $content_data['project'] = [
                    'name'      => $project->name,
                    'url'       => $project->link,
                    'address'   => $projectContactInfo['address'] ?? '',
                    'phone'     => $projectContactInfo['phone'] ?? '',
                    'email'     => $projectContactInfo['email'] ?? '',
                ];
            }
        }

        return [
            'project' => $projectData,
            'order' => $order->serialize(),
            'content' => '',
            'subject' => '',
        ];
    }
}
