<?php namespace System\Converter\Helper\LaTeX;

/**
 * Description of Pageview
 *
 * @author slaw
 */
class Module extends Missing
{

    public function get()
    {
        $tmpl = new \Engine\Response\Template('Ui/module');
        if ($this->content) {
            $tmpl->set('content', $this->content);
        } else {
            $err = \System\Registry::translation()->sys('LB_HEADER_404');
            $tmpl->set('error', '{!} ' . $err);
        }
        return $tmpl->compile();
    }

}