<?php
/**
 *
 * Class TrieNode
 *
 */
class TrieNode {
    /**
     * Array of child nodes indexed by next character
     *
     * @var   TrieNode[]
     **/
    public $children = array();

    /**
     * Data value (empty unless this is an end node)
     *
     * @var   mixed
     **/
    public $value = null;
}