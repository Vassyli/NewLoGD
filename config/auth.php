<?php
/**
 * authentification configuration file
 *
 * @package Config
 */

return [
	"facebook" => [
		"enabled" => true,
        "text" => [
            "name" => "Facebook",
            "logintext" => "Login via your Facebook account",
        ],
		"id" => "829434160511879",
		"secret" => "a9390952075060d7d1cfcf1ac21cf222",
		"scope" => "public_profile email",
	],
	
	"google" => [
		"enabled" => true,
        "text" => [
            "name" => "Google",
            "logintext" => "Login via your Google account",
        ],
		"id" => "224689782474-knlr3vesj32280eutpen4o7fbu59gj1k.apps.googleusercontent.com",
		"secret" => "wfmeRhIIWk3oPKrMSzwcdJet",
		"hybridauth" => "Google",
		"scope" => "profile email",
	],
];