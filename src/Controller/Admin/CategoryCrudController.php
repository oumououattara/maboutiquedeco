<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

/**
 * Class CategoryCrudController
 * @package App\Controller\Admin
 */
class CategoryCrudController extends AbstractCrudController
{
    /**
     * Undocumented function
     *
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return Category::class;
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
            TextareaField::new('description')
        ];
    }
    
}
