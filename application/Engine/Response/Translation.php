<?php namespace Engine\Response;

use System\Registry;
use Engine\Response\Meta;
use Engine\Response\Meta\Ogp\Locales;

/**
 * Get translated field
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Engine/Response
 */
class Translation
{

    /**
     * @var string - name of translation's .mo/po file
     */
    const TB_SYSTEM = 'system';

    // const TB_DESCR = 'description';

    /**
     * @var string - delimiter
     */
    const BR = '|';

    protected $sTargetKey;
    protected $aList = array();
    protected $sDomain;

    public function __construct($sLanguage)
    {
        $this->setTargetLanguage($sLanguage);
    }

    public function getBasicPageKeys()
    {
        return array(
            Meta\Ogp::TYPE_TITLE,
            Meta\Meta::TYPE_KEYWORDS,
            Meta\Meta::TYPE_DESCRIPTION,
            Meta\Ogp::TYPE_IMAGE
        );
    }

    /**
     * Get URL description
     *
     * @param string $sUrl
     * @param array $aToUpdate
     * @array - of Meta objects
     */
    public function desc($sUrl, $sTargetLang, $sBasicLink)
    {
        $this->setTargetLanguage($sTargetLang);
        $sDesc = $this->get([Meta\Meta::TYPE_DESCRIPTION, $sUrl], null, function($desc) {
            return str_replace('"', '', $desc);
        });
        $sImage = $this->get([Meta\Ogp::TYPE_IMAGE, $sUrl], null, function($image) {
            if (!$image || strpos($image, '}') !== false) {
                $image = (new \System\Minify\Images)->get() . 'logo.jpg';
            }
            return $image;
        });

        $title = $this->get([Meta\Ogp::TYPE_TITLE, $sUrl]);

        return array(
            $title,
            new Meta\Meta(Meta\Meta::TYPE_DESCRIPTION, $sDesc),
            new Meta\Ogp(Meta\Ogp::TYPE_DESC, $sDesc),
            new Meta\MetaHttp(Meta\MetaHttp::TYPE_LANGUAGE, $sTargetLang),
            new Meta\Ogp\Locales(Meta\Ogp\Locales::TYPE_LOCALE, $sTargetLang),
            new Meta\Meta(Meta\Meta::TYPE_KEYWORDS, $this->get([Meta\Meta::TYPE_KEYWORDS, $sUrl])),
            new Meta\Favicon(),
            new Meta\Ogp(Meta\Ogp::TYPE_IMAGE, $sBasicLink . $sImage),
            new Meta\Link(Meta\Link::TYPE_IMAGE_SRC, $sBasicLink . $sImage),
            // Twitter
            new Meta\Ogp('twitter:card', 'summary'),
            new Meta\Ogp('twitter:title', $title),
            new Meta\Ogp('twitter:description', $sDesc),
            new Meta\Ogp('twitter:image', $sBasicLink . $sImage)
        );
    }

    /**
     * Define language that has to be returned
     * @param string $sLanguage
     */
    public function setTargetLanguage($sLanguage)
    {
        if ($sLanguage === false) {
            $this->sTargetKey = $sLanguage;
            return;
        }
        // Check if the language exist
        if (!in_array($sLanguage, \Defines\Language::getList(true))) {
            throw new \Error\Validation("Incorrect language is defined");
        }
        $this->sTargetKey = $sLanguage;
        $sLocale = Locales::getLocale($sLanguage);
        setlocale(LC_MESSAGES, $sLocale);
        putenv("LANG={$sLocale}");
        putenv("LANGUAGE={$sLocale}");
        \bindtextdomain(self::TB_SYSTEM, Registry::config()->getTranslationPath());
        // \bindtextdomain( self::TB_DESCR, Registry::config()->getTranslationPath() );
    }

    /**
     * Return defined language
     *
     * @return string
     */
    public function getTargetLanguage()
    {
        return $this->sTargetKey;
    }

