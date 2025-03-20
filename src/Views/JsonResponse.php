<?php

namespace App\Views;

class JsonResponse
{
    public function render(mixed $data, int $status = 200, bool $exit = true): void
    {
        $this->setResponseHeaders($status);
        echo json_encode($data, JSON_THROW_ON_ERROR);
        if ($exit) {
            exit;
        }
    }

    public function error(string $message, int $status = 400, bool $exit = true): void
    {
        $this->setResponseHeaders($status);
        echo json_encode(['error' => $message], JSON_THROW_ON_ERROR);
        if ($exit) {
            exit;
        }
    }

    private function setResponseHeaders(int $status): void
    {
        header('Content-Type: application/json', true, $status);
    }
}