<?php

// Quick hack to send the source to the browser
if ( isset($_GET['source'] )) {
    highlight_file('parse.php');
    exit;
}
?>
<html>
<head>
    <title>spektrum spm file parser example</title>
</head>
<style type="text/css">
    html{
        font-family: sans-serif;
        font-size: 90%;
    }
</style>
<body>
<h1>Spektrum spm file parser example</h1>
<p>
The output below is the resultant JSON from reading in a Spektrum saved model (.spm) file and running it through the parser.
The <tt>parse_file()</tt> PHP function itself actually returns a PHP associative array, which in this example is then passed to json_encode 
and returned to the browser.
</p>
<p>This example is for the <a href="http://www.horizonhobby.co.uk/aeroonline/e2eflite/e2-efl6350/e2-inversa.html">Inverza 280</a> model, the saved file originally being downloaded from the Spektrum Website, you can grab the file
directly <a href="Inverza280.spm">here</a>.
</p>
<h2>Source</h2>
<p>If you click <a href="<?php echo $_SERVER['PHP_SELF'] . "?source"; ?>">here</a> you will see the source of the <tt>parse_file()</tt> and the helper function <tt>tidyline()</tt>.
</p>

<h2>Parsed file follows:</h2>
<?php

require_once('parse.php');

$file="Inverza280.spm";

$fileContents = file_get_contents($file);

/*
 * Call parse_file, which does the heavy lifting of parsing
 * the file, then pass the output to json_encode to output 
 * a json interpretation of the file, then to print_r to
 * dump the json to the browser
 * 
 * Note: JSON_PRETTY_PRINT requires php 5.4.0 or higher but
 * is only needed to make the output more human readable
 */

echo '<pre>';
print_r(json_encode(parse_file($fileContents),JSON_PRETTY_PRINT));
echo '</pre>';

?>
<hr>
<p>End of file</p>
</body>
</html>