    /**
     * Get internal (system) translation
     *
     * @param string $sSearch
     * @param string $sTargetLang
     * @return string
     */
    public function sys($sSearch, $sTargetLang = null)
    {
        if (is_null($sTargetLang)) {
            $sTargetLang = $this->sTargetKey;
        }
        $tran = $sSearch;
        if ($sTargetLang !== false) {
            $tran = $this->find($sSearch, self::TB_SYSTEM, $sTargetLang);
        }
        return $tran;
    }

    /**
     * Prepare translation identificator
     *
     * @param string $sUrl
     * @param string $sType
     * @param string $sLang
     * @return string
     */
    public static function getMarker($sUrl, $sType, $sLang)
    {
        return "{[$sUrl|$sType|$sLang]}";
    }

    /**
     * Get translation for description
     * @note database connection [content{pattern = $sSearch}]
     *
     * @param string $mSearch
     * @param string $sTargetLang
     * @return string
     */
    public function get($mSearch, $sTargetLang = null, $fCallback = null)
    {
        if (is_null($sTargetLang)) {
            $sTargetLang = $this->sTargetKey;
        }

        if (is_string($mSearch)) {
            list($sType, $sUrl) = explode(self::BR, $mSearch);
        } else {
            list($sType, $sUrl) = $mSearch;
        }
        $sUrl = trim($sUrl, '/');

        $sTempValue = self::getMarker($sUrl, $sType, $sTargetLang);
        Helper\Description::registry($sUrl, $sType, $sTargetLang, $sTempValue, $fCallback);
        return $sTempValue;
    }

    /**
     * Get (or create) entiry
     *
     * @param array $aSearch
     * @param string $language
     * @return \Data\Doctrine\Main\Content
     */
    public function entity(array $aSearch, $language = null, $autoCreate = true)
    {
        if (is_null($language)) {
            $language = $this->sTargetKey;
        }
        list ($type, $pattern) = $aSearch;
        /* @var $rep \Data\Model\ContentRepository */
        $em = \System\Registry::connection();
        $rep = $em->getRepository(\Defines\Database\CrMain::CONTENT);
        /* @var $entity \Data\Doctrine\Main\Conten */
        $entity = $rep->findOneBy([
            'pattern' => $pattern,
            'type' => $type,
            'language' => $language
        ]);
        if (!$entity && $autoCreate) {
            $entity = new \Data\Doctrine\Main\Content();
            $entity->setPattern($pattern)
                ->setType($type)
                ->setLanguage($language)
                ->setUpdatedAt(new \DateTime)
                ->setContent(self::getMarker($pattern, $type, $language));
            $em->persist($entity);
            $em->flush();
        }
        return $entity;
    }

    /**
     * Find translations for the value
     *
     * @param string $sSearch
     * @param string $sTranslationTable
     * @param string $sTargetLang
     * @return string
     */
    protected function find($sSearch, $sTranslationTable, $sTargetLang = null)
    {
        if (!is_null($sTargetLang)) {
            $sPrevLang = $this->sTargetKey;
            $this->setTargetLanguage($sTargetLang);
        }

        if ($this->sDomain !== $sTranslationTable) {
            \textdomain($sTranslationTable);
            \bind_textdomain_codeset($sTranslationTable, 'UTF-8');
            $this->sDomain = $sTranslationTable;
        }

        $pattern = preg_replace('/[^A-Z0-9]/', '_', strtoupper($sSearch));
        $sText = \gettext($pattern);

        if (!is_null($sTargetLang)) {
            $this->setTargetLanguage($sPrevLang);
        }

        return $sText;
    }

    /**
     * To avoid autocreated content mechanism initialization
     */
    public function skipUpdate($url = null)
    {
        if (is_null($url)) {
            $url = (new \Engine\Request\Input)->getUrl(null);
        }
        $this->get(['content#0', $url]);
    }

    public function checkMissings()
    {
        try {
            \System\Registry::connection()->transactional(function() {
                $aData = \Engine\Response\Helper\Description::getMissings();
                $aData && (new \Data\ContentHelper)->addTemporary($aData);
            });
        } catch (\Exception $e) {
            // ignore error
        }
    }
}
