<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
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

class ProductCrudController extends AbstractCrudController
{   
     public const ACTION_DUPLICATE ='duplicate' ;
     public const PRODUCTS_BASE_PATH = "upload/images/products";
     public const PRODUCTS_UPLOAD_PATH = "public/upload/images/products";
         public static function getEntityFqcn(): string
    {
        return Product::class;
    }


    public function configureCrud(Crud $crud): Crud
{
    return $crud
        // ...
      

        ->setSearchFields(['name', 'description'])
        // use dots (e.g. 'seller.email') to search in Doctrine associations
        ->setSearchFields(['name', 'description', 'seller.email', 'seller.address.zipCode'])
        // set it to null to disable and hide the search box
        ->setSearchFields(null)
        // call this method to focus the search input automatically when loading the 'index' page
        ->setAutofocusSearch()
        // the max number of entities to display per page
        ->setPaginatorPageSize(5)
        // the number of pages to display on each side of the current page
        // e.g. if num pages = 35, current page = 7 and you set ->setPaginatorRangeSize(4)
        // the paginator displays: [Previous]  1 ... 3  4  5  6  [7]  8  9  10  11 ... 35  [Next]
        // set this number to 0 to display a simple "< Previous | Next >" pager
        ->setPaginatorRangeSize(4)
      
        // these are advanced options related to Doctrine Pagination
        // (see https://www.doctrine-project.org/projects/doctrine-orm/en/2.7/tutorials/pagination.html)
        ->setPaginatorUseOutputWalkers(true)
        ->setPaginatorFetchJoinCollection(true)
    ;
}
  public function configureActions(Actions $actions): Actions
  {
        $duplicate =Action::new(self::ACTION_DUPLICATE)->linkToCrudAction('duplicateProduct');

    return $actions
    ->add(Crud::PAGE_EDIT,$duplicate);

    
  }
    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name','Label')->setRequired(false),
            MoneyField::new('price')->setCurrency('EUR'),
            TextEditorField::new('description'),
            ImageField::new('image')
            ->setBasePath(self::PRODUCTS_BASE_PATH)
            ->setUploadDir(self::PRODUCTS_UPLOAD_PATH)
            ->setSortable(false),
            BooleanField::new('active'),
            AssociationField::new('category')->setQueryBuilder(function(QueryBuilder $queryBuilder){
                $queryBuilder->where('entity.active =true');
            }),
            DateTimeField::new('updated_at')->hideOnForm(),
            DateTimeField::new('created_at')->hideOnForm(),
        ];
    }
    /*
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Product) return;
            $entityInstance->setCreatedAt( new \DateTimeImmutable); 
            parent::persistEntity($entityManager , $entityInstance);        
    }
    
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Product) return;
        $entityInstance->setUpdatedAt( new \DateTimeImmutable); 
        parent::updateEntity($entityManager , $entityInstance);        
        
    }
    */
     
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
