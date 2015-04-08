<?php

interface LocalmoduleAPI {
	public function __construct(\Model $model, array $row, $page);
	public function execute();
	public function output();
}