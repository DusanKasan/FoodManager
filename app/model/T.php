<?php

/**
 * Class T
 *
 * @author Dusan Kasan <dusan@kasan.sk>
 * @copyright Copyright (c) 2013, DevelHell s.r.o.
 */

class T
{
    protected static $translator;

    public static function setTranslator(\Nette\Localization\ITranslator $translator)
    {
        self::$translator = $translator;
    }

    public static function _($string)
    {
        if (!isset(self::$translator)) {
            throw new Exception('No translator specified!');
        }

        return self::$translator->translate($string);
    }
}