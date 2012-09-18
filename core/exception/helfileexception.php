<?php
/** 
 * This exception handles easier some file/directory issues with predefined
 * error messages
 *
 * @author Hendrik Schaeidt
 * @version 1 - 04.12.2011
 *
 * This is open source. Just keep copyright assignement and enjoy.
 *
 * © Hendrik Schaeidt & Steven Zumpe 2011
 */
 
namespace core\exception;
use core\interfaces\helException;
 
class helFileException extends \Exception implements helException {
    /**
     * Predefined Exception error message
     */
    private $_sMessage = "";
    
    /**
     * Additional information you may have been sent
     */
    private $_sAdditionalInfo = "";
    
    /**
     * Error Type
     */
    private $_sErrorType = "";
    
    /**
     * Constructor
     *
     * @param int $iErrorType: errorcode
     * @param String $sAdditionalInfo: custom information
     * @return void
     */
    public function __construct($iErrorType, $sAdditionalInfo = "") {
        //Clean vars
        $iErrorType = intval($iErrorType);
        $sAdditionalInfo = strval($sAdditionalInfo);
        
        //Process
        $this->_sErrorType = $this->_getErrorType($iErrorType);
        $this->_sAdditionalInfo = $sAdditionalInfo;
        $this->_sMessage = $this->_getErrorMessage($iErrorType);
    }
    
    /**
     * Returns error type
     *
     * @return String $sErrorType: Exception name followed by error type
     */
    public function getErrorType() {
        $sErrorType = "helFileException\/".$this->_sErrorType;
        return strval($sErrorType);
    }
    
    /**
     * Returns message
     *
     * @return String $this->_sMessage: predefined message
     */
    public function getCustomMessage() {
        return strval($this->_sMessage);
    }
    
     /**
     * Returns additional info
     *
     * @return String $sAdditionalInfo: formatted custom comments
     */
    public function getAdditionalInfo() {
        if($this->_sAdditionalInfo != "") {
            $sAdditionalInfo = $this->_sAdditionalInfo ." .-. ";
            return strval($sAdditionalInfo);
        } else {
            return " .-. ";
        }
    }
    
    /**
     * Transforms error code in error type
     *
     * @param int $iErrorType: errorcode
     * @return String $sErrorType: logfile formatted error type
     */
    private function _getErrorType($iErrorType) {
        //Clean vars
        $iErrorType = intval($iErrorType);
        
        //Process
        $aErrorTypes = array(
            "FILE OPEN ERROR", 
            "FILE CLOSE ERROR", 
            "FILE READ ERROR", 
            "FILE WRITE ERROR", 
            "FILE DELETE ERROR", 
            "FILE DONT EXIST", 
            "FILE WRONG TYPE",
        );
        
        if(isset($aErrorTypes[$iErrorType])) {
            $sErrorType = $aErrorTypes[$iErrorType]. " .-. ";
        } else {
            $sErrorType = "UNKNOWN ERROR CHAR .-. ";
        }
        
        return strval($sErrorType);
    }
    
    /**
     * Transform error code in error message
     *
     * @param int $iErrorType: errorcode
     * @return String $sMessage: predefined message
     */
    private function _getErrorMessage($iErrorType) {
        //Clean vars
        $iErrorType = intval($iErrorType);
        
        //Process
        $aErrorTypes = array(
            "Couldn't open a file/directory", 
            "Couldn't close a file/directory", 
            "Couldn't read a file/directory", 
            "Couldn't write/create a file/directory", 
            "Couldn't delete a file/directory",
            "Filename is not a valable File/Directory", 
            "0ther resource expected",
        );
        
        if(isset($aErrorTypes[$iErrorType])) {
            $sMessage = $aErrorTypes[$iErrorType]. " .-. ";
        } else {
            $sMessage = "No known message for error code: ". $iErrorType ."";
        }
        
        return strval($sMessage);
    }
}