<?php

namespace Query;

class Base {
	const OPERATOR_EQ = "=";
	const OPERATOR_NEQ = "<>";
	const OPERATOR_GT = ">";
	const OPERATOR_LT = "<";
	const OPERATOR_IS = "IS";
	const OPERATOR_ISNOT = "IS NOT";
	
	const JOIN_INNER = "INNER";
	const JOIN_OUTER = "OUTER";
	
	const ORDER_ASC = "ASC";
	const ORDER_DESC = "DESC";
}