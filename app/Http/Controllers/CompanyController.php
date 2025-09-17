<?php

namespace App\Http\Controllers;

use App\DTOs\Model\CompanyDTO;
use App\DTOs\Requests\Company\CreateCompanyRequest;
use App\DTOs\Requests\Company\SearchCompanyRequest;
use App\DTOs\Requests\Company\UpdateCompanyRequest;
use App\Models\Building;
use App\Models\Company;
use App\Services\CompanyService;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Schemantic\Exception\SchemaException;
use Schemantic\Exception\ValidationException;
use OpenApi\Attributes as OA;

#[OA\PathItem(
    path: "/api/company",
)]
class CompanyController extends Controller
{
    #[OA\Get(
        path: '/api/company',
        description: 'Get list of all companies',
        tags: ['Company'],
        security: [['http' => []]],
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
        ]
    )]
    public function index(): JsonResponse
    {
        $companies = Company::all()->toArray();
        return new JsonResponse(
            CompanyDTO::fromArrayMultiple($companies, byAlias: false)
        );
    }

    #[OA\Post(
        path: '/api/company/search',
        description: 'Search companies by name',
        tags: ['Company'],
        security: [['http' => []]],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                type: 'object',
                ref: SearchCompanyRequest::class,
            )
        ),
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
                response: Response::HTTP_BAD_REQUEST,
                description: 'Bad request',
            )
        ]
    )]
    public function search(
        Request $request,
        CompanyService $companyService,
    ): Response|JsonResponse {
        try {
            $data = SearchCompanyRequest::fromArray($request->all());
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

        $companies = $companyService->searchCompanies($data->search ?? '')->toArray();
        return new JsonResponse(
            CompanyDTO::fromArrayMultiple($companies, byAlias: false)
        );
    }

    #[OA\Post(
        path: '/api/company',
        description: 'Create a company',
        tags: ['Company'],
        security: [['http' => []]],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                type: 'object',
                ref: CreateCompanyRequest::class,
            )
        ),
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Company created',
            ),
            new OA\Response(
                response: Response::HTTP_BAD_REQUEST,
                description: 'Bad request',
            ),
            new OA\Response(
                response: Response::HTTP_NOT_FOUND,
                description: 'Cannot find building by buildingId',
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
            $data = CreateCompanyRequest::fromArray($request->all());
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

        $building = Building::query()->find($data->buildingId);
        if ($building === null) {
            return new Response(
                "Cannot create company: no such buildingId",
                status: Response::HTTP_NOT_FOUND
            );
        }

        $company = new Company();
        $company->id = $data->id;
        $company->name = $data->name;
        $company->building_id = $building->id;

        try {
            $company->save();
        } catch (UniqueConstraintViolationException $ue) {
            return new Response(
                "Cannot create company: id is taken already",
                status: Response::HTTP_CONFLICT,
            );
        }

        return new Response();
    }

    #[OA\Get(
        path: '/api/company/{id}',
        description: 'Get info about specific company',
        tags: ['Company'],
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
                description: 'Company',
                content: new OA\JsonContent(
                    type: 'object',
                    ref: CompanyDTO::class,
                ),
            ),
            new OA\Response(
                response: Response::HTTP_NOT_FOUND,
                description: 'Cannot find company by id',
            ),
        ]
    )]
    public function show(string $id): Response|JsonResponse
    {
        $company = Company::query()->find($id);
        if ($company === null) {
            return new Response(
                status: Response::HTTP_NOT_FOUND,
            );
        }

        return new JsonResponse(
            CompanyDTO::fromArray($company->toArray(), byAlias: false, parse: true)
        );
    }

    #[OA\Put(
        path: '/api/company/{id}',
        description: 'Update a company',
        tags: ['Company'],
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
                ref: UpdateCompanyRequest::class,
            )
        ),
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Company updated',
            ),
            new OA\Response(
                response: Response::HTTP_NOT_FOUND,
                description: 'Cannot find company by id',
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
        $company = Company::query()->find($id);
        if ($company === null) {
            return new Response(
                status: Response::HTTP_NOT_FOUND
            );
        }

        try {
            $data = UpdateCompanyRequest::fromArray($request->all());
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

        $company->name = $data->name;
        $company->save();

        return new Response();
    }

    #[OA\Delete(
        path: '/api/company/{id}',
        description: 'Delete a company',
        tags: ['Company'],
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
                description: 'Company deleted',
            ),
            new OA\Response(
                response: Response::HTTP_NOT_FOUND,
                description: 'Cannot find company by id',
            ),
        ]
    )]
    public function destroy(string $id): Response
    {
        $company = Company::query()->find($id);
        if ($company === null) {
            return new Response(
                status: Response::HTTP_NOT_FOUND
            );
        }

        $company->delete();

        return new Response();
    }
}
