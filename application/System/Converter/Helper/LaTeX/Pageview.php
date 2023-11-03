<?php namespace System\Converter\Helper\LaTeX;

/**
 * Description of Pageview
 *
 * @author slaw
 */
class Pageview extends Missing
{

    public function get()
    {
        $tmpl = new \Engine\Response\Template('Ui/pageview');
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
        $tmpl->set('pattern', $this->content);
        if ($content && $valid->isAccepted($content)) {
            $tmpl->set('content', $content);
        } elseif ($content) {
            $err = \System\Registry::translation()->sys('LB_HEADER_423');
            $tmpl->set('error', '{!} ' . $err);
            $tmpl->set('pattern', '');
        } else {
            $err = \System\Registry::translation()->sys('LB_HEADER_404');
            $tmpl->set('error', '{!} ' . $err);
        }

        return $tmpl->compile();
    }

}
