<?php

namespace App\Controller;

use App\Dto\ProductInput;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ApiProductController extends AbstractController
{
    public function __construct(
        private readonly ProductRepository $repo,
        private readonly ValidatorInterface $validator,
    ) {}

    #[Route('/api/products', name: 'api_products_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        return $this->json($this->repo->findAll());
    }

    #[Route('/api/products/{id}', name: 'api_products_show', methods: ['GET'])]
    public function show(string $id): JsonResponse
    {
        $product = $this->repo->findById($id);

        if (!$product) {
            return $this->json(['message' => 'Product not found'], 404);
        }

        return $this->json($product);
    }

    #[Route('/api/products', name: 'api_products_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);

        if (!is_array($payload)) {
            return $this->json(['message' => 'Invalid JSON body'], 400);
        }

        $dto = new ProductInput(
            name: (string)($payload['name'] ?? ''),
            price: (float)($payload['price'] ?? 0),
            stock: (int)($payload['stock'] ?? 0),
        );

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return $this->json([
                'message' => 'Validation failed',
                'errors' => array_map(fn($e) => [
                    'field' => $e->getPropertyPath(),
                    'error' => $e->getMessage(),
                ], iterator_to_array($errors)),
            ], 422);
        }

        $id = $this->repo->insert([
            'name' => $dto->name,
            'price' => $dto->price,
            'stock' => $dto->stock,
        ]);

        return $this->json(['id' => $id], 201);
    }

    #[Route('/api/products/{id}', name: 'api_products_update', methods: ['PUT'])]
    public function update(string $id, Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);

        if (!is_array($payload)) {
            return $this->json(['message' => 'Invalid JSON body'], 400);
        }

        $dto = new ProductInput(
            name: (string)($payload['name'] ?? ''),
            price: (float)($payload['price'] ?? 0),
            stock: (int)($payload['stock'] ?? 0),
        );

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return $this->json([
                'message' => 'Validation failed',
                'errors' => array_map(fn($e) => [
                    'field' => $e->getPropertyPath(),
                    'error' => $e->getMessage(),
                ], iterator_to_array($errors)),
            ], 422);
        }

        $updated = $this->repo->update($id, [
            'name' => $dto->name,
            'price' => $dto->price,
            'stock' => $dto->stock,
        ]);

        if (!$updated) {
            return $this->json(['message' => 'Product not found'], 404);
        }

        return $this->json(['message' => 'Updated']);
    }

    #[Route('/api/products/{id}', name: 'api_products_delete', methods: ['DELETE'])]
    public function delete(string $id): JsonResponse
    {
        $deleted = $this->repo->delete($id);

        if (!$deleted) {
            return $this->json(['message' => 'Product not found'], 404);
        }

        return $this->json(null, 204);
    }
}