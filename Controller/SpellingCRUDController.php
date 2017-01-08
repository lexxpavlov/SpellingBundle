<?php

namespace Lexxpavlov\SpellingBundle\Controller;

use Lexxpavlov\SpellingBundle\Entity\Spelling;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SpellingCRUDController extends Controller
{
    public function okAction()
    {
        /** @var Spelling $object */
        $object = $this->admin->getSubject();
        $object->setCorrected();
        $this->admin->update($object);

        return new RedirectResponse($this->admin->generateUrl('list', $this->admin->getFilterParameters()));
    }
}