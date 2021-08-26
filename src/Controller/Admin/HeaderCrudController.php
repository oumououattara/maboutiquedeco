<?php

namespace App\Controller\Admin;

use App\Entity\Header;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

/**
 * Class HeaderCrudController
 * @package App\Controller\Admin
 */
class HeaderCrudController extends AbstractCrudController
{
    /**
     * Undocumented function
     *
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return Header::class;
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
            TextField::new('title'),
            TextField::new('btnTitle'),
            TextField::new('btnUrl'),
            TextareaField::new('content'),
            ImageField::new('image')->setBasePath('uploads/')->setUploadDir('public/uploads')->setUploadedFileNamePattern('[randomhash].[extension]')
        ];
    }
    
}
