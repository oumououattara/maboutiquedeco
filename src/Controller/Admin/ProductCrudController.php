<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

/**
 * Class ProductCrudController
 * @package App\Controller\Admin
 */
class ProductCrudController extends AbstractCrudController
{
    /**
     * Undocumented function
     *
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    /**
     * Undocumented function
     *
     * @param string $pageName
     * @return iterable
     */
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name'),
            SlugField::new('slug')->setTargetFieldName('name'),
            TextField::new('subtitle'),
            TextareaField::new('description'),
            MoneyField::new('price')->setCurrency('EUR'),
            ImageField::new('image')->setBasePath('uploads/')
                ->setUploadDir('public/uploads')
                ->setUploadedFileNamePattern('[randomhash].[extension]'),
            AssociationField::new('category')
        ];
    }
    
}
