<?php

namespace common\components;

use Yii;
use yii\helpers\FileHelper;
use lajax\translatemanager\services\scanners\ScannerFile;
use yii\helpers\Console;
use yii\base\InvalidConfigException;

/**
 * Class for processing Twig files.
 *
 * Language elements detected in Twig files:
 * "t" functions:
 *
 * ~~~
 * ::t('category of language element', 'language element');
 * ::t('category of language element', 'language element {replace}', ['replace' => 'String']);
 * ::t('category of language element', "language element");
 * ::t('category of language element', "language element {replace}", ['replace' => 'String']);
 * ~~~
 *
 * @author Alexandr <chalpet@gmail.com>
 *
 * @since 1.0
 */
class ScannerTwigFunction extends ScannerFile
{
    /**
     * Extension of Twig files.
     */
    const EXTENSION = '*.twig';

    /**
     * @var array Array to store patsh to project files.
     */
    protected static $files = ['*.php' => [], '*.js' => [], '*.twig' => []];


    /**
     * @inheritdoc Initialise the $files static array.
     */
    public function init()
    {
        $this->initFiles();

        parent::init();
    }

    protected function initFiles()
    {
        if (!empty(self::$files[static::EXTENSION]) || !in_array(static::EXTENSION, $this->module->patterns)) {
            return;
        }

        self::$files[static::EXTENSION] = [];

        foreach ($this->_getRoots() as $root) {
            $root = realpath($root);
            Yii::trace('Scanning ' . static::EXTENSION . " files for language elements in: $root", 'translatemanager');

            $files = FileHelper::findFiles($root, [
                'except' => $this->module->ignoredItems,
                'only' => [static::EXTENSION],
            ]);
            self::$files[static::EXTENSION] = array_merge(self::$files[static::EXTENSION], $files);
        }

        self::$files[static::EXTENSION] = array_unique(self::$files[static::EXTENSION]);
    }

    /**
     * Start scanning PHP files.
     *
     * @param string $route
     * @param array $params
     * @inheritdoc
     */
    public function run($route, $params = [])
    {
        $this->scanner->stdout('Detect TwigFunction - BEGIN', Console::FG_CYAN);
        foreach (self::$files[static::EXTENSION] as $file) {
            if ($this->containsTranslator($this->module->phpTranslators, $file)) {
                $this->extractMessages($file, [
                    'translator' => (array) $this->module->phpTranslators,
                    'begin' => '(',
                    'end' => ')',
                ]);
            }
        }

        $this->scanner->stdout('Detect TwigFunction - END', Console::FG_CYAN);
    }

    /**
     * Extracts messages from a file
     *
     * @param string $fileName name of the file to extract messages from
     * @param array $options Definition of the parameters required to identify language elements.
     * example:
     *
     * ~~~
     * [
     *      'translator' => ['Yii::t', 'Lx::t'],
     *      'begin' => '(',
     *      'end' => ')'
     * ]
     * ~~~
     * @param array $ignoreCategories message categories to ignore Yii 2.0.4
     */
    protected function extractMessages($fileName, $options, $ignoreCategories = [])
    {
        $this->scanner->stdout('Extracting messages from ' . $fileName, Console::FG_GREEN);
        $subject = file_get_contents($fileName);
        if (static::EXTENSION !== '*.php') {
            $subject = "<?php\n" . $subject;
        }
        foreach ($options['translator'] as $currentTranslator) {
            $translatorTokens = token_get_all('<?php ' . $currentTranslator);
            array_shift($translatorTokens);

            $tokens = token_get_all($subject);

            $this->checkTokens($options, $translatorTokens, $tokens);
        }
    }

    /**
     * @inheritdoc
     */
    protected function getLanguageItem($buffer)
    {
        if (isset($buffer[0][0], $buffer[1], $buffer[2][0]) && $buffer[0][0] === T_CONSTANT_ENCAPSED_STRING && $buffer[1] === ',' && $buffer[2][0] === T_CONSTANT_ENCAPSED_STRING) {
            // is valid call we can extract
            $category = stripcslashes($buffer[0][1]);
            $category = mb_substr($category, 1, mb_strlen($category) - 2);
            if (!$this->isValidCategory($category)) {
                return null;
            }

            $message = implode('', $this->concatMessage($buffer));

            return [
                [
                    'category' => $category,
                    'message' => $message,
                ],
            ];
        }

        return null;
    }

    /**
     * Recursice concatenation of multiple-piece language elements.
     *
     * @param array $buffer Array to store language element pieces.
     *
     * @return array Sorted list of language element pieces.
     */
    protected function concatMessage($buffer)
    {
        $messages = [];
        $buffer = array_slice($buffer, 2);
        $message = stripcslashes($buffer[0][1]);
        $messages[] = mb_substr($message, 1, mb_strlen($message) - 2);
        if (isset($buffer[1], $buffer[2][0]) && $buffer[1] === '.' && $buffer[2][0] == T_CONSTANT_ENCAPSED_STRING) {
            $messages = array_merge_recursive($messages, $this->concatMessage($buffer));
        }

        return $messages;
    }

    /**
     * Returns the root directories to scan.
     *
     * @return array
     */
    private function _getRoots()
    {
        $directories = [];

        if (is_string($this->module->root)) {
            $root = Yii::getAlias($this->module->root);
            if ($this->module->scanRootParentDirectory) {
                $root = dirname($root);
            }

            $directories[] = $root;
        } elseif (is_array($this->module->root)) {
            foreach ($this->module->root as $root) {
                $directories[] = Yii::getAlias($root);
            }
        } else {
            throw new InvalidConfigException('Invalid `root` option value!');
        }

        return $directories;
    }
}
