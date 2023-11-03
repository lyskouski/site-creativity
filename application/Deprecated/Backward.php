<?php namespace Deprecated;

/**
 * Backward compatibility with previous versions
 *
 * @since 2016-01-28
 * @author Viachaslau Lyskouski
 * @package Deprecated
 */
class Backward
{

    /**
     * @var array
     */
    protected $params;

    /**
     * @var string
     */
    protected $language = '';

    /**
     * @var boolean
     */
    protected $isSubdomain = false;

    public function __construct()
    {
        $request = new \Engine\Request\Input();
        $name = $request->getServer('HTTP_HOST');

        $a = explode('.', $name);
        $this->isSubdomain = false;
        if (strlen($a[0]) == 2 && $a[0] !== 'cr') {
            $this->language = strtolower($a[0]);
            if (!in_array($this->language, \Defines\Language::getList())) {
                $this->language = 'ru';
            }
            $this->isSubdomain = true;

        } elseif (strpos($name, 'www.') !== false) {
            $this->language = \System\Registry::translation()->getTargetLanguage();
            $this->isSubdomain = true;

        } elseif (
                !in_array('http://' . $name, \System\Registry::config()->getUrlList())
                && !in_array('https://' . $name, \System\Registry::config()->getUrlList())
        ) {
            $this->isSubdomain = true;
        }

        $this->params = $request->getGet();
    }

    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Check redirects from Citadel
     * @link citadel-liga.info
     */
    protected function citadelRedirect($pg, $act)
    {
        $newContent = null;
        switch ($pg) {
            case 'wtry':
                $table = 'nwrtry';
                break;
            case 'poem':
                $table = 'nwrpoem';
                break;
            case 'gold':
            case 'try':
                $table = 'nwriter';
                break;
            case 'verse':
                $table = 'nwrpoem';
                break;
            default:
                $table = 'next';
        }
        $db = new \System\Database\Connector(\Defines\Connector::MYSQL, 'creativi_db');
        $res = $db->getConnection()->query(
            "SELECT `AUTH` `user`, `NAME` `topic`
            FROM `{$table}`
            WHERE `MNICK` = '{$pg}' AND `MID` = " . (int) $act
        );
        if ($res && $res->rowCount()) {
            $row = $res->fetch(\PDO::FETCH_ASSOC);
            $row['topic'] = rtrim($row['topic'], '.?!');

            $helper = (new \Data\ContentHelper)->getRepository();
            $newContent = $helper->findOneBy(array(
                'author' => $this->findByUsername($row['user']),
                'language' => \Defines\Language::RU,
                'type' => 'og:title',
                'search' => $row['topic']
            ));

        }
        return $newContent;
    }

    public function getRandom()
    {
        $db = new \System\Database\Connector(\Defines\Connector::MYSQL, 'cr_main');
        $res = $db->getConnection()->query(
            "SELECT `id`
            FROM `content`
            WHERE (`pattern` LIKE 'oeuvre/%/i%' AND `pattern` NOT LIKE '%/search/%') AND `type` = 'og:title'
            ORDER BY RAND()
            LIMIT 1"
        );
        $a = $res->fetch(\PDO::FETCH_ASSOC);
        return (new \Data\ContentHelper)->getRepository()->find($a['id']);
    }

    /**
     * Find user by name
     *
     * @param string $username
     * @return \Data\Doctrine\Main\User
     */
    private function findByUsername($username) {
        switch ($username) {
            case 'Огненный котяра': $username = 'FieryCat'; break;
        }
        return (new \Data\UserHelper)->getUserByName([
            $username,
            str_replace(' ', '_', $username),
            str_replace('_', ' ', $username),
            str_replace(array('_', '%20'), array(' ', ' '), $username)
        ]);
    }

    /**
     * Find user personal page
     */
    protected function userRedirect($username)
    {
        $user = $this->findByUsername($username);
        if (!$user) {
            throw new \Error\Validation(
                \System\Registry::translation()->sys('LB_HEADER_404'),
                \Defines\Response\Code::E_NOT_FOUND
            );
        }
        return (new \Data\ContentHelper)->getRepository()->findOneBy(array(
            'language' => \Defines\Language::RU,
            'type' => 'og:title',
            'pattern' => "person/{$user->getUsername()}"
        ));
    }

    protected function replaceAttr($str)
    {
        return str_replace(array('_', '%20', "/'"), array(' ', ' ', "'"), $str);
    }

