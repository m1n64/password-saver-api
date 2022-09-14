<?php


namespace App\Interfaces;


use Illuminate\Database\Eloquent\Model;

interface ParserInterface
{
    /**
     * @return mixed
     */
    public function execute(Model $model, int $cityId) : void;

}
