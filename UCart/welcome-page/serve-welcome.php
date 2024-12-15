<?php
// Set the content type to HTML
header('Content-Type: text/html');

// Read and output the contents of index.html
echo file_get_contents('../template/nav-bar.html');
echo file_get_contents('welcome.html');
?>