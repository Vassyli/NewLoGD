<?php
/**
 * NewLoGD
 *
 * @author      Basilius Sauter <basilius.sauter@hispeed.ch>
 * @copyright   Copyright (c) 2015, Basilius Sauter
 * @licence     https://www.gnu.org/licenses/agpl-3.0.html GNU Affero GPL 3.0
 */

interface LocalmoduleAPI {
    /**
     * Constructor
     * @param \Model $model Main-Model instance
     * @param array $row Database-Information about this instance, contains configuration-per-page and such
     * @param type $page Reference to the Page.
     */
	public function __construct(\Model $model, array $row, page\api $page);
    /**
     * Executes Controller-Logic (edit/change/create data)
     */
	public function execute();
    /**
     * Executes View-Logic
     * @return string A string containing data added to the output buffer.
     */
	public function output();
    /**
     * Modifies Navigation
     * @param Navigation\Container instance of navigation container
     */
    public function navigationHook(Navigation\Container $navigation);
    /**
     * Get the pageconfig-Formular for this module
     * @return \FormGenerator instance of the settings
     */
    public function getPageconfigForm($action);
}