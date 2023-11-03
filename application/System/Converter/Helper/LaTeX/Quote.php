<?php namespace System\Converter\Helper\LaTeX;

/**
 * Description of Quote
 *
 * @since 2016-09-22
 * @author Viachaslau Lyskouski
 */
class Quote extends Missing
{

    public function get()
    {
        $tmpl = new \Engine\Response\Template('Ui/quote');
        $valid = new \Access\Validate\Check();
        $valid->setType(\Defines\User\Access::READ);

        /* @var $content \Data\Doctrine\Main\Content */
        $content = \System\Registry::connection()
                ->find(\Defines\Database\CrMain::CONTENT, (int) $this->content);
        if ($content && $valid->isAccepted($content)) {
            $tmpl->set('text', $content->getContent())
                ->set('url', $content->getPattern())
                ->set('lang', $content->getLanguage());
            if ($content->getAuthor()) {
                $tmpl->set('author', $content->getAuthor()->getUsername());
            }
        } elseif ($content) {
            $err = \System\Registry::translation()->sys('LB_HEADER_423');
            $tmpl->set('text', '{!} ' . $err);
        } else {
            $err = \System\Registry::translation()->sys('LB_HEADER_404');
            $tmpl->set('text', $err);
        }

        return $tmpl->compile();
    }

}
