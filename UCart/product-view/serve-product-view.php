<?php
// Set the content type to HTML
header('Content-Type: text/html');

// Read and output the contents of index.html
echo file_get_contents('../template/home-nav.html');
echo file_get_contents('product-view.html');
?>