<?php

namespace App\Controller;

use AllowDynamicProperties;
use App\Entity\Workstation;
use App\Repository\WorkstationRepository;
use App\Service\ErrorResponseService;
use App\Service\WorkstationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
        ErrorResponseService $errorResponseService,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        WorkstationRepository $workstationRepository,
        WorkstationService $workstationService,
    ) {
        $this->errorResponseService = $errorResponseService;
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->workstationRepository = $workstationRepository;
        $this->workstationService = $workstationService;
    }

    #[Route('/workstation', name: 'workstation_list', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $workstations = $this->workstationRepository->findAll();

        $jsonData = $this->serializer->serialize($workstations, 'json', [
            'groups' => ['workstation', 'workstation_processes', 'workstation_resource'],
        ]);

        return new JsonResponse($jsonData, Response::HTTP_OK, [], true);
    }

    #[Route('/workstation', name: 'workstation_create', methods: ['POST'])]
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
    public function delete(int $id): JsonResponse
    {
        $workstation = $this->workstationRepository->find($id);

        if (!$workstation) {
            return new JsonResponse(['error' => 'Workstation not found'], Response::HTTP_NOT_FOUND);
        }

        $this->workstationService->killWorkstation($workstation);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