    /**
     * Check redirects from the previous version (www|ru|en|de|fr|ua).creativity.by
     * @link creativity.by
     *
     * @param \Engine\Request\Input $request
     */
    protected function subdomainRedirect($request)
    {
        $helper = new \Data\ContentHelper();
        $params = $request->getGet();

        $newContent = null;
        $search = '';
        // Check first attribute
        switch ($request->getGet('/0')) {
            case 'Мнемоніка':
            case 'Мнемоника':
            case 'Мнемоніка':
            case 'Mnemonics':
            case 'Gedächtniskunst':
            case 'Mnémonique':
                $search = 'mind/article';
                break;
            case 'База ведаў':
            case 'База знаний':
            case 'База знань':
            case 'Knowledge base':
            case 'Wissensdatenbank':
            case 'Base de connaissances':
            case 'База_ведаў':
            case 'База_знаний':
            case 'База_знань':
            case 'Knowledge_base':
            case 'Base_de_connaissances':
                $search = 'cognition';
                break;
            case 'Творчасць':
            case 'Творчество':
            case 'Творчість':
            case 'Оeuvre':
            case 'Schaffen':
            case 'Сréation':
                $search = 'oeuvre';
                break;

            case 'Дакладныя_навукі':
                $this->language = \Defines\Language::BE;
                $search = 'mind/article/search/Дакладныя навукі';
                break;
            case 'Датыкальныя_памяць':
                $this->language = \Defines\Language::BE;
                $search = 'mind/article/search/Датыкальная памяць';
                break;
            case 'Зрокавая_памяць':
                $this->language = \Defines\Language::BE;
                $search = 'mind/article/search/Глядзельная памяць';
                break;
            case 'mobile':
                $search = 'index';
                break;


        }
        // Fix second parameter
        switch ($request->getGet('/1')) {
            case '¶:Семіотика':
                $this->language = \Defines\Language::UK;
                $request->setGet('/1', '¶:Семиотика');
                break;
        }

        if (!$this->language) {
            $this->language = \Defines\Language::RU;
        }

        // About author
        $about = array(
            'Пра_аўтара',
            'Об_авторе',
            'Про_автора',
            'About_the_Author',
            'Über_den_Autor',
            "À_propos_de_l'Auteur",
            "À_propos_de_l/'Auteur",
            'Пра аўтара',
            'Об авторе',
            'Про автора',
            'About the Author',
            'Über den Autor',
            "À propos de l'Auteur",
            "À propos de l/'Auteur",
        );
        if (in_array($request->getGet('/1'), $about, true)) {
            /* @var $user \Data\Doctrine\Main\User */
            $user = \System\Registry::connection()->getRepository(\Defines\Database\CrMain::USER)->findOneBy(array(
                'username' => array(
                    $request->getGet('/0'),
                    $this->replaceAttr($request->getGet('/0'))
                )
            ));
            if ($user) {
                $newContent = new \Data\Doctrine\Main\Content;
                $newContent->setLanguage($this->language)
                    ->setType('og:title')
                    ->setPattern('person/' . $user->getUsername());
            }

        // Site navigation
        } elseif (sizeof($params) > 1) {
            $key = end($params);
            if (strpos($key, ':') === 0) {
                $key = prev($params);
            }
            // Search by keywords
            if (strpos($key, ':')) {
                $a = explode(':', $key);
                $newContent = $helper->getRepository()->findOneBy(array(
                    'type' => 'og:title',
                    'pattern' => [
                        'oeuvre/search/' . $this->replaceAttr(end($a)),
                        'mind/article/search/' . $this->replaceAttr(end($a))
                    ]
                ));
            // Site categories
            } else {
                $newContent = $helper->getRepository()->findOneBy(array(
                    'author' => $this->findByUsername($request->getGet('/0')),
                    'type' => 'og:title',
                    'search' => [
                        $request->getGet('/1'),
                        $this->replaceAttr($request->getGet('/1')),
                        rtrim($request->getGet('/1'), '.!?')
                    ]
                ));
            }
        } else {
            $newContent = $helper->getRepository()->findOneBy(array(
                'type' => 'og:title',
                'pattern' => [
                    'oeuvre/search/' . $request->getGet('/0'),
                    'mind/article/search/' . $request->getGet('/0'),
                    'oeuvre/search/' . $this->replaceAttr($request->getGet('/0')),
                    'mind/article/search/' . $this->replaceAttr($request->getGet('/0')),
                ]
            ));
        }
        // Main pages
        if (!$newContent) {
            $newContent = $helper->getRepository()->findOneBy(array(
                'language' => $this->language,
                'type' => 'og:title',
                'pattern' => $search
            ));
        }
        return $newContent;
    }

    public function checkRedirect()
    {
        $request = new \Engine\Request\Input();
        /* @var $newContent \Data\Doctrine\Main\Content */
        $newContent = null;
        $follow = false;

        $code = "301 Moved Permanently";// Random content
        if (array_key_exists('random', (array)$this->params) || $request->getGet('/0') === 'random') {
            $code = "302 Moved Temporarily";
            $follow = true;
            $newContent = $this->getRandom();
        }
        // Check ?pg=...&act=...
        if (!$newContent && isset($this->params['pg']) && isset($this->params['act'])) {
            $follow = true;
            $newContent = $this->citadelRedirect($this->params['pg'], $this->params['act']);
        }

        // Check ?usr_info=...
        if (!$newContent && isset($this->params['usr_info'])) {
            $follow = true;
            $newContent = $this->userRedirect($this->params['usr_info']);
        }
        // Check forum urls
        // ...
        // Check previous implementation
        if (!$newContent && $request->getGet()) {
            $newContent = $this->subdomainRedirect($request);
        }

        $lang = $this->language ? $this->language : 'ru';
        $url = \System\Registry::config()->getUrl($lang);
        $refUrl = $request->getRefererUrl();

        $migr = new Migration();
        if ($newContent) {
            $url = \System\Registry::config()->getUrl($newContent->getLanguage());
            $migr->redirect("{$url}/{$newContent->getPattern()}.html", $code);
            $follow = true;

        } elseif ($this->isSubdomain || $follow && !strpos($refUrl, '/index.html')) {
            $follow = true;

        } else {
            $migr->checkBackward();
        }

        return $follow;
    }
}
