<?php

namespace Lexxpavlov\SpellingBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

abstract class Spelling
{
    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $prefix;

    /**
     * @var string
     */
    protected $error;

    /**
     * @var string
     */
    protected $suffix;

    /**
     * @var string
     */
    protected $comment;

    /**
     * @var boolean
     */
    protected $corrected = false;

    /**
     * @var \DateTime
     */
    protected $created;

    /**
     * @var \DateTime
     */
    protected $updated;

    /**
     * @var UserInterface
     */
    protected $creator;

    /**
     * @var UserInterface
     */
    protected $corrector;

    /**
     * @param string $url
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $prefix
     * @return $this
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @param string $error
     * @return $this
     */
    public function setError($error)
    {
        $this->error = $error;

        return $this;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param string $suffix
     * @return $this
     */
    public function setSuffix($suffix)
    {
        $this->suffix = $suffix;

        return $this;
    }

    /**
     * @return string
     */
    public function getSuffix()
    {
        return $this->suffix;
    }

    /**
     * @return string Text of error, with prefix and suffix
     */
    public function getText()
    {
        return $this->prefix . $this->error . $this->suffix;
    }

    /**
     * @return string Text of error, with prefix and suffix
     */
    public function getTextHtml($cssErrorClass = 'spelling-error')
    {
        return $this->prefix . "<span class=\"$cssErrorClass\">" . $this->error . '</span>' . $this->suffix;
    }

    /**
     * @param string $comment
     * @return $this
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param boolean $corrected
     * @return $this
     */
    public function setCorrected($corrected = true)
    {
        $this->corrected = $corrected;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isCorrected()
    {
        return $this->corrected;
    }

    /**
     * @param \DateTime $created
     * @return $this
     */
    public function setCreated(\DateTime $created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param \DateTime $updated
     * @return $this
     */
    public function setUpdated(\DateTime $updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param UserInterface $creator
     * @return $this
     */
    public function setCreator(UserInterface $creator)
    {
        $this->creator = $creator;

        return $this;
    }

    /**
     * @return UserInterface
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * @param UserInterface $corrector
     * @return $this
     */
    public function setCorrector(UserInterface $corrector)
    {
        $this->corrector = $corrector;

        return $this;
    }

    /**
     * @return UserInterface
     */
    public function getCorrector()
    {
        return $this->corrector;
    }
}
