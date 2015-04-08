<?php

interface Basicmodelitem extends Modelitem {
	public function __construct(\Model $model, array $row);
}