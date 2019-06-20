<?php

namespace sales\repositories\airport;

use common\models\Airport;
use sales\repositories\NotFoundException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

class AirportRepository
{

    /**
     * @param int $id
     * @return Airport
     */
    public function get(int $id): Airport
    {
        if ($airport = Airport::findOne($id)) {
            return $airport;
        }
        throw new NotFoundException('Airport is not found.');
    }

    /**
     * @param string $iata
     * @return Airport
     */
    public function getByIata(string $iata): Airport
    {
        if ($airport = Airport::findOne(['iata' => $iata])) {
            return $airport;
        }
        throw new NotFoundException('Airport (' . $iata . ') is not found.');
    }

    /**
     * @param string $iata
     * @return bool
     */
    public function iataExists(string $iata): bool
    {
        if (Airport::find()->where(['iata' => $iata])->exists()) {
            return true;
        }
        return false;
    }

    public function getListByIata($iata = []): array
    {
        $data = [];
        foreach (Airport::find()->where(['iata' => $iata])->all() as $airport){
            $data[$airport['iata']] = ['name' => $airport['name'], 'city' => $airport['city'], 'country' => $airport['country']];
        }
        return $data;
    }

    /**
     * @param string $term
     * @return array|ActiveRecord[]
     */
    public function getListForSearch(string $term): array
    {
        $countTerm = mb_strlen($term);

        $query = Airport::find();

        if($countTerm < 4) {
            $query->orfilterWhere(['like', 'LOWER(iata)', $term]);
        }

        if($countTerm > 3) {
            $query->orFilterWhere(['like', 'LOWER(name)', $term]);
            $query->orFilterWhere(['like', 'LOWER(city)', $term]);
            $query->orFilterWhere(['like', 'LOWER(country)', $term]);
        }

        $query->orderBy(['iata' => SORT_ASC]);
        $query->limit(30);
        return $query->all();
    }
}
