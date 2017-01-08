<?php

namespace Lexxpavlov\SpellingBundle\Feature;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Lexxpavlov\SpellingBundle\Entity\SpellingSecurity;
use Lexxpavlov\SpellingBundle\Event\SpellingNewErrorEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class FloodControl
{
    /** @var EntityManager */
    protected $em;

    /** @var EntityRepository */
    protected $repository;

    /** @var Request */
    protected $request;

    /** @var string */
    protected $entityClass;

    /** @var int */
    protected $queryInterval;

    /** @var int */
    protected $banPeriod;

    /** @var int */
    protected $banCheckPeriod;

    /** @var int */
    protected $banCount;

    public function __construct(
        EntityManager $entityManager,
        RequestStack $requestStack,
        $entityClass,
        $queryInterval,
        $banPeriod,
        $banCheckPeriod,
        $banCount)
    {
        $this->em = $entityManager;
        $this->repository = $entityManager->getRepository($entityClass);
        $this->request = $requestStack->getCurrentRequest();
        $this->entityClass = $entityClass;
        $this->queryInterval = $queryInterval;
        $this->banPeriod = $banPeriod;
        $this->banCheckPeriod = $banCheckPeriod;
        $this->banCount = $banCount;
    }

    public function onNewError(SpellingNewErrorEvent $event)
    {
        $this->clearExpired();

        if ($this->queryInterval < 0) {
            return;
        }

        $ip = $this->request->getClientIp();
        $now = time();

        /** @var SpellingSecurity $security */
        $security = $this->repository->findOneBy(['ip' => $ip]);

        if (!$security) {
            $security = new $this->entityClass($ip, new \DateTime);
            $this->em->persist($security);
            $this->em->flush();
            return;
        }

        if (!is_null($security->getBannedUntil()) && $now < $security->getBannedUntil()->getTimestamp()) {
            $event->addError('ip_banned');
            return;
        }

        if (($this->queryInterval + $security->getLastQuery()->getTimestamp()) > $now) {
            $event->addError('too_much_errors');

            $security
                ->incrementCount()
                ->setErrorTime(new \DateTime);

            if ($this->banCheckPeriod > 0 && $this->banCount > 0 && $security->getCount() >= $this->banCount) {
                $event->addError('ip_banned');
                $security->setBannedUntil($this->getTime(-$this->banPeriod));
            }
        }

        $security->setLastQuery(new \DateTime);
        $this->em->flush();
    }

    public function clearExpired()
    {
        $this->em->createQuery("delete from $this->entityClass s ".
                "where s.lastQuery < :queryTime and (s.errorTime is null or s.errorTime < :errorTime) ".
                "  or (s.bannedUntil is not null and s.bannedUntil < :now)")
            ->setParameter('queryTime', $this->getTime($this->queryInterval))
            ->setParameter('errorTime', $this->getTime($this->banCheckPeriod))
            ->setParameter('now', new \DateTime)
            ->execute();
    }

    /**
     * @param int $seconds
     * @return \DateTime
     */
    protected function getTime($seconds)
    {
        $sign = $seconds > 0 ? '-' : '+';
        return (new \DateTime)->modify("$sign$seconds seconds");
    }
}