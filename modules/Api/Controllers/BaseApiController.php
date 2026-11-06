<?php

namespace Modules\Api\Controllers;

use CodeIgniter\RESTful\ResourceController;

class BaseApiController extends ResourceController
{
    protected $format = 'json';

    /**
     * Success response
     */
    protected function successResponse($data = null, string $message = 'Success', int $statusCode = 200)
    {
        return $this->response->setStatusCode($statusCode)->setJSON([
            'status' => 'success',
            'data' => $data,
            'message' => $message
        ]);
    }

    /**
     * Error response
     */
    protected function errorResponse(string $message = 'Error', int $statusCode = 400, $errors = null)
    {
        $response = [
            'status' => 'error',
            'message' => $message
        ];
        if ($errors !== null) {
            $response['errors'] = $errors;
        }
        return $this->response->setStatusCode($statusCode)->setJSON($response);
    }

    /**
     * Not found response
     */
    protected function notFoundResponse(string $message = 'Resource not found')
    {
        return $this->errorResponse($message, 404);
    }

    /**
     * Paginated response with cursor-based pagination
     */
    protected function paginatedResponse(array $data, ?int $nextCursor, bool $hasMore, int $total = 0, int $perPage = 20)
    {
        return $this->response->setJSON([
            'status' => 'success',
            'data' => $data,
            'meta' => [
                'cursor' => $nextCursor,
                'has_more' => $hasMore,
                'total' => $total,
                'per_page' => $perPage,
            ],
            'message' => 'Success'
        ]);
    }

    /**
     * Get authenticated user from JWT payload (set by ApiGuard)
     */
    protected function getAuthUser()
    {
        return $this->request->user ?? null;
    }

    /**
     * Get requested fields from query string (?fields=id,name,logo)
     */
    protected function getRequestedFields(array $allowedFields = []): ?array
    {
        $fields = $this->request->getGet('fields');
        if (empty($fields)) {
            return null;
        }

        $requested = array_map('trim', explode(',', $fields));

        if (!empty($allowedFields)) {
            $requested = array_intersect($requested, $allowedFields);
        }

        return !empty($requested) ? $requested : null;
    }

    /**
     * Get cursor and limit from query string
     */
    protected function getCursorParams(int $defaultLimit = 20): array
    {
        $cursor = (int) ($this->request->getGet('cursor') ?? 0);
        $limit = (int) ($this->request->getGet('limit') ?? $defaultLimit);
        $limit = min($limit, 100); // Max 100 per page

        return ['cursor' => $cursor, 'limit' => $limit];
    }

    /**
     * Build full image URL from relative path
     */
    protected function imageUrl(?string $path, string $size = 'full'): ?string
    {
        if (empty($path)) {
            return null;
        }
        return base_url($path);
    }

    /**
     * Validation error response
     */
    protected function validationErrorResponse()
    {
        return $this->errorResponse('Validation failed', 422, $this->validator->getErrors());
    }
}
