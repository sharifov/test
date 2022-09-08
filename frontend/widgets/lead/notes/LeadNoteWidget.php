<?php

namespace frontend\widgets\lead\notes;

use common\models\Note;
use yii\base\Widget;

class LeadNoteWidget extends Widget
{
    public int $leadID;

    public function run()
    {
        if (Note::find()->where(['lead_id' => $this->leadID])->exists()) {
            return $this->render('lead-note', [
                'leadID' => $this->leadID,
            ]);
        }
        return '';
    }
}
