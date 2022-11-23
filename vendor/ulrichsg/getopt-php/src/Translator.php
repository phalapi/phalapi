<?php

namespace GetOpt;

class Translator
{
    const PATH_TEMPLATE = '%s/../resources/localization/%s.php';

    /** @var string */
    protected $languageFile;

    /** @var array */
    protected $translations;

    /** @var Translator */
    protected static $fallbackTranslator;

    /**
     * Translator constructor.
     *
     * @param string  $language
     * @internal bool $asFallback
     */
    public function __construct($language = 'en')
    {
        if (!$this->setLanguage($language)) {
            throw new \InvalidArgumentException(sprintf('$language %s not available', $language));
        }

        // create a fallback translator if not exists
        if (!self::$fallbackTranslator && (func_num_args() < 2 || func_get_arg(1) !== true)) {
            self::$fallbackTranslator = new self('en', true);
        }
    }

    /**
     * Translate $key
     *
     * Returns the translation for the given key; falls back to English if it is
     * not localized in the configured language, and ultimately returns the key
     * itself should it not exist in the English language file.
     *
     * @param string $key
     * @return string
     */
    public function translate($key)
    {
        if ($this->translations === null) {
            $this->loadTranslations();
        }

        if (!isset($this->translations[$key])) {
            // the key if $this is already the fallback translator
            return $this !== self::$fallbackTranslator ? self::$fallbackTranslator->translate($key) : $key;
        }

        return  $this->translations[$key];
    }

    /**
     * Set the language to $language
     *
     * The language can either be a known language from resources/localization (feel free to contribute your language)
     * or a path to a file that returns an array like the files in resources/localization.
     *
     * @param string $language
     * @return bool Whether the language change was successful
     */
    public function setLanguage($language)
    {
        $languageFile = file_exists($language) ?
            $language : sprintf(static::PATH_TEMPLATE, __DIR__, $language);
        if (!file_exists($languageFile)) {
            return false;
        }

        if ($this->languageFile != $languageFile) {
            $this->translations = null;
        }

        $this->languageFile = $languageFile;
        return true;
    }

    /**
     * Load the current languageFile
     */
    protected function loadTranslations()
    {
        $this->translations = include $this->languageFile;
    }
}
