<?php

namespace Navigation;

interface ItemAPI {
	public function getId();
	public function getParentid();
	public function getAction();
	public function getTitle();
}