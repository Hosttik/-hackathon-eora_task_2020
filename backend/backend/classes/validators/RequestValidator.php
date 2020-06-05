<?php

namespace app\classes\validators;
use backend\classes\api\Response;
use backend\classes\validators\ValidatorInterface;

/**
 * Валидатор данных приходящих из request
 */
class RequestValidator implements ValidatorInterface
{

    const MAX_VALIDATE_CALL_COUNT = 30;

    /** @var string Поле в строке конфига, отвечающее за валидацию, если true, то строка конфига не валидируется */
    const CONFIG_FIELD_NO_VALIDATE = 'no_validate';

    /**
     * @var string Поле в строке конфига, если false и значение НЕ пустое, то будет проверка типа передаваемого значения
     * и если это array или object, то валидация значений происходить не будет,
     * если false и значение пустое, то проверка на тип будет автоматически пройдена.
     */
    const CONFIG_FIELD_REQUIRED = 'required';

    /** @var string Предполагаемый тип пришедшего значения, поддерживаются следующие типы $request_data_types */
    const CONFIG_FIELD_TYPE = 'type';

    /** @var string Правила для проверки значений */
    const CONFIG_FIELD_RULES = 'rules';

    /** @var string Валидация на пустоту значения. Выдает ошибку если ключ есть, а значение пустое */
    const CONFIG_FIELD_NOT_EMPTY = 'not_empty';


    /**
     * Ниже константы с префиксом RULE - это кастомные правила валидации
     * Пример массива конфига с кастомными правилами валидации:
     * 'query' => ['type' => 'string', 'required' => false, 'rules' => ['letters_numbers_and_dashes' => '', 'min_chars' => 3, 'max_chars' => 30]]
     * Ключ rules представляет из себя массив кастомных правил валидации,
     * где ключ - название правила, значение - параметр для сравнения, или пустая строка (null, что-то еще), если
     * сравнение не требуется.
     * Сами правила описываются в методе getRules()
     */

    /** @var string Проверяет, что число целое и положительное */
    const RULE_NATURAL_NUMBER = 'natural_number';

    /** @var string Проверяет, что строка содержит только буквы, числа и тире */
    const RULE_ONLY_LETTERS_NUMBERS_AND_DASHES = 'letters_numbers_and_dashes';

    /** @var string Проверяет, что строка представляет из себя номер телефона формата +#(###)###-##-## */
    const RULE_IS_CORRECT_PHONE_NUMBER = 'is_correct_phone_number';

    /**
     * @var string
     * Проверяет, что строка представляет из себя корректное название компании:
     * может содержать буквы, тире, кавычки и цифры и не может состоять целиком из цифр
     */
    const RULE_IS_CORRECT_COMPANY_NAME = 'is_correct_company_name';

    /**
     * @var string
     * Проверяет, что строка представляет из себя корректное имя пользователя:
     * Имя Фамилия, с тире и точками
     * Пример: Mr. Михаил Салтыков-Щедрин
     */
    const RULE_IS_CORRECT_USER_NAME = 'is_correct_user_name';

    /**
     * @var string
     * Проверяет, что строка представляет из себя корректный адрес электронной почты
     */
    const RULE_IS_CORRECT_EMAIL = 'is_correct_email';

    /**
     * @var string
     * Проверяет, что в строке не меньше N символов
     */
    const RULE_MIN_CHARS = 'min_chars';

    /**
     * @var string
     * Проверяет, что в строке не больше N символов
     */
    const RULE_MAX_CHARS = 'max_chars';

    /**
     * @var string
     * Проверяет, что строка содержит только буквы латинского и русского алфавита
     */
    const RULE_ONLY_LETTERS = 'only_letters';

    /**
     * @var int кол-во рекурсивных вызовов вылидации
     * не должно превышать self::MAX_VALIDATE_CALL_COUNT
     * Сделано для защиты от вечного цикла.
     */
    private $validate_call_count = 0;

    protected $request_data_types = [
        'string',
        'boolean',
        'integer',
        'float',
        'array',
        'object',
    ];

    protected $request_data_rules = [];

    /**
     * @var Response
     */
    private $response;

    public function __construct(Response $response)
    {
        $this->setResponse($response);
        $this->request_data_rules = $this->getRules();
    }

    /**
     * @return Response
     */
    public function getResponse() {
        return $this->response;
    }

    private function setResponse(Response $response) {
        $this->response = $response;
    }

