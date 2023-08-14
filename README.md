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

Often times you want to use a special enum to look up related models. We
provide a few convenient ways to do this:

```php
PullRequests::query()
  ->hasSpecial(SpecialOrganizations::Laravel)
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
