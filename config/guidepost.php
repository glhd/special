<?php

return [
	/*
	|--------------------------------------------------------------------------
	| Default `int` column
	|--------------------------------------------------------------------------
	|
	| Guidepost guesses column names based on the type of value that your enum
	| is backed by. If the enum is backed by an integer, we will use this key
	| name (usually `id` if you're mapping enums to primary keys).
	|
	*/
	
	'default_int_key_name' => 'id',
	
	/*
	|--------------------------------------------------------------------------
	| Default `string` column
	|--------------------------------------------------------------------------
	|
	| Guidepost guesses column names based on the type of value that your enum
	| is backed by. If the enum is backed by a string, we will use this key
	| name (often `slug` or `key` or some other unique column).
	|
	*/
	
	'default_string_key_name' => 'slug',
	
	/*
	|--------------------------------------------------------------------------
	| How to handle missing records
	|--------------------------------------------------------------------------
	|
	| When a model is missing from the database, you can configure guidepost
	| to either create it for you, or throw an error. Usually you want to
	| automatically create missing records when running tests, but throw in
	| production.
	|
	*/
	
	'fail_when_missing' => 'testing' !== env('APP_ENV'),
];
