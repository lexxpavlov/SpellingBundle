<?php

namespace Lexxpavlov\SpellingBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Lexxpavlov\SpellingBundle\Entity\Spelling as SpellingEntity;
use Lexxpavlov\SpellingBundle\Event\SpellingNewErrorEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class Spelling
{
    /** @var EntityManagerInterface */
    protected $em;

    /** @var RouterInterface */
    protected $router;

    /** @var EventDispatcherInterface */
    protected $dispatcher;

    /** @var TokenStorageInterface */
    protected $tokenStorage;

    /** @var string */
    protected $entityClass;

    /** @var string*/
    protected $defaultFindBy;

    /** @var string */
    protected $dataDelimiter;

    /** @var int */
    protected $notCorrectedCount;

    /** @var string */
    protected $errorTransDomain;

    public function __construct(
        EntityManagerInterface $em,
        RouterInterface $router,
        EventDispatcherInterface $dispatcher,
        TokenStorageInterface $tokenStorage,
        $entityClass,
        $defaultFindBy,
        $dataDelimiter,
        $errorTransDomain)
    {
        $this->em = $em;
        $this->router = $router;
        $this->dispatcher = $dispatcher;
        $this->tokenStorage = $tokenStorage;
        $this->entityClass = $entityClass;
        $this->defaultFindBy = $defaultFindBy;
        $this->dataDelimiter = $dataDelimiter;
        $this->errorTransDomain = $errorTransDomain;
    }

    /**
     * @param string      $url
     * @param string      $prefix
     * @param string      $error
     * @param string      $suffix
     * @param string|null $comment
     * @return SpellingEntity
     */
    public function createSpelling($url, $prefix, $error, $suffix, $comment = null)
    {
        /** @var SpellingEntity $spelling */
        $spelling = new $this->entityClass;
        $spelling
            ->setUrl($url)
            ->setPrefix($prefix)
            ->setError($error)
            ->setSuffix($suffix)
            ->setComment($comment)
            ->setCreated(new \DateTime);

        $token = $this->tokenStorage->getToken();
        if (null !== $token) {
            $user = $token->getUser();
            if (is_object($user) && $user instanceof UserInterface) {
                $spelling->setCreator($user);
            }
        }

        return $spelling;
    }

    /**
     * @param SpellingEntity $spelling
     * @return SpellingEntity
     */
    public function updateSpelling(SpellingEntity $spelling)
    {
        $spelling->setUpdated(new \DateTime);

        $token = $this->tokenStorage->getToken();
        if (null !== $token) {
            $user = $token->getUser();
            if (is_object($user) && $user instanceof UserInterface) {
                $spelling->setCorrector($user);
            }
        }

        return $spelling;
    }

    /**
     * @param SpellingEntity|array $data Spelling entity or data of error (url, prefix, error, suffix, comment)
     *
     * @return true|array Result of processing
     *
     * @throws \InvalidArgumentException
     */
    public function processError($data)
    {
        if ($data instanceof SpellingEntity) {
            $spelling = $data;
        } elseif (is_array($data)) {
            $spelling = $this->createSpelling($data['url'], $data['prefix'], $data['error'], $data['suffix'], $data['comment']);
        } else {
            throw new \InvalidArgumentException("Invalid spelling data");
        }

        $event = new SpellingNewErrorEvent($spelling);
        $this->dispatcher->dispatch(SpellingNewErrorEvent::ON_NEW_ERROR, $event);

        $errors = $event->getErrors();

        if (count($errors) > 0) {
            return $errors;
        }

        try {
            $this->em->persist($spelling);
            $this->em->flush();
            return true;
        }
        catch (\Exception $e) {
            return [ 'database_error' ];
        }
    }

    /**
     * Get contributors and its count of fixes
     *
     * @return array
     */
    public function getContribute()
    {
        return $this->em
            ->createQuery("SELECT s spelling, count(s.corrector) cnt FROM $this->entityClass s WHERE s.corrected = true GROUP BY s.corrector ORDER BY cnt DESC")
            ->getResult();
    }

    /**
     * Get editors and its count of errors
     *
     * @return array
     */
    public function getEditors()
    {
        return $this->em
            ->createQuery("SELECT s spelling, count(s.creator) cnt FROM $this->entityClass s GROUP BY s.creator ORDER BY cnt DESC")
            ->getResult();
    }

    /**
     * Get url without system data
     *
     * @param string $url
     * @return string
     */
    public function getUrl($url)
    {
        $delimiterPos = strrpos($url, $this->dataDelimiter);
        if ($delimiterPos !== false) {
            return substr($url, 0, $delimiterPos);
        }
        return $url;
    }

    /**
     * Get url to correcting of error
     *
     * @param string $url
     * @param string $field
     * @param string $action
     * @return string|null
     */
    public function getCorrectingLink($url, $field = null, $action = 'edit')
    {
        if (!$url) return null;

        $delimiterPos = strrpos($url, $this->dataDelimiter);
        if ($delimiterPos !== false) {
            $data = explode('=', substr($url, $delimiterPos + strlen($this->dataDelimiter)), 3);
            $type = $data[0];
            $id = $data[1];
            $prefix = isset($data[2]) ? $data[2] : 'admin';
            try {
                return $this->router->generate($this->getRouteName($type, $action, $prefix), ['id' => $id]);
            }
            catch (\Exception $ex) {
                return null;
            }
        }

        try {
            $match = $this->router->match($url);

            if (!preg_match('/\\\\(\w+)Controller:/', $match['_controller'], $matches)) {
                return null;
            }
            $entity = strtolower($matches[1]);

            if (isset($match['id'])) {
                $id = $match['id'];
                return $this->router->generate($this->getRouteName($entity, $action), ['id' => $id]);
            } else {
                $id = $match[$field ?: $this->defaultFindBy];
                $customField = $field ? "?field=$field" : '';
                return $this->router->generate($this->getRouteName($entity, 'find'), ['id' => $id]) . $customField;
            }
        }
        catch (\Exception $ex) {
            return null;
        }
    }

    /**
     * Get count of not corrected spelling errors
     *
     * @return int
     */
    public function getNotCorrectedCount()
    {
        if (is_null($this->notCorrectedCount)) {
            $this->notCorrectedCount = $this->em
                ->createQuery("SELECT count(o) FROM $this->entityClass o WHERE o.corrected = 0")
                ->getSingleScalarResult();
        }
        return $this->notCorrectedCount;
    }

    /**
     * @param string $type
     * @param string $action
     * @param string $prefix
     * @return string
     */
    protected function getRouteName($type, $action, $prefix = 'admin')
    {
        return "{$prefix}_{$type}_{$action}";
    }
}