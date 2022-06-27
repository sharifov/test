<?php

/**
 * @link      https://github.com/index0h/yii2-log
 */

namespace common\components\logger\traits;

use src\helpers\app\AppHelper;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\log\Logger;

/**
 * @property string[]    categories     List of message categories that this target is interested in.
 * @property string[]    except         List of message categories that this target is NOT interested in
 * @property int         exportInterval How many messages should be accumulated before they are exported.
 * @property string[]    logVars        List of the PHP predefined variables that should be logged in a message.
 * @property array       messages       The messages that are retrieved from the logger so far by this log target.
 *
 * @method int getLevels() The message levels that this target is interested in.
 * @method array filterMessages(array $messages, int $levels, array $categories, array $except)
 *     Filters the given messages according to their categories and levels.
 * @method void export Exports log [[messages]] to a specific destination.
 *
 * @author Roman Levishchenko <index.0h@gmail.com>
 */
trait TargetTrait
{
    /** @var bool Whether to log a message containing the current user name and ID. */
    public $logUser = false;

    /** @var array Add more context */
    public $context = [];

    /**
     * Processes the given log messages.
     *
     * @param array $messages Log messages to be processed.
     * @param bool  $final    Whether this method is called at the end of the current application
     */
    public function collect($messages, $final)
    {
        $this->messages = array_merge(
            $this->messages,
            $this->filterMessages($messages, $this->getLevels(), $this->categories, $this->except)
        );
        $count = count($this->messages);
        if (($count > 0) && (($final == true) || ($this->exportInterval > 0) && ($count >= $this->exportInterval))) {
            $this->addContextToMessages();
            $this->export();
            $this->messages = [];
        }
    }

    /**
     * Formats a log message.
     *
     * @param array $message The log message to be formatted.
     *
     * @return string
     */
    public function formatMessage($message)
    {
        return json_encode($this->prepareMessage($message));
    }

    /**
     * Updates all messages if there are context variables.
     */
    protected function addContextToMessages()
    {
        $context = $this->getContextMessage();

        if ($context === []) {
            return;
        }

        //VarDumper::dump($this->messages, 10, true);// exit;

        foreach ($this->messages as &$message) {
            $message[0] = ArrayHelper::merge($context, $this->preParseText($message[0]));
        }
        //VarDumper::dump($message, 10, true); exit;
    }

    /**
     * Generates the context information to be logged.
     *
     * @return array
     */
    protected function getContextMessage()
    {
        $context = $this->context;

        if (($this->logUser === true) && ($user = \Yii::$app->get('user', false)) !== null) {
            /** @var \yii\web\User $user */
            $context['userId'] = $user->getId();
        }

        foreach ($this->logVars as $name) {
            if (empty($GLOBALS[$name]) === false) {
                $context[$name] = & $GLOBALS[$name];
            }
        }

        return $context;
    }

    /**
     * @param $text
     * @param false $isData
     * @return array
     */
    protected function parseTextArray($text, bool $isData = false): array
    {
        $data = [];
        if ($text) {
            $text = AppHelper::shotArrayData($text);
            foreach ($text as $key => $value) {
                if ($isData) {
                    $data['@app.data'][$key] = $value;
                } else {
                    $data[$key] = $value;
                }
            }
        }
        if (!empty($text['message']) && empty($data['@message'])) {
            $data['@message'] = $text['message'];
            if (isset($data['@app.data']['message'])) {
                unset($data['@app.data']['message']);
            }
        }

        if (empty($data['@message']) && is_array($text)) {
            $data['@message'] = json_encode($text);
        }

        if (!empty($text['trace']) && empty($data['@trace'])) {
            $data['@trace'] = $text['trace'];

            if (is_array($data['@trace'])) {
                $data['@trace'] = @json_encode($data['@trace']);
            }

            if (isset($data['@app.data']['trace'])) {
                unset($data['@app.data']['trace']);
            }
        }

        if (!empty($text['line']) && empty($data['@line'])) {
            $data['@line'] = $text['line'];
            if (isset($data['@app.data']['line'])) {
                unset($data['@app.data']['line']);
            }
        }

        if (!empty($text['file']) && empty($data['@file'])) {
            $data['@file'] = $text['file'];
            if (isset($data['@app.data']['file'])) {
                unset($data['@app.data']['file']);
            }
        }

        if (!empty($text['code']) && empty($data['@statusCode'])) {
            $data['@statusCode'] = $text['code'];
            if (isset($data['@app.data']['code'])) {
                unset($data['@app.data']['code']);
            }
        }
        return $data;
    }

    /**
     * @param $text
     * @param false $isData
     * @return array
     */
    protected function parseTextObject($text, bool $isData = false): array
    {

        if (is_a($text, \Throwable::class)) {
            $dataList = AppHelper::throwableLog($text, true);
        } else {
            $dataList = get_object_vars($text);

            if ($dataList) {
                $dataList = AppHelper::shotArrayData($dataList);
                foreach ($dataList as $key => $value) {
                    if ($isData) {
                        $data['@app.data'][$key] = $value;
                    } else {
                        $data[$key] = $value;
                    }
                }
            }
        }


        if (!empty($dataList['message']) && empty($dataList['@message'])) {
            $data['@message'] = $dataList['message'];
        } else {
            $data['@message'] = var_export($dataList, true);
        }

        if (empty($data['@message']) && is_array($dataList)) {
            $data['@message'] = json_encode($dataList);
        }

        if (!empty($dataList['code']) && empty($data['@statusCode'])) {
            $data['@statusCode'] = $dataList['code'];
        }

        if (!empty($dataList['trace']) && empty($data['@trace'])) {
            $data['@trace'] = $dataList['trace'];
            if (is_array($data['@trace'])) {
                $data['@trace'] = @json_encode($dataList['trace']);
            }
        }

        if (!empty($dataList['line']) && empty($data['@line'])) {
            $data['@line'] = $dataList['line'];
        }

        if (!empty($dataList['file']) && empty($data['@file'])) {
            $data['@file'] = $dataList['file'];
        }

        return $data;
    }

    /**
     * Convert's any type of log message to array.
     *
     * @param mixed $text Input log message.
     *
     * @return array
     */
    protected function parseText($text)
    {
        $type = gettype($text);

        switch ($type) {
            case 'array':
                return $this->parseTextArray($text);
            case 'string':
                return ['@message' => $text];
            case 'object':
                return $this->parseTextObject($text);
            default:
                return ['@message' => \Yii::t('log', "Warning, wrong log message type '{$type}'")];
        }
    }

    protected function preParseText($text)
    {
        $type = gettype($text);

        switch ($type) {
            case 'array':
                return $this->parseTextArray($text, true);
            case 'string':
                return ['@message' => $text];
            case 'object':
                return $this->parseTextObject($text, true);
            default:
                return ['@message' => \Yii::t('log', "Warning, wrong log message type '{$type}'")];
        }
    }

    /**
     * Transform log message to assoc.
     *
     * @param array $message The log message.
     *
     * @return array
     */
    protected function prepareMessage($message)
    {
        list($text, $level, $category, $timestamp) = $message;

        $level = Logger::getLevelName($level);
        $timestamp = date('c', intval($timestamp));

        //$txt = $this->parseText($text);
        //чVarDumper::dump($text, 10, true); exit;

        $result = ArrayHelper::merge(
            $this->parseText($text),
            ['@level' => $level, '@category' => $category, '@timestamp' => $timestamp]
        );

        if (!empty($message[4])) {
            $result['@trace'] = $message[4];
        }

        if (!empty($message[5])) {
            $result['@memory'] = $message[5];
        }

        return $result;
    }
}
