<?php


namespace App\Classes\Helpers;


use Illuminate\Database\Eloquent\Model;

class ConductorsHelper
{
    /**
     * @param Model $model
     * @param int $cityId
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function getConductorsByCityId(Model $model, int $cityId)
    {
        return $model::with(["conductors" => function($q){
            $q->whereRaw("last_seen >= DATE_SUB(NOW(), INTERVAL 3 HOUR)");
        }])
            ->with("city")
            ->where("city_id", $cityId)
            ->get();
    }
}
