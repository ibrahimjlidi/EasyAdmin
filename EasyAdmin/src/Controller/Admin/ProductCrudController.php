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
           ;
       }

    public function configureCrud(Crud $crud): Crud
{
    return $crud
        ->setSearchFields(['name', 'description'])
        ->setSearchFields(['name', 'description', 'seller.email', 'seller.address.zipCode'])
        ->setSearchFields(null)
        ->setAutofocusSearch()
        ->setPaginatorPageSize(5)
        ->setPaginatorRangeSize(4)
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
