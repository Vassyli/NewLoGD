<?php

namespace Submodel;

interface EditableSubmodel {
    public function getById($id);
    public function all();
    public function create(array $sanitize);
}