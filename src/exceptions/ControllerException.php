<?php

namespace NewLoGD\Exceptions;

abstract class ControllerException extends BaseException {
    abstract public function getResponseMethod() : string;
}