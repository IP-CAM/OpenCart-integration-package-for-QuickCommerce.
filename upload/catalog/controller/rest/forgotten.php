<?php  

/**
 * forgotten.php
 *
 * Forgotten password
 *
 * @author     Makai Lajos
 * @copyright  2015
 * @license    License.txt
 * @version    2.0
 * @link       http://opencart-api.com/product/opencart-restful-api-pro-v2-0/
 * @see        http://newapi2.opencart-api.com/demo/
 */
require_once(DIR_SYSTEM . 'engine/restcontroller.php');

class ControllerRestForgotten extends RestController {
	
	/*
	* forgotten password
	*/
	public function forgotten() {

		$this->checkPlugin();
		
		$json = array('success' => true);
		
		if ( $_SERVER['REQUEST_METHOD'] === 'POST'){
			
			$requestjson = file_get_contents('php://input');
            $post = json_decode($requestjson, true);

			if ($this->customer->isLogged()) {
				$json['error']		= "User is already logged";
				$json['success']	= false;			
			} else {
                $this->load->model('account/customer');
                $this->load->language('account/forgotten');
                $this->load->language('mail/forgotten');

                $error = $this->validate($post);
                if(empty($error)){
                    $password = substr(sha1(uniqid(mt_rand(), true)), 0, 10);

                    $this->model_account_customer->editPassword($post['email'], $password);

                    $subject = sprintf($this->language->get('text_subject'), $this->config->get('config_name'));

                    $message  = sprintf($this->language->get('text_greeting'), $this->config->get('config_name')) . "\n\n";
                    $message .= $this->language->get('text_password') . "\n\n";
                    $message .= $password;

                    $mail = new Mail($this->config->get('config_mail'));
                    $mail->setTo($post['email']);
                    $mail->setFrom($this->config->get('config_email'));
                    $mail->setSender($this->config->get('config_name'));
                    $mail->setSubject($subject);
                    $mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
                    $mail->send();

                    // Add to activity log
                    $customer_info = $this->model_account_customer->getCustomerByEmail($post['email']);

                    if ($customer_info) {
                        $this->load->model('account/activity');

                        $activity_data = array(
                            'customer_id' => $customer_info['customer_id'],
                            'name'        => $customer_info['firstname'] . ' ' . $customer_info['lastname']
                        );

                        $this->model_account_activity->addActivity('forgotten', $activity_data);
                    }
                } else {
                    $json["error"]		= $error;
                    $json["success"]	= false;
                }
            }
		} else {
				$json["error"]		= "Only POST request method allowed";
				$json["success"]	= false;
		}

		$this->sendResponse($json);
	
	}

    protected function validate($post) {
        $error = array();
        if (!isset($post['email'])) {
            $error[] = $this->language->get('error_email');
        } elseif (!$this->model_account_customer->getTotalCustomersByEmail($post['email'])) {
            $error[] = $this->language->get('error_email');
        }
        return $error;
    }
}