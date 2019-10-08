<?php

namespace App\Controller;

use App\Services\CartService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    /**
     * @Route("/cart", name="cart")
     */
    public function index(CartService $cartService)
    {
        $cartwithData = $cartService->getAll();
        return $this->render('cart/index.html.twig', [
            'items' => $cartwithData,
            'total' => $cartService->getTotal($cartwithData)
        ]);
    }

    /**
     * @Route("/cart/add{id}", name="cart_add")
     */
    public function add(CartService $cartService, $id)
    {
        $cartService->add($id);
        
        return $this->redirectToRoute('blog');
    }
    /**
     * @Route("/cart/delete{id}", name="cart_delete")
     */
    public function delete(CartService $cartService, $id)
    {
        $cartService->delete($id);
        
        return $this->redirectToRoute('cart');
    }
}
