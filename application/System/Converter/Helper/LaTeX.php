<?php namespace System\Converter\Helper;

class LaTeX
{
    const PATTERN = '/\\\[a-z]{3,}{[^}]*?}/';//[0-9a-zA-Zа-яА-ЯёЁ \_\/\,\;\-\.\?\’\:\(\)\_\$\|]{1,}}/';

    protected function isEditable($text) {
        $areaPosition = strpos($text, '</textarea>');
        $sectionPosition = strpos($text, '</section>');
        $articlePosition = strpos($text, '</article>');
        return $areaPosition !== false
                && ($sectionPosition === false || $areaPosition < $sectionPosition)
                && ($articlePosition === false || $areaPosition < $articlePosition);
    }

    public function compile($content)
    {
        if (strpos($content, 'contenteditable="true"') !== false) {
            return $content;
        }
        $key = array();
        $result = array();
        /* @var $class LaTeX\TexInterface */
        $class = null;

        preg_match_all(self::PATTERN, $content, $key);
        $list = preg_split(self::PATTERN, $content);
        $result[] = $list[0];
        $toSkip = false;
        $iClosure = 0;
        $isOpened = false;

        foreach ($key[0] as $i => $pattern) {
            $skip = $toSkip;
            // Check compilation
            $tmp = preg_split('/(\{|\}|\\\)/', $pattern);
            if ($tmp[2] === 'begin' && $toSkip === $tmp[1]) {
                $iClosure++;
            } elseif ($tmp[2] === 'end' && $toSkip === $tmp[1]) {
                if ($iClosure) {
                    $iClosure--;
                } else {
                    $toSkip = false;
                }
            }
            // Skip validation
            if ($skip) {
                if ($isOpened || $this->isEditable($list[$i+1]) || strpos($list[$i], '<textarea') !== false) {
                    $isOpened = true;
                    $result[] = '\\' . $tmp[1] . "{begin}";
                    $result[] = substr($class->initial(), strlen($tmp[1]));
                    $result[] = '\\' . $tmp[1] . '{end}';
                    $result[] = $list[$i+1];
                    if ($this->isEditable($list[$i+1])) {
                        $isOpened = false;
                    }
                } elseif ($toSkip) {
                    $class->bind($pattern . $list[$i+1]);
                } else {
                    $result[] = $this->compile($class->get());
                    $result[] = $list[$i+1];
                }
                continue;
            }

            $className = ucwords($tmp[1]);
            if (file_exists(__DIR__ . '/LaTeX/' . $className . '.php')) {
                $className = __CLASS__ . '\\' . $className;
                $class = new $className($tmp[2]);
            } else {
                $class = new LaTeX\Missing($tmp[1]);
            }
            if ($isOpened || strpos($list[$i], '<textarea') !== false || $this->isEditable($list[$i+1])) {
                $isOpened = true;
                $result[] = $pattern . $list[$i+1];
                if ($this->isEditable($list[$i+1])) {
                    $isOpened = false;
                }
            } elseif ($tmp[2] === 'begin') {
                $toSkip = $tmp[1];
                $class->bind($list[$i+1]);
            } else {
                $result[] = $class->get();
                $result[] = $list[$i+1];
            }
        }
        return implode('', $result);
    }

    public function get() {
        return '<br />';
    }
}
