<?php

namespace Lexxpavlov\SpellingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SpellingController extends Controller
{
    protected $adminCode = 'lexxpavlov_spelling.admin.corrected';

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function errorAction(Request $request)
    {
        $result = $this->get('lexxpavlov_spelling.service.spelling')
            ->processError($request->request->all());

        return new JsonResponse($result === true ?: $this->buildErrors($result));
    }

    /**
     * @return Response
     *
     * @throws AccessDeniedException If access is not granted
     */
    public function contributeAction()
    {
        $adminPool = $this->get('sonata.admin.pool');

        $adminPool
            ->getAdminByAdminCode($this->adminCode)
            ->checkAccess('list');

        return $this->render('LexxpavlovSpellingBundle:Spelling:contribute.html.twig', [
            'admin_pool' => $adminPool,
            'stat' => $this->get('lexxpavlov_spelling.service.spelling')->getContribute(),
            'user_field' => $this->getParameter('lexxpavlov_spelling.user_field'),
        ]);
    }

    /**
     * @param array $errorData
     * @return array
     */
    protected function buildErrors(array $errorData)
    {
        $translator = $this->get('translator');
        $domain = $this->getParameter('lexxpavlov_spelling.error_trans_domain');
        $errors = [];
        foreach ($errorData as $key => $value) {
            if (is_numeric($key)) {
                $name = $value;
                $parameters = [];
            } else {
                $name = is_string($value) ? $value : $key;
                $parameters = is_array($value) ? $value : [];
            }
            $errors[$name] = $translator->trans($name, $parameters, $domain);
        }
        return $errors;
    }
}
