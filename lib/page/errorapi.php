<?php

namespace page;

interface errorapi {
	const ERROR_ACCESS_FORBIDDEN = 403;
	const ERROR_NOT_FOUND        = 404;
	
	public function get_errorcode();
}