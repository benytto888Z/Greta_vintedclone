<?php

namespace App\Controller\Admin;

use App\Entity\Categorie;
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
