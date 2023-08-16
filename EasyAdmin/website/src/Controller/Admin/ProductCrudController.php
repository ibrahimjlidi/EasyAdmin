<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProductCrudController extends AbstractCrudController
{   
     public const ACTION_DUPLICATE ='duplicate' ;
     public const PRODUCTS_BASE_PATH = "upload/images/products";
     public const PRODUCTS_UPLOAD_PATH = "public/upload/images/products";
         public static function getEntityFqcn(): string
    {
        return Product::class;
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
}
