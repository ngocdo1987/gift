<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api extends CI_Controller {
	function __construct() {
		parent::__construct();
	}
	
	function register() {
		$fb_uid = $this->input->post('fb_uid');
		$fb_email = $this->input->post('fb_email');

		if($fb_uid != '' && $fb_email != '') {
			$check_user = $this->db->where('fb_uid', $fb_uid)
									->where('fb_email', $fb_email)
									->get('users')->row();
			if(empty($check_user)) {
				$dataAdd = array(
					'fb_uid' => $fb_uid,
					'fb_email' => $fb_email,
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s')	
				);
				$this->db->insert('users', $dataAdd);
				$user_id = $this->db->insert_id();

				echo json_encode(array(
					'status' => 1,	
					'message' => 'Register successfully!',
					'user_id' => $user_id
				));
			}else{
				$user_id = $check_user->id;

				echo json_encode(array(
					'status' => 1,	
					'message' => 'Login successfully!',
					'user_id' => $user_id
				));
			}	
		}else{
			echo json_encode(array(
				'status' => 0,	
				'message' => 'Please provide Facebook UID and Facebook email!'
			));
		}
								
	}

	function create_event() {
		$user_id = (int)$this->input->post('user_id');
		$event_name = $this->input->post('event_name');
		$product_json = json_decode($this->input->post('product_json'));

		if($user_id > 0 && $event_name != '') {
			$dataAdd = array(
				'event_name' => $event_name,
				'user_id' => $user_id,
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')	
			);
			$this->db->insert('events', $dataAdd);
			$event_id = $this->db->insert_id();

			if(!empty($product_json)) {

				foreach($product_json as $product) {
					$product_link = $product->product_link;
					$check_product = $this->db->where('product_link', $product_link)
											->get('products')->row();
					if(empty($check_product)) {
						$product_name = $product->product_name;
						$product_image = $product->product_image;
						$product_price = $product->product_price;

						$dataAdd = array(
							'product_name' => $product_name,
							'product_image' => $product_image,
							'product_price' => $product_price,
							'product_link' => $product_link,
							'created_at' => date('Y-m-d H:i:s'),
							'updated_at' => date('Y-m-d H:i:s')
						);	

					}else{
						$product_id = $check_product->id;	
					}						 	
				}
			}

			echo json_encode(array(
				'status' => 1,
				'message' => 'Create event successfully!',
				'event_id' => $event_id
			));
		}else{
			echo json_encode(array(
				'status' => 0,
				'message' => 'Please define user and event name is required!'
			));
		}
	}

	function get_events() {
		$user_id = (int)$this->input->post('user_id');
		$events = $this->db->where('user_id')
						->order_by('created_at', 'DESC')
						->get('events')->result();
		if(!empty($events)) {
			$event_arr = array();
			foreach($events as $event) {
				$event_arr[] = array(
					'event_id' => $event->id,
					'event_name' => $event->event_name
				);
			}

			echo json_encode(array(
				'status' => 1,
				'message' => 'Get events successfully!',
				'events' => $event_arr
			));
		}else{
			echo json_encode(array(
				'status' => 0,
				'message' => 'Events not exist!'
			));
		}				
	}

	function get_products() {
		$user_id = (int)$this->input->post('user_id');
		$event_id = (int)$this->input->post('event_id');
		/*
		$query = "
			SELECT `events_products`.*, `products`.`product_name`, `products`.`product_image`, `products`.`product_price`, `products`.`product_link`, `events`.`user_id`  
			FROM `events_products` 
			JOIN `products` ON `products`.`id` = `events_products`.`product_id` 
			WHERE `events`.`user_id` = ".$user_id." 
			AND `events_products`.`event_id` = ".$event_id." 
			ORDER BY `products`.`created_at` DESC 
		";
		*/
		$products = $this->db->select('events_products.*, products.product_name, products.product_image, products.product_price, products.product_link, events.user_id')
							->from('events_products')
							->join('products', 'products.id = events_products.product_id', 'inner')
							->where('events.user_id', $user_id)
							->where('events_products.event_id', $event_id)
							->order_by('products.created_at', 'DESC');
		$products = $this->db->query($query)->result();
		if(!empty($products)) {
			$product_arr = array();
			foreach($products as $product) {
				$product_arr[] = array(
					'product_name' => $product->product_name,
					'product_image' => $product->product_image,
					'product_price' => $product->product_price,
					'product_link' => $product->product_link	
				);
			}

			echo json_encode(array(
				'status' => 1,
				'message' => 'Get products successfully!',
				'products' => $product_arr
			));
		}else{
			echo json_encode(array(
				'status' => 0,
				'message' => 'Products not exist!'
			));
		}
	}

	function amz_categories() {
		$categories = $this->db->select('category_key, category_value')->get('categories')->result_array();
		if(!empty($categories)) {
			echo json_encode(array(
				'status' => 1,
				'message' => 'Get Amazon categories successfully!',
				'categories' => $categories
			));
		}else{
			echo json_encode(array(
				'status' => 0,
				'message' => 'Amazon categories not exist!'
			));
		}
	}

	function search_amz_products() {
		
		$search = $this->input->post('search');
		$category = $this->input->post('category');
		$page = (int)$this->input->post('page');
		$page = ($page == 0) ? 1 : $page;

		if($search != '' && $category != '') {
			require_once('application/libraries/amazon_product_api_class.php');
			$public = 'AKIAIAY2PFVI2RPY2M2A'; //amazon public key here
			$private = 'kE6lCqkxagPlAmCMfC411HPq2nw6nGlvkGZXb8ad'; //amazon private/secret key here
			$site = 'com'; //amazon region
			$affiliate_id = 'azto-20'; //amazon affiliate id
			$amazon = $amazon = new AmazonProductAPI($public, $private, $site, $affiliate_id);

			$params = array(
				"ItemPage" => $page,	
				"Operation"     => "ItemSearch",
				"SearchIndex"   => $category,
				"Keywords" => $search,
				"ResponseGroup" => "Medium,Reviews"    
			);
				
			$result =	$amazon->queryAmazon($params);
			
			$total_products = (int)$result->Items->TotalResults[0];
			$total_pages = (int)$result->Items->TotalPages[0];
			$similar_products = $result->Items->Item;

			$product_arr = array();

			foreach($similar_products as $si){
				$product_name = (string)$si->ItemAttributes->Title[0];
				$product_image = (string)$si->MediumImage->URL[0];

				$product_price = (string)$si->ItemAttributes->ListPrice->FormattedPrice[0];
				$product_price = (float)str_replace('$', '', $product_price);

				$product_link = (string)$si->DetailPageURL[0];

				$product_arr[] = array(
					'product_name' => $product_name,
					'product_image' => $product_image,
					'product_price' => $product_price,
					'product_link' => $product_link
				);
			}

			if(!empty($product_arr)) {
				echo json_encode(array(
					'status' => 1,
					'message' => 'Get products successfully!',
					'total_products' => $total_products,
					'total_pages' => $total_pages,
					'products' => $product_arr
				));
			}else{
				echo json_encode(array(
					'status' => 0,
					'message' => 'Products not exist!'
				));
			}	
		}else{
			echo json_encode(array(
				'status' => 0,
				'message' => 'Please enter keyword and category for searching products!'
			));
		}
		
	}

	function demo_search_amz_products() {
		$data['categories'] = $this->db->get('categories')->result();
		$this->load->view('demo_search_amz_products', $data);
	}
}