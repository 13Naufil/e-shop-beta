<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer extends CI_Controller {


	function login()
	{
		if ($this->input->server('REQUEST_METHOD') == 'POST') {
			$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
			$this->form_validation->set_rules('password', 'Password', 'required');

			if ($this->form_validation->run() !== FALSE) {
				$email = getVar('email');
				$password = getVar('password');

				$login_sql = "SELECT  * FROM `customers` WHERE `email` = '" . $email . "' AND `password` = '" . md5($password) . "' AND status='Active' AND customer_type != 'guest'";
				$user_detail = $this->db->query($login_sql);

				if ($user_detail->num_rows() > 0) {
					$user_detail = $user_detail->row();
					$user_id = $user_detail->id;
					$this->session->set_userdata('customer_login', true);
					$this->session->set_userdata('customer_user_id', $user_id);

				} else {
					$data['login_error'] = 'Invalid email or password!';
				}
			}
		}
		$this->template->load('customer/login', $data);
	}

	function registration()
	{

		if ($this->input->server('REQUEST_METHOD') == 'POST') {

			$this->form_validation->set_rules('first_name', 'First Name', 'required');
			$this->form_validation->set_rules('last_name', 'Last Name', 'required');
			$this->form_validation->set_rules('email', 'Email', 'required|valid_email|callback_email_check');
			$this->form_validation->set_rules('password', 'Password', 'required|min_length[6]|max_length[12]');
			$this->form_validation->set_rules('confirm_password', 'Password Confirmation', 'required');


				if ($this->form_validation->run() !== FALSE) {

					# +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					# Customer Data
					$DBdata = getVar('customer');;
					unset($DBdata['confirm_password']);

					$DBdata['password'] = md5($DBdata['password']);
					$user_id = save('customers', $DBdata);
					$this->session->set_userdata('customer_login', true);
					$this->session->set_userdata('customer_user_id', $user_id);
				}

		}
		$data['row'] = array2object($this->input->post());
		$this->template->load('customer/registration', $data);

	}
}
