<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	function registration()
	{
		$checkout = getVar('checkout');
		$edit = getVar('edit');
		$change_pass = getVar('change_pass');

		if ($this->input->server('REQUEST_METHOD') == 'POST') {

			$chk_where = '';
			if ($edit) {
				$customer_id = getSession('customer_user_id');
				if (!$customer_id) {
					redirect('customer/login');
				}
				//$customer = $this->m_customers->customer($customer_id);
				$chk_where .= " AND id !='{$customer_id}'";
			}
			# +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			# Validation
			//$email_exist = $this->db->query("SELECT * FROM `customers` WHERE `email`='" . getVar('email') . "'" . $chk_where);
			if ($change_pass == 1) {
				//$chk_pass = $this->db->query("SELECT id FROM `customers` WHERE 1 AND password='" . md5(getVar('current_password')) . "'" . $chk_where);
			}
			/*if (empty($_SESSION['captcha']) || trim(strtolower($_REQUEST['captcha'])) != $_SESSION['captcha']) {
				$captcha_error = "Invalid captcha";
				$data['captcha_error'] = $captcha_error;
				//$this->session->set_flashdata('error', $captcha_error);
			} else */
			/*if ($email_exist->num_rows() > 0) {
				$this->session->set_flashdata('error', 'Email address already exist.');
			} else */{

				$this->form_validation->set_rules('first_name', 'First Name', 'required');
				$this->form_validation->set_rules('last_name', 'Last Name', 'required');
				if (!$customer_id) {
					$this->form_validation->set_rules('email', 'Email', 'required|valid_email|callback_email_check');
					$this->form_validation->set_rules('password', 'Password', 'required|min_length[6]|max_length[12]');
				}

				if ($change_pass == 1 && $customer_id) {
					$this->form_validation->set_rules('password', 'Password', 'required|min_length[6]|max_length[12]|matches[confirm_password]');
					$this->form_validation->set_rules('confirm_password', 'Password Confirmation', 'required');
				}

				$this->form_validation->set_rules('address', 'Address', 'required');
				$this->form_validation->set_rules('city', 'City', 'required');
				//$this->form_validation->set_rules('state', 'State/Province', 'required');
				//$this->form_validation->set_rules('country', 'Country', 'required');
				$this->form_validation->set_rules('phone', 'Phone', 'required');

				if ($this->form_validation->run() !== FALSE) {

					# +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					# Customer Data
					$DbArray = getDbArray('customers');
					$DBdata = $DbArray['dbdata'];
					$password = $DBdata['password'];
					$DBdata['password'] = md5($password);
					$DBdata['customer_type'] = 'customer';
					$DBdata['modified'] = date('Y-m-d H:i:s');
					$DBdata['created'] = date('Y-m-d H:i:s');
					if ($customer_id) {
						unset($DBdata['email'],$DBdata['customer_type'],$DBdata['created']);
					}
					if ($customer_id && $change_pass != 1) {
						unset($DBdata['password']);
					}

					if ($customer_id) {
						save('customers', $DBdata, "id='{$customer_id}'");
					} else {

						$user_id = save('customers', $DBdata);
						$this->update_customer_orders($user_id);

						# +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
						# registration_email
						$customer = $this->m_customers->customer($user_id);
						$customer->password = $password;
						$msg = get_email_template($customer, 'New Account');
						if ($msg->status == 'Active') {
							$emaildata = array(
								'to' => $customer->email,
								'subject' => $msg->subject,
								'message' => $msg->message
							);
							if (!send_mail($emaildata)) {
								$this->session->set_flashdata('error', 'Email sending faild.');
							} else {
								$this->session->set_flashdata('success', 'Please check your email for username & password!');
							}
						}

						# +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
						# customer_addresses
						$DbArray = getDbArray('customer_addresses', array(), $DBdata);
						$DbArray['dbdata']['customer_id'] = $user_id;
						$DbArray['dbdata']['default_billing'] = 1;
						$DbArray['dbdata']['default_shipping'] = 1;
						save('customer_addresses', $DbArray['dbdata']);


						# +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
						# Set Session
						$this->session->set_userdata('customer_login', true);
						$this->session->set_userdata('customer_user_id', $user_id);
					}
					activity_log('Customer Registration', 'customers', $user_id, $user_id);
					# +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					# redirect
					if ($checkout) {
						redirect('cart/checkout');
					} else if (getVar('redirect') != '') {
						redirect(getVar('redirect'));
					} else {
						redirect('');
					}
				}
			}
		}
		$data['row'] = array2object($this->input->post());
		$data['checkout'] = $checkout;

		$this->template->load('customer/registration', $data);

	}
}
