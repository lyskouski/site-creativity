<?php namespace System;

/**
 * Shell executions
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package System
 */
class Shell
{

    /**
     * Run shell_exec
     *
     * @param string $command
     * @return string
     */
    public function run($command, $lang = \Defines\Language::EN)
    {
        $OTranslate = \System\Registry::translation();
        $tmp = $OTranslate->getTargetLanguage();
        if ($lang && $tmp !== $lang) {
            $OTranslate->setTargetLanguage($lang);
        }
        $log = shell_exec($command);
        if ($lang && $tmp !== $lang) {
            $OTranslate->setTargetLanguage($tmp);
        }
        return (new Converter\StringUtf)->convert($log);
    }
}
