<?php

namespace Lexxpavlov\SpellingBundle\Event;

use Lexxpavlov\SpellingBundle\Entity\Spelling;
use Symfony\Component\EventDispatcher\Event;

class SpellingNewErrorEvent extends Event
{
    const ON_NEW_ERROR = 'lexxpavlov_spelling.new_error';

    /** @var array */
    private $errors = [];

    /** @var Spelling */
    private $spelling;

    function __construct(Spelling $spelling)
    {
        $this->spelling = $spelling;
    }

    /**
     * @return Spelling
     */
    public function getSpelling()
    {
        return $this->spelling;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param array $errors
     */
    public function setErrors(array $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @param string      $error
     * @param string|null $key
     */
    public function addError($error, $key = null)
    {
        if (is_null($key)) {
            $this->errors[] = $error;
        } else {
            $this->errors[$key] = $error;
        }
    }
}