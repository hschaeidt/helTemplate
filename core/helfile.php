<?php
/**
 * Handles file data and directories
 *
 * @author Steven Zumpe & Hendrik Schaeidt
 * @version 1 - 31.08.2011
 *
 * This is open source. Just keep copyright assignement and enjoy.
 *
 * © Hendrik Schaeidt & Steven Zumpe 2011
 */
 
namespace core;
use core\exception\helFileException;
use core\exception\helCustomException;
 
class helFile {

    /**
     * Contains self object
     */
    private static $_oInstance = null;
    
    /**
     * Debug engine
     */
    protected $_oDebug = null;
    
    /**
     * Constructor
     *
     * @return void
     */
    public function __construct($oDebug = null) {
        //Set Debug Engine if available
        if(is_object($oDebug)) {
            $aInterfaces = class_implements($oDebug);
            if(in_array("core\interfaces\helDebug", $aInterfaces)) {
                $this->_oDebug = $oDebug;
            }
        }
    }
    
    /**
     * Returns an self instance or creates it first
     *
     * @return helFile $_oInstance
     */
    public static function getInstance() {
        if(self::$_oInstance == null) {
            self::$_oInstance = new helFile();
        }
        return self::$_oInstance;
    }
    
    /**
     * Writes String in a file
     *
     * @param String $sFilePath: absolute file path
     * @param String $sContent: content to write into file
     * @param String $sParam: fopen flag
     * @return void
     */
    protected function write($sFilePath, $sContent, $sParam = "w") {
        //Clean vars
        $sFilePath = strval($sFilePath);
        $sContent = strval($sContent);
        $sParam = strval($sParam);
        
        //Process
        if(function_exists("verifyPath")) {
            verifyPath($sFilePath);
        }
        $bSuccess = false;
        try {
            $fFile = $this->openFile($sFilePath, $sParam);
            if(!fwrite($fFile, $sContent)) {
                throw new helFileException(3, 'Maybe the file has 0 bytes '. $sFilePath);
            } else {
                $bSuccess = true;
            }
            
            $this->closeFile($fFile);
        } catch(helFileException $e) {
            //If debug engine injected
            if($this->_oDebug !== null) {
                $this->_oDebug->bindExceptionToTrace($e, uniqid());
            } else {
                echo $e->getMessage();
            }
        }
        
        return $bSuccess;
    }
    
    /**
     * Opens the file and returns the instance
     *
     * @param String $sFilePath: Absolute path to file to open
     * @param String $sParam: fopen flag
     * @return File $fFile: Opened file
     */
    protected function openFile($sFilePath, $sParam = 'w') {
        //Clean vars
        $sFilePath = strval($sFilePath);
        $sParam = strval($sParam);    
        
        //Verifys if file and (creates) folders exists
        $this->verifyFile($sFilePath);
        try {
            if(!($fFile = fopen($sFilePath, $sParam))) {
                throw new helFileException(0);
            } else {
                return $fFile;
            }
        } catch(helFileException $e) {
            //If debug engine injected
            if($this->_oDebug !== null) {
                $this->_oDebug->bindExceptionToTrace($e, uniqid());
            } else {
                echo $e->getMessage();
            }
        }
    }
    
    /**
     * Opens a directory and checks if it is readable
     *
     * @param String $sDir: Full path to directory
     * @return Resource $rDir: Opened directory
     * @return null: when read error
     */
    protected function openDirectory($sDir) {
        //Clean vars
        $sDir = strval($sDir);
        
        //Process
        try {
            if(is_dir($sDir)) {
                if ($rDir = opendir($sDir)) {
                    return $rDir;
                } else {
                    throw new helFileException(0, $sDir);
                }
            } else {
                throw new helFileException(6, $sDir);
            }
        } catch(helFileException $e) {
            //If debug engine injected
            if($this->_oDebug !== null) {
                $this->_oDebug->bindExceptionToTrace($e, uniqid());
            } else {
                echo $e->getMessage();
            }
        }

        return null;
    }
    
    /**
     * Closes the opened file
     *
     * @param String $fFile: Has to been a file pointer
     * @return void;
     */
    protected function closeFile($fFile) {
        //Clean vars
        //No known function for resource typecast
        
        //Process
        if(ftell($fFile)) {
            try {
                if(!fclose($fFile)) {
                    throw new helFileException(1);
                }
            } catch(helFileException $e) {
            //If debug engine injected
            if($this->_oDebug !== null) {
                $this->_oDebug->bindExceptionToTrace($e, uniqid());
            } else {
                echo $e->getMessage();
            }
        }
        }
    }
    
