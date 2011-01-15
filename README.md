# Kodwoo

A Kohana dwoo view adapter

## About

[Dwoo](http://dwoo.org/) is a template engine, similar to [Smarty](http://smarty.net), but re-written for PHP5.

Kodwoo allows you to use Dwoo with Kohana 3 with minimal effort. Simply use the `Kodwoo_View` class instead of
`View` or `Kohana_View` like you were used to, and (by default) name your template files ending in `.tpl` instead of `.php`, and you're up and running. 

Template parameters are added exactly the same way you would with standard Kohana views. That is, either supplied to the `Kodwoo_view` constructor/factory, or by using the view instance's `bind()` and `set()` routines.

## Configuration

Further customization can be added by adding a *dwoo* configuration section to your application or module configuration area. For details of what goes into this configuration, see `kodwoo/config/dwoo.php`. Note that some parts of the configuration can be segmented out to allow different parts of the application (different modules, for example) to use different dwoo template configuration options, such as which extension to use or whether to turn on automatic escaping. Use the `$group` parameter of the `Kodwoo_View` constructor to specify a which configuration group you want to use, or "default" by default.

## Installation

To install from git, don't forget to update your submodules in order to import the Dwoo source tree. The process looks something like this (stating from your Kohana root dir)"

    git submodule add https://tylerl@github.com/tylerl/Kodwoo.git modules/kodwoo
    cd modules/kodwoo
    git submodule update --init

## Notes

Note that for best results, your application's cache directory needs to be be writable by Kohana.
