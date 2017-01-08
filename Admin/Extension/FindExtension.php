<?php

namespace Lexxpavlov\SpellingBundle\Admin\Extension;

use Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Route\RouteCollection;

class FindExtension extends AbstractAdminExtension
{
    public function configureRoutes(AdminInterface $admin, RouteCollection $collection)
    {
        $collection->add('find', $admin->getRouterIdParameter().'/find');
    }
}