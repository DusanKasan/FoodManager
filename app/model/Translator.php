<?php

/**
 * Class Translator
 *
 * @author Dusan Kasan <dusan@kasan.sk>
 * @copyright Copyright (c) 2013, DevelHell s.r.o.
 */

class Translator implements \Nette\Localization\ITranslator
{

    /**
     * Translates the given string.
     *
     * @param  string   \Nette\Localization\message
     * @param  int      \Nette\Localization\plural count
     *
     * @return string
     */
    function translate($message, $count = null)
    {
        //@todo: Cache translations somewhere

        $translator_mockup = array(
            'Comment :' => 'Komentár :',
            'Comment text...' => 'Text vášho komentáru...',
            'Food' => 'Jedlo',
        );

        return array_key_exists($message, $translator_mockup) ? $translator_mockup[$message] : $message;
    }
}

