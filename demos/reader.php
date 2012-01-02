<?php
/**
 * @package     Robots
 * @author      Axel Etcheverry <axel@etcheverry.biz>
 * @copyright   Copyright (c) 2011 Axel Etcheverry (http://www.axel-etcheverry.com)
 * Displays     <a href="http://creativecommons.org/licenses/MIT/deed.fr">MIT</a>
 * @license     http://creativecommons.org/licenses/MIT/deed.fr    MIT
 */

/**
 * @namespace
 */
namespace Application;

require_once __DIR__ . '/../library/Robots/Reader.php';

use Robots;

$r = new Robots\Reader();

print_r($r->parse(
	file_get_contents(Robots\Reader::url('http://www.robotstxt.org'))
));

exit;

$robots = "# Disallow all crawlers access to certain pages.

User-agent: * # test comment
Disallow: /test/1
Allow: /test/2*

User-Agent: Googlebot
Disallow: /test/3 #comment
Allow: /test/4*


# Sitemap files
Sitemap: http://www.amazon.com/sitemap-manual-index.xml
Sitemap: http://www.amazon.com/sitemap_dp_index.xml
";

print_r($r->parse($robots));