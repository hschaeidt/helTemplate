<?php
/**
 * helTemplate compiles and handles templates.
 *
 * @author Hendrik Schaeidt
 * @version 1 - 26.08.2011
 *
 * This is open source. Just keep copyright assignement and enjoy.
 *
 * ? Hendrik Schaeidt & Steven Zumpe 2011
 */
namespace core\heltemplate;
use core\helFile;
use core\heltemplate\interfaces;
use core\exception\helCompilerException;
use core\exception\helTemplateException;

class helTemplate extends helFile {
    /**
     * Render mode, if set true, templates will be 
     * compiled everytime they will be called
     * Only use this in developement phase
     * If set false, templates will be generated every 
     * 24 hours only
     * @see checkRender()
     */
    private $_bCompile = true;
    
    /**
     * Time to recompile templates
     * Stdandard the templates are recompiled once a day
     */
    private $_iRefreshTime = 86400;
    
    /**
     * Full path to template folder
     */
    private $_sViewPath = null;
    
    /**
     * Full path to temp folder, where templates will be compiled
     */
    private $_sTmpPath = null;
    
    /**
     * Full path to heltemplate folder
     */
    private $_sHeltemplateDir = null;
    
    /**
     * Name of the view/template file
     * @see __construct()
     */
    private $_sView = null;
    
    /**
     * Full path to view/template file
     * @see __construct()
     */
    private $_sViewFile = null;
    
    /**
     * Full path to the temp/compiled file
     * @see checkRender()
     */
    private $_sTmpFile = null;
    
    /**
     * Defines template tag begin delimiter
     */
    private $_sDelimiterBegin = "<hel";
    
    /**
     * Defines template tag end delimiter
     */
    private $_sDelimiterEnd = "hel>";
    
    /**
     * Search pattern for replace your template tags 
     * with php tags or other
     * Is set dynamically in _compileSnippet()
     */
    private $_aTagPattern = array(); 
    
    /**
     * Here you can define your own replacement tags
     * for your template tags
     *
     */
    private $_aTagReplace = array("<?php", "?>");
    
    /**
     * Verify value to check template generating process
     */
    private $_iVerifyParsing = null;
    
    /**
     * Contains all Compiled Snippets
     */
    private $_aCompiledSnippets = array();
    
    /**
     * Contains Content of Compiled File
     */
    private $_sCompiled;
    
    /**
     * If file to handle is an included file
     */
    private $_bIsInclude = false;
    
    /**
     * Contains name of active section (backend/frontend)
     */
    private $_sSelected = "";
    
    /**
     * Contains controller instance
     */
    private $_oController = null;
    
    /**
     * Config object
     */
    private $_oConfig = null;
    
    /**
     * Debug object
     */
    protected $_oDebug = null;
    
    /**
     * Public constructor, set all the paths the engine needs to work
     *
     * @param helTemplateConfig $oConfig: config class
     * @param String $sSelected: called section(basic or admin)
     * @param String $sView: called controller-file name
     * @param helDebug $oDebug: Debug engine
     */
    public function __construct($oConfig = null, $sSelected = null, $sView = null, $oDebug = null) {
        //Reject without config
        if(!is_object($oConfig)) {
            throw new helTemplateException(0);
            return;
        }
        
        //Clean vars
        $sSelected = strval($sSelected);
        $sView = strval($sView);
        $this->_sSelected = $sSelected;
        
        //Process
        parent::__construct($oDebug);
        $aInterfaces = array();
        //Set Debug Engine if available
        if(is_object($oDebug)) {
            $aInterfaces = class_implements($oDebug);
            if(in_array("core\interfaces\helDebug", $aInterfaces)) {
                $this->_oDebug = $oDebug;
            }
        }
        
        $aInterfaces = class_implements($oConfig);
        
        //Reject config without helTemplateConfig Interface
        if(!in_array("core\heltemplate\interfaces\helTemplateConfig", $aInterfaces)) {
            throw new helTemplateException(1);
            return;
        } else {
            $this->_oConfig = $oConfig;
        }
        
        //Methods from interface
        $this->_sViewPath = $this->_oConfig->getTemplatePath($sSelected);
        $this->_sTmpPath = $this->_oConfig->getTemplateTmpPath($sSelected);
        $this->_sHeltemplateDir = $this->_oConfig->getTemplatePluginPath();
        
        //Set template files
        $this->_sView = $sView;
        $this->_sViewFile = $this->_sViewPath . $sView;
    }
    
    /**
     * Returns the path to active tmp dir
     *
     * @return String $_sTmpPath: full path to active temp dir
     */
    public function getTmpPath() {
        return $this->_sTmpPath;
    }
    
    /**
     * Returns the active selection
     *
     * @return String $_sSelected: active session name basic/admin
     */
    public function getSelected() {
        return $this->_sSelected;
    }
    
