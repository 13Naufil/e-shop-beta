<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cart extends CI_Controller {

	public function index()
	{
		echo '<pre>';
		print_r($this->cart->contents());
		die();
		//return $this->cart->contents();
	}


	public function add_item(){
		$req = $_REQUEST['cart'];
		if(!empty($req)){
			$data = array(
				'id'      => $req['id'],
				'qty'     => $req['qty'],
				'price'   => $req['price'],
				'name'    => $req['name']
			);

			return $this->cart->insert($data);
		}
	}

	public function update_item(){
		$row_id = $_REQUEST['row_id'];
		$req = $_REQUEST['cart'];
		if(!empty($req)){
			$data = array(
				'rowid'	  => $row_id,
				'qty'     => $req['qty'],
			);

			return $this->cart->insert($data);
		}
	}

	public function delete_item(){
		$row_id = $_REQUEST['row_id'];
		if(!empty($row_id)){
			$res = $this->cart->remove($row_id);
			print_r($res); die();
		}
	}

}
