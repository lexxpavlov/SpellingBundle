<?php

namespace Lexxpavlov\SpellingBundle\Entity;

abstract class SpellingSecurity
{
    /**
     * @var string
     */
    protected $ip;

    /**
     * @var \DateTime
     */
    protected $lastQuery;

    /**
     * @var int
     */
    protected $count = 0;

    /**
     * @var \DateTime
     */
    protected $errorTime;

    /**
     * @var \DateTime
     */
    protected $bannedUntil;

    public function __construct($ip = null, \DateTime $lastQuery = null)
    {
        $this->ip = $ip;
        $this->lastQuery = $lastQuery;
    }

    /**
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     * @return $this
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getLastQuery()
    {
        return $this->lastQuery;
    }

    /**
     * @param \DateTime $lastQuery
     * @return $this
     */
    public function setLastQuery($lastQuery)
    {
        $this->lastQuery = $lastQuery;
        return $this;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param int $count
     * @return $this
     */
    public function setCount($count)
    {
        $this->count = $count;
        return $this;
    }

    /**
     * @return $this
     */
    public function incrementCount()
    {
        $this->count++;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getErrorTime()
    {
        return $this->errorTime;
    }

    /**
     * @param \DateTime $errorTime
     * @return $this
     */
    public function setErrorTime($errorTime)
    {
        $this->errorTime = $errorTime;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getBannedUntil()
    {
        return $this->bannedUntil;
    }

    /**
     * @param \DateTime $bannedUntil
     * @return $this
     */
    public function setBannedUntil($bannedUntil)
    {
        $this->bannedUntil = $bannedUntil;
        return $this;
    }
}
