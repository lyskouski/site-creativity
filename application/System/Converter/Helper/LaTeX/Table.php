<?php namespace System\Converter\Helper\LaTeX;

/**
 * Description of Table
 *
 * @author s.lyskovski
 */
class Table extends Missing
{
    public function __construct($content)
    {
        // do nothing, exclude first content
    }

    public function get()
    {
        $tmpl = new \Engine\Response\Template('Ui/table');
        $table = array();
        // Remove all brokable spaces
        $this->content = str_replace(
            ['<br>', '<br/>', "\n", "\r", '<br /><br />', '<br /><br />'],
            ['<br />', '<br />', '<br />', '<br />', '<br />', '<br />'],
            $this->content
        );
        $list = explode('|', $this->content);
        foreach ($list as $i => $val) {
            $list[$i] = trim($val);
        }
        // Prepare table
        foreach (explode('|<br />', implode('|', $list)) as $row) {
            $row = trim($row, " \n\r|");
            if (!$row) {
                continue;
            }
            $values = array();
            foreach (explode('|', $row) as $i => $val) {
                if (!$i && $val === '<br />') {
                    continue;
                }
                $values[] = array(
                    'value' => trim($val, ' *'),
                    'rowspan' => strlen($val) - strlen(ltrim($val, '*')),
                    'colspan' => strlen($val) - strlen(rtrim($val, '*'))
                );
            }
            $table[] = $values;
        }
        $tmpl->set('table', $table);
        return $tmpl->compile();
    }
}
