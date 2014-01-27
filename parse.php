<?php
/**
 * Takes a string in the Spektrum Radio model memory save format
 *
 * @param string $str The original .SPM file
 *
 * @return associative array of the parsed file
 */
function parse_file($str) {

    /*
     * TL and SL in these two arrays mean Top-Level and Second-Level
    */
    $TLmultiples = array(
                    "Special",
                    "Timer",
                    "DR_Expo",
                    "Trim",
                    "TrimID",
                    "Analog",
                    "SoftSw",
                    "P-Mix",
                    "Servo",
                    "Digital",
                    );

    $SLmultiples = array(
                    "[FlightLog]",
                    "[Module]",
                    "[Checklist]",
                    "[fmName]",
                    "[Curvedata]",
                    );


    if(empty($str)) return false;

    $lines               = preg_split( '/\r|\r|\n/', $str );
    $ret                 = Array();
    
    $duplicateSection    = false;
    $inSubSection        = false;
    
    $currentSection      = "";
    $currentSubsection   = "";

    $sectionIndex    = 0;
    $subsectionIndex = 0;

    foreach($lines as $line) {
        
        $line = trim($line);

        if (!$line || $line[0] == "#" || $line[0] == ";" ) continue; // remove comments

        if ( $line == "*EOF*" ) break; // Stop if we reach the end of the file

        // Look for the end marker for each type of section and unset the markers if found
        if(preg_match('/<\//',$line)) { continue; }
        if(preg_match('/\[\//',$line)) { continue; }

        /*
         * <SECTION> detection
        */ 
        if ( $line[0] == "<" && $endIdx = strpos($line, ">") ) {

            $currentSection = substr($line, 1, $endIdx-1);

            // If we find the start of a <SECTION>, set the [SECTION] marker false
            $inSubSection    = false;
            // And set the subsection index back to 0
            $subsectionIndex = 0;

            /* 
             * If this section is in the list of sections which have duplicates
             * then set the duplicateSection marker to true, otherwise force it
             * to false
            */
            if ( in_array($currentSection, $TLmultiples) ){

                $duplicateSection = true;
                continue;

            }else{

                $duplicateSection = false;
                continue;

            }
        }

        /*
         * [SUBSECTION] detection
        */ 
        if ($line[0] == "[" && $endIdx = strpos($line, "]" ) ) {

            $currentSubsection = substr($line, 1, $endIdx-1);
            $inSubSection = true;
            $subsectionIndex++;
            continue;

        }

        if ( preg_match('/\*Index=/', $line) ) {
            $tmp = explode("=", $line);
            $sectionIndex = trim($tmp[1]);
        }

        $data = tidyline($line);

        /*
         * This is a little messy but...
         * 
         * *) If we are inside a subsection AND inside a duplicate section we need to include the indexes
         * *) elseif if we are in a subsection, just include the index for that
         * *) elseif we are in a duplicate master section, include the index for that
         * *) else everything else just goes in the top level
        */
        if ( $inSubSection && $duplicateSection ){
            $ret[$currentSection][$sectionIndex][$currentSubsection][$subsectionIndex][$data[0]] = $data[1];
        }elseif( $inSubSection ) {
            $ret[$currentSection][$currentSubsection][$subsectionIndex][$data[0]] = $data[1];
        }elseif ( $duplicateSection ){
            $ret[$currentSection][$sectionIndex][$data[0]] = $data[1];
        }else{
            $ret[$currentSection][$data[0]] = $data[1];
        }

    }     

    return $ret;
}

/**
 * Pass in a line from a SPM file and it returns an array with key at index 0
 * and value at index 1, which will be an array if necessary.
 *
 * This is needed because, while most lines in the spm file are in the format:
 * 
 * name=value
 *
 * Some lines are (entertainingly) in the format:
 *
 * name: val1 val2 val3
 *
 * Having the values as an array is easier to handle, so I split these up
 * and return an array.
 *
 *
 * @param string $line is a line from a SPM file which needs tidying
 *
 * @return array with thing[0] = key and thing[1] = value
 *
 */
function tidyLine($line){

    $return = array();


    if ( preg_match("/:/",$line) ){

        /*
         * If the line has a colon in it the left side is the key
         * but the right side is an array of data points, so trim
         * any leading whitespace, then split that up into an array
         * The return is: key = array(value); in this case
        */
    
        $foo = explode(":", $line);
        $bar = explode(" ", trim($foo[1]));
        
        $return[0] = trim($foo[0]);
        $return[1] = $bar;

    }else{

        // Otherwise just split on = and return key = value as an array
        
        $tmp = explode("=", $line, 2);

        $return[0] = trim(preg_replace('/"/', "", $tmp[0]));
        $return[1] = trim(preg_replace('/"/', "", $tmp[1]));
    }  

    return $return;
}

?>
