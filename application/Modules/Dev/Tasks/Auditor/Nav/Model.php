<?php namespace Modules\Dev\Tasks\Auditor\Nav;

use Defines\Database\CrMain;

/**
 * Model object for index page
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Modules/Index
 */
class Model extends \Modules\AbstractModel
{
    public function updateNavigation($id)
    {
        $em = \System\Registry::connection();
        $str = new \System\Converter\StringUtf();

        $headers = array(
            'title' => \System\Registry::translation()->sys('LB_CONTENT'),
            'url' => '#',
            'sync' => true,
            'sub' => array()
        );
        $prnt = array(
            & $headers
        );

        $template = new \Engine\Response\Template();

        /* @var $entity \Data\Doctrine\Main\Content */
        $entity = $em->find(CrMain::CONTENT, $id);
        $list = $em->getRepository(CrMain::CONTENT)->findBy([
            'pattern' => $entity->getPattern(),
            'language' => $entity->getLanguage()
        ], ['id' => 'ASC']);
        /* @var $o \Data\Doctrine\Main\Content */
        $act = null;
        $page = 0;
        foreach ($list as $o) {
            if (strpos($o->getType(), 'content#') === false) {
                if ($o->getType() === 'nav') {
                    $em->remove($o);
                    $em->flush();
                }
                continue;
            }
            $page = substr($o->getType(), 8);
            preg_match_all(
                "#<h(\d)[^>]*?>(.*?)<[^>]*?/h\d>#siU",
                $o->getContent(),
                $headings,
                PREG_PATTERN_ORDER
            );

            if (sizeof($headings) === 3) {
                foreach ($headings[2] as $i => $title) {
                    $key = (int) $headings[1][$i];
                    if (is_null($act)) {
                        $act = $key;

                    } elseif ($act < $key) {
                        $sub = & $head['sub'];
                        end($sub);
                        $prnt[] = & $sub[key($sub)];

                    } elseif ($act > $key) {
                        while ($act > $key && sizeof($prnt) > 1) {
                            unset($head);
                            $head = array_pop($prnt);
                            $act--;
                        }
                    }
                    end($prnt);
                    $head = & $prnt[key($prnt)];
                    $act = $key;

                    $pageUrl = $page ? "{$entity->getPattern()}/{$page}" : $entity->getPattern();
                    $pageTitle = trim(strip_tags($title));
                    $head['sub'][] = array(
                        'title' => $str->strlen($pageTitle) > 36 ? $str->substr($pageTitle, 0, 36) . '&hellip;' : $pageTitle,
                        'url' => $template->getUrl($pageUrl, \Defines\Extension::HTML, $entity->getLanguage()),
                        'key' => $key,
                        'sync' => true,
                        'sub' => array()
                    );
                }
            }
        }

        $content = $template->partial('Basic/Nav/catalog', array(
            'list' => [$headers],
            'render' => true
        ));
        $nav = clone $entity;
        $nav->setAuditor(\System\Registry::user()->getEntity())
            ->setType('nav')
            ->setContent($content)
            ->setSearch('');
        $em->persist($nav);
        $em->flush();

    }
}
