<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index(CacheInterface $cache)
    {
        // $articleRepo = $this->getDoctrine()->getRepository(Article::class);
        // $articles = $articleRepo->findAll();
        $articles = $cache->get('articles', function(ItemInterface $item){
            $item->expiresAfter(10);
            //sleep(2);
            $articleRepo = $this->getDoctrine()->getRepository(Article::class);
            return $articleRepo->findAll();
            });
            

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
    public function show(Article $article, Request $request, ObjectManager $manager)
    {

        // pour l'ajout d'un commentaire
        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);

        // traitement du formulaire
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // en création : pas d'id
            if (!$comment->getId()) {
                $comment->setCreatedAt(new \DateTime())
                    ->setContent(nl2br($comment->getContent()))
                    ->setArticle($article);
            }

            // ajout dans la base :
            $manager->persist($comment);
            $manager->flush();
            // add flash messages
            $this->addFlash('success', 'Le commentaire a bien été posté');
            // redirection sur la vue de l'article
            return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);
        }else{
            $this->addFlash('error', 'Une erreur est survenue');
        }

        // manière longue :
        //public function show(Request $request)
        //$articleRepo = $this->getDoctrine()->getRepository(Article::class);
        // $article = $articleRepo->findOneById($request->attributes->get('id'));

        $title = "Article";
        return $this->render('blog/show.html.twig', [
            'controller_name' => 'BlogController',
            'article' => $article,
            'formComment' =>  $form->createView(),
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
        } else {
            $title = "Edition de l'article n° " . $article->getId();
        }

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
            // add flash messages
            $this->addFlash('success', 'L\'article a bien été posté');

            // redirection sur la vue de l'article
            return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);
        }else{
            $this->addFlash('error', 'Une erreur est survenue');
        }

        return $this->render('blog/new.html.twig', [
            'controller_name' => 'BlogController',
            'formArticle' =>  $form->createView(),
            'title' => $title
        ]);
    }

    /**
     * @Route("/blog/{id}/delete", name="blog_delete")
     */
    public function delete(ObjectManager $manager, Article $article)
    {
        // suppression de la base :
        $manager->remove($article);
        $manager->flush();
        // add flash messages
        $this->addFlash('success', 'L\'article a bien été supprimé');

        // redirection sur la vue de la boutique
        return $this->redirectToRoute('blog');
    }

    /**
     * @Route("/blog/{id}/deleteComment", name="blog_delete_comment")
     */
    public function deleteComment(ObjectManager $manager, Comment $comment, Session $session)
    {
        // suppression de la base :
        $manager->remove($comment);
        $manager->flush();

        // add flash messages
        $this->addFlash('success', 'Le commentaire a bien été supprimé');

        // redirection sur la vue de la boutique
        return $this->redirectToRoute('blog_show', ['id' => $comment->getArticle()->getId()]);
    }

    /**
     * méthode utilisée pour l'ajout et l'édition d'un commentaire (2 routes)
     * @Route("/blog/{idArticle}/{id}/editcomment", name="blog_edit_comment")
     * @Route("/blog/{idArticle}/comment", name="blog_comment")
     */
    public function comment(Request $request, ObjectManager $manager, int $idArticle, Comment $comment = null)
    {
        $article = $manager->getRepository(Article::class)->find($idArticle);

        // méthode utilisée pour l'ajout et l'édition d'un article
        if (!$comment) {
            $comment = new Comment();
            $title = "Nouveau commentaire";
        } else {
            $title = "Edition du commentaire sur l'article n° " . $article->getId();
        }

        // à partir du type de formulaire associé à l'entity Comment
        // par la commande : php bin/console make:form CommentType Comment

        $form = $this->createForm(CommentType::class, $comment);

        // traitement du formulaire
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // en création : pas d'id
            if (!$comment->getId()) {
                $comment->setCreatedAt(new \DateTime())
                    ->setArticle($article);
            }
            $comment->setContent(nl2br($comment->getContent()));

            // ajout dans la base :
            $manager->persist($comment);
            $manager->flush();

            // redirection sur la vue de l'article
            return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);
        }else{
            $this->addFlash('error', 'Une erreur est survenue');
        }

        return $this->render('blog/comment.html.twig', [
            'controller_name' => 'BlogController',
            'formComment' =>  $form->createView(),
            'title' => $title
        ]);
    }
}