    /**
     * Files will be differenciate in controller-views and included-views.
     * Please notice that including an controller-view is actually impossible.
     * After the check the template will be rendered.
     *
     * @return String $sTmpFile: full path to temp file
     */
    public function checkRender() {
        $aFileCheck = explode('/', $this->_sView);
        $aTempFiles = array();
        if(count($aFileCheck) > 1) {
            $this->_bIsInclude = true;
        } else {
            $dDir = parent::openDirectory($this->_sTmpPath);
            $aFileList = parent::readDirectory($dDir);
            if(is_array($aFileList)) {
                foreach($aFileList as $sFile) {
                    $sFileC = explode('|', $sFile);
                        if(is_array($sFileC) && count($sFileC) >= 3) {
                            if($sFileC[2] == $this->_sView) {
                                $iLastUpdate = $sFileC[1];
                                $aTempFiles[] = $sFile;
                            }
                        }
                }
            }
        }
        
        if($this->_bCompile === true || time() > $iLastUpdate + $this->_iRefreshTime || $this->_bIsInclude === true) {
            $this->_sTmpFile = $this->_sTmpPath . $this->_render();
            if(count($aTempFiles) > 0) {
                foreach($aTempFiles as $sTmpFile) {
                    parent::removeFile($this->_sTmpPath . $sTmpFile);
                }
            }
            $this->_write($this->_sCompiled);
        } else {
            $this->_sTmpFile = $this->_sTmpPath . $sTmpFile;
        }
        
        return $this->_sTmpFile;
    }
    
    /**
     * Launchs the compiling process and returns the temp-filename
     *
     * @return String $sView: temp-filename || temp-inc-filename(without timestamp)
     */
    protected function _render() {
        $sContent = parent::readFile($this->_sViewFile);
        $this->_oController = new helCompile($this->_sHeltemplateDir ."extensions/", $this);
        $this->_parseTemplate($sContent);
        if($this->_bIsInclude === false) {
            return uniqid() . '|' . time() . "|" . $this->_sView;
        } else {
            return $this->_sView;
        }
    }
    
    /**
     * Writes the templates temp-file
     *
     * @return void
     */
    private function _write($sContent) {
        if(function_exists("verifyPath")) {
            verifyPath($this->_sTmpPath . $this->_sView);
        }
        
        parent::write($this->_sTmpFile, $sContent);
    }
    
    /**
     * Searches all helTemplate code snippets in the template file
     * and sends them to the compiler.
     *
     * @return void
     */
    protected function _parseTemplate($sContent = null) {
        try {
            $sContent = preg_replace("/>/", ">\r\n", $sContent);
            $sPattern = "/" . $this->_sDelimiterBegin . "(.*)". $this->_sDelimiterEnd ."/";
            if(preg_match_all($sPattern, $sContent, $aResult)) {
                //Roundabout the 2 dimensional array that preg_match_all returns
                $aResult = $aResult[0];
                $this->_iVerifyParsing = count($aResult);
                $aCompiled = array();
                foreach($aResult as $sToCompile) {
                    $this->_compileSnippet($sToCompile);
                    $aSearchSnippet[] = $sPattern;
                }
                $sCompiled = preg_replace($aSearchSnippet, $this->_aCompiledSnippets, $sContent, 1);
                $sCompiled = preg_replace("/\r\n/", "", $sCompiled);
                $this->_sCompiled = $sCompiled;
            } else {
                throw new helCompilerException(0, $this->_sView);
            }
        } catch(helCompilerException $e) {
            //If debug engine injected
            if($this->_oDebug !== null) {
                $this->_oDebug->bindExceptionToTrace($e, uniqid());
            } else {
                echo $e->getErrorType() . $e->getAdditionalInfo() . $e->getCustomMessage();
            }
        }
    }
    
    /**
     * Removes the hel tags.
     * Sends the snippet to compilation.
     * Adds the PHP Tags to the compiled snippet.
     *
     * @param String $sToCompile: full snippet with tags to be compiled
     * @return void
     */
    protected function _compileSnippet($sToCompile = null) {
        $this->_aTagPattern = array('/'. $this->_sDelimiterBegin .'/', '/'. $this->_sDelimiterEnd .'/');
        $sCompiled = $this->_getCompiledSnippet($sToCompile);
        //if empty it's a comment
        if($sCompiled != null) {
            $sCompiled = $this->_aTagReplace[0] .' '. $sCompiled .' '.$this->_aTagReplace[1];
        } else {
            $sCompiled = "";
        }

        $this->_aCompiledSnippets[] = $sCompiled;
    }
    
    /**
     * Gets the snippet pattern or function and execute the compilation.
     *
     * @param String $sToCompile: clean snipet pattern (without delimiters)
     */
    protected function _getCompiledSnippet($sToCompile = null) {
        $sCompiled = null;
        if($sToCompile !== null) {
            $sTagReplace = array("", "");
            if(!($sToCompile = preg_replace($this->_aTagPattern, $sTagReplace, $sToCompile))) {
                
            }
            
            $sCompiled = $this->_oController->translate($sToCompile);
        }
        
        return $sCompiled;
    }
}