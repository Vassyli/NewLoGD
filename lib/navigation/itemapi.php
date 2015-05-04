<?php
/**
 * NewLoGD
 *
 * @author      Basilius Sauter <basilius.sauter@hispeed.ch>
 * @copyright   Copyright (c) 2015, Basilius Sauter
 * @licence     https://www.gnu.org/licenses/agpl-3.0.html GNU Affero GPL 3.0
 */

namespace Navigation;

interface ItemAPI {
	public function getId();
	public function getParentid();
	public function getAction();
    public function getParsedAction();
	public function getTitle();
}