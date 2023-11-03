<?php namespace System\Converter\Helper\LaTeX;

/**
 * Description of Pageref
 *
 * @author slaw
 */
class Pageref extends Missing
{

    public function get()
    {
        $tmpl = new \Engine\Response\Template('Ui/pageref');
        $valid = new \Access\Validate\Check();
        $valid->setType(\Defines\User\Access::READ);

        /* @var $content \Data\Doctrine\Main\Content */
        $content = \System\Registry::connection()
                ->getRepository(\Defines\Database\CrMain::CONTENT)
                ->findOneBy(array(
                    'pattern' => $this->content,
                    'type' => 'og:title',
                    'language' => \System\Registry::translation()->getTargetLanguage()
                ));
        if ($content && $valid->isAccepted($content)) {
            $tmpl->set('text', $content->getContent())
                ->set('url', $content->getPattern())
                ->set('lang', $content->getLanguage());
            if ($content->getAuthor()) {
                $tmpl->set('author', $content->getAuthor()->getUsername());
            }
        } elseif ($content) {
            $err = \System\Registry::translation()->sys('LB_HEADER_423');
            $tmpl->set('error', '{!} ' . $err);
        } else {
            $err = \System\Registry::translation()->sys('LB_HEADER_404');
            $tmpl->set('error', '{!pageref} ' . $err);
        }

        return $tmpl->compile();
    }

}
