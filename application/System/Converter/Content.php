<?php namespace System\Converter;

class Content
{
    /**
     * @var string
     */
    protected $content = '';
    protected $extra = array();

    /**
     * Init content helper
     *
     * @param string $content
     */
    public function __construct($content, array $extra = array())
    {
        if (strpos($content, '<br') === false) {
            $content = str_replace(
                ["\r\n", "\n\r", "\n", "\r"],
                ['<br />', '<br />', '<br />', '<br />'],
                $content
            );
        }
        $this->content = $content;
        $this->extra = $extra;
    }

    /**
     * Get valid HTML content
     *
     * @return string
     */
    public function getHtml($indent = false)
    {
        $content = (new Helper\Html)->filter($this->content, $this->extra);
        if ($indent) {
            $content = str_replace('<br/>', '', $content);
        }
        return $content;
    }

    /**
     * Get only plain text
     *
     * @return string
     */
    public function getText()
    {
        return str_replace(
            ['<', '>'],
            ['&lt;', '&gt;'],
            trim(filter_var($this->content, FILTER_SANITIZE_STRING))
        );
    }

    /**
     * @todo LaTeX representation
     *
     * @param string $content
     * @return string
     */
    public function compileLaTeX($content = '')
    {
        return (new Helper\LaTeX)->compile($content ? $content : $this->content);
    }
}
