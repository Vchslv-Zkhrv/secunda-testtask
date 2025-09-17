<?php

namespace App\Http\Controllers;

use App\DTOs\Model\BuildingDTO;
use App\DTOs\Model\CompanyDTO;
use App\DTOs\Requests\Building\CreateBuildingRequest;
use App\DTOs\Requests\Building\UpdateBuildingRequest;
use App\DTOs\Spatial\Circle;
use App\DTOs\Spatial\Rect;
use App\Models\Building;
use App\Services\BuildingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\UniqueConstraintViolationException;
use MatanYadaev\EloquentSpatial\Objects\Point;
use Schemantic\Exception\SchemaException;
use Schemantic\Exception\ValidationException;
use OpenApi\Attributes as OA;

#[OA\PathItem(
    path: "/api/building",
)]
class BuildingController extends Controller
{
    #[OA\Get(
        path: '/api/building',
        description: 'Get list of all buildings',
        tags: ['Building'],
        security: [['http' => []]],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'List of buildings',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        type: 'object',
                        ref: BuildingDTO::class
                    )
                ),
            ),
        ]
    )]
    public function index(): Response
    {
        $buildings = Building::all()->all();

        return new Response(
            array_map(
                fn (Building $b) => BuildingDTO::fromModel($b),
                $buildings
            )
        );
    }

    #[OA\Post(
        path: '/api/building/geo/rect',
        description: 'Find buildings inside a rect area',
        tags: ['Building'],
        security: [['http' => []]],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                type: 'object',
                ref: Rect::class,
            )
        ),
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'List of buildings',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        type: 'object',
                        ref: BuildingDTO::class
                    )
                ),
            ),
            new OA\Response(
                response: Response::HTTP_BAD_REQUEST,
                description: 'Bad request',
            ),
        ]
    )]
    public function findInRect(
        Request $request,
        BuildingService $buildingService,
    ): Response|JsonResponse {
        try {
            $rect = Rect::fromArray($request->all());
        } catch (ValidationException $ve) {
            return new Response(
                $ve->getMessage(),
                status: Response::HTTP_BAD_REQUEST
            );
        } catch (SchemaException $se) {
            return new Response(
                status: Response::HTTP_BAD_REQUEST
            );
        }

        $buildings = $buildingService->findInRect($rect);
        return new JsonResponse($buildings);
    }

    #[OA\Post(
        path: '/api/building/geo/circle',
        description: 'Find buildings inside a circle area',
        tags: ['Building'],
        security: [['http' => []]],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                type: 'object',
                ref: Circle::class,
            )
        ),
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'List of buildings',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        type: 'object',
                        ref: BuildingDTO::class
                    )
                ),
            ),
            new OA\Response(
                response: Response::HTTP_BAD_REQUEST,
                description: 'Bad request',
            ),
        ]
    )]
    public function findInCircle(
        Request $request,
        BuildingService $buildingService,
    ): Response|JsonResponse {
        try {
            $circle = Circle::fromArray($request->all());
        } catch (ValidationException $ve) {
            return new Response(
                $ve->getMessage(),
                status: Response::HTTP_BAD_REQUEST
            );
        } catch (SchemaException $se) {
            return new Response(
                status: Response::HTTP_BAD_REQUEST
            );
        }

        $buildings = $buildingService->findInCircle($circle);
        return new JsonResponse($buildings);
    }

    #[OA\Post(
        path: '/api/building',
        description: 'Create a building',
        tags: ['Building'],
        security: [['http' => []]],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                type: 'object',
                ref: CreateBuildingRequest::class,
            )
        ),
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Building created',
            ),
            new OA\Response(
                response: Response::HTTP_BAD_REQUEST,
                description: 'Bad request',
            ),
            new OA\Response(
                response: Response::HTTP_CONFLICT,
                description: 'Id already taken',
            )
        ]
    )]
    public function store(Request $request): Response
    {
        try {
            $data = CreateBuildingRequest::fromArray($request->all());
        } catch (ValidationException $ve) {
            return new Response(
                $ve->getMessage(),
                status: Response::HTTP_BAD_REQUEST
            );
        } catch (SchemaException $se) {
            return new Response(
                status: Response::HTTP_BAD_REQUEST
            );
        }

        $building = new Building();
        $building->id = $data->id;
        $building->address = $data->address;
        $building->coordinates = new Point(
            $data->coordinates->latitude,
            $data->coordinates->longitude
        );

        try {
            $building->save();
        } catch (UniqueConstraintViolationException $ue) {
            return new Response(
                status: Response::HTTP_CONFLICT
            );
        }

        return new Response();
    }

    #[OA\Get(
        path: '/api/building/{id}',
        description: 'Get info about specific building',
        tags: ['Building'],
        security: [['http' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path'
            )
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Building',
                content: new OA\JsonContent(
                    type: 'object',
                    ref: BuildingDTO::class,
                ),
            ),
            new OA\Response(
                response: Response::HTTP_NOT_FOUND,
                description: 'Cannot find building by id',
            ),
        ]
    )]
    public function show(string $id): Response|JsonResponse
    {
        $building = Building::query()->find($id);
        if ($building === null) {
            return new Response(
                status: Response::HTTP_NOT_FOUND
            );
        }

        return new JsonResponse(BuildingDTO::fromModel($building));
    }

    #[OA\Get(
        path: '/api/building/{id}/companies',
        description: 'List companies in building',
        tags: ['Building'],
        security: [['http' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path'
            )
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'List of companies',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        type: 'object',
                        ref: CompanyDTO::class
                    )
                ),
            ),
            new OA\Response(
                response: Response::HTTP_NOT_FOUND,
                description: 'Cannot find building by id',
            ),
        ]
    )]
    public function listCompanies(string $id): Response|JsonResponse
    {
        $building = Building::query()->find($id);
        if ($building === null) {
            return new Response(
                status: Response::HTTP_NOT_FOUND
            );
        }

        $companies = $building->companies()->get()->toArray();
        return new JsonResponse(
            CompanyDTO::fromArrayMultiple($companies)
        );
    }

    #[OA\Put(
        path: '/api/building/{id}',
        description: 'Update building',
        tags: ['Building'],
        security: [['http' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path'
            )
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Building updated',
            ),
            new OA\Response(
                response: Response::HTTP_NOT_FOUND,
                description: 'Cannot find building by id',
            ),
            new OA\Response(
                response: Response::HTTP_BAD_REQUEST,
                description: 'Bad request',
            ),
        ]
    )]
    public function update(
        string $id,
        Request $request
    ): Response {
        $building = Building::query()->find($id);
        if ($building === null) {
            return new Response(
                'No such building',
                status: Response::HTTP_NOT_FOUND
            );
        }

        try {
            $data = UpdateBuildingRequest::fromArray($request->all());
        } catch (ValidationException $ve) {
            return new Response(
                $ve->getMessage(),
                status: Response::HTTP_BAD_REQUEST
            );
        } catch (SchemaException $se) {
            return new Response(
                status: Response::HTTP_BAD_REQUEST
            );
        }

        $building->address = $data->address;
        $building->coordinates = new Point(
            $data->coordinates->latitude,
            $data->coordinates->longitude
        );
        $building->save();

        return new Response();
    }

    #[OA\Delete(
        path: '/api/building/{id}',
        description: 'Delete building',
        tags: ['Building'],
        security: [['http' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path'
            )
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Building updated',
            ),
            new OA\Response(
                response: Response::HTTP_NOT_FOUND,
                description: 'Cannot find building by id',
            ),
        ]
    )]
    public function destroy(string $id): Response
    {
        $building = Building::query()->find($id);
        if ($building === null) {
            return new Response(
                'No such building',
                status: Response::HTTP_NOT_FOUND
            );
        }

        $building->delete();
        return new Response();
    }
}
