<?php namespace System\Converter\Helper;

use Defines\Database\Params;

/**
 * Sanitize content
 *
 * You can use the TidyAPIs to load/clean the source document and then iterate
 * over the document tree outputing the nodes (elements) and text data content.
 * This would give you complete control over which (if any) attributes you want
 * to output.
 *
 * @since 2016-03-16
 * @author Viachaslau Lyskouski
 * @package System/Converter/Helper
 */
class Html
{
    protected $template = '<!DOCTYPE html><html><head><meta content="text/html; charset=utf-8" http-equiv="content-type" /></head><body>%s</body></html>';

    protected function fixTags($text)
    {
        return preg_replace(
            array(
            '@<head[^>]*?>.*?</head>@siu',
            '@<style[^>]*?>.*?</style>@siu',
            '@<script[^>]*?.*?</script>@siu',
            '@<object[^>]*?.*?</object>@siu',
            '@<embed[^>]*?.*?</embed>@siu',
            '@<applet[^>]*?.*?</applet>@siu',
            '@<noframes[^>]*?.*?</noframes>@siu',
            '@<noscript[^>]*?.*?</noscript>@siu',
            '@<noembed[^>]*?.*?</noembed>@siu',
            '@</?((frameset)|(frame)|(iframe))@iu',
            ),
            '',
            $text
        );
    }

    protected function fixStyles($text)
    {
        $errorLevel = error_reporting(0);
        $doc = new \DOMDocument('1.0');
        $doc->loadHTML(sprintf($this->template, $text));
        error_reporting($errorLevel);

        $xpath = new \DOMXPath($doc);
        foreach ($xpath->query('//*[@style]') as $node) {
            $node->removeAttribute('style');
        }

        return $this->repair($xpath->document->saveHTML($doc));
    }

    protected function fixAttr(\DOMElement $element)
    {
        $allowed = array(
            //'href', 'border',
            'src', 'alt', 'title', 'class',
            'colspan', 'rowspan', 'cellpadding', 'cellspacing'
        );

        if ($element->hasAttributes()) {
            $list = array();
            foreach ($element->attributes as $attr) {
                $list[] = $attr->nodeName;
            }
            foreach (array_diff($list, $allowed) as $name) {
                $element->removeAttribute($name);
            }
        }
    }

    public function filter($text, $extra = array())
    {
        $allowed = array_merge(array(
            // 'a',
            'p', 'img', 'br', 'em', 'i', 'strong', 'b', 'center',
            'ul', 'ol', 'li', 'dl', 'dt', 'dd',
            'table', 'thead', 'tbody', 'tr', 'td', 'th', 'caption',
            'h1', 'h2', 'h3', 'h5', 'h6', 'h7', 'h8',
            'u', 'del', 'ins', 'sub', 'sup', 'strike',
            'blockquote', 'q'
        ), $extra);

        $content = $this->fixStyles($this->fixTags($text));

        $doc = new \DOMDocument('1.0', Params::ENCODING);
        $doc->loadHTML(sprintf($this->template, $content));
        $xpath = new \DOMXPath($doc);
        /* @var $element \DOMElement */
        // Filter content
        foreach ($doc->getElementsByTagName('*') as $element) {
            $name = strtolower($element->nodeName);
            if (in_array($name, ['html', 'body', 'head', 'br'])) {
                continue;
            }
            $tagName = 'span';
            if ($name === 'div') {
                $tagName = 'p';
            }
            if (!in_array($name, $allowed)) {
                $newnode = $doc->createElement($tagName);
                while ($element->hasChildNodes()) {
                    $item = $element->childNodes->item(0);
                    $child = $element->ownerDocument->importNode($item, true);
                    $newnode->appendChild($child);
                }
                $element->parentNode->replaceChild($newnode, $element);
            }
            $this->fixAttr($element);
        }

        //$doc->preserveWhitespace = true;
        $doc->formatOutput = true;
        return substr(
            $xpath->document->saveXML($doc->getElementsByTagName('body')->item(0)),
            6,
            -7
        );
    }

    /**
     * Fix incorrect HTML
     *
     * @param string $text
     * @return string
     */
    public function repair($text)
    {
        $config = array(
            'clean' => true,
            'doctype' => '<!DOCTYPE HTML>',
            'drop-proprietary-attributes' => true,
            'output-xhtml' => true,
            'show-body-only' => true,
            'drop-font-tags' => true,
            'decorate-inferred-ul' => false,
            'merge-spans' => true,
            'join-styles' => false,
            'wrap' => '0',
            'ascii-chars' => false,
            'char-encoding' => Params::ENCODING,
            'input-encoding' => Params::ENCODING,
            'output-encoding' => Params::ENCODING,
            'join-styles' => false
        );

        $tidy = new \tidy();
        $tidy->parseString($text, $config, Params::ENCODING);
        //$tidy->cleanRepair();
        $content = tidy_get_output($tidy);
        /* // Issue with 'u' and 's' tags
        $idxU = strpos($text, '<u>');
        $classU = 'c1';
        $classS = 'c2';
        $idxS = strpos($text, '<s>');
        if ($idxU && $idxS && $idxS < $idxU || !$idxU && $idxS) {
            $classU = 'c2';
            $classS = 'c1';
        }
        return $this->restoreTag($this->restoreTag($content, $classU, 'u'), $classS, 's'); */
        return $content;
    }

    /**
     * Fix incorrect tidy behaviour
     *
     * @param string $content
     * @param string $class
     * @param string $tag
     * @return string
     */
    protected function restoreTag($content, $class = 'c1', $tag = 'u')
    {
        $uPattern = '<span class="' . $class . '">';
        while (($idx = strpos($content, $uPattern)) !== false) {
            $i = strpos($content, '</span>', $idx);
            if ($i) {
                $tmp = substr($content, 0, $i) . "</$tag>" . substr($content, $i + 7);
            } else {
                $tmp = $content;
            }
            $content = preg_replace('/' . preg_quote($uPattern, '/') . '/', "<$tag>", $tmp, 1);
        }
        return $content;
    }
}
