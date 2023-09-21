<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use App\Service\ChartBuilder;
// use Symfony\UX\Chartjs\Model\Chart;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface; // Import the EntityManagerInterface

use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
class DashboardController extends AbstractDashboardController
{
    public function __construct(private ChartBuilderInterface $chartBuilder, private EntityManagerInterface $entityManager,private SerializerInterface $serializer)
    {
    }

    #[Route('/{_locale}/admin', name: 'admin')]
    public function index(): Response
    {
        $productRepository = $this->entityManager->getRepository(Product::class);
        $numberOfProducts = $productRepository->count([]); 
        $products = $productRepository->findAll();// Uncomment this line
        $userRepository = $this->entityManager->getRepository(User::class);
$categoryRepository = $this->entityManager->getRepository(Category::class);
$productValues = [];
$productLabels = [];
$numberOfUsers = $userRepository->count([]);
$numberOfCategories = $categoryRepository->count([]);
foreach ($products as $product) {
    // $productValues[] = $product->getValue();
    $productLabels[] = $product->getName(); // You can use the product name as labels
}
$chartData = [
    'type' => 'bar',
    'data' => [
        'labels' => $productLabels, // Labels can be product names
        'datasets' => [
            [
                'label' => 'Product Values',
                'data' => $productValues,
                'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                'borderColor' => 'rgba(75, 192, 192, 1)',
                'borderWidth' => 1,
            ],
        ],
    ],
    
];
// Serialize the chart data to JSON
$chartDataJson = $this->serializer->serialize($chartData, 'json');
        return $this->render('admin/dashboard.html.twig', [
            'chartData' => $chartDataJson,
            'numberOfProducts' => $numberOfProducts,
            'numberOfUsers' => $numberOfUsers,
            'numberOfCategories' => $numberOfCategories,
         
        ]);
    }

    // public function configureAssets(): Assets
    // {
    //     $assets = parent::configureAssets();

    //     $assets->addWebpackEncoreEntry('app');

    //     return $assets;
    // }

    // public function configureCrud(): Crud
    // {
    //     return Crud::new()
    //         ->renderContentMaximized()
    //         ->showEntityActionsInlined()
    //         ->setDateIntervalFormat('%%y Year(s) %%m Month(s) %%d Day(s)')
    //         ->setEntityPermission('ROLE_ADMIN');
    // }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Website')
            ->setTranslationDomain('admin')
            ->setLocales([
                'en' => 'EN English',
                'fr' => 'FR France',
                "ar" => "AR العربية"
            ]);
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToUrl('Search in Google', 'fab fa-google', 'https://www.google.com');
        yield MenuItem::linkToUrl('Visit Elite Auto','fa fa-car', 'https://www.elite-auto.fr');
        yield MenuItem::section('Products');
        yield MenuItem::subMenu('Actions', 'fas fa-bars')
                          ->setSubItems([
                            MenuItem::linkToCrud('create product', 'fas fa-plus',Product::class)->setAction(Crud::PAGE_NEW)     
                          ->setPermission('ROLE_ADMIN'),
                            MenuItem::linkToCrud('show products', 'fas fa-eye',Product::class)
                                       ]);
        yield MenuItem::section('Categories');
        yield MenuItem::subMenu('Actions', 'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud('create category', 'fas fa-plus',Category::class)->setAction(Crud::PAGE_NEW)
            ->setPermission('ROLE_ADMIN'),
            MenuItem::linkToCrud('show Categories', 'fas fa-eye',Category::class),
        ]);
        yield MenuItem::section('Users')->setPermission('ROLE_ADMIN');
        yield MenuItem::subMenu('Actions', 'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud('create user', 'fas fa-plus',User::class)->setAction(Crud::PAGE_NEW)
            ->setPermission('ROLE_ADMIN'),
            MenuItem::linkToCrud('show users', 'fas fa-eye',User::class)
        ]) ->setPermission('ROLE_ADMIN');
        yield MenuItem::section('Settings');
        yield MenuItem::linkToRoute('My Profile', 'fa fa-id-card', 'user_profile');
        yield MenuItem::linkToLogout('Logout', 'fa fa-sign-out');
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        if(!$user instanceof User){
            throw new \Exception ('You must');
        }
        return parent::configureUserMenu($user)
            ->setMenuItems(([
                MenuItem::linkToLogout('Logout', 'fa fa-sign-out'),
            ]));
    }
}
