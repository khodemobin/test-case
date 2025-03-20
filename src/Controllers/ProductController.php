<?php

namespace App\Controllers;

use App\Models\CreateProductRequestDTO;
use App\Models\Product;
use App\Models\ProductDTOFactory;
use App\Repositories\ProductRepositoryInterface;
use App\Validation\Validator;
use App\Views\JsonResponse;
use Ramsey\Uuid\Uuid;
use DateTime;

class ProductController
{
    public function __construct(
        private ProductRepositoryInterface $repository,
        private JsonResponse $view,
        private ProductDTOFactory $dtoFactory,
        private Validator $validator
    ) {}

    public function create(array $data, bool $exit = true): void
    {
        $requestDTO = new CreateProductRequestDTO($data);
        $isValid = $this->validator->validate($requestDTO, $exit);

        if (!$isValid) {
            return;
        }

        $product = $this->createProductFromData($requestDTO);
        $this->repository->save($product);
        $dto = $this->dtoFactory->createFromProduct($product);
        $this->view->render($dto, 201, $exit);
    }

    public function get(string $id, bool $exit = true): void
    {
        $product = $this->findProductOrFail($id);
        $dto = $this->dtoFactory->createFromProduct($product);
        $this->view->render($dto, 200, $exit);
    }

    public function update(string $id, array $data, bool $exit = true): void
    {
        $this->findProductOrFail($id);
        $updateData = $this->filterUpdateData($data);
        $this->repository->update($id, $updateData);
        $this->view->render(null, 204, $exit);
    }

    public function delete(string $id, bool $exit = true): void
    {
        $this->findProductOrFail($id);
        $this->repository->delete($id);
        $this->view->render(null, 204, $exit);
    }

    public function list(array $filters, bool $exit = true): void
    {
        $products = $this->repository->findAll($filters);
        $dtos = $this->dtoFactory->createCollection($products);
        $this->view->render($dtos, 200, $exit);
    }

    private function createProductFromData(CreateProductRequestDTO $requestDTO): Product
    {
        return new Product(
            Uuid::uuid4()->toString(),
            $requestDTO->name,
            (float)$requestDTO->price,
            $requestDTO->category ?? '',
            $requestDTO->attributes ?? [],
            new DateTime()
        );
    }

    private function findProductOrFail(string $id): Product
    {
        $product = $this->repository->findById($id);
        if ($product === null) {
            $this->view->error('Product not found', 404, true);
        }
        return $product;
    }

    private function filterUpdateData(array $data): array
    {
        $updateData = [];
        if (isset($data['name']) && is_string($data['name'])) $updateData['name'] = $data['name'];
        if (isset($data['price']) && is_numeric($data['price']) && $data['price'] > 0) $updateData['price'] = (float)$data['price'];
        if (isset($data['category'])) $updateData['category'] = $data['category'];
        if (isset($data['attributes']) && is_array($data['attributes'])) $updateData['attributes'] = $data['attributes'];
        return $updateData;
    }
}