    /**
     * Closes a directory
     *
     * @param Resource $rDir: Openend directory to close
     * @return void
     */
    protected function closeDirectory($rDir) {
        try {
            if(!is_resource($rDir)) {
                throw new helFileException('t');
            } else {
               closedir($rDir);
            }
            
        } catch(helFileException $e) {
            //If debug engine injected
            if($this->_oDebug !== null) {
                $this->_oDebug->bindExceptionToTrace($e, uniqid());
            } else {
                echo $e->getMessage();
            }
        }
    }
    
    /**
     * Returna an array of all files in the directory
     *
     * @param Resource $rDir: Opened directory
     * @return Array $aFileNames: List of all files inm directory
     */
    protected function readDirectory($rDir) {
        $aFileList = array();
        try {
            if(!is_resource($rDir)) {
                throw new helFileException('t');
            } else {
                while(true == ($sFile = readdir($rDir))) {
                    if($sFile != "." && $sFile != "..") {
                        $aFileList[] = $sFile;
                    }
                }
                
                $this->closeDirectory($rDir);
                return $aFileList;
            }
        } catch(helFileException $e) {
            //If debug engine injected
            if($this->_oDebug !== null) {
                $this->_oDebug->bindExceptionToTrace($e, uniqid());
            } else {
                echo $e->getMessage();
            }
        }
        
        return null;
    }
    
    /**
     * Reads a file content and returns it
     *
     * @param String $sFilePath
     * @return String $sContent
     */
    protected function readFile($sFilePath = "") {
        try {
            if(file_exists($sFilePath) && is_readable($sFilePath)) {
                $sContent = file_get_contents($sFilePath);
                return $sContent;
            } else {
                throw new helFileException(2, $sFilePath);
            }
        } catch(helFileException $e) {
            //If debug engine injected
            if($this->_oDebug !== null) {
                $this->_oDebug->bindExceptionToTrace($e, uniqid());
            } else {
                echo $e->getMessage();
            }
        }
    }
    
    /**
     * Checks if the file already exists, if not, it will try to create it.
     *
     * @param String $sFilePath: Absolute path to file to check
     * @return bool $bool: true if verified/created
     */
    protected function verifyFile($sFilePath) {
        //Clean vars
        $sFilePath = strval($sFilePath);
        
        //Process
        if(!file_exists($sFilePath)) {
            if(function_exists("verifyPath")) {
                if(!verifyPath($sFilePath)) {
                    return false;
                }
            }
        }
        
        return true;
    }
    
    /**
     * Includes several PHP files containing one array and merges
     * the array to one, or includes one single file and returns 
     * the containing array.
     *
     * @param String $sDirPath: full path to directory, leave empty if you want load a file
     * @param String $sFilePath: full path to file, leave empty if you want load a directory
     * @return Array $aArray
     */
    protected function loadFile($sDirPath = null, $sFilePath = null) {
        try {
            $aArray = array();
            if($sDirPath !== null) {
                if(($openDir = opendir($sDirPath)) !== false) {
                    while(($sFile = readdir($openDir)) !== false) {
                        $sVarName = array();
                        $sVarName = explode(".", $sFile);
                        // Verify if File is a php File
                        if($sFile != "." && $sFile != ".." && end($sVarName) == "php") {
                            require_once $sDirPath . $sFile;
                            if(isset($array) && is_array($array)) {
                                // Adds the included array to our array list
                                $aArray = array_merge($aArray, $array);
                                unset($array);
                            }
                        }
                    }
                } else {
                    throw new helFileException(0, $sDirPath);
                }
            } elseif($sFilePath !== null) {
                if(file_exists($sFilePath)) {
                    require $sFilePath;
                    if(isset($array) && is_array($array)) {
                        $aArray = $array;
                    }
                } else {
                     throw new helFileException(0, $sFilePath);
                }
            }
            
            if(empty($aArray)) {
                return null;
            }
            
            return $aArray;
        } catch(helFileException $e) {
            //If debug engine injected
            if($this->_oDebug !== null) {
                $this->_oDebug->bindExceptionToTrace($e, uniqid());
            } else {
                echo $e->getMessage();
            }
        }
    }
    
    /**
     *    Returns true if the file type is ok
     *
     *    @param Array $aFileData
     *    @return bool;
     */
    protected function checkFileType($aFileData) {
        //Clean vars
        $aFileData = (array)$aFileData;
        $sType = "image/gif";
        if($aFileData['type'] == "image/png") {
            return true;
        }else {
            return false;
        }
    }
    
    /**
     *    Returns true if the file size is ok
     *
     *    @param Array $aFileData
     *    @return bool
     */
    protected function checkFileSize($aFileData) {
        //Clean vars
        $aFileData = array($aFileData);
        
        //Process
        $iSize = 1048576;
        if($aFileData['size'] <= $iSize) {
            return true;
        }else {
            return false;
        }
    }
}