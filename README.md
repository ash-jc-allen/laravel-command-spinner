<p align="center">
<img src="/art/logo.png" alt="Laravel Command Spinner" width="600">
</p>

<p align="center">
<a href="https://packagist.org/packages/ashallendesign/laravel-command-spinner"><img src="https://img.shields.io/packagist/v/ashallendesign/laravel-command-spinner.svg?style=flat-square" alt="Latest Version on Packagist"></a>
<a href="https://packagist.org/packages/ashallendesign/laravel-command-spinner"><img src="https://img.shields.io/packagist/dt/ashallendesign/laravel-command-spinner.svg?style=flat-square" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/ashallendesign/laravel-command-spinner"><img src="https://img.shields.io/packagist/php-v/ashallendesign/laravel-command-spinner?style=flat-square" alt="PHP from Packagist"></a>
<a href="https://github.com/ash-jc-allen/laravel-command-spinner/blob/master/LICENSE"><img src="https://img.shields.io/github/license/ash-jc-allen/laravel-command-spinner?style=flat-square" alt="GitHub license"></a>
</p>

## Table of Contents

- [Overview](#overview)
- [Installation](#installation)
    - [Requirements](#requirements)
    - [Install the Package](#install-the-package)
- [Usage](#usage)
  - [Adding Loading Spinners to Commands](#adding-loading-spinners-to-commands)
  - [Adding Text to the Spinner](#adding-text-to-the-spinner) 
  - [Customising the Spinner Type](#customising-the-spinner-type)
- [Examples](#examples)
- [Gotchas](#gotchas)
- [Security](#security)
- [Contribution](#contribution)
- [License](#license)

## Overview

A Laravel package that allows you to add 47 different styles of loading spinners to your Artisan commands.

## Installation

### Requirements
The package has been developed and tested to work with the following minimum requirements:

- PHP 8
- Laravel 8

### Install the Package
You can install the package via Composer:

```bash
composer require ashallendesign/laravel-command-spinner
```

## Usage

### Adding Loading Spinners to Commands

Using a loading spinner can be useful for when executing long-running commands, such as fetching data from an API.

To add a loading spinner to your command, first start by adding ` HasSpinner ` trait to your command. After that, you can then use the ` withSpinner() ` method like shown in the example below:

```php
use AshAllenDesign\CommandSpinner\Traits\HasSpinner;

class YourCommand extends Command
{
    use HasSpinner;

    public function handle(): int
    {
        $this->withSpinner(function () {
            // Run your code here.
        });
    }
}
```

### Adding Text to the Spinner

When you're displaying a loading spinner in your console command, you may wish to also display a message.

The example below shows how you can add some text to be displayed alongisde the loading spinner:

```php
use AshAllenDesign\CommandSpinner\Traits\HasSpinner;

class YourCommand extends Command
{
    use HasSpinner;

    public function handle(): int
    {
        $this->withSpinner(function () {
            // Run your code here.
        }, 'Fetching data from API...');
    }
}
```

### Customising the Spinner Type

The package comes with many types of loading spinners out of the box that you can use. To view all of the different possible loading spinners, check out the [SpinnerType](/src/Classes/SpinnerType.php) class.

The example below shows how you can choose a different spinner type:

```php
use AshAllenDesign\CommandSpinner\Classes\SpinnerType;
use AshAllenDesign\CommandSpinner\Traits\HasSpinner;

class YourCommand extends Command
{
    use HasSpinner;

    public function handle(): int
    {
        $this->withSpinner(function () {
            // Run your code here.
        }, 'Fetching data from API...', SpinnerType::BLOCK_VARIANT_1);
    }
}
```

## Examples

The below example shows you can use the PHP 8 named parameters to add a spinner to your command:

```php
use AshAllenDesign\CommandSpinner\Classes\SpinnerType;
use AshAllenDesign\CommandSpinner\Traits\HasSpinner;

class YourCommand extends Command
{
    use HasSpinner;

    public function handle(): int
    {
        $this->withSpinner(
            closure: function () {
                $this->fetchDataFromAPI();
            },
            outputText: 'Fetching data from API...',
            spinnerType: SpinnerType::BLOCK_VARIANT_1
        );
    }
}
```

In the below example, let's imagine that we have a class that calculates something for us and returns it. So, we'll display a loading spinner while it's calculating and then output the result when it's returned.

```php
use AshAllenDesign\CommandSpinner\Traits\HasSpinner;

class YourCommand extends Command
{
    use HasSpinner;

    public function handle(): int
    {
        $result = $this->withSpinner(function () {
            return (new Calculator)->calculate();
        });
        
        $this->line($result);
    }
}
```

## Gotchas

Here's a list of a few gotchas that I'm currently aware of at the time of writing this:

- This package's functionality is based on the [spatie/fork](https://github.com/spatie/fork) package which is currently under active development. Therefore, it's possible that the functionality of this package may change if the ` fork ` package is updated before it's initial release.

- Due to the fact that the package is based on the ` fork ` package, 2 child PHP processes are created for each spinner that is created. One of them handles the spinning animation, while the other executes the closure that is passed in. This means that because the closure is executed in a child process, it won't be able to directly change any data in the command class itself.

- The ` withSpinner() ` method currently only allows for primitive types to be returned. Therefore, you can't currently return objects.

## Security

If you find any security related issues, please contact me directly at [mail@ashallendesign.co.uk](mailto:mail@ashallendesign.co.uk) to report it.

## Contribution

If you wish to make any changes or improvements to the package, feel free to make a pull request.

To contribute to this library, please use the following guidelines before submitting your pull request:

- Write tests for any new functions that are added. If you are updating existing code, make sure that the existing tests
  pass and write more if needed.
- Follow [PSR-2](https://www.php-fig.org/psr/psr-2/) coding standards.
- Make all pull requests to the ``` master ``` branch.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
