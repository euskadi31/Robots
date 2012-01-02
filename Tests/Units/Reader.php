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
namespace Robots\Tests\Units;

require_once __DIR__ . '/../../library/mageekguy.atoum.phar';
require_once __DIR__ . '/../../library/Robots/Reader.php';

use \mageekguy\atoum;
use Robots;

class Reader extends atoum\test
{
    public function __construct(score $score = null, locale $locale = null, adapter $adapter = null)
    {
        $this->setTestNamespace('Tests\Units');
        parent::__construct($score, $locale, $adapter);
    }
    
    public function testUrl()
    {
        $this->assert->string(Robots\Reader::url('http://www.google.com'))
            ->isEqualTo('http://www.google.com/robots.txt');
            
        $this->assert->string(Robots\Reader::url('http://www.google.com/robots.txt'))
            ->isEqualTo('http://www.google.com/robots.txt');
            
        $this->assert->string(Robots\Reader::url('http://www.google.com:80'))
            ->isEqualTo('http://www.google.com:80/robots.txt');
        
        $this->assert->exception(function() {
            Robots\Reader::url('www.google.com');
        })
        ->isInstanceOf('\RuntimeException');
        
        $this->assert->string(Robots\Reader::url('http://www.google.com/search'))
            ->isEqualTo('http://www.google.com/robots.txt');
    }
    
    public function testReader()
    {
        $reader = new Robots\Reader;
        
        $this->assert->object($reader)
            ->isInstanceOf('\Robots\Reader');
        
        $data = $reader->parse("# Disallow all crawlers access to certain pages.

        User-agent: * # test comment
        Disallow: /test/1
        Allow: /test/2*

        User-Agent: Googlebot
        Disallow: /test/3 #comment
        Allow: /test/4*


        # Sitemap files
        Sitemap: http://www.amazon.com/sitemap-manual-index.xml
        Sitemap: http://www.amazon.com/sitemap_dp_index.xml");
         
        $this->assert->array($data)->isEqualTo(array(
            'User-agent' => array(
                0 => array(
                    'Name' => '*',
                    'Disallow' => array(
                        0 => '/test/1'
                    ),
                    'Allow' => array(
                        0 => '/test/2*'
                    )
                ),
                1 => array(
                    'Name' => 'Googlebot',
                    'Disallow' => array(
                        0 => '/test/3'
                    ),
                    'Allow' => array(
                        0 => '/test/4*'
                    )
                )
            ),
            'Sitemap' => array(
                0 => 'http://www.amazon.com/sitemap-manual-index.xml',
                1 => 'http://www.amazon.com/sitemap_dp_index.xml'
            )
        ));
         
        $this->assert->exception(function() use ($reader) {
            $reader->parse("");
        })
        ->isInstanceOf('\RuntimeException')
        ->hasMessage('Contents is empty');;
    }
}