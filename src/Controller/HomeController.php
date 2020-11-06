<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
    public function articles(): Response
    {
        return $this->render('home/articles.html.twig');
    }
}
