<?php

namespace sales\model\department\departmentPhoneProject\useCases\loadCsv;

use common\models\DepartmentPhoneProject;

class ImportPhones
{
    public function import($file): array
    {
        $rows = [];
        if (($handle = fopen($file, 'r')) !== false) {
            while (($row = fgetcsv($handle, 0, "\t")) !== false) {
                $rows[] = $this->processRow($row);
            }
            fclose($handle);
        }
        if (isset($rows[0])) {
            unset($rows[0]);
        }
        if ($rows) {
            $logs = $this->importPhones($rows);
        } else {
            $logs = ['Not found phones'];
        }
        return $logs;
    }

    private function importPhones(array $phones): array
    {
        $log = [];
        foreach ($phones as $key => $phone) {
            $error = $this->addPhone($phone);
            if ($error) {
                $log[] = $error;
            }
        }
        return $log ?: ['Done'];
    }

    private function addPhone(array $data): array
    {
        $phone = new DepartmentPhoneProject();
        $phone->load($data, '');
        if ($phone->save()) {
            return [];
        }
        return [
            'attributes' => $phone->getAttributes(),
            'errors' => $phone->getErrors(),
        ];
    }

    private function processRow($row): array
    {
        $data = [];
        $data['dpp_project_id'] = $row[0];
        $data['dpp_source_id'] = $row[1];
        $data['dpp_phone_number'] = $row[2];
        $data['dpp_dep_id'] = $row[3];
        $data['dpp_params'] = $row[4];
        $data['dpp_ivr_enable'] = $row[5] === 'TRUE';
        $data['dpp_enable'] = $row[6] === 'TRUE';
        $data['dpp_redial'] = $row[7] === 'TRUE';
        $data['dpp_description'] = $row[8];
        $data['dpp_default'] = $row[9] === 'TRUE';
        $data['dpp_show_on_site'] = $row[10] === 'TRUE';
        $data['dpp_phone_list_id'] = $row[11];
        return $data;
    }
}
