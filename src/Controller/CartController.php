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

    /**
     * @Route("/cart/send", name="cart_send")
     */
    public function send(CartService $cartService, \Swift_Mailer $mailer)
    {
        $message = (new \Swift_Message('Confirmation de commande'))
            // ->setFrom(MAILER_FROM)
            // ->setFrom(MAILER_FROM)
            ->setTo('isabu77@gmail.com')
            ->setFrom('montluconaformac2019@gmail.com')
            ->setBody(
                $this->renderView(
                    // templates/emails/confirm.html.twig
                    'emails/confirm.html.twig'
                    //array('name' => $name)
                ),
                'text/html'
            )
            /*
         * If you also want to include a plaintext version of the message
        ->addPart(
            $this->renderView(
                'emails/confirm.txt.twig',
                array('name' => $name)
            ),
            'text/plain'
        )
        */;

        $mailer->send($message);
        // add flash messages
        $this->addFlash('success', 'La commande est confirmée par l\'envoi d\'un mail');

        return $this->redirectToRoute('cart');
    }
}
