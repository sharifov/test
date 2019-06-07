<?php

namespace sales\repositories\airport;

use common\models\Airport;
use sales\repositories\NotFoundException;

class AirportRepository
{
    public function get($id): Airport
    {
        if (!$airport = Airport::findOne($id)) {
            throw new NotFoundException('Airport is not found.');
        }
        return $airport;
    }

    public function getByIata($iata): Airport
    {
        if (!$airport = Airport::findOne(['iata' => $iata])) {
            throw new NotFoundException('Airport (' . $iata . ') is not found.');
        }
        return $airport;
    }

    public function iataExists($iata): bool
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

    public function getListForSearch($term)
    {
        $query = Airport::find();
        $query->filterWhere(['like', 'LOWER(iata)', $term]);
        $query->orFilterWhere(['like', 'LOWER(name)', $term]);
        $query->orFilterWhere(['like', 'LOWER(city)', $term]);
        $query->orFilterWhere(['like', 'LOWER(country)', $term]);
        $query->orderBy(['iata' => SORT_ASC]);
        $query->limit(30);
        return $query->all();
    }
}
