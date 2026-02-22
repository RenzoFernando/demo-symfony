<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class ApiProductController extends AbstractController
{
    #[Route('/api/products', name: 'api_products_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        return $this->json([
            ['id' => 1, 'name' => 'Keyboard', 'price' => 49.99, 'stock' => 10],
            ['id' => 2, 'name' => 'Mouse', 'price' => 19.99, 'stock' => 25],
        ]);
    }

    #[Route('/api/products/{id}', name: 'api_products_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        return $this->json([
            'id' => $id,
            'name' => 'Mock product',
            'price' => 0,
            'stock' => 0,
        ]);
    }
}