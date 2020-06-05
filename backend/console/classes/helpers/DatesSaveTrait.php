<?php

namespace console\classes\helpers;

/**
 * Trait DatesSaveTrait
 * Автоматически сохраняет date_created и date_modified если подмешать его в AR модели, в которой есть соотвествующие поля
 * и не переопределен родительский метод beforeSave
 */
trait DatesSaveTrait {

    public function beforeSave($insert) {
        $time = time();
        $this->date_updated = $time;
        if ($insert) {
            $this->date_created = $time;
        }
        return parent::beforeSave($insert);
    }
}
