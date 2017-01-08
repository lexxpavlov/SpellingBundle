<?php

namespace Lexxpavlov\SpellingBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;

trait FindTrait
{
    public function findAction($id = null)
    {
        if (is_string($id)) {
            $field = $this->getRequest()->query->get('field', $this->getParameter('lexxpavlov_spelling.find_by'));
            $object = $this->admin->getModelManager()->findOneBy($this->admin->getClass(), [$field => $id]);
            if ($object) $id = $object->getId();
        }
        return new RedirectResponse($this->admin->generateUrl('edit', ['id' => $id]));
    }
}