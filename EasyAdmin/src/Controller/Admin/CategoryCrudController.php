<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;

class CategoryCrudController extends AbstractCrudController
{

    public const ACTION_DUPLICATE ='duplicate' ;

    public static function getEntityFqcn(): string
    {
        return Category::class;
    }
    public function configureActions(Actions $actions): Actions
    {
          $duplicate =Action::new(self::ACTION_DUPLICATE)
          ->linkToCrudAction('duplicateCategory')
          ->setCssClass('btn btn-info');
  
      return $actions
      ->add(Crud::PAGE_EDIT,$duplicate)
      ->reorder(Crud::PAGE_EDIT,[self::ACTION_DUPLICATE,Action::SAVE_AND_RETURN]);
    }
    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name'),
            BooleanField::new('active'),
            DateTimeField::new('updated_at')->hideOnForm(),
            DateTimeField::new('created_at')->hideOnForm(),
        ];
    }
    // public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    // {
    //     if (!$entityInstance instanceof Category) return;
    //     $entityInstance->setCreatedAt( new \DateTimeImmutable()); 
    //     parent::persistEntity($entityManager , $entityInstance);        
    // }

    // public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    // {
    //     if (!$entityInstance instanceof Category) return;
    //     $entityInstance->setUpdatedAt( new \DateTimeImmutable()); 
    //     parent::updateEntity($entityManager , $entityInstance);        
        
    // }

    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Category) return;
          foreach ($entityInstance->getProducts() as $product) {
            $entityManager->remove($product);
          }
            parent::deleteEntity($entityManager, $entityInstance); 
        }
    
    
    public function duplicateCategory(AdminContext $context,AdminUrlGenerator $adminUrlGenerator, EntityManagerInterface $em): Response {
        /** @var Category $category  */
        
        $category = $context->getEntity()->getInstance();

        $duplicateCategory =  clone $category;
        parent::persistEntity($em,$duplicateCategory);

        $url = $adminUrlGenerator->setController(self::class)
          ->setAction(Action::DETAIL)
          ->setEntityId($duplicateCategory->getId())
          ->generateUrl();


        return $this->redirect($url);
    }
}
