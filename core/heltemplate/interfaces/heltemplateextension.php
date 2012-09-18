<?php
/** 
 * It is highly recommanded to use this for all helTemplate extension classes
 *
 * @author Hendrik Schaeidt & Steven Zumpe
 * @version 1 - 26.08.2011
 *
 * This is open source. Just keep copyright assignement and enjoy.
 *
 * © Hendrik Schaeidt & Steven Zumpe 2011
 */
 
namespace core\heltemplate\interfaces;
 
interface helTemplateExtension {

    /**
     * Returns an array with all Pattern
     *
     * @return Object $oInstance
     */
    public function getSearchPattern();
    
    /**
     * Returns an active instance from itself
     * @param Int $iKey
     * @return Object $oInstance
     */
    public function getReplace($iKey, $bIsFromController);
}