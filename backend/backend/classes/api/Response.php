<?php

namespace backend\classes\api;

use backend\classes\helpers\ArrayHelper;

/**
 * Class Response
 * Класс-структура, через который происходит любой ответ от Api
 */
class Response
{

    /** @var bool */
    public $is_success = true;
    /** @var string|[] */
    public $content = [];
    /** @var [] */
    public $errors = [];
    /** @var [] */
    public $warnings = [];
    /** @var [] служит для сбора notice в api контроллерах и выводе в ExceptionsWidget */
    public $notices = [];

    /**
     * @param array|string $error
     * @param array|string $keys
     * @return $this
     */
    public function addError($error, $keys = []): self
    {
        $this->is_success = false;
        $keys = (array)$keys;
        if (!$keys) {
            $this->errors[] = $error;
            return $this;
        }
        $this->errors = ArrayHelper::mergeRecursive($this->errors, ArrayHelper::recursiveGenerateArrayFromArrayKeys($keys, $error));

        return $this;
    }

    /**
     * @param array|string $error
     * @param int $key
     * @return $this
     */
    public function setError($error, $key = null): self
    {
        $this->errors = [];
        $this->addError($error, $key);

        return $this;
    }

    /**
     * @param array $errors
     * @param array $keys
     * @return Response
     */
    public function setErrors(array $errors, $keys = []): self
    {
        if (!$errors) {
            return $this;
        }
        foreach ($errors as $error_message) {
            $this->addError($error_message, $keys);
        }

        return $this;
    }

    /**
     * @param array|string $warning
     * @param int $key
     * @return $this
     */
    public function addWarning($warning, $key = null): self
    {
        if (!$key) {
            $this->warnings[] = $warning;
        } else {
            $this->warnings[$key] = $warning;
        }

        return $this;
    }

    /**
     * @param array|string $warning
     * @param int $key
     * @return $this
     */
    public function setWarning($warning, $key = null): self
    {
        $this->warnings = [];
        $this->addWarning($warning, $key);

        return $this;
    }

    /**
     * @param array $notices
     * @return $this
     */
    public function setNotices(array $notices): self
    {
        $this->notices = $notices;

        return $this;
    }

    /**
     * @param []|string $content
     * @return $this
     */
    public function setContent($content): self
    {
        $this->content = $content;

        return $this;
    }


    /**
     * @return array|string $content
     */
    public function getContent()
    {
        return $this->content;
    }


    /**
     * @return bool
     */
    public function hasErrors(): bool
    {
        return (bool)count($this->errors);
    }

    /**
     * @return bool
     */
    public function hasWarnings(): bool
    {
        return (bool)count($this->warnings);
    }
}