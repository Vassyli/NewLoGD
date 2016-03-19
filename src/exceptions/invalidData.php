<?php

namespace NewLoGD\Exceptions;

class InvalidData extends ControllerException {
    public function getResponseMethod() : string {
        return "invalidData";
    }
}