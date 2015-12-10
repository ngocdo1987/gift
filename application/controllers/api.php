<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api extends CI_Controller {
	function __construct() {
		parent::__construct();
	}
	
	function register() {
		$type = (string)$this->input->post('type');
		
		switch($type) {
			case "facebook":
				$fb_uid = (int)$this->input->post('fb_uid');
				$fb_email = (string)$this->input->post('fb_email');
				file_put_contents('register_'.date('d_m_Y_H_i_s').'.txt', $fb_uid." // ".$fb_email." // ".$type);

				if($fb_uid > 0 && $fb_email != '') {
					$check_user = $this->db->where('type', 'facebook')
										//->where('fb_uid', $fb_uid)
										->where('fb_email', $fb_email)
										->get('users')->row();	

					if(empty($check_user)) {
						$dataAdd = array(
							'type' => 'facebook',
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
				break;
			case "google":
				echo json_encode(array(
					'status' => 0,
					'message' => 'Under construction!'
				));
				break;
			case "email":
				echo json_encode(array(
					'status' => 0,
					'message' => 'Under construction!'
				));
				break;
			default:
				echo json_encode(array(
					'status' => 0,
					'message' => 'You must register via Facebook, Google or Email!'
				));
				break;			
		}
								
	}

	function create_event() {
		$user_id = (int)$this->input->post('user_id');
		$event_name = $this->input->post('event_name');
		$event_day = $this->input->post('event_day');
		$event_location = $this->input->post('event_location');
		$product_json = json_decode($this->input->post('product_json'));

		if($user_id > 0 && $event_name != '') {
			$dataAdd = array(
				'event_name' => $event_name,
				'event_day' => $event_day,
				'event_location' => $event_location,
				'user_id' => $user_id,
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')	
			);
			$this->db->insert('events', $dataAdd);
			$event_id = $this->db->insert_id();

			if(!empty($product_json)) {
				//$product_ids = array();

				foreach($product_json as $product) {
					$product_asin = $product->product_asin;
					$check_product = $this->db->where('product_asin', $product_asin)
											->get('products')->row();
					if(empty($check_product)) {
						$product_name = $product->product_name;
						$product_image = $product->product_image;
						$product_price = $product->product_price;
						$product_manufacturer = $product->product_manufacturer;
						$product_model = $product->product_model;
						$product_link = $product->product_link;
						$product_features = json_encode($product->product_features);
						$product_dimensions = json_encode($product->product_dimensions);

						$dataAdd = array(
							'product_asin' => $product_asin,
							'product_name' => $product_name,
							'product_image' => $product_image,
							'product_price' => $product_price,
							'product_manufacturer' => $product_manufacturer,
							'product_model' => $product_model,
							'product_link' => $product_link,
							'product_features' => $product_features,
							'product_dimensions' => $product_dimensions,
							'created_at' => date('Y-m-d H:i:s'),
							'updated_at' => date('Y-m-d H:i:s')
						);	
						$this->db->insert('products', $dataAdd);	
					}else{
						$product_id = $check_product->id;	
					}

					$check_event_product = $this->db->where('event_id', $event_id)
													->where('product_id', $product_id)
													->get('events_products')->row();
					if(empty($check_event_product)) {
						$dataAdd = array(
							'event_id' => $event_id,
							'product_id' => $product_id
						);
						$this->db->insert('events_products', $dataAdd);
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

	function delete_event() {
		$user_id = (int)$this->input->post('user_id');
		$event_id = (int)$this->input->post('event_id');

		if($user_id > 0 && $event_id > 0) {
			// Delete event product
			$this->db->where('event_id', $event_id)
					->delete('events_products');

			// Delete event
			$this->db->where('id', $event_id)	
					->where('user_id', $user_id)
					->delete('events');

			echo json_encode(array(
				'status' => 1,
				'message' => 'Delete event successfully!'
			));			
		}else{
			echo json_encode(array(
				'status' => 0,
				'message' => 'Please choose user and event for deleting event!'
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
					'product_asin' => $product->product_asin,
					'product_name' => $product->product_name,
					'product_image' => $product->product_image,
					'product_price' => $product->product_price,
					'product_manufacturer' => $product->product_manufacturer,
					'product_model' => $product->product_model,
					'product_link' => $product->product_link,
					'product_features' => json_decode($product->product_features),
					'product_dimensions' => json_decode($product->product_dimensions)	
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
			//echo '<pre>'; print_r($result); echo '</pre>'; die('stop');
			$total_products = (int)$result->Items->TotalResults[0];
			$total_pages = (int)$result->Items->TotalPages[0];
			$similar_products = $result->Items->Item;

			$product_arr = array();

			foreach($similar_products as $si){
				$product_asin = (string)$si->ASIN[0];
				$product_name = (string)$si->ItemAttributes->Title[0];

				$imagesets = $si->ImageSets->ImageSet;
				$product_image = array();
				if(!empty($imagesets)) {
					foreach($imagesets as $imageset) {
						$product_image[] = (string)$imageset->LargeImage->URL[0];	
					}
				}


				$product_price = (string)$si->ItemAttributes->ListPrice->FormattedPrice[0];
				$product_price = (float)str_replace('$', '', $product_price);

				$product_manufacturer = (string)$si->ItemAttributes->Manufacturer[0];
				$product_model = (string)$si->ItemAttributes->Model[0];

				$product_link = (string)$si->DetailPageURL[0];

				$product_features = array();
				$features = $si->ItemAttributes->Feature;
				if(!empty($features)) {
					foreach($features as $k => $v) {
						$product_features[] = (string)$v[0];
					}
				}

				//print_r($product_features); die('');
				$product_dimensions = $si->ItemAttributes->ItemDimensions;
					
				$product_arr[] = array(
					'product_asin' => $product_asin,
					'product_name' => $product_name,
					'product_image' => $product_image,
					'product_price' => $product_price,
					'product_manufacturer' => $product_manufacturer,
					'product_model' => $product_model,
					'product_link' => $product_link,
					'product_features' => $product_features,
					'product_dimensions' => $product_dimensions
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

	function demo_register() {
		$this->load->view('demo_register');
	}

	function ex_json() {
		$ex_json = file_get_contents('ex_json.txt');
		$ex_json = nl2br($ex_json);
		$ex_json = str_replace("\t", '&nbsp;&nbsp;&nbsp;&nbsp;', $ex_json);
		echo $ex_json;
	}
}