<div style="float: right;">
	<a href="https://github.com/glhd/special/actions" target="_blank">
		<img 
			src="https://github.com/glhd/special/workflows/PHPUnit/badge.svg" 
			alt="Build Status" 
		/>
	</a>
	<a href="https://codeclimate.com/github/glhd/special/test_coverage" target="_blank">
		<img 
			src="https://api.codeclimate.com/v1/badges/17364871b7617d29896e/test_coverage" 
			alt="Coverage Status" 
		/>
	</a>
	<a href="https://packagist.org/packages/glhd/special" target="_blank">
        <img 
            src="https://poser.pugx.org/glhd/special/v/stable" 
            alt="Latest Stable Release" 
        />
	</a>
	<a href="./LICENSE" target="_blank">
        <img 
            src="https://poser.pugx.org/glhd/special/license" 
            alt="MIT Licensed" 
        />
    </a>
    <a href="https://twitter.com/inxilpro" target="_blank">
        <img 
            src="https://img.shields.io/twitter/follow/inxilpro?style=social" 
            alt="Follow @inxilpro on Twitter" 
        />
    </a>
</div>

# Special✨

Sometimes, certain database records are just **special✨**, and you need to
reference them inside your code.

You might have a few special vendors that have special handling in a few 
special places, and maybe their own special artisan commands run from time-to-time.

special✨ lets you use [backed enums](https://www.php.net/manual/en/language.enumerations.backed.php)
to reference Eloquent models. Rather than backing your enum with a string or
integer, think of it as backing your enum with a database record.

If the record is missing, you can let special✨ automatically create it for
you. This is especially great in testing, where you may have a few special
records that need to exist for a few special tests, but you don't want to 
track which tests need to run special seeders at setup.

## Installation

```shell
composer require glhd/special
```

## Usage

To start, create a new enum and use the `EloquentBacking` trait provided by 
this package. 

You can **optionally** add a `CreateWith` attribute to any of your enum cases, 
and special✨ will use those values to automatically create the model record
for you if it's missing.

```php
use Glhd\Special\EloquentBacking;

enum SpecialOrganizations: string
{
	use EloquentBacking;
	
	#[CreateWith(['name' => 'Laravel', 'url' => 'https://laravel.com/'])]
	case Laravel = 'laravel';
	
	#[CreateWith(['name' => 'Spatie', 'url' => 'https://spatie.be/'])]
	case Spatie = 'spatie';
	
	#[CreateWith(['name' => 'Thunk', 'url' => 'http://thunk.dev/'])]
	case Thunk = 'kathunk';
	
	// If your enum name is the same as the model name, this is optional.
	public function modelClass(): string
	{
		return Organization::class;
	}
}
```

Now, you can use those enums to access the backing models. By default,
strings are assumed to be a `slug` column, and integers are assumed to
be the `id` column, but this can be configured at the project level or
the individual enum level.

```php
SpecialOrganizations::Laravel->toArray();

// [
//   'id' => 1337,
//   'slug' => 'laravel',
//   'name' => 'Laravel',
//   'url' => 'https://laravel.com/',
//   'created_at' => [...],
//   'updated_at' => [...],
// ]
```

Special enums decorate the underlying model, so you can often just call
the enum as though it were the model itself. But sometimes you want the
actual copy of the model instance, which you can do with:

```php
// Get a copy of the model — only loads from DB once, but clones each time
SpecialOrganizations::Laravel->get();

// Get a single, shared copy — same instance each time
SpecialOrganizations::Laravel->singleton();

// Get a fresh copy — always loads from the DB
SpecialOrganizations::Laravel->fresh();
```

## Using the primary key cache

Often, the only reason you need a special enum is to use its primary key
in another query or to set up a relationship. Special✨ keeps a cache of
the 50 most recently-used primary keys so that in many cases, you don't
have to do a single database lookup. You can configure the number of keys
cached and the cache TTL by publishing the package config.

```php
PullRequest::create([
    'organization_id' => SpecialOrganizations::Laravel->getKey(),
    'ref_number' => 47785,
    'title' => '[10.x] Add Collection::enforce() method',
]);
```

As long as `SpecialOrganizations::Laravel` has been used in the last hour,
the `'organization_id'` value can be set without making a single query
to the database.

Due to the nature of these kinds of enums, this is usually pretty safe,
since they're used with the types of records that aren't likely to change
in your application ever. That said, you can always clear the cache
at any time with `php artisan cache:clear-special-keys`.

## Using with Laravel relations

Often times you want to use a special enum to look up related models. We
provide a few convenient ways to do this:

```php
PullRequests::query()
  ->forSpecial(SpecialOrganizations::Laravel)
  ->dumpRawSql();

// select *
// from `pull_requests`
// where `organization_id` = 1337
```

Or, you can use a special enum to constrain an existing query.
The exact same query can be generated with:

```php
SpecialOrganizations::Laravel
  ->constrain(PullRequests::query())
  ->dumpRawSql();
```

The `constrain()` method (and `forSpecial` macro) both use the primary
key cache under the hood. This means that most relational queries
using special enums will not trigger any additional database queries.

## Automatic Model Observation

Special✨ automatically registers model observers for any model that
you retrieve. This means that if you update or delete a special model 
during a request, subsequent calls to the enum will automatically
reflect those changes.

This works regardless of whether the updated model was retrieved using
the package.

```php
// Get a copy of the Laravel organization, which causes it to be
// cached for the rest of the request.
$laravel = SpecialOrganizations::Laravel->singleton();
assert($laravel->name === 'Laravel');

// Now we'll update it without using our enum
$org = Organizations::where('slug', 'laravel')->first();
$org->update(['name' => 'Laravel LLC']);

// Later calls to the enum will reflect the changes
$laravel = SpecialOrganizations::Laravel->singleton();
assert($laravel->name === 'Laravel LLC');
```

