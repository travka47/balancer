<?php

namespace App\Controller;

use AllowDynamicProperties;
use App\Entity\Process;
use App\Repository\ProcessRepository;
use App\Repository\WorkstationRepository;
use App\Service\ErrorResponseService;
use App\Service\ProcessService;
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
class ProcessApiController extends AbstractController
{
    private ErrorResponseService $errorResponseService;
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;
    private WorkstationRepository $workstationRepository;
    private ProcessRepository $processRepository;
    private ProcessService $processService;

    public function __construct(
        ErrorResponseService $errorResponseService,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        WorkstationRepository $workstationRepository,
        ProcessRepository $processRepository,
        ProcessService $processService,
    ) {
        $this->errorResponseService = $errorResponseService;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->workstationRepository = $workstationRepository;
        $this->processRepository = $processRepository;
        $this->processService = $processService;
    }

    #[Route('/process', name: 'process_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        try {
            $process = $this->serializer->deserialize(
                $request->getContent(),
                Process::class,
                'json',
            );
        } catch (NotNormalizableValueException $e) {
            return $this->errorResponseService->createErrorResponse($e);
        }

        $errors = $this->validator->validate($process);
        if (count($errors) > 0) {
            return $this->errorResponseService->createErrorResponse($errors);
        }

        $workstation = $this->workstationRepository->findFreeWorkstation($process);

        if (!$workstation) {
            return new JsonResponse(['error' => 'Unable to deploy the process'], Response::HTTP_EXPECTATION_FAILED);
        }

        $this->processService->deployProcess($process, $workstation);

        $jsonData = $this->serializer->serialize($process, 'json', ['groups' => 'process']);

        return new JsonResponse($jsonData, Response::HTTP_CREATED, [], true);
    }

    #[Route('/process/{id}', name: 'process_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $process = $this->processRepository->find($id);

        if (!$process) {
            return new JsonResponse(['error' => 'Process not found'], Response::HTTP_NOT_FOUND);
        }

        $this->processService->killProcess($process);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
