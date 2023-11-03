<?php namespace System\Converter\Helper\LaTeX;

/**
 * Description of Author
 *
 * @author s.lyskovski
 */
class Author extends Missing
{

    public function get()
    {
        $tmpl = new \Engine\Response\Template('Ui/author');
        $valid = new \Access\Validate\Check();
        $valid->setType(\Defines\User\Access::READ);

        /* @var $content \Data\Doctrine\Main\User */
        $content = \System\Registry::connection()
            ->getRepository(\Defines\Database\CrMain::USER)
            ->findOneByUsername($this->content);
        if ($content) {
            $tmpl->set('author', $content->getUsername());
        } else {
            $err = \System\Registry::translation()->sys('LB_HEADER_404');
            $tmpl->set('error', '{!author} '. htmlspecialchars($this->content) . ' - ' . $err);
        }

        return $tmpl->compile();
    }
}