    /**
     * Валидирует данные из request (post или get) на соотвествие типам указанным в $config.
     * Если параметр required в $config указан как false, проверка данного поля будет пропущена.
     * @param array $config
     * @param array $request_data
     * @return bool
     */
    public function validate(array $config, array $request_data): bool
    {
        $this->validate_call_count++;
        if ($this->validate_call_count > self::MAX_VALIDATE_CALL_COUNT) {
            \Yii::warning('too much recursion calls of BaseApiController::validate()');
            $this->response->addError('too much recursion calls of BaseApiController::validate()');
            return false;
        }

        if (!$this->checkIfAllRequiredFieldsDescribed($config, $request_data)) {
            return false;
        }

        $title = null;
        foreach ($request_data as $title => $value) {
            if (isset($config[$title][self::CONFIG_FIELD_NO_VALIDATE]) && $config[$title][self::CONFIG_FIELD_NO_VALIDATE] === true) {
                continue;
            }
            if (!$this->checkRequestConfig($config, $title)) {
                return false;
            }
            if (!$this->validateType($config, $title, $value)) {
                return false;
            }
            if (!$this->validateRules($config, $title, $value)) {
                return false;
            }
            if (!$this->validateEmpty($config, $title, $value)) {
                return false;
            }
            if ($this->isNeededRecursiveValidate($config[$title]) && !$this->validate($config[$title]['config'], $value)) { // рекурсия!
                return false;
            }
        }

        return true;
    }

    /**
     * Проверяет, что у поля с флагом not_empty в config не пустое значение
     *
     * @param array $config
     * @param $title
     * @param $value
     * @return bool
     */
    private function validateEmpty(array $config, $title, $value): bool
    {
        $not_empty = $config[$title][self::CONFIG_FIELD_NOT_EMPTY] ?? false;
        if ($not_empty === true && empty($value)) {
            $this->response->addError($title .' cant be an empty');
            return false;
        }

        return true;
    }

    /**
     * Проверяет тип у обязательных, и необязательных полей c непустым значением
     *
     * @param array $config
     * @param string $title
     * @param mixed $value
     * @return bool
     */
    private function validateType(array $config, $title, $value): bool
    {
        if (!$value && $config[$title][self::CONFIG_FIELD_REQUIRED] === false) {
            return true;
        }

        if ($config[$title][self::CONFIG_FIELD_TYPE] === gettype($value)) {
            return true;
        }

        $this->response->addError($title .' has invalid type: ' . gettype($value));
        return false;
    }

    /**
     * Проверяет значение на соответствие заданным правилам. Обрабатывает как одиночные, так и массив правил
     *
     * @param array $config
     * @param string $title
     * @param mixed $value
     * @return bool
     */
    private function validateRules(array $config, $title, $value): bool
    {
        if (!$value && $config[$title][self::CONFIG_FIELD_REQUIRED] === false) {
            return true;
        }

        if (array_key_exists(self::CONFIG_FIELD_RULES, $config[$title])) {
            $config_rules = $config[$title][self::CONFIG_FIELD_RULES];

            if (is_array($config_rules)) {
                $rules = $config_rules;
            } else {
                $this->response->addError('Rules config must be array!');
                return false;
            }

            foreach ($rules as $rule_name => $rule_params) {
                if (!is_string($rule_name)) {
                    $this->response->addError('Key for rules config array must content string value of rule name!');
                    return false;
                }
                if (array_key_exists($rule_name, $this->request_data_rules)) {
                    $validate_rule = $this->request_data_rules[$rule_name];
                    if (is_callable($validate_rule)) {
                        if (!$validate_rule($title, $value, $rule_params)) {
                            return false;
                        }
                    }
                }
            }
        }
        return true;
    }

    /**
     * Проверяет нужна ли рекурсиваня валидация,
     * Нужна если это массив или объект, для рекурсивного обхода полей
     *
     * @param array $config_for_one_field
     * @return bool
     */
    private function isNeededRecursiveValidate(array $config_for_one_field): bool
    {
        return ($config_for_one_field[self::CONFIG_FIELD_TYPE] === 'array' || $config_for_one_field[self::CONFIG_FIELD_TYPE] === 'object')
            && $config_for_one_field[self::CONFIG_FIELD_REQUIRED] === true
            && !empty($config_for_one_field['config']);
    }

    /**
     * Проверят что все поля с required = true из $config пришли в $request_data
     *
     * @param array $config
     * @param array $request_data
     * @return bool
     */
    private function checkIfAllRequiredFieldsDescribed(array $config, array $request_data): bool
    {
        $required_fields = [];
        foreach ($config as $key => $row_conf) {
            $required = $row_conf[self::CONFIG_FIELD_REQUIRED] ?? false;
            if ($required === true) {
                $required_fields[] = $key;
            }
        }
        $diff = array_diff($required_fields, array_keys($request_data));
        if ($diff) {
            $this->response->addError('invalid config '.implode(',', $diff).' found in config, but not found in request');
            return false;
        }
        return true;
    }

