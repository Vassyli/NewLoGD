<?php

namespace NewLoGD\Helper;

use NewLoGD\Application;

trait find {
    public static function find(int $id) {
        return Application::getEntityManager()->find(__CLASS__, $id);
    }
}