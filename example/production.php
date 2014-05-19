<?php
// PRODUCTION STYLE USAGE

// Get router (and the inroute Router package)
$router = require 'router.php';

// Include site (of course autoloading is prefered)
require 'Working.php';
require 'HtmlFilter.php';

// Dispatch application
//echo $router->dispatch('/base/app/pagename', $_SERVER);
