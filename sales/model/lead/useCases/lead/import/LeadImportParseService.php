<?php

namespace sales\model\lead\useCases\lead\import;

class LeadImportParseService
{
    /**
     * @param array $rows
     * @return Parse
     */
    public function parsing(array $rows): Parse
    {
        $forms = [];
        $errors = [];

        foreach ($rows as $rn => $row) {
            $rowData = explode(',', $row);
            if (!$rowData || $rn ===0) {
                continue;
            }

            try {
                $request = [];
                $request['ClientForm']['first_name'] = trim($rowData[0]);
                $request['ClientForm']['last_name'] = trim($rowData[1]);
                $request['ClientForm']['email'] = trim($rowData[2]);
                $request['ClientForm']['phone'] = trim(str_replace([' ', '-', '(', ')'], '', $rowData[3]));
                $request['LeadImportForm']['rating'] = (int) trim($rowData[4]);
                $request['LeadImportForm']['notes'] = str_replace('"', '', trim($rowData[5]));
                $request['LeadImportForm']['marketing_info_id'] = trim($rowData[6]);
                $request['LeadImportForm']['project_id'] = (int) trim($rowData[7]);

                $form = new LeadImportForm();

                if ($form->load($request)) {
                    $forms[$rn] = $form;
                } else {
                    $errors[$rn] = 'Cant load.';
                }

            } catch (\Throwable $e) {
                $errors[$rn] = $e->getMessage();
            }

        }

        return new Parse($errors, $forms);
    }
}
