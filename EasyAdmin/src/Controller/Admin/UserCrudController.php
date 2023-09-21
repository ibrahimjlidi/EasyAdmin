<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;


class UserCrudController extends AbstractCrudController
{
    public const ACTION_DUPLICATE ='duplicate' ;
    public static function getEntityFqcn(): string
    {
        return User::class;
    }
    public function configureActions(Actions $actions): Actions
    {
          $duplicate =Action::new(self::ACTION_DUPLICATE)->linkToCrudAction('duplicateUser');
     
      return $actions
      
      // ->remove(Crud::PAGE_INDEX, Action::NEW)
      // ->remove(Crud::PAGE_DETAIL, Action::EDIT)
      // ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
      //     return $action->setIcon('fa fa-file-alt')->setLabel(false);
      // })
      // ->disable(Action::NEW, Action::DELETE)

      ->add(Crud::PAGE_INDEX, Action::DETAIL)
      ->add(Crud::PAGE_EDIT, Action::SAVE_AND_ADD_ANOTHER)
      ->reorder(Crud::PAGE_INDEX, [Action::DETAIL, Action::EDIT, Action::DELETE])
      ->add(Crud::PAGE_EDIT,$duplicate);
  
      
    }
     /**
     * @Route("/profile", name="user_profile")
     */
    #[Route('/profile', name: 'user_profile')]
    public function profile(UserInterface $user)
    {
        // $user is the currently logged-in user
        return $this->render('/profile.html.twig', [
            'user' => $user,
        ]);
    }
    
}
