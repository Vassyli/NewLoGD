<?php

interface LocalmoduleAPI {
	public function __construct($model, $row, $page);
	public function execute();
	public function output();
}