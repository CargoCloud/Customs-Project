<?php

class EdiWriter {

    const CDE_SEPARATOR = "+";
    const DE_SEPARATOR = ":";
    const DECIMAL_NOTATION = ".";
    const RELEASE_INDICATOR = "?";
    const REPETITION_SEPARATOR = " ";
    const SEGMENT_TERMINATOR = "'";

    static $segbuf = "";
    static $segcnt = 0;

    function __construct() {
        
    }

    /**
     * Returns Service String Advice (UNA segment, without segment tag)
     * @return Service String Advice
     */
    static function getUNA() {
        return "UNA" . self::CDE_SEPARATOR . self::DE_SEPARATOR
                . self::DECIMAL_NOTATION . self::RELEASE_INDICATOR
                . self::REPETITION_SEPARATOR;
    }

    /**
     * Converts Object Identifier to Sender Reference Number
     * @param type $OID Object Identifier
     * @return type Sender Reference Number
     */
    static function getSenderRef($OID) {
        $s = str_replace("-", "", $OID);
        return strtoupper($s);
    }

    /**
     * Writes UNA segment. If file exists, it is overwritten.
     * @param type $file edifact file
     */
    static function writeUNA($file) {
        self::$segbuf = "" . self::getUNA();
        self::writeSegment($file);
    }

    /**
     * Clears segment buffer to start new segment.
     * Call this function to build segment.
     * @param type $segtag segment tag
     */
    static function beginSegment($segtag) {
        self::$segbuf = "" . $segtag;
        if ($segtag != "UNH") {
            self::$segcnt++;
        } else {
            self::$segcnt = 1;
        }
    }

    /**
     * Appends composite data element separator followed by data element
     * @param type $de data element
     */
    static function appendCDE($de) {
        self::$segbuf .= self::CDE_SEPARATOR . $de;
    }

    /**
     * Appends data element separator followed by data element
     * @param type $de data element
     */
    static function appendDE($de) {
        self::$segbuf .= self::DE_SEPARATOR . $de;
    }

    /**
     * Writes segment into file. If file exists, it is overwritten.
     * @param type $file edifact file
     */
    static function writeSegment($file) {
        self::$segbuf .= self::SEGMENT_TERMINATOR;
        if (file_put_contents($file, self::$segbuf) === FALSE) {
            $errmsg = "File $file: Error writing segment " . self::$segbuf
                    . ".\n";
            fwrite(STDERR, $errmsg);
            exit(1);
        }
    }

    /**
     * Appends segment to file.
     * @param type $file edifact file
     */
    static function appendSegment($file) {
        self::$segbuf .= self::SEGMENT_TERMINATOR;
        //self::trimSeparators(self::$segbuf);
        if (file_put_contents($file, self::$segbuf, FILE_APPEND) === FALSE) {
            $errmsg = "File $file: Error writing segment " . self::$segbuf
                    . ".\n";
            fwrite(STDERR, $errmsg);
            exit(1);
        }
    }
    /**
     * Returns number of segment in message UNH to UNT inclusively. 
     * @return type number of segment in message
     */
    static function getSegmentCount() {
        return self::$segcnt;
    }
    
    /**
     * Remove trailing separators from segment buffer:
     * "::+" -> "+"
     * "+'" -> "'"
     * @param type $segbuf segment buffer
     */
    static function trimSeparators($segbuf) {
        $s1 = self::CDE_SEPARATOR . self::SEGMENT_TERMINATOR;
        $s2 = self::DE_SEPARATOR . self::CDE_SEPARATOR;
        
        while (strstr($segbuf, $s1)) {
            $segbuf = str_replace($s1, self::SEGMENT_TERMINATOR, $segbuf);
        }
        
        while (strstr($segbuf, $s2)) {
            $segbuf = str_replace($s2, self::CDE_SEPARATOR, $segbuf);
        }       
    }

}

?>
