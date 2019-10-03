<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
     * méthode utilisée pour l'ajout et l'édition d'un article (2 routes)
     * @Route("/blog/new", name="blog_create")
     * @Route("/blog/{id}/edit", name="blog_edit")
     */
    public function form(Request $request, ObjectManager $manager, Article $article = null)
    {
        // méthode utilisée pour l'ajout et l'édition d'un article
        if (!$article) {
            $article = new Article();
            $title = "Nouvel article";
        }
        else{
            $title = "Edition de l'article n° ". $article->getId();
        }

        // méthode OBSOLETE statique d'enregistrement de l'article 
        // if (count($request->request) > 0){
        //     $article->setTitle($request->request->get('title'))
        //     ->setContent($request->request->get('content'))
        //     ->setImage('https://placehold.it/300x250')
        //     ->setCreatedAt(new \DateTime());

        //     // ajout dans la base :
        //     $manager->persist($article);
        //     $manager->flush();
        // }

        // 1ere méthode symfony : création du formulaire avec ses attributs FRONT
        // à éviter si on travaille en équipe avec un designer
        // $form = $this->createFormBuilder($article)
        //     ->add('title', TextType::class, [
        //         'attr' => ['class' => 'form-control'],
        //         'label' => 'Titre'
        //     ])
        //     ->add('content', TextareaType::class, [
        //         'attr' => ['class' => 'form-control'],
        //         'label' => 'Contenu'
        //     ])
        //     ->add('Sauvegarder', SubmitType::class, [
        //         'attr' => ['class' => 'btn btn-primary mt-3']
        //     ])
        //     ->getForm();

        // 2ème méthode symfony : création du formulaire SANS les attributs FRONT
        // qui seront gérés dans la vue avec form_start(form)
        // $form = $this->createFormBuilder($article)
        //     ->add('title', TextType::class)
        //     ->add('content', TextareaType::class)
        //     ->add('Sauvegarder', SubmitType::class)
        //     ->getForm();

        // 3ème méthode symfony (la meilleure): 
        // à partir du type de formulaire associé à l'entity Article
        // par la commande : php bin/console make:form ArticleType Article

        $form = $this->createForm(ArticleType::class, $article);

        // traitement du formulaire
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // en création : pas d'id
            if (!$article->getId()) {
                $article->setCreatedAt(new \DateTime());
                //->setImage('https://picsum.photos/300/250')
            }

            // ajout dans la base :
            $manager->persist($article);
            $manager->flush();

            // redirection sur la vue de l'article
            return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);
        }


        return $this->render('blog/new.html.twig', [
            'controller_name' => 'BlogController',
            'formArticle' =>  $form->createView(),
            'title' => $title
        ]);
    }
}
