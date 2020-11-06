<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_accueil")
     */
    public function index(): Response
    {
        return $this->render('home/accueil.html.twig');
    }

     /**
     * @Route("/articles", name="articles")
     */
    public function articles(ArticleRepository $repo): Response
    {
        //$articles = $repo->findBy([]);
       // $articles = $repo->findAll();
       $articles = $repo->findBy(['actif'=>1]);
        return $this->render('home/articles.html.twig',compact('articles'));
    }

    /**
     * @Route("/article/{slug}", name="article_details")
     */

     public function articleDetails(ArticleRepository $repo, $slug){
        $user = $this->getUser();
        $article = $repo->findOneBy(['slug'=>$slug]);
        return $this->render('home/articledetails.html.twig',compact('article','user'));
     }
}
