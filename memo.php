<?php

The metadata storage is not up to date, please run the sync-metadata-storage comma
  nd to fix this issue.

  1

  -------------------------

  1

Same problem here.. I "sloved" it but dont try this at home!

I removed these lines in vendor\doctrine\migrations\lib\Doctrine\Migrations\Metadata\Storage\TableMetadataStorage.php start on line 191

$expectedTable = $this->getExpectedTable();

        if ($this->needsUpdate($expectedTable) !== null) {
            throw MetadataStorageError::notUpToDate();
        }
Then run make:migration and migrations:migrate. After success migration paste the function back.

Symfony 5.1

if you got:

Invalid platform version "maridb-10.4.13" specified. The platform version has to be specified in the format: "<major_version>.<minor_version>.<patch_version>".

just do one of

config/doctrine.yaml

doctrine:
  dbal:
    server_version: 'mariadb-10.4.13'
or in configuration file .env

DATABASE_URL=mysql://...yoursettings...?serverVersion=mariadb-10.4.13




users(id,email,roles,password)

articles(id,users_id,titre,description,slug,prix,created_at,updated_at,image,actif)

categories_articles(cat_id,art_id)

categories(id,nom,slug);



/////////////////////////TP approfondissement ////////////////////



////////////////////// orm doctrine //////


// src/Controller/ProductController.php
namespace App\Controller;

// ...
use App\Entity\Category;
use App\Entity\Product;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends AbstractController
{
    /**
     * @Route("/product", name="product")
     */
    public function index()
    {
        $category = new Category();
        $category->setName('Computer Peripherals');

        $product = new Product();
        $product->setName('Keyboard');
        $product->setPrice(19.99);
        $product->setDescription('Ergonomic and stylish!');

        // relates this product to the category
        $product->setCategory($category);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($category);
        $entityManager->persist($product);
        $entityManager->flush();

        return new Response(
            'Saved new product with id: '.$product->getId()
            .' and new category with id: '.$category->getId()
        );
    }
}

///////////////////////////////////////


// src/Controller/ProductController.php
namespace App\Controller;

// ...
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends AbstractController
{
    /**
     * @Route("/product", name="create_product")
     */
    public function createProduct(): Response
    {
        // you can fetch the EntityManager via $this->getDoctrine()
        // or you can add an argument to the action: createProduct(EntityManagerInterface $entityManager)
        $entityManager = $this->getDoctrine()->getManager();

        $product = new Product();
        $product->setName('Keyboard');
        $product->setPrice(1999);
        $product->setDescription('Ergonomic and stylish!');

        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($product);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return new Response('Saved new product with id '.$product->getId());
    }
}



//////////////////////////2

Fetching Objects from the Database¶
Fetching an object back out of the database is even easier. Suppose you want to be able to go to /product/1 to see your new product:


// src/Controller/ProductController.php
// ...

/**
 * @Route("/product/{id}", name="product_show")
 */
public function show($id)
{
    $product = $this->getDoctrine()
        ->getRepository(Product::class)
        ->find($id);

    if (!$product) {
        throw $this->createNotFoundException(
            'No product found for id '.$id
        );
    }

    return new Response('Check out this great product: '.$product->getName());

    // or render a template
    // in the template, print things with {{ product.name }}
    // return $this->render('product/show.html.twig', ['product' => $product]);
}


/////////////////////////3/////////////////////////

$repository = $this->getDoctrine()->getRepository(Product::class);

// look for a single Product by its primary key (usually "id")
$product = $repository->find($id);

// look for a single Product by name
$product = $repository->findOneBy(['name' => 'Keyboard']);
// or find by name and price
$product = $repository->findOneBy([
    'name' => 'Keyboard',
    'price' => 1999,
]);

// look for multiple Product objects matching the name, ordered by price
$products = $repository->findBy(
    ['name' => 'Keyboard'],
    ['price' => 'ASC']
);

// look for *all* Product objects
$products = $repository->findAll();

///////like query/////

$qb = $this->createQueryBuilder('a')
->where('a.type = :type')
->setParameter('type', $type)
->andWhere('a.inLanguage LIKE :inLanguage')
->setParameter('inLanguage', '%'.$lang.'%')
->addOrderBy('a.datePublished', 'desc');

if (null !== $keyword) {
$qb->andWhere('a.keyword LIKE :keyword')
->setParameter('keyword', '%'.$keyword.'%');
}

if (null !== $maxResults) {
$qb->setMaxResults($maxResults);
}

return $qb->getQuery()->execute();



///// queries example//////


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