    /**
     * @param array $config
     * @param string $title
     * @return bool
     */
    private function checkRequestConfig($config, $title): bool
    {
        if (empty($config[$title])) {
            $this->response->addError('config is empty for '.$title);
            return false;
        }
        if (!isset($config[$title][self::CONFIG_FIELD_REQUIRED])) {
            $this->response->addError('config error: no required for '.$title);
            return false;
        }
        if (!isset($config[$title][self::CONFIG_FIELD_TYPE])) {
            $this->response->addError('config error: no type for '.$title);
            return false;
        }
        if (!in_array($config[$title][self::CONFIG_FIELD_TYPE], $this->request_data_types, true)) {
            $this->response->addError('config error: unknown type '.$config[$title][self::CONFIG_FIELD_TYPE].' for '.$title);
            return false;
        }
        return true;
    }

    /**
     * Возвращает массив правил
     * @return array
     */
    private function getRules() {
        return [
            self::RULE_NATURAL_NUMBER => function ($title, $value, $rule_params) {
                if( is_int($value) && $value >= 1) {
                    return true;
                }
                $this->response->addError('Данное поле должно быть целым положительным числом больше 0!', $title);
                return false;
            },
            self::RULE_ONLY_LETTERS_NUMBERS_AND_DASHES => function ($title, $value, $rule_params) {
                if (preg_match('/^[a-zA-Zа-яёА-ЯЁ0-9\s\-]+$/u', $value) !== 1) {
                    $this->response->addError('Данное поле должно содержать только буквы, цифры и тире!', $title);
                    return false;
                }
                return true;
            },
            self::RULE_ONLY_LETTERS => function ($title, $value, $rule_params) {
                if (preg_match('/^[a-zA-Zа-яёА-ЯЁ]+$/u', $value) !== 1) {
                    $this->response->addError('Данное поле должно содержать только буквы русского или латинского алфавита!', $title);
                    return false;
                }
                return true;
            },
            self::RULE_IS_CORRECT_PHONE_NUMBER => function ($title, $value, $rule_params) {
                if (preg_match('/^[+]{1}\d{1}[(]{1}\d{3}[)]{1}\d{3}[-]{1}\d{2}[-]{1}\d{2}$/', $value)  !== 1) {
                    $this->response->addError('Неправильный формат телефона (+#(###)###-##-##)', $title);
                    return false;
                }
                return true;
            },
            self::RULE_IS_CORRECT_COMPANY_NAME => function ($title, $value, $rule_params) {
                if (preg_match('/^[a-zA-Zа-яёА-ЯЁ0-9\s\-,"\'№\.]{2,100}$/u', $value)  !== 1
                    || preg_match('/^\d+$/', $value) === 1)
                {
                    $this->response->addError('Данное поле может содержать буквы, тире, кавычки и цифры и не может состоять целиком из цифр', $title);
                    return false;
                }
                return true;
            },
            self::RULE_IS_CORRECT_USER_NAME => function ($title, $value, $rule_params) {
                if (preg_match('/^[a-zA-Zа-яёА-ЯЁ\s\-.]{2,100}$/u', $value)  !== 1) {
                    $this->response->addError('Данное поле должно содержать только буквы русского или латинского алфавита, тире и точки', $title);
                    return false;
                }
                return true;
            },
            self::RULE_IS_CORRECT_EMAIL => function ($title, $value, $rule_params) {
                $min_length = $rule_params['min_length'];
                $max_length = $rule_params['max_length'];
                if (preg_match('/^[\w-\.]+@[\w-]+\.[a-z]{2,4}$/i', $value)  !== 1) {
                    $this->response->addError('Неправильный формат почты', $title);
                    return false;
                }
                if (strlen($value) < $min_length || strlen($value) > $max_length) {
                    $this->response->addError('Длина email должна быть не менее ' . $min_length . ' символов и не более ' . $max_length . ' символов', $title);
                    return false;
                }
                return true;
            },
            self::RULE_MIN_CHARS => function ($title, $value, $rule_params) {
                $min_length = $rule_params[0];
                if ($min_length < 1) {
                    $this->response->addError('Для использования данного правила валидации необходимо передать натуральное число не меньше 1');
                    return false;
                }
                if (iconv_strlen($value) < $min_length) {
                    $this->response->addError('Минимальная длина - ' . $min_length . ' символа(ов)', $title);
                    return false;
                }
                return true;
            },
            self::RULE_MAX_CHARS => function ($title, $value, $rule_params) {
                $max_length = $rule_params[0];
                if ($max_length < 1) {
                    $this->response->addError('Для использования данного правила валидации необходимо передать натуральное число не меньше 1');
                    return false;
                }
                if (iconv_strlen($value) > $max_length) {
                    $this->response->addError('Максимальная длина - ' . $max_length . ' символа(ов)', $title);
                    return false;
                }
                return true;
            },

        ];
    }

}