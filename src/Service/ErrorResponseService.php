<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;

class ErrorResponseService
{
    public function createErrorResponse(mixed $errors): JsonResponse
    {
        $errorMessages = [];

        if ($errors instanceof NotNormalizableValueException) {
            $errorMessages[$errors->getPath()] = $errors->getMessage();
        } else {
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
        }

        return new JsonResponse(['errors' => $errorMessages], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}