<?php namespace System\Minify;

/**
 * Helper for images
 *
 * @author Viachaslau Lyskouski
 * @since 2015-11-16
 */
class Images
{

    const PATH = '/img%s/%s';

    protected $pref = '';
    protected $path = '';

    public function __construct()
    {
        if (\System\Registry::config()->getMinimize()) {
            $this->pref = '.min';
        }

        $this->path = realpath(\System\Registry::config()->getConfigPath() . '/../public');
    }

    public function adapt($path)
    {
        return str_replace('/img/', $this->get(), $path);
    }

    /**
     * Callback for images translation
     *
     * @param string $pattern - pattern URL
     * @param string $suff
     * @return function
     */
    public function adaptWork($pattern, $suff = '', $def = 'default.png')
    {
        $imgPath = $this;
        $path = $this->path;
        $a = explode('/', $pattern);
        return function($data) use ($a, $path, $imgPath, $suff, $def) {
            if (sizeof($a) < 2) {
                $data = $imgPath->adapt($data);
            } elseif (!$data || $data[0] === '{' || $data[0] === '[' || $data === '/img/logo.jpg') {
                $data = $imgPath->getWork() . $a[1] . $suff . '.svg';
                if (!file_exists($path . $data)) {
                    $data = $imgPath->getWork() . $a[0] . $suff . '.svg';
                    if (!file_exists($path . $data)) {
                        $data = $imgPath->get() . 'css/el_notion/' . $def;
                    }
                }
            } else {
                $data = $imgPath->adapt($data);
            }
            return $data;
        };
    }

    public function adaptWorkUrl($pattern, $suff = '')
    {
        $a = explode('/', $pattern);
        reset($a);
        $type = end($a);
        while (preg_match('/^[0-9i]{1,}$/', $type)) {
            $type = prev($a);
        }
        $path = $this->getWork() . $type . $suff . '.svg';
        if (!file_exists(\System\Registry::config()->getPublicPath() . $path)) {
            $type = prev($a) . '/' . $type;
        }
        return $this->getWork() . $type . $suff . '.svg';
    }

    public function adaptAccount($type, $suff = '')
    {
        $aTypes = \Defines\User\Account::getTextList();
        return $this->getAccount() . strtolower($aTypes[$type]) . $suff . '.svg';
    }


    public function get($path = '')
    {
        return sprintf(self::PATH, $this->pref, $path);
    }

    public function getWork()
    {
        return $this->get('css/el_notion/work/');
    }

    public function getAccount()
    {
        return $this->get('css/el_notion/accounts/');
    }
}
