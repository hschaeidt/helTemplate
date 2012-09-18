<?php
/** 
 * This exception handles easier some helTemplate issues with predefined
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
 
class helCompilerException extends \Exception implements helException {
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
     * Debug engine object
     */
    private $_oDebug = null;
    
    /**
     * Constructor
     *
     * @param int $iErrorType: int errorcode
     * @param String $sAdditionalInfo: custom information
     * @return void
     */
    public function __construct($iErrorType = 0, $sAdditionalInfo = "", $oDebug = null) {
        //Clean vars
        $iErrorType = intval($iErrorType);
        $sAdditionalInfo = strval($sAdditionalInfo);
        
        //Process
        $this->_sErrorType = $this->_getErrorType($iErrorType);
        $this->_sAdditionalInfo = $sAdditionalInfo;
        $this->_sMessage = $this->_getErrorMessage($iErrorType);
        
        //Set Debug Engine if available
        if(is_object($oDebug)) {
            $aInterfaces = class_implements($oDebug);
            if(in_array("core\interfaces\helTrace", $aInterfaces)) {
                $this->_oDebug = $oDebug;
            }
        }
    }
    
    /**
     * Returns error type
     *
     * @return String $sErrorType: Exception name followed by error type
     */
    public function getErrorType() {
        $sErrorType = "helCompilerException\/". $this->_sErrorType;
        return strval($sErrorType);
    }
    
    /**
     * Returns predefined message
     *
     * @return String $this->_sMessage: one predefined message
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
     * @param int $iErrorType: int errorcode
     * @return String $sErrorType: logfile formatted error type
     */
    private function _getErrorType($iErrorType) {
        //Clean vars
        $iErrorType = intval($iErrorType);
        
        //Process
        $aErrorTypes = array(
            "NO TAGS MATCHED", 
            "EMPTY PARAMETER", 
            "MISSING FILE",
            "MATCHING ERROR"
        );
        
        if(isset($aErrorTypes[$iErrorType])) {
            $sErrorType = $aErrorTypes[$iErrorType]. " .-. ";
        } else {
            //If debug engine injected
            if($this->_oDebug !== null) {
                $this->_oDebug->setTrace(uniqid());
            }
            
            $sErrorType = "INVALID ERROR CODE .-. ";
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
            "The file you tried to compile don't contain any template tag and couldn't be compiled.", 
            "Getted a empty code snippet", 
            "Some important core file is missing",
            "The snippet doesnt match",
        );
            
        if(isset($aErrorTypes[$iErrorType])) {
            $sMessage = $aErrorTypes[$iErrorType]. " .-. ";
        } else {
            //If debug engine injected
            if($this->_oDebug !== null) {
                $this->_oDebug->setTrace(uniqid());
            }
            
            $sMessage = "No known message for error code '". $iErrorType ."'";
        }
        
        return strval($sMessage);
    }
}