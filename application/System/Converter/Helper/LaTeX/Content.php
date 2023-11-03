<?php namespace System\Converter\Helper\LaTeX;

/**
 * Description of Content
 *
 * @since 2016-12-19
 * @author Viachaslau Lyskouski
 */
class Content extends Missing
{

    public function get()
    {
        $tmpl = new \Engine\Response\Template('Ui/content');
        $a = explode(',', $this->content);
        $tmpl->set('head', $a[0]);
        $tmpl->set('page', $a[1]);
        $tmpl->set('title', implode(',', array_splice($a, 2)));
        return $tmpl->compile();
    }
}
