<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use function Symfony\Component\Translation\t;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Translation\TranslatableMessage;



class ProductCrudController extends AbstractCrudController
{   
     public const ACTION_DUPLICATE ='duplicate' ;
     public const PRODUCTS_BASE_PATH = "upload/images/products";
     public const PRODUCTS_UPLOAD_PATH = "public/upload/images/products";
         public static function getEntityFqcn(): string
    {
        return Product::class;
    }
    public function configureFilters(Filters $filters): Filters
       {
          return $filters
               ->add(EntityFilter::new('category'))
               ->add('name')
               ->add('price')
               ->add('active')
               ->add('id')
               ->add('image')
               ->add('publisher');
       }

    public function configureCrud(Crud $crud): Crud
{
    return $crud
        ->setSearchFields(['id','located','name','price','active','image'])
        // ->setSearchFields(['name', 'description'])
        // ->setSearchFields(null)
        ->setAutofocusSearch()
        ->setPaginatorPageSize(5)
        ->setDateIntervalFormat('%%y Year(s) %%m Month(s) %%d Day(s)')
        ->setPaginatorRangeSize(4)
        ->setPaginatorUseOutputWalkers(true)
        ->setPaginatorFetchJoinCollection(true)
      

    ;
}


    /**
     * @Route("/api/products", name="api_products_list", methods={"GET"})
     */
public function listProductsForAngular(EntityManagerInterface $entityManager): JsonResponse
{
    $productRepository = $entityManager->getRepository(Product::class);
    $products = $productRepository->findAll();

    // Serialize the products to JSON
    $serializedProducts = [];
    foreach ($products as $product) {
        $serializedProducts[] = [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'price' => $product->getPrice(),
            'description' => $product->getDescription(),
            // Add other fields as needed
        ];
    }

    return new JsonResponse($serializedProducts);
}
public function configureActions(Actions $actions): Actions
{
    $duplicate = Action::new(self::ACTION_DUPLICATE)->linkToCrudAction('duplicateProduct');
    
    // Add a new action to list products for Angular
    $listForAngular = Action::new('listForAngular', 'List for Angular')
        ->linkToRoute('list_products_for_angular');

    return $actions
        ->add(Crud::PAGE_INDEX, Action::DETAIL)
        ->add(Crud::PAGE_EDIT, Action::SAVE_AND_ADD_ANOTHER)
        ->reorder(Crud::PAGE_INDEX, [Action::DETAIL, Action::DELETE, Action::EDIT])
        ->add(Crud::PAGE_EDIT, $duplicate)
        ->add(Crud::PAGE_INDEX, $listForAngular); // Add the new action
}
    
    public function configureFields(string $pageName): iterable
    {
        return [
        
            IdField::new('id')->hideOnForm(),
            TextField::new('name')->setRequired(false),
            MoneyField::new('price')->setCurrency('EUR'),
            TextEditorField::new('description'),
            ImageField::new('image')
                 ->setBasePath(self::PRODUCTS_BASE_PATH)
                 ->setUploadDir(self::PRODUCTS_UPLOAD_PATH)
                 ->setSortable(false),
            BooleanField::new('active'),
            AssociationField::new('publisher')->hideOnForm()->onlyOnIndex(),
            AssociationField::new('category')->setQueryBuilder(function(QueryBuilder $queryBuilder){
                $queryBuilder->where('entity.active =true');
            }),
            DateTimeField::new('updated_at')->hideOnForm()->setColumns('col-sm-6 col-lg-5 col-xxl-3'),
            DateTimeField::new('created_at')->hideOnForm(),
    
        ];
      
    }

    public function duplicateProduct(AdminContext $context,AdminUrlGenerator $adminUrlGenerator, EntityManagerInterface $em): Response {
        /** @var Product $product  */
        
        $product = $context->getEntity()->getInstance();

        $duplicateProduct =  clone $product;
        parent::persistEntity($em,$duplicateProduct);

        $url = $adminUrlGenerator->setController(self::class)
          ->setAction(Action::DETAIL)
          ->setEntityId($duplicateProduct->getId())
          ->generateUrl();


        return $this->redirect($url);
    }
}
