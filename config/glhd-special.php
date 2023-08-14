<?php

return [
	/*
	|--------------------------------------------------------------------------
	| Key caching
	|--------------------------------------------------------------------------
	|
	| By default, we will cache the primary keys of all special enums for one
	| hour. Due to the nature of these models, this is likely to be quite safe.
	| Set the TTL to 0 to disable this behavior.
	|
	*/
	
	'cache_ttl' => 3600,
	
	/*
	|--------------------------------------------------------------------------
	| Cache size limit
	|--------------------------------------------------------------------------
	|
	| By default, we only keep 50 primary keys in cache. This prevents the
	| cache from growing too large (especially if you set the TTL very high).
	| You can adjust this or set the limit to 0 to never prune.
	|
	*/
	
	'cache_limit' => 50,
	
	/*
	|--------------------------------------------------------------------------
	| Default `int` column
	|--------------------------------------------------------------------------
	|
	| We guess column names based on the type of value that your enum
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
	| We guess column names based on the type of value that your enum
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
	| When a model is missing from the database, you can configure the package
	| to either create it for you, or throw an error. Usually you want to
	| automatically create missing records when running tests, but throw in
	| production.
	|
	*/
	
	'fail_when_missing' => 'testing' !== env('APP_ENV'),
];
