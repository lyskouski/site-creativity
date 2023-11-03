<?php namespace System\Minify;

use Engine\Request\Input;

/**
 * Description of MetaFiles
 *
 * @author Viachaslau Lyskouski
 */
class MetaFiles
{

    const DEF_URL = 'default';
    const TYPE_JS = 'js';
    const TYPE_CSS = 'css';
    const IS_EXTENDED = '@extend';

    protected $url;
    protected $default = array();
    protected $config = array();

    public function __construct($urlPath = null)
    {
        if (!$urlPath) {
            $urlPath = (new Input)->getUrl($urlPath);
        }

        $path = \System\Registry::config()->getConfigPath() . '/task/params/minify.json';
        $all = json_decode(file_get_contents($path), true);

        $this->default = $all[self::DEF_URL];
        $this->identifyPath($urlPath, $all);

        if (!$this->url) {
            $this->url = self::DEF_URL;
        }
        $this->config = $this->bindAllPath($this->url, $all);
    }

    protected function bindAllPath($path, $list)
    {
        // Extends
        foreach ($list as $i => $config) {
            if (isset($config[self::IS_EXTENDED])) {
                $extra = $list[$config[self::IS_EXTENDED]];
                foreach ([self::TYPE_JS, self::TYPE_CSS] as $key) {
                    $config[$key] = array_merge($extra[$key], $config[$key]);
                }
                $list[$i] = $config;
            }
        }
        // Last position
        foreach ($list as $i => $config) {
            foreach ([self::TYPE_JS, self::TYPE_CSS] as $key) {
                $push = array();
                foreach ($config[$key] as $k => $val) {
                    if ($val[0] === '*') {
                        $push[] = substr($val, 1);
                        unset($config[$key][$k]);
                    }
                }
                if ($push) {
                    $list[$i][$key] = array_merge(array_values($config[$key]), $push);
                }
            }
        }

        return $list[$path];
    }

    protected function identifyPath($url, $all)
    {
        if (array_key_exists($url, $all)) {
            $this->url = $url;
        } elseif ($url) {
            $a = array_slice(explode('/', $url), 0, -1);
            $this->identifyPath(implode('/', $a), $all);
        }
    }

    public function getList($type)
    {
        $list = array();
        if (array_key_exists($type, $this->config)) {
            $list = $this->config[$type];
        }
        return array_filter($list, function($path) {
            return $path[0] !== '!';
        });
    }

    public function getDefault()
    {
        return $this->url;
    }

    public function getDatetime()
    {
        return $this->config['datetime'];
    }

    public function getVersion()
    {
        return $this->default['version'];
    }

    public function getSuffix()
    {
        $suff = '?_v=' . $this->getVersion();
        //if (\System\Registry::config()->getMinimize()) {
        //    $suff = '';
        //}
        return $suff;
    }

    public function getPath($url, $type)
    {
        if (strpos($url, '//') === false) {
            $url = $this->getPrefix($url, $type) . $url . $this->getSuffix();
        }
        return $url;
    }

    public function getPrefix($url, $type)
    {
        $dir = '';
        //if (array_key_exists($type, $this->config)) {
        if (\System\Registry::config()->getMinimize()) {
            $type .= '.min';
            if (strpos($url, $this->getDefault()) !== 0) {
                $type .= '/' . $this->getVersion();
            }
        }
        $dir .= "/$type/";
        //}
        return $dir;
    }

}
