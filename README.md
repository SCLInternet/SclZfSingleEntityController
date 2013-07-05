SclZfUtilities
==============

[![Build Status](https://travis-ci.org/SCLInternet/SclZfUtilities.png?branch=master)](https://travis-ci.org/SCLInternet/SclZfUtilities)

Some useful utility classes, plugins and view helpers.

GenericDoctrineMapper
=====================

This is a class that can be extended to create Doctrine ORM based Mapper classes
very quickly and easily.


Controller plugins
==================

formSubmitted
-------------

This is a quick way to check if a form has been posted and the content is valid.

To use it in your controller simple do

```php
if ($this->formSubmitted($myForm)) {
    doWhatNeedsToBeDone();
}
````
