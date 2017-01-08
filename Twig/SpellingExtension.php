<?php
namespace Lexxpavlov\SpellingBundle\Twig;

use Lexxpavlov\SpellingBundle\Service\Spelling;

class SpellingExtension extends \Twig_Extension
{
    /** @var Spelling */
    private $spelling;

    /**
     * @param Spelling $spelling
     */
    public function __construct(Spelling $spelling)
    {
        $this->spelling = $spelling;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('spelling_url', [$this, 'getUrl']),
            new \Twig_SimpleFunction('spelling_correcting_link', [$this, 'getCorrectingLink']),
            new \Twig_SimpleFunction('spelling_count', [$this, 'getNotCorrectedCount']),
        ];
    }

    /**
     * @param string $url
     * @return string
     */
    public function getUrl($url)
    {
        return $this->spelling->getUrl($url);
    }

    /**
     * @param string      $url
     * @param string|null $field
     * @return string
     */
    public function getCorrectingLink($url, $field = null)
    {
        return $this->spelling->getCorrectingLink($url, $field);
    }

    /**
     * @return int
     */
    public function getNotCorrectedCount()
    {
        return $this->spelling->getNotCorrectedCount();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'lexxpavlov_spelling_extension';
    }
}