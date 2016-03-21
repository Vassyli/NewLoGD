<?php

namespace NewLoGD\Exceptions;

class RequestNotFound extends ControllerException {
    public function getResponseMethod() : string {
        return "notFound";
    }
}