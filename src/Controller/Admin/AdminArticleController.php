<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Entity\Categorie;
use App\Entity\User;
use App\Form\ArticleFormType;
use App\Form\CategorieFormType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminArticleController extends AbstractController
{
    /**
     * @Route("/admin/articles", name="admin_affichage_articles")
     */
    public function index(ArticleRepository $repo): Response
    {
        $admin = $this->getUser();
        $articles = $repo->findAll();

        return $this->render('admin/admin_article/affichagearticles.html.twig', compact('articles', 'admin'));
    }

    /**
     *@Route("admin/article/{id}", name="admin_article_edit", methods="GET|POST")
     *@Route("/admin/article/ajout", name="admin_article_ajout")
     */
    public function ajouterArticle(Article $article = null, Request $request, EntityManagerInterface $em): Response
    {
        //dd($request);
        //  dd($this->getUser());
        $user = $this->getUser();

        if (!$article) {
            $article = new article();
            $article->setUser($user);
        }

        //variable pour savoir si on est en création ou modif
        // dd($article->getId());
        $modif = $article->getId() !== null;

        $form = $this->createForm(ArticleFormType::class, $article);
        $form->handleRequest($request);

        // dd($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($article);
            $em->flush();

            $this->addFlash('message', $modif ? 'Article modifié avec succès' : 'Article ajouté avec succès');

            return $this->redirectToRoute('admin_affichage_articles');
        }

        return $this->render('admin/admin_article/ajoutarticle.html.twig', ['formulaireArticle' => $form->createView()]);
    }

    /**
     * @Route("/admin/article/{id}", name="admin_article_delete", methods="SUP")
     */
    public function deleteArticle(Article $article = null, Request $request, EntityManagerInterface $em)
    {
        //  dd($request->get('_token'));
        if ($this->isCsrfTokenValid('SUP'.$article->getId(), $request->get('_token'))) {
            $em->remove($article);
            $em->flush();
            $this->addFlash('message', 'Article supprimé avec succès');

            return $this->redirectToRoute('admin_affichage_articles');
        }
    }

    /**
     * @Route("/admin/categorie/ajout", name="admin_categorie_ajout")
     */
    public function ajouterCategorie(Categorie $categorie = null, Request $request, EntityManagerInterface $em): Response
    {
        if (!$categorie) {
            $categorie = new Categorie();
        }

        $form = $this->createForm(CategorieFormType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($categorie);
            $em->flush();
        }

        return $this->render('admin/admin_article/ajoutcategorie.html.twig', ['formulaireCateg' => $form->createView()]);
    }

    /**
     * @Route("/user/article/ajout", name="user_article_ajout")
     */
    public function ajoutArticle(EntityManagerInterface $em): Response
    {
        // $em = $this->getDoctrine()->getManager();

        //  $currentUser = $this->getUser();
        // dd($currentUser);
        //echo("ajout article");

        $u2 = new User();
        $u2->setEmail('kitkat2@yahoo.fr');
        $u2->setPassword('romeojuliette');
        //$u1->setRoles(["ROLE_ADMIN"]);
        $em->Persist($u2);

        $a2 = new Article();
        $a2->setTitre('casquette');
        $a2->setDescription('casquette été');
        $a2->setPrix(55);
        $a2->setImage('casquette.png');
        $a2->setActif(1);

        // $a1->setUser($currentUser);

        $a2->setUser($u2);

        $em->Persist($a2);
        $em->flush();

        return new Response('article ajouté');
    }

    /**
     * @Route("/user/articles/{id}", name="user_articles")
     */
    public function getArt(Article $article): Response
    {
        /*$em = $this->getDoctrine()->getManager();
        $articles = $em->getRepository(Article::class)->findAll();
        dd($articles);*/

        /*find($id) --> pour id seulement*/

        /*
        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository(Article::class)->findOneBy(['slug' => $slug]);
        dd($article);*/

        /* parameter converter

        dd($article);*/

       // dd($article->getCategorie()[0]->getNom());
        dd($article->getUser()->getEmail());

        return new Response('Liste articles');
    }

    /**
     * @Route("/user/articles_prix/{prix}", name="user_articles_prix")
     */
    public function getArtPrix($prix): Response
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Article::class);
        //$articles = $repo->findArticlesByPriceLess($prix);
        $articles = $repo->findArticlesByPriceGreat($prix);
        dd($articles);

        return new Response('Liste articles par prix');
    }

     /**
     * @Route("/user/articles_search/{mot}", name="user_articles_search")
     */
    public function searchArticle($mot): Response
    {
        $em = $this->getDoctrine()->getManager();
       /* $repo = $em->getRepository(Article::class);
        
        $articles = $repo->filterArticleBy($mot);
        dd($articles);
        */

        /*
        $repo = $em->getRepository(Categorie::class);
        $cat = $repo->findOneBy(['slug'=>$mot]);
       // dd($cat);
        $arts = $cat->getArticles();
       // dd($articles);

        foreach ($arts as $art) {
          echo("<h1>".$art->getTitre()."</h1>");
        }*/


        return new Response('Liste articles par prix');
    }

     /**
     * @Route("/userandarticles/{email}", name="user_and_articles")
     */
    public function UserAndArticles($email): Response
    {
        $mail = $email;
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Article::class);
        
        $userarticles = $repo->findUserAndArticle($email);

        //dd($userarticles);
   /*
        echo "<table class='table'>";
        echo "<th>Vendeur</th><th>Nom article</th><th>Prix article</th><th>Date Mise en ligne</th>";
        foreach ($userarticles as $userart) {

        echo"<tr><td>".$userart['vendeur']."</td><td>".$userart['nom_article']."</td><td>".$userart['prix_article']."</td><td>".$userart['date_mise_en_ligne']."</td></tr>";
         
        }

        echo "</table>";*/
        
        return $this->render('home/userarticles.html.twig', compact('userarticles','email'));
    }
}
