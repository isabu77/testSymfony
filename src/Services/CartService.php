<?php
namespace App\Services;

use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    protected $session;
    protected $articleRepo;

    /**
     * 
     */
    public function __construct(SessionInterface $session, ArticleRepository $articleRepo)
    {
        $this->session = $session;
        $this->articleRepo = $articleRepo;
    }
    /**
     * 
     */
    public function add($id)
    {
        $panier = $this->session->get('panier', []);
        if (!empty($panier[$id])) {
            $panier[$id]++;
        } else {
            $panier[$id] = 1;
        }
        $this->session->set('panier', $panier);
    }

    /**
     * 
     */
    public function delete($id)
    {
        $panier = $this->session->get('panier', []);
        if (!empty($panier[$id])) {
            unset($panier[$id]);
        }
        $this->session->set('panier', $panier);
    }

    /**
     * 
     */
    public function getAll()
    {
        $panier = $this->session->get('panier', []);
        $cartwithData = [];
        foreach ($panier as $id => $qty) {
            $cartwithData[] = [
                'article' => $this->articleRepo->find($id),
                'qty' => $qty
            ];
        }

        return $cartwithData;
    }
    /**
     * 
     */
    public function getTotal($cartwithData)
    {
        $total = 0;
        foreach ($cartwithData as $key => $value) {
            $total += $value['article']->getPrice() * $value['qty'];
        }

        return $total;
    }

}

