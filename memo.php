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