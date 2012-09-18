<?php
/** 
 * You must have implemented this interface in your custom Exception class
 * in order to can log the entire Exception with helLog function logCustomException($e)
 *
 * @author Hendrik Schaeidt
 * @version 1 - 26.08.2011
 *
 * This is open source. Just keep copyright assignement and enjoy.
 *
 * © Hendrik Schaeidt & Steven Zumpe 2011
 */
 
namespace core\interfaces;
 
interface helException {
    
    /**
     * Returns a String containing the error type you want
     *
     * @return String $sErrorType
     */
    public function getErrorType();
    
    /**
     * Returns additional information if there are some
     *
     * @return String $sAdditionalInfo
     */
    public function getAdditionalInfo();
    
    /**
     * Returns predefined message
     *
     * @return String $sMessage
     */
    public function getCustomMessage();
}