<?php

namespace Lexxpavlov\SpellingBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class BannedAdmin extends AbstractAdmin
{
    protected $baseRoutePattern = 'lexxpavlov/spelling/banned';
    protected $baseRouteName = 'admin_lexxpavlov_spelling_banned';

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(['list', 'delete', 'batch']);
    }

    public function createQuery($context = 'list')
    {
        $query = parent::createQuery($context);
        $alias = $query->getRootAliases()[0];
        $query->where($alias . '.bannedUntil is not null');
        return $query;
    }

    public function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('ip')
            ->add('bannedUntil')

            ->add('_action', 'actions', [
                'actions' => [
                    'delete' => [],
                ]
            ])
        ;
    }

    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('ip')
            ->add('bannedUntil')
        ;
    }
}
