<?php

namespace App\Controller;

use AllowDynamicProperties;
use App\Entity\Workstation;
use App\Repository\WorkstationRepository;
use App\Service\ErrorResponseService;
use App\Service\WorkstationService;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OpenApi\Attributes as OA;

#[AllowDynamicProperties]
#[Route('/api', name: 'api_')]
class WorkstationApiController extends AbstractController
{
    private ErrorResponseService $errorResponseService;
    private EntityManagerInterface $entityManager;
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;
    private WorkstationRepository $workstationRepository;
    private WorkstationService $workstationService;

    public function __construct(
        ErrorResponseService   $errorResponseService,
        EntityManagerInterface $entityManager,
        SerializerInterface    $serializer,
        ValidatorInterface     $validator,
        WorkstationRepository  $workstationRepository,
        WorkstationService     $workstationService,
    )
    {
        $this->errorResponseService = $errorResponseService;
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->workstationRepository = $workstationRepository;
        $this->workstationService = $workstationService;
    }

    #[Route('/workstation', name: 'workstation_list', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns all workstations with deployed processes and free resource',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(
                ref: new Model(type: Workstation::class, groups: ['workstation', 'workstation_processes', 'workstation_resource'])
            )
        )
    )]
    public function index(): JsonResponse
    {
        $workstations = $this->workstationRepository->findAll();

        $jsonData = $this->serializer->serialize($workstations, 'json', [
            'groups' => ['workstation', 'workstation_processes', 'workstation_resource'],
        ]);

        return new JsonResponse($jsonData, Response::HTTP_OK, [], true);
    }

    #[Route('/workstation', name: 'workstation_create', methods: ['POST'])]
    #[OA\RequestBody(
        required: true,
        content: new Model(type: Workstation::class, groups: ['create_workstation']),
    )]
    #[OA\Response(
        response: 201,
        description: 'Returns created workstation',
        content: new Model(type: Workstation::class, groups: ['workstation'])
    )]
    #[OA\Response(
        response: 422,
        description: 'Validation error',
        content: new OA\JsonContent(
            type: 'object',
            example: [
                'errors' => [
                    'totalRam' => 'This value should not be blank.',
                    'totalCpu' => 'This value should be greater than 0.'
                ]
            ]
        )
    )]
    public function create(Request $request): Response
    {
        try {
            $workstation = $this->serializer->deserialize(
                $request->getContent(),
                Workstation::class,
                'json',
            );
        } catch (NotNormalizableValueException $e) {
            return $this->errorResponseService->createErrorResponse($e);
        }

        $errors = $this->validator->validate($workstation);
        if (count($errors) > 0) {
            return $this->errorResponseService->createErrorResponse($errors);
        }

        $this->workstationService->deployWorkstation($workstation);

        $jsonData = $this->serializer->serialize($workstation, 'json', ['groups' => 'workstation']);

        return new JsonResponse($jsonData, Response::HTTP_CREATED, [], true);
    }

    #[Route('/workstation/{id}', name: 'workstation_delete', methods: ['DELETE'])]
    #[OA\Response(
        response: 204,
        description: 'Returns an empty response, if the workstation was deleted',
    )]
    #[OA\Response(
        response: 404,
        description: "Workstation with given ID doesn't exist",
        content: new OA\JsonContent(
            type: 'object', example: ['error' => 'Workstation not found.']
        )
    )]
    public function delete(int $id): JsonResponse
    {
        $workstation = $this->workstationRepository->find($id);

        if (!$workstation) {
            return new JsonResponse(['error' => 'Workstation not found.'], Response::HTTP_NOT_FOUND);
        }

        $this->workstationService->killWorkstation($workstation);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
