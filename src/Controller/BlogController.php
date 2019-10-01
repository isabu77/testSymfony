<?php

namespace App\Controller;

use App\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index()
    {
         $articleRepo = $this->getDoctrine()->getRepository(Article::class);
        $articles = $articleRepo->findAll();
        
        
        $title = "Les articles";
        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles' => $articles,
            'title' => $title
        ]);
    }

    /**
     * @Route("/blog/show/{id}", name="blog_show")
     */
    public function show(Article $article)
    {
       // manière longue :
       //public function show(Request $request)
       //$articleRepo = $this->getDoctrine()->getRepository(Article::class);
       // $article = $articleRepo->findOneById($request->attributes->get('id'));

        $title = "Article";
        return $this->render('blog/show.html.twig', [
            'controller_name' => 'BlogController',
            'article' => $article,
            'title' => $title
        ]);
    }

    /**
     * @Route("/blog/edit/{id}", name="blog_edit")
     */
    public function edit(Article $article)
    {
        $title = "Edit Article";
        return $this->render('blog/edit.html.twig', [
            'controller_name' => 'BlogController',
            'article' => $article,
            'title' => $title
        ]);
    }
}
