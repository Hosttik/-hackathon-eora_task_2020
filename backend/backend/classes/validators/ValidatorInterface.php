<?php

namespace backend\classes\validators;

interface ValidatorInterface {
    public function validate(array $config, array $data);
}