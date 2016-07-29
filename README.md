# ALPHA - NEED IMPROVEMENTS

## based on fatfree composer demo

This is a simple Image Processing API using a little F3 app that uses composer for loading dependencies.

To install the requirements, just run `composer install` and it'll download the fatfree-core and additional packages.

This setup uses the composer autoloader for loading all fixed dependencies, but the F3 autoloader for all application classes. 
This way you don't need to update your composer classmap all the time while developing your application, since the F3 classloader is smart enough to load your files dynamically. 

## methods 

Different methods (i need to list them all) but the classic route is something like

**GET** /image/transform/**@operations**?img=YOUR_URL 

The operations param can be something like... 

/rotate,60|crop,100,100|sepia

/pixel,20|bright,20|hflip

I am sure if you really want to use it it you'll understand the following expression (it'll help you to define @parameters depending on your needs)

 $pattern = '/(resize,(\d)*,(\d)*,(crop|d),(enlarge|d)|pixel,(\d)*|bright,(-|)(\d)*|contrast,(-|)(\d)*|rotate,(\d)*|smooth,(-|)(\d)*|vflip|hflip|invert|grey|sepia|emboss|sketch|blur,(selective|d))/im';
