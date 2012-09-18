<?php
/**
 * Searchs for patterns and replaces it with the extensions matched replace array
 *
 * @author Steven Zumpe & Hendrik Schaeidt
 * @version 1 - 12.09.2011
 *
 * This is open source. Just keep copyright assignement and enjoy.
 *
 * © Hendrik Schaeidt & Steven Zumpe 2011
 */

namespace core\heltemplate;
use core\helFile;

class helCompile extends helFile {
    
    private $_sExtKeyFile = "extension.key.php";
    
    private $_sExtPath = null;
    
    private $_aExtensionList = array();
    
    private $_oHelTemplate = null;

    /**
     * Constructor
     *
     * @param $sExtPath: full path to extension directory
     * @return void
     */
    public function __construct($sExtPath, $oHelTemplate) {
        $this->_oHelTemplate = $oHelTemplate;
        $this->_sExtPath = $sExtPath;
        $this->getExtension($sExtPath);
    }
    
    /**
     * Translates a snippet
     *
     * @param String $sDir: full path to extension directory
     * @param String $sToCompile: snippet to be translated
     * @return $sCompiled: translated snippet
     */
    public function translate($sToCompile) {
        $aResults = array();
        $sToCompile = trim($sToCompile);
        
        foreach($this->_aExtensionList as $sKey => $aExtension) {
            for($i = 0; $i < count($aExtension); $i++) {
                if(preg_match($aExtension[$i], $sToCompile)) {
                    $sExtFile = $sKey;
                    $oExtension = new $sExtFile($this->_oHelTemplate);
                    $oExtension->sToCompile = $sToCompile;
                    $sReplace = $oExtension->getReplace($i);
                    $sCompiled = preg_replace($aExtension[$i], $sReplace, $sToCompile);
                    return $sCompiled;
                }
            }
        }
        return '';
    }
    
    /**
     * loading the extension
     *
     * @return void
     */
    protected function getExtension($sDir) {
        if(!file_exists($sDir . "/" . $this->_sExtKeyFile)) {
            $this->createExtKeyList($sDir);
        }
        
        require($sDir . "/" . $this->_sExtKeyFile);
        if(is_array($array)) {
            $this->_aExtensionList = $array;
        }
    }
    
    /**
     * Creates a PHP File, if not exist, constaining all
     * search params of all extensions and the extension name
     *
     * @return void
     */
    protected function createExtKeyList($sExtDir) {
        $sTag = '<?php $array = array(';
        $aPattern = array();
        $rDir = parent::openDirectory($sExtDir);
        if(($aFiles = parent::readDirectory($rDir)) !== null) {
            foreach($aFiles as $sFile) {
                $sVarName = explode(".", $sFile);
                $sClassName = "core\\heltemplate\\extensions\\".$sVarName[0];
                $oClass = new $sClassName($this->_oHelTemplate);
                $aPattern[$sClassName] = $oClass->getSearchPattern();
            }
            
            foreach($aPattern as $sPatternKey => $aPatternVals){
                $sTag .= "'". $sPatternKey ."' => array(";
                foreach($aPatternVals as $sKey => $sPattern) {
                    $sTag .= "'". $sKey ."' => '". $sPattern ."',";
                }
                
                $sTag .= "),";
            }
        }
        $sTag .= ');';
        
        if(file_exists($sExtDir . "/" . $this->_sExtKeyFile)) {
            parent::removeFile($sExtDir . $this->_sExtKeyFile);
        }
        
        parent::write($sExtDir . "/" . $this->_sExtKeyFile, $sTag);
    }
}