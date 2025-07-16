# php-onefile-debug
Better debugging in PHP. Easy setup with just one file.

To Install: Just copy debug.php into your project somewhere and include it.

To use: Include the Debug file (debug.php) and call the Debug::dbg() function with as many parameters as you want. Each parameter will be shown in a separate div so you can easily see what is going on.
Example:
$a = null;
$b = get_data_from_somewhere();
$c = 'string'.$num;
Debug::dbg($a,$b,$c);

For use in a command-line interface, use the console output for a more readable format:
Debug::consoledbg($a,$b,$c);
