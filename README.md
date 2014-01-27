Spektrum saved model file parser
================================

This parser, written in PHP takes a SPM file and returns an associative array of 
the contents, taking account of all the quirks in the file format.

Files in this repo are:

* example.php - Simple php script which calls the fn on the Inverza280.spm and outputs some JSON
* Inverza280.spm - An example SPM file (from Spektrumrc.com)
* parse.php - The actual functions to do the parsing
* README.md - This file

Usage
-----

If you want to see this in action, clone this repo onto any webserver with PHP instaled
and load up the file example.php.

It requires PHP 5.4.0 or newer, but if you remove the ',JSON_PRETTY_PRINT' in example.php
it should work with earlier versions.

About
-----

My name is Robin Kearney <robin@kearney.co.uk> I blog at http://riviera.org.uk/ and 
have written a little project which uses this code at http://spektrum.riviera.org.uk/

I originally intended the site to be a nice way of sharing saved model files, anybody
can register, upload a SPM, have some degree of visualisation and, most importantly, 
get a unique, permanent link to that file which can be used in forums and the like.
