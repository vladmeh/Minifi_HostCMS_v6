<?php
defined('HOSTCMS') || exit('HostCMS: access denied.');
/**
 * Utility functions for generating URIs in HTML files
 *
 * @warning These functions execute min/groupsConfig.php, sometimes multiple times.
 * You must make sure that functions are not redefined, and if your use custom sources,
 * you must require_once dirname(__FILE__) . '/lib/Minify/Source.php' so that
 * class is available.
 *
 * @package Minify
 */

class Min_Utils
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        if (!class_exists('Minify_Loader', false)) {
            require CMS_FOLDER . 'min/config.php';
            require $min_libPath . '/Minify/Loader.php';
            Minify_Loader::register();
        }
    }

    /**
     * The singleton instances.
     * @var mixed
     */
    static public $instance = NULL;

    /**
     * Register an existing instance as a singleton.
     * @return object
     */
    static public function instance()
    {
        if (is_null(self::$instance))
        {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * Get an HTML-escaped Minify URI for a group or set of files. By default, URIs
     * will contain timestamps to allow far-future Expires headers.
     *
     * <code>
     * <link rel="stylesheet" type="text/css" href="<?= Minify_getUri('css'); ?>" />
     * <script src="<?= Minify_getUri('js'); ?>"></script>
     * <script src="<?= Minify_getUri(array(
     *      '//scripts/file1.js'
     *      ,'//scripts/file2.js'
     * )); ?>"></script>
     * </code>
     *
     * @param mixed $keyOrFiles a group key or array of file paths/URIs
     * @param array $opts options:
     *   'farExpires' : (default true) append a modified timestamp for cache revving
     *   'debug' : (default false) append debug flag
     *   'charset' : (default 'UTF-8') for htmlspecialchars
     *   'minAppUri' : (default '/min') URI of min directory
     *   'rewriteWorks' : (default true) does mod_rewrite work in min app?
     *   'groupsConfigFile' : specify if different
     * @return string
     */
    public function Minify_getUri($keyOrFiles, $opts = array())
    {
        return Minify_HTML_Helper::getUri($keyOrFiles, $opts);
    }


    /**
     * Get the last modification time of several source js/css files. If you're
     * caching the output of Minify_getUri(), you might want to know if one of the
     * dependent source files has changed so you can update the HTML.
     *
     * Since this makes a bunch of stat() calls, you might not want to check this
     * on every request.
     *
     * @param array $keysAndFiles group keys and/or file paths/URIs.
     * @return int latest modification time of all given keys/files
     */
    public function Minify_mtime($keysAndFiles, $groupsConfigFile = null)
    {
        $gc = null;
        if (! $groupsConfigFile) {
            $groupsConfigFile = dirname(__FILE__) . '/groupsConfig.php';
        }
        $sources = array();
        foreach ($keysAndFiles as $keyOrFile) {
            if (is_object($keyOrFile)
                || 0 === strpos($keyOrFile, '/')
                || 1 === strpos($keyOrFile, ':\\')) {
                // a file/source obj
                $sources[] = $keyOrFile;
            } else {
                if (! $gc) {
                    $gc = (require $groupsConfigFile);
                }
                foreach ($gc[$keyOrFile] as $source) {
                    $sources[] = $source;
                }
            }
        }
        return Minify_HTML_Helper::getLastModified($sources);
    }

    /**
     * Options utils
     * @var array
     */
    protected $opts = array();

    /**
     * Show minify css
     * @param mixed $keyOrFiles a group key or array of file paths/URIs
     * @param boolean $debug optional(default false) append debug flag
     * @return string
     */
    public function showMinCss($keysOrFiles, $debug=false){
        $this->opts['debug'] = ($debug)? $debug : false;
        echo '<link rel="stylesheet" type="text/css" href="'.$this->Minify_getUri($keysOrFiles, $this->opts).'" />';
    }

    /**
     * Show minify css current template
     * @param boolean $debug optional(default false) append debug flag
     * @return string
     */
    public function showMinCss_Tmp($debug = false){
        $this->opts['debug'] = (isset($debug))? $debug : false;
        $aCss = array_reverse(Core_Page::instance()->css);
        array_walk($aCss, function(&$n) {
          $n = '/'.$n;
        });
        echo '<link rel="stylesheet" type="text/css" href="'.$this->Minify_getUri($aCss, $this->opts).'" />';
    }

    /**
     * Show minify js
     * @param mixed $keyOrFiles a group key or array of file paths/URIs
     * @param boolean $debug optional(default false) append debug flag
     * @return string
     */
    public function showMinJs($keysOrFiles, $debug=false){
        $this->opts['debug'] = ($debug)? $debug : false;
        echo '<script type="text/javascript" src="'.$this->Minify_getUri($keysOrFiles, $this->opts).'"></script>';
    }
}
