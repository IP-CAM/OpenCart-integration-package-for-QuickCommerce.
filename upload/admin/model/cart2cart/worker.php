<?php

class ModelCart2CartWorker extends Model {

  const API_PATH_FOR_BRIDGE = 'https://app.shopping-cart-migration.com/api.bridge.download/';

  var $root ='';
  var $c2cBridgePath ='';
  var $errorMessage = '';

  public function __construct() {
    $this->root = realpath(dirname(__FILE__).'/../../../');
    $this->c2cBridgePath = $this->root . '/bridge2cart';
  }

  public function isBridgeExist()
  {
    if (is_dir($this->c2cBridgePath) && file_exists($this->c2cBridgePath.'/bridge.php') && file_exists($this->c2cBridgePath.'/config.php')) {
      return true;
    }
    return false;
  }

  public function getMessage($hello)
  {
    return $hello . ' world YYEESS '.$this->root .'  '.$this->c2cBridgePath;
  }

  public function installBridge($token)
  {
    if ($this->isBridgeExist()) {
      return true;
    }
    return $this->copyBridge($this->root, $token);
  }

  public function copyBridge($path, $token)
  {
    $zippedBridge = file_get_contents(self::API_PATH_FOR_BRIDGE . 'token/' . $token);
    file_put_contents($path . '/bridge.zip', $zippedBridge);
    $zip = new ZipArchive();
    if ($zip->open($path . '/bridge.zip')) {
      $zip->extractTo($path . '/');
      $zip->close();
      unlink($path . '/bridge.zip');
      return true;
    } else {
      return false;
    }
  }

  public function unInstallBridge()
  {
    if (!$this->isBridgeExist()) {
      return true;
    }
    return $this->deleteDir($this->c2cBridgePath);
  }

  public function deleteDir($dirPath)
  {
    if (is_dir($dirPath)) {
      $objects = scandir($dirPath);
      foreach ($objects as $object) {
        if ($object != "." && $object !="..") {
          if (filetype($dirPath . DIRECTORY_SEPARATOR . $object) == "dir") {
            $this->deleteDir($dirPath . DIRECTORY_SEPARATOR . $object);
          } else {
            if(!unlink($dirPath . DIRECTORY_SEPARATOR . $object)){
              return false;
            }
          }
        }
      }
      reset($objects);
      if (!rmdir($dirPath)) {
        return false;
      }
    } else {
      return false;
    }
    return true;
  }
}