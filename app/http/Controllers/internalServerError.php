<?php

namespace NewLoGD\Exceptions;

class internalServerError extends ControllerException {
    public function getResponseMethod() : string {
        return "internalError";
    }
}