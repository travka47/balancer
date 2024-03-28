<?php

namespace App\Controller;

use AllowDynamicProperties;
use App\Entity\Process;
use App\Repository\ProcessRepository;
use App\Repository\WorkstationRepository;
use App\Service\ErrorResponseService;
use App\Service\ProcessService;
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
class ProcessApiController extends AbstractController
{
    private ErrorResponseService $errorResponseService;
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;
    private WorkstationRepository $workstationRepository;
    private ProcessRepository $processRepository;
    private ProcessService $processService;

    public function __construct(
        ErrorResponseService  $errorResponseService,
        SerializerInterface   $serializer,
        ValidatorInterface    $validator,
        WorkstationRepository $workstationRepository,
        ProcessRepository     $processRepository,
        ProcessService        $processService,
    )
    {
        $this->errorResponseService = $errorResponseService;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->workstationRepository = $workstationRepository;
        $this->processRepository = $processRepository;
        $this->processService = $processService;
    }

    #[Route('/process', name: 'process_create', methods: ['POST'])]
    #[OA\RequestBody(
        required: true,
        content: new Model(type: Process::class, groups: ['create_process'])
    )]
    #[OA\Response(
        response: 201,
        description: 'Returns created process',
        content: new Model(type: Process::class, groups: ['process'])
    )]
    #[OA\Response(
        response: 417,
        description: 'Unable to deploy the process',
        content: new OA\JsonContent(
            type: 'object', example: ['error' => 'Unable to deploy the process.']
        )
    )]
    #[OA\Response(
        response: 422,
        description: 'Validation error',
        content: new OA\JsonContent(
            type: 'object',
            example: [
                'errors' => [
                    'requiredRam' => 'This value should not be blank.',
                    'requiredCpu' => 'This value should be greater than 0.'
                ]
            ]
        )
    )]
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
            return new JsonResponse(['error' => 'Unable to deploy the process.'], Response::HTTP_EXPECTATION_FAILED);
        }

        $this->processService->deployProcess($process, $workstation);

        $jsonData = $this->serializer->serialize($process, 'json', ['groups' => 'process']);

        return new JsonResponse($jsonData, Response::HTTP_CREATED, [], true);
    }

    #[Route('/process/{id}', name: 'process_delete', methods: ['DELETE'])]
    #[OA\Response(
        response: 204,
        description: 'Returns an empty response, if the process was deleted',
    )]
    #[OA\Response(
        response: 404,
        description: "Process with given ID doesn't exist",
        content: new OA\JsonContent(
            type: 'object', example: ['error' => 'Process not found.']
        )
    )]
    public function delete(int $id): JsonResponse
    {
        $process = $this->processRepository->find($id);

        if (!$process) {
            return new JsonResponse(['error' => 'Process not found.'], Response::HTTP_NOT_FOUND);
        }

        $this->processService->killProcess($process);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
