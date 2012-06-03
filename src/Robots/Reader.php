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
namespace Robots;

use RuntimeException;

class Reader
{
    const USER_AGENT    = 'User-agent';
    const DISALLOW      = 'Disallow';
    const ALLOW         = 'Allow';
    const SITEMAP       = 'Sitemap';
    
    /**
     * Get robots.txt url
     * 
     * @param String $url
     * @return String
     */
    public static function url($url)
    {
        if(strpos($url, 'robots.txt') === false) {
            $parts = parse_url($url);

            if(isset($parts['scheme'])) {
                $url = $parts['scheme'] . '://';
            } else {
                throw new RuntimeException(sprintf('url "%s" is invalid.', $url));
            }
            
            $url .= $parts['host'];
            
            if(isset($parts['port'])) {
                $url .= ':' . $parts['port'];
            }
            
            $url .= '/robots.txt';
            unset($parts);
            return $url;
        }
        
        return $url;
        
    }
    
    protected function _cleanComment($value)
    {
        if(strpos($value, '#') !== false) {
            return trim(current(explode('#', $value)));
        }
        return $value; 
    }
    
    /**
     * Normalize name
     *
     * @param String $key
     * @return String
     */
    protected function _normalize($key)
    {
        return ucwords(strtolower(trim($key)));
    }
    
    /**
     * Parse robots.txt
     * 
     * @param String $content
     * @return Array
     */
    public function parse($content)
    {
        if(empty($content)) {
            throw new RuntimeException("Contents is empty");
        }
        
        $robots = array();
        
        $data = explode("\n", $content);
        unset($content);
        $i=0;
        foreach($data as $row) {
            
            if(strpos($row, ': ') !== false) {
                list($key, $val) = explode(': ', $row);
                $key = $this->_normalize($key);
                $val = $this->_cleanComment($val);
                
                if($key == self::USER_AGENT) {

                    if(!isset($robots[self::USER_AGENT])) {
                        $robots[self::USER_AGENT] = array();
                    }
                    
                    $useragent = array(
                        'Name' => $val
                    );
                    
                    unset($data[$i]);
                    $j = $i;
                    foreach($data as $item) {
                        
                        if(strpos($item, ': ') !== false) {
                            
                            list($subkey, $subval) = explode(': ', $item);
                            $subkey = $this->_normalize($subkey);
                            $subval = $this->_cleanComment($subval);
                            
                            if($subkey == self::USER_AGENT || $subkey == self::SITEMAP) {
                                prev($data);
                                break;
                            }
                            
                            switch($subkey) {
                                case self::DISALLOW:
                                    if(!isset($useragent[self::DISALLOW])) {
                                        $useragent[self::DISALLOW] = array();
                                    }
                                    
                                    $useragent[self::DISALLOW][] = $subval;
                                    unset($data[$j]);
                                    break;
                                case self::ALLOW:
                                    if(!isset($useragent[self::ALLOW])) {
                                        $useragent[self::ALLOW] = array();
                                    }
                                    
                                    $useragent[self::ALLOW][] = $subval;
                                    unset($data[$j]);
                                    break;
                            }
                            
                            
                        } else {
                            unset($data[$j]);
                        }
                        $j++;
                    }
                    
                    $robots[$key][] = $useragent;
                    
                } elseif($key == self::SITEMAP) {
                    
                    if(!isset($robots[self::SITEMAP])) {
                        $robots[self::SITEMAP] = array();
                    }
                    
                    $robots[self::SITEMAP][] = $val;
                    unset($data[$i]);
                }
                
            } else {
                unset($data[$i]);
            }

            $i++;
        }
        unset($data);
        
        return $robots;
    }
}