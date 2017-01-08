<?php

namespace Lexxpavlov\SpellingBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class CorrectedAdmin extends AbstractAdmin
{
    protected $baseRoutePattern = 'lexxpavlov/spelling/corrected';
    protected $baseRouteName = 'admin_lexxpavlov_spelling_corrected';

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(['list', 'delete', 'batch']);
    }

    public function createQuery($context = 'list')
    {
        $query = parent::createQuery($context);
        $alias = $query->getRootAliases()[0];
        $query->where($alias . '.corrected = true');
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
            ->add('updated')
            ->add('corrector', null, ['associated_property' => $userField])

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
            ->add('url')
            ->add('corrector')
            ->add('created')
            ->add('updated')
        ;
    }

    public function postUpdate($object)
    {
        $this->getConfigurationPool()->getContainer()->get('lexxpavlov_spelling.service.spelling')
            ->updateSpelling($object);
    }
}
