<?php
// this is a generator function that returns a line from the file at the first call
// and the following lines (individually) on subsequent calls
function readLineFromFile($file) {
    $f = fopen($file, 'r');
    try {
        while ($line = fgets($f)){
            yield $line;
        }
    }
    finally {
        fclose($f);
    }
}

/*
 * ReadLines would normally be used thus:
 *
 * foreach( readLineFromFile("languages.dat") as $line ){
 *     // do something with $line
 * }
*/

// fetchline returns the next line (including eol/eof markers if present)
// or false if nothing more to be read
function fetchLine( $resource ){
    return fgets( $resource );
}
?>
