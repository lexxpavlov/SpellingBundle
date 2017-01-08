<?php

namespace Lexxpavlov\SpellingBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class ErrorsAdmin extends AbstractAdmin
{
    protected $baseRoutePattern = 'lexxpavlov/spelling';
    protected $baseRouteName = 'admin_lexxpavlov_spelling';

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(['list', 'delete', 'batch']);
        $collection->add('ok', $this->getRouterIdParameter() . '/ok');
    }

    public function createQuery($context = 'list')
    {
        $query = parent::createQuery($context);
        $alias = $query->getRootAliases()[0];
        $query->where($alias . '.corrected = false');
        return $query;
    }

    public function configureListFields(ListMapper $listMapper)
    {
        $userField = $this->getConfigurationPool()->getContainer()->getParameter('lexxpavlov_spelling.user_field');

        $listMapper
            ->add('url', null, ['template' => "LexxpavlovSpellingBundle:Sonata:list_url.html.twig"])
            ->add('correction', null, ['template' => "LexxpavlovSpellingBundle:Sonata:list_correcting_link.html.twig"])
            ->add('textHtml', 'html', ['label' => 'Text'])
            ->add('comment')
            ->add('created')
            ->add('creator', null, ['associated_property' => $userField])

            ->add('_action', 'actions', [
                'actions' => [
                    'ok' => ['template' => 'LexxpavlovSpellingBundle:Sonata:list__action_ok.html.twig'],
                    'delete' => [],
                ]
            ])
        ;
    }

    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('url')
            ->add('created')
        ;
    }

    public function postUpdate($object)
    {
        $this->getConfigurationPool()->getContainer()->get('lexxpavlov_spelling.service.spelling')
            ->updateSpelling($object);
    }
}
