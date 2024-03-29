<?php
/**
 * Groups configuration for default Minify implementation
 * @package Minify
 */

/**
 * You may wish to use the Minify URI Builder app to suggest
 * changes. http://yourdomain/min/builder/
 *
 * See http://code.google.com/p/minify/wiki/CustomSource for other ideas
 **/

return array(
    'js' => array('//hostcmsfiles/jquery/jquery.js',
                  '//hostcmsfiles/jquery/jquery.validate.js',
                  '//hostcmsfiles/jquery/lightbox/js/jquery.lightbox.js'),
    'jsTmp' => array('//templates/template1/hostcms.js'),
    'css' => array('//hostcmsfiles/jquery/lightbox/css/jquery.lightbox.css'),
);
