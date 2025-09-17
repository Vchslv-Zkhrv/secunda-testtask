<?php

namespace App\Http\Controllers;

use App\DTOs\Model\CompanyDTO;
use App\DTOs\Requests\BusinessActivity\CreateBusinessActivityRequest;
use App\DTOs\Requests\BusinessActivity\MoveBusinessActivityRequest;
use App\DTOs\Requests\BusinessActivity\UpdateBusinessActivityRequest;
use App\DTOs\Tree\BusinessActivityTreeItem;
use App\Models\BusinessActivity;
use App\Services\BusinessActivityService;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Schemantic\Exception\SchemaException;
use Schemantic\Exception\ValidationException;
use OpenApi\Attributes as OA;

#[OA\PathItem(
    path: "/api/business-activity",
)]
class BusinessActivityController extends Controller
{
    #[OA\Get(
        path: '/api/business-activity',
        description: 'Get full tree of activities',
        tags: ['BusinessActivity'],
        security: [['http' => []]],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Tree of activities',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        type: 'object',
                        ref: BusinessActivityTreeItem::class
                    )
                ),
            ),
        ]
    )]
    public function index(BusinessActivityService $businessActivityService): JsonResponse
    {
        $tree = $businessActivityService->getFullTree();
        return new JsonResponse($tree);
    }

    #[OA\Post(
        path: '/api/business-activity',
        description: 'Create an activity',
        tags: ['BusinessActivity'],
        security: [['http' => []]],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                type: 'object',
                ref: CreateBusinessActivityRequest::class,
            )
        ),
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Activity created',
            ),
            new OA\Response(
                response: Response::HTTP_BAD_REQUEST,
                description: 'Bad request',
            ),
            new OA\Response(
                response: Response::HTTP_NOT_FOUND,
                description: 'Cannot find parent activity by id',
            ),
            new OA\Response(
                response: Response::HTTP_CONFLICT,
                description: 'Id already taken|Max tree depth exceeded',
            ),
        ]
    )]
    public function store(
        Request $request,
        BusinessActivityService $businessActivityService,
    ): Response|JsonResponse {
        try {
            $data = CreateBusinessActivityRequest::fromArray($request->all());
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

        $directParent = null;
        if ($data->parentId !== null) {
            $directParent = BusinessActivity::query()->find($data->parentId); // прямой родитель
            if ($directParent === nulL) {
                return new Response(
                    "invalid parentId: no such parent activity",
                    status: Response::HTTP_NOT_FOUND,
                );
            }
        }

        $activity = new BusinessActivity();
        $activity->id = $data->id;
        $activity->name = $data->name;

        try {
            $tree = $businessActivityService->addOne($activity, $directParent, true);
        } catch (UniqueConstraintViolationException $ue) {
            return new Response(
                status: Response::HTTP_CONFLICT
            );
        } catch (\LogicException $le) {
            return new Response(
                $le->getMessage(),
                status: Response::HTTP_CONFLICT
            );
        }

        return new JsonResponse($tree);
    }

    #[OA\Get(
        path: '/api/business-activity/{id}',
        description: 'Get activity subtree',
        tags: ['BusinessActivity'],
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
                description: 'Tree of activities',
                content: new OA\JsonContent(
                    type: 'object',
                    ref: BusinessActivityTreeItem::class,
                ),
            ),
            new OA\Response(
                response: Response::HTTP_NOT_FOUND,
                description: 'Cannot find activity by id',
            ),
        ]
    )]
    public function show(
        string $id,
        BusinessActivityService $businessActivityService,
    ): Response|JsonResponse {
        $activity = BusinessActivity::query()->find($id);
        if ($activity === null) {
            return new Response(status: Response::HTTP_NOT_FOUND);
        }

        $subtree = $businessActivityService->getSubTree($activity);

        if ($subtree) {
            return new JsonResponse($subtree);
        }

        return new Response(status: Response::HTTP_NOT_FOUND);
    }

    #[OA\Get(
        path: '/api/business-activity/{id}/companies',
        description: 'Get companies by activity',
        tags: ['BusinessActivity'],
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
                description: 'Cannot find activity by id',
            ),
        ]
    )]
    public function listCompanies(
        string $id,
        BusinessActivityService $businessActivityService,
    ): Response|JsonResponse {
        $activity = BusinessActivity::query()->find($id);
        if ($activity === null) {
            return new Response(status: Response::HTTP_NOT_FOUND);
        }

        $companies = $businessActivityService->getChildCompanies($activity);

        return new JsonResponse($companies);
    }

    #[OA\Put(
        path: '/api/business-activity/{id}',
        description: 'Update activity',
        tags: ['BusinessActivity'],
        security: [['http' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path'
            )
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                type: 'object',
                ref: UpdateBusinessActivityRequest::class,
            )
        ),
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Activity updated',
            ),
            new OA\Response(
                response: Response::HTTP_NOT_FOUND,
                description: 'Cannot find activity by id',
            ),
        ]
    )]
    public function update(
        string $id,
        Request $request,
        BusinessActivityService $businessActivityService,
    ): Response {
        $activity = BusinessActivity::query()->find($id);
        if ($activity === null) {
            return new Response(status: Response::HTTP_NOT_FOUND);
        }

        try {
            $data = UpdateBusinessActivityRequest::fromArray($request->all());
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

        $activity->name = $data->name;
        $activity->save();

        return new Response();
    }

    #[OA\Patch(
        path: '/api/business-activity/{id}',
        description: 'Move activitity within a tree',
        tags: ['BusinessActivity'],
        security: [['http' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path'
            )
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                type: 'object',
                ref: MoveBusinessActivityRequest::class,
            )
        ),
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'New activity tree',
                content: new OA\JsonContent(
                    type: 'object',
                    ref: BusinessActivityTreeItem::class,
                ),
            ),
            new OA\Response(
                response: Response::HTTP_NOT_FOUND,
                description: 'Cannot find activity by id',
            ),
            new OA\Response(
                response: Response::HTTP_CONFLICT,
                description: 'Max tree depth exceeded',
            ),
        ]
    )]
    public function moveTree(
        string $id,
        Request $request,
        BusinessActivityService $businessActivityService,
    ): Response|JsonResponse {
        $activity = BusinessActivity::query()->find($id);
        if ($activity === null) {
            return new Response(status: Response::HTTP_NOT_FOUND);
        }

        try {
            $data = MoveBusinessActivityRequest::fromArray($request->all());
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

        $newParent = null;
        if ($data->parentId !== null) {
            $newParent = BusinessActivity::query()->find($data->parentId);
            if ($newParent === null) {
                return new Response(
                    "Cannot update tree: no such parentId",
                    status: Response::HTTP_NOT_FOUND
                );
            }
        }

        try {
            $tree = $businessActivityService->changeParent($activity, $newParent, true);
        } catch (\LogicException $le) {
            return new Response(
                $le->getMessage(),
                status: Response::HTTP_CONFLICT
            );
        }

        return new JsonResponse($tree);
    }

    #[OA\Delete(
        path: '/api/business-activity/{id}',
        description: 'Delete a company',
        tags: ['BusinessActivity'],
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
                description: 'Activity deleted',
            ),
            new OA\Response(
                response: Response::HTTP_NOT_FOUND,
                description: 'Cannot find activity by id',
            ),
        ]
    )]
    public function destroy(
        string $id,
        BusinessActivityService $businessActivityService,
    ): Response {
        $activity = BusinessActivity::query()->find($id);
        if ($activity === null) {
            return new Response(status: Response::HTTP_NOT_FOUND);
        }

        $businessActivityService->deleteSubTree($activity);

        return new Response();
    }
}
