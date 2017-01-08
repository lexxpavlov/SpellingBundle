<?php

namespace Lexxpavlov\SpellingBundle\EventListener;

use Sonata\AdminBundle\Event\ConfigureMenuEvent;

class MenuBuilderListener
{
    public function addMenuItems(ConfigureMenuEvent $event)
    {
        $event->getMenu()
            ->getChild('lexxpavlov_spelling')
            ->addChild('contribute', [
                'route' => 'lexxpavlov_spelling_contribute',
                'label' => 'Correctors contribute',
            ])
            ->setExtra('translation_domain', 'LexxpavlovSpellingBundle');
    }
}
