<?php
/**
 *
 * Class DomainTrieEntry
 *
 */
class DomainTrieEntry {
    /**
     * The domain
     *
     * @var   string
     **/
    public $domain = null;

    /**
     * The index
     *
     * @var   string
     **/
    public $index = null;

    /**
     * @param string $index
     * @param string $domain
     **/
    public function __construct($index, $domain) {
        $this->domain = $domain;
        $this->index = $index;
    }
}