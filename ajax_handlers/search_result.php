<?php
ini_set('memory_limit','256M');

require_once(__DIR__ . '/../classes/DomainTrie.php');

define('CACHE_KEY', 'domain_trie');

// Cache the big trie, which is quite expensive to init.
$domainTrie = apc_fetch(CACHE_KEY);

if (empty($domainTrie)) {
    try {
        $domainTrie = new DomainTrie();
        $raw_data_file = __DIR__ . '/../data/top-20k.csv';

        if (file_exists($raw_data_file) && ($fp = fopen($raw_data_file, "r")) !== false) {

            while ($line = fgetcsv($fp)) {
                if (empty($line[0]) || empty($line[1])) {
                    continue;
                }

                $domainTrie->add($line[1], $line[0]);
            }
        } else {
            throw new Exception('Failed to instantiate the DomainTrie. Data file is not available.');
        }
    }
    catch(Exception $e) {
        header("HTTP/1.0 500 Internal Server Error." . $e->getMessage());
        die();
    }

    apc_store(CACHE_KEY, $domainTrie, 10800); // Cache for 3 days
}

header('Content-Type: application/json');

$search_str = trim($_GET['s']);

if (strlen($search_str) === 0) {
    die('[]');
}

$search_result = $domainTrie->search($search_str);
echo json_encode($search_result);
