<?php


namespace App\Repositories\KeyModel;


class Key extends \App\Models\Key implements KeyInterface
{
    protected $connection = "pgsql";
}
