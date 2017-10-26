<?php
require_once(__DIR__ . '/DomainTrieEntry.php');
require_once(__DIR__ . '/TrieNode.php');

/**
 *
 * Class DomainTrie
 *
 */
class DomainTrie {

    /**
     * Root-level TrieNode
     *
     * @var   TrieNode[]
     */
    protected $trie = null;

    /**
     * Create a new Trie
     */
    public function __construct() {
        $this->trie = new TrieNode();
    }

    /**
     * Adds a new entry to the Trie
     *
     * If the specified node already exists, its value will be overwritten
     *
     * @param   string  $key     Key for this node entry
     * @param   mixed   $value   Data Value for this node entry
     * @return  void
     * @throws  Exception if the provided key argument is empty
     *
     */
    public function add($key, $value = null) {
        if (!is_string(($key))) {
            throw new Exception('The key must be a string.');
        }

        if (empty($key)) {
            throw new Exception('Key value must not be empty');
        }

        $trieNodeEntry = $this->getTrieNodeByKey($key);
        $trieNodeEntry->value = $value;
    }

    /**
     * Return an array of key/value pairs for nodes matching a specified prefix
     *
     * @param   mixed   $prefix    The key for the node that we want to return
     * @return  TrieEntiy[] The search result
     */
    public function search($prefix) {
        $trieNode = $this->getTrieNodeByKey($prefix);

        if (empty($trieNode)) {
            return array();
        }

        return $this->getAllChildren($trieNode, $prefix);
    }

    /**
     * Fetch a node by key. If it does not exists, create a new one to return
     *
     * @param   mixed     $key  The key
     * @return  TrieNode
     */
    protected function getTrieNodeByKey($key) {
        $trieNode = $this->trie;
        $keyLen = strlen($key);

        $i = 0;

        // Create node recursively
        while ($i < $keyLen) {
            $character = $key[$i++];

            if (!isset($trieNode->children[$character])) {
                $trieNode->children[$character] = new TrieNode();
            }

            $trieNode = $trieNode->children[$character];
        };

        return $trieNode;
    }

    /**
     * Fetch all child nodes with a value below a specified node
     *
     * @param   TrieNode   $trieNode   Node to start the search
     * @param   string     $prefix     Full Key for the requested start point
     * @return  DomainTrieEntiy[] The trie nodes as a DomainTrieEntry array
     */
    protected function getAllChildren(TrieNode $trieNode, $prefix) {
        $return = array();

        // Include the start node itself if it has value
        if ($trieNode->value !== null) {
            $return[] = new DomainTrieEntry($trieNode->value, $prefix);
        }

        foreach ($trieNode->children as $character => $trie) {
            $return = array_merge($return, $this->getAllChildren($trie, $prefix . $character));
        }

        return $return;
    }
}
