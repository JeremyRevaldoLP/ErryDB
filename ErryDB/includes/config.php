<?php
// ================================================
// CONFIG.PHP - Konfigurasi Global Proyek
// ================================================

// ================================================
// Sembunyikan Deprecated Warning dari EasyRdf
// (kompatibilitas PHP 8.1+ dengan EasyRdf 1.1.1)
// ================================================
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
ini_set('display_errors', '0');

// Path ke file RDF
define('RDF_FILE', __DIR__ . '/../data/data.rdf');
define('OWL_FILE', __DIR__ . '/../data/ontology.owl');

// Base URI / Namespace
define('NS_FILM', 'http://www.semanticweb.org/filmrekomendasi/ontology#');
define('NS_RDF',  'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
define('NS_RDFS', 'http://www.w3.org/2000/01/rdf-schema#');
define('NS_OWL',  'http://www.w3.org/2002/07/owl#');
define('NS_XSD',  'http://www.w3.org/2001/XMLSchema#');

// Prefix SPARQL (dipakai di semua query)
define('SPARQL_PREFIXES', "
    PREFIX film: <http://www.semanticweb.org/filmrekomendasi/ontology#>
    PREFIX rdf:  <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
    PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
    PREFIX xsd:  <http://www.w3.org/2001/XMLSchema#>
    PREFIX owl:  <http://www.w3.org/2002/07/owl#>
");

// Nama aplikasi
define('APP_NAME', 'ErryDB - Sistem Rekomendasi Film');
?>