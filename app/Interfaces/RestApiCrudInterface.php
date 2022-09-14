<?php


namespace App\Interfaces;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\ResponseTrait;

interface RestApiCrudInterface
{

    /**
     * @return Response
     */
    public function all() : Response;

    /**
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function get(Request $request, $id) : Response;

    /**
     * @param Request $request
     * @return Response
     */
    public function insert(Request $request) : Response;

    /**
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function update(Request $request, $id) : Response;

    /**
     * @param $id
     * @return Response
     */
    public function delete($id) : Response;
}
