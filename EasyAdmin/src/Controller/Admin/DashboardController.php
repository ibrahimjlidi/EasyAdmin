<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    public function __construct(private AdminUrlGenerator $adminUrlGenerator)
    {
        
    }


    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {

        $url = $this->adminUrlGenerator
        ->setController(ProductCrudController::class)
        ->generateUrl();
        
        // return parent::index();

        return $this->redirect($url);
    }
    public function configureCrud(): Crud
    {
        return Crud::new()
            ->renderContentMaximized()
            -> showEntityActionsInlined();
            
            
    }
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Website')
            ->setLocales([
                'en' => 'EN English',
                'fr' => 'FR France'
            ]);
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::section('Products');
        yield MenuItem::subMenu('Actions', 'fas fa-bars')->setSubItems([
                   MenuItem::linkToCrud('create product', 'fas fa-plus',Product::class)->setAction(Crud::PAGE_NEW),
                   MenuItem::linkToCrud('show products', 'fas fa-eye',Product::class)
        ]);
        yield MenuItem::section('Categories');
        yield MenuItem::subMenu('Actions', 'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud('create category', 'fas fa-plus',Category::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('show Categories', 'fas fa-eye',Category::class)
        ]);
        yield MenuItem::section('Users');
        yield MenuItem::subMenu('Actions', 'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud('create user', 'fas fa-plus',User::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('show users', 'fas fa-eye',User::class)
        ]);

    }
}
