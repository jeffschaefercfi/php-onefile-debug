# php-onefile-debug
Better debugging in PHP. Easy setup with just one file.

To Install: Just copy debug.php into your project somewhere and include it.

To use: call the dbg() function with as many parameters as you want. Each parameter will be shown in a separate div so you can easily see what is going on.
Example:
$a = null;
$b = get_data_from_somewhere();
$c = 'string'.$num;
dbg($a,$b,$c);
