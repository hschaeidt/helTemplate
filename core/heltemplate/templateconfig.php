<?php
/**
 * The core class handles the main web functionality and creates
 * the absolute paths from your customized "config.inc.php" file
 * in the root directory.
 *
 * @author Hendrik Schaeidt
 * @version 1 - 26.08.2011
 *
 * This is open source version and does not contains fully features. 
 * Just keep copyright assignement and enjoy.
 *
 * © Hendrik Schaeidt & Steven Zumpe 2011
 */
namespace core\heltemplate;
use core\heltemplate\interfaces\helTemplateConfig;
 
class templateConfig implements helTemplateConfig{
    //All folders containing templates
    private $_aHelTemplatePaths = array(
        "views/",
        "views_admin/",
    );
    
    //Folder where to compile
    private $_sHelCompiledPath = "tmp/";
    
    //Absolute path to template engine
    private $_sAbsoluteHelTemplatePath = "core/heltemplate/";
    
    /**
     * Returns the path to the templates to compile
     */
    public function getTemplatePath($sSelected) {
        //Clean var
        $sSelected = strval($sSelected);
        
        //Verify if path in array
        if(in_array($sSelected, $this->_aHelTemplatePaths)) {
            //Fetch key
            $iKey = array_search($sSelected, $this->_aHelTemplatePaths);
            //return path
            return $this->_aHelTemplatePaths[$iKey];
        }
    }
    
    /**
     * Returns the Temp Path for compiled templates
     */
    public function getTemplateTmpPath($sSelected) {
        //Clean var
        $sSelected = strval($sSelected);
        
        //Verify if path in array
        if(in_array($sSelected, $this->_aHelTemplatePaths)) {
            return $this->_sHelCompiledPath . $sSelected;
        }
    }
    
    /**
     * Returns the absolute path to the helTemplate folder
     */
    public function getTemplatePluginPath() {
        return $this->_sAbsoluteHelTemplatePath;
    }
}