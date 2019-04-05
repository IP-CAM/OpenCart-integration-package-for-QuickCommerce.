<?php

class ModelCart2CartFtpUpload extends Model {
  var $conn;
  var $messages;
  var $messageType = 'success';
  var $dir;
  var $token;
  var $localpath;

  public function init($host,$user,$pass,$dir,$token)
  {
    $this->dir = $dir;
    $this->token = $token;
    $this->localpath = realpath(dirname(__FILE__)) . '/bridge2cart';
    $ftpRes = ftp_connect($host);
    if (($ftpRes !== false) && @ftp_login($ftpRes, $user, $pass)) {
      ftp_pasv($ftpRes, true);
      $this->conn = $ftpRes;
      if (!ftp_chdir ($this->conn,$dir)) {
        $this->messages = 'Can\'t open target directory.';
        $this->messageType = 'error';
        return false;
      }

      return true;
    } else {
      $this->messages = 'Can\'t login to FTP account';
      $this->messageType = 'error';
      return false;
    }
  }

  private function checkBridge()
  {
    ftp_chdir($this->conn,'.');
    $contents = ftp_nlist($this->conn, '.');
    if (!is_array($contents)) {
      return false;
    }
    return in_array('bridge2cart', $contents);
  }

  public function uploadBridge()
  {
    $this->load->model('setting/setting');
    $settings = $this->model_setting_setting->getSetting('cart2cart');

    $this->load->model('cart2cart/worker');
    $worker =  $this->model_cart2cart_worker;
    if (isset($settings['Cart2CartStoreToken'])) {
      $root_dir = realpath(dirname(__FILE__).'/../../../');
      $tmp_dir = $root_dir . '/bridge_tmp';
      mkdir($tmp_dir);
      chmod($tmp_dir, 0777);
      $this->localpath = $tmp_dir.'/bridge2cart';
      $worker->copyBridge($tmp_dir, $settings['Cart2CartStoreToken']);
    } else {
      return false;
    }

    do {
      if ($this->conn == false) {
        $this->messages = 'Can\'t connect to FTP host';
        $this->messageType = 'error';
        break;
      }

      if ($this->checkBridge()) {
        $this->messages = 'Bridge already installed';
        $this->messageType = 'success';
        break;
      }

      $configFileName = $this->localpath . '/config.php';
      if(file_exists($configFileName)) {
        unlink($configFileName);
      }
      file_put_contents($configFileName, "<?php define('M1_TOKEN', '".$this->token."');");

      if (!ftp_mkdir($this->conn,'bridge2cart')) {
        $this->messages = 'Can\'t create bridge directory.';
        $this->messageType = 'error';
        break;
      }

      if (!ftp_chmod($this->conn,0755,'bridge2cart')) {
        $this->messages = 'Can\'t  change permissions to bridge directory.';
        $this->messageType = 'error';
        break;
      }

      if (!ftp_chdir ($this->conn,'bridge2cart')) {
        $this->messages = 'Can\'t open bridge directory.';
        $this->messageType = 'error';
        break;
      }

      if (!ftp_put($this->conn,'bridge.php',$this->localpath . '/bridge.php',FTP_BINARY)) {
        $this->messages = 'Can\'t copy bridge files.';
        $this->messageType = 'error';
        break;
      }

      if (!ftp_put($this->conn,'config.php',$this->localpath . '/config.php',FTP_BINARY)) {
        $this->messages = 'Can\'t copy bridge files.';
        $this->messageType = 'error';
        break;
      }
      if (!ftp_chmod($this->conn,0644,'bridge.php')) {
        $this->messages = 'Can\'t  change permissions to bridge files.';
        $this->messageType = 'error';
        break;
      }
      if (!ftp_chmod($this->conn,0644,'config.php')) {
        $this->messages = 'Can\'t  change permissions to bridge files.';
        $this->messageType = 'error';
        break;
      }
      $this->messages = "Connection bridge uploaded.";
      $this->messageType = 'success';
    } while (false);

    $worker->deleteDir($tmp_dir);

    if ($this->messageType == 'success') {
      return true;
    }

    $this->removeBridge();
    return false;
  }

  private function removeBridge()
  {
    ftp_chdir($this->conn,'../');
    if (!$this->checkBridge()) {
      return true;
    }

    ftp_chdir ($this->conn,'bridge2cart');
    $contents = ftp_nlist($this->conn, '.');
    $failToDelete = false;
    foreach ($contents as $file) {
      if ($file !== '.' || $file !== '..') {
        if (!ftp_delete($this->conn, $file)) {
          $failToDelete = true;
        }
      }
    }

    ftp_chdir($this->conn,'../');
    if (ftp_rmdir($this->conn,'bridge2cart')) {
      $failToDelete = true;
    }

    if ($failToDelete) {
//      $this->messages = 'Couldn\'t delete bridge directory.';
//      $this->messageType = 'error';
      return false;
    } else {
//      $this->messages = 'Bridge removed.';
//      $this->messageType = 'success';
      return true;
    }
  }

  public function __destruct()
  {
    if ($this->conn !== NULL){
      return ftp_close($this->conn);
    }
  }
}