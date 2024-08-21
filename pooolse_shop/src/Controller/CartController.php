<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Service\ProductService;

class CartController extends AbstractController
{
    private ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    #[Route('/cart', name: 'cart', methods: ['GET'])]
    public function index(): Response
    {

        return $this->render('cart/index.html.twig');
    }

    #[Route('/api/shoppingCartPrice', name: 'shoppingCartPrice', methods: ['POST'])]
    public function getShoppingCartPrice(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $shoppingCart = $data['cart'] ?? [];
        $prices = $this->productService->getProductsByIdsWithDiscounts($shoppingCart);

        return new JsonResponse(['totalPrices' => $prices]);
    }

    #[Route('/api/shoppingCartData', name: 'shoppingCartData', methods: ['POST'])]
    public function getShoppingCartData(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $shoppingCart = $data['cart'] ?? [];
        $data = $this->productService->getCartProductsWithDiscounts($shoppingCart);

        return new JsonResponse(['data' => $data]);
    }

};    