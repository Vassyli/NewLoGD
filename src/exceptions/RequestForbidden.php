<?php

namespace NewLoGD\Exceptions;

class RequestForbidden extends ControllerException {
    public function getResponseMethod() : string {
        return "forbidden";
    }
}