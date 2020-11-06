<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Entity\Categorie;
use App\Form\ArticleFormType;
use App\Form\CategorieFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminArticleController extends AbstractController
{
    /**
     * @Route("/admin/article", name="admin_article")
     */
    public function index(): Response
    {
        return $this->render('admin/admin_article/index.html.twig', [
            'controller_name' => 'AdminArticleController',
        ]);
    }

     /**
     * @Route("/admin/article/ajout", name="admin_article_ajout")
     */
    public function ajouterArticle(Article $article=null, Request $request, EntityManagerInterface $em): Response
    {
        if(!$article){
            $article = new article();
        }

        $form = $this->createForm(ArticleFormType::class,$article);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $em->persist($article);
            $em->flush();
        }

        return $this->render('admin/admin_article/ajoutarticle.html.twig',['formulaireArticle'=>$form->createView()]);
    }


    /**
     * @Route("/admin/categorie/ajout", name="admin_categorie_ajout")
     */
    public function ajouterCategorie(Categorie $categorie=null, Request $request, EntityManagerInterface $em): Response
    {

        if(!$categorie){
            $categorie = new Categorie();
        }

        $form = $this->createForm(CategorieFormType::class,$categorie);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $em->persist($categorie);
            $em->flush();
        }


        return $this->render('admin/admin_article/ajoutcategorie.html.twig',['formulaireCateg'=>$form->createView()]);
    }

}
