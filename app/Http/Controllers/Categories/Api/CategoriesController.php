<?php

namespace App\Http\Controllers\Categories\Api;

use App\Http\Controllers\Controller;
use App\Models\Categories\Category;
use App\Traits\JsonResponseTrait;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class CategoriesController extends Controller
{
    use JsonResponseTrait;

    /**
     * @var Category
     */
    protected Category $category;

    /**
     * CategoriesController constructor.
     * @param Category $category
     */
    public function __construct(
        Category $category
    )
    {
        $this->category = $category;
    }

    /**
     * @OA\Get(
     *     path="/categories/all",
     *     description="Get all categories",
     *     tags={"Categories"},
     *     operationId="Categories All",
     *     security={{"apiAuth": {}}},
     *     @OA\Response(
     *          response=200,
     *          description="Success Answer",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="success", type="bool", example="true"),
     *              @OA\Property(property="message", type="string", example=""),
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/Category")
     *          )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unathorized",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="Unathorized."),
     *          )
     *     )
     * )
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function all(Request $request)
    {
        $category = $this->category::all();

        return $this->success("", $category);
    }
}
