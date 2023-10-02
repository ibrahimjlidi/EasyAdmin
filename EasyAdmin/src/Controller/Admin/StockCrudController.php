<?php

namespace App\Controller\Admin;


use App\Entity\Stock;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class StockCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Stock::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            NumberField::new('total'),
            // NumberField::new('exist'),
        ];
    }
 
}
