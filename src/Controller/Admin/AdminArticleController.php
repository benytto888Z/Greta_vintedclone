<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Entity\Categorie;
use App\Form\ArticleFormType;
use App\Form\CategorieFormType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminArticleController extends AbstractController
{
    /**
     * @Route("/admin/article", name="admin_affichage_articles")
     */
    public function index(ArticleRepository $repo): Response
    {
        $admin = $this->getUser();
        $articles = $repo->findAll();
        return $this->render('admin/admin_article/affichagearticles.html.twig',compact('articles','admin'));
    }

     /**
      *@Route("admin/article/{id}", name="admin_article_edit", methods="GET|POST")
     * @Route("/admin/article/ajout", name="admin_article_ajout")
     */
    public function ajouterArticle(Article $article=null, Request $request, EntityManagerInterface $em): Response
    {

      //  dd($this->getUser());
        $user = $this->getUser();

        if(!$article){
            $article = new article();
            $article->setUser($user);
        }

        //variable pour savoir si on est en création ou modif

        $modif = $article->getId() !== null;

        $form = $this->createForm(ArticleFormType::class,$article);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $em->persist($article);
            $em->flush();

            $this->addFlash('message', $modif ? 'Article modifié avec succès' : 'Article ajouté avec succès');

            return $this->redirectToRoute('articles');
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
