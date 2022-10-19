<?php 
class ControllerAjaxIndex extends Controller {
  public function index() {
    $this->load->language('ajax/index');
    $this->load->model('catalog/product');
     
    $this->document->setTitle($this->language->get('heading_title'));
     
    // загружаем все товары
    $products = $this->model_catalog_product->getProducts();
    $data['products'] = $products;
     
    $data['breadcrumbs'] = array();
 
    $data['breadcrumbs'][] = array(
      'href' => $this->url->link('common/home'),
      'text' => $this->language->get('text_home')
    );
 
    $data['breadcrumbs'][] = array(
      'href' => $this->url->link('ajax/index'),
      'text' => $this->language->get('heading_title')
    );
 
    $data['heading_title'] = $this->language->get('heading_title');
    $data['text_product_dropdown_label'] = $this->language->get('text_product_dropdown_label');
 
    $data['column_left'] = $this->load->controller('common/column_left');
    $data['column_right'] = $this->load->controller('common/column_right');
    $data['content_top'] = $this->load->controller('common/content_top');
    $data['content_bottom'] = $this->load->controller('common/content_bottom');
    $data['footer'] = $this->load->controller('common/footer');
    $data['header'] = $this->load->controller('common/header');
 
    if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/ajax/index.tpl')) {
      $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/ajax/index.tpl', $data));
    } else {
      $this->response->setOutput($this->load->view('default/template/ajax/index.tpl', $data));
    }
  }
  
  // метод получения Заказов пользователя с нужныи id
  public function ajaxGetOrders() {
    
        $this->response->addHeader('Content-Type: application/json');
        $id_user = $this->request->get['id_user'];
        $orders = array();
        $data = array();
        $products = array();
        $data['status'] = 'success';
        $query = $this->db->query("SELECT * FROM oc_order where customer_id = " . $id_user); 
        foreach ($query->rows as $order) {
              $orders = $order;
              $data['orders'][$orders['order_id']]['order_id'] = $orders['order_id'];
              $data['orders'][$orders['order_id']]['total'] = $orders['total'];
              $data['orders'][$orders['order_id']]['date_added'] = $orders['date_added'];
              $products = $this->db->query("SELECT * FROM oc_order_product where order_id = ". $orders['order_id']); 
                  foreach ($products->rows as $product) {
                    $data['orders'][$orders['order_id']]['products'][$product['product_id']]['name'] = $product['name'];
                    $data['orders'][$orders['order_id']]['products'][$product['product_id']]['quantity'] = $product['quantity'];
                    $data['orders'][$orders['order_id']]['products'][$product['product_id']]['price'] = $product['price'];
                  }                  
        }
      $this->response->setOutput(json_encode($data));     
 
  }
   // метод получения Заказов пользователя с нужныи id
  public function ajaxGetCatergory () {
   
      $this->response->addHeader('Content-Type: application/json');
      $data = array();
      $data['status'] = 'success';
      $iter = 0;
      $query = $this->db->query("SELECT * FROM oc_category"); 
      foreach ($query->rows as $category) {
        $data['categories'][$iter]['category_id'] = $category['category_id'];
        $data['categories'][$iter]['image'] = $category['image'];
        $data['categories'][$iter]['parent_id'] = $category['parent_id'];
        $data['categories'][$iter]['top'] = $category['top'];
        $data['categories'][$iter]['column'] = $category['column'];
        $data['categories'][$iter]['sort_order'] = $category['sort_order'];
        $data['categories'][$iter]['status'] = $category['status'];
        $data['categories'][$iter]['date_added'] = $category['date_added'];
        $data['categories'][$iter]['date_modified'] = $category['date_modified'];
        $category_descriptions = $this->db->query("SELECT * FROM oc_category_description where category_id = " . $category['category_id']); 
        foreach ($category_descriptions->rows as $category_description) {
          $data['categories'][$iter]['language_id'] = $category_description['language_id'];
          $data['categories'][$iter]['name'] = $category_description['name'];
          $data['categories'][$iter]['description'] = $category_description['description'];
          $data['categories'][$iter]['meta_title'] = $category_description['meta_title'];
          $data['categories'][$iter]['meta_description'] = $category_description['meta_description'];
          $data['categories'][$iter]['meta_keyword'] = $category_description['meta_keyword'];          
        }
        $category_stores = $this->db->query("SELECT * FROM oc_category_to_store where category_id = " . $category['category_id']);
        foreach ($category_stores->rows as $category_store) {
          $data['categories'][$iter]['store_id'] = $category_store['store_id']; 
        }
       
        $iter++;     
      }
      $this->response->setOutput(json_encode($data));     
   
  }
  public function ajaxGetProducts() {
   
        $this->response->addHeader('Content-Type: application/json');
        $id_category = $this->request->get['id_category'];       
        $data = array();       
        $data['status'] = 'success';
        $query = $this->db->query("SELECT * FROM oc_product_to_category where category_id = " . $id_category); 
        foreach ($query->rows as $product) {
            $data['products'][$product['product_id']]['product_id'] = $product['product_id'];
            $oc_product_descriptions = $this->db->query("SELECT * FROM oc_product_description where product_id = " . $product['product_id']); 
            foreach ($oc_product_descriptions->rows as $oc_product_description) {
               $data['products'][$product['product_id']]['name'] = $oc_product_description['name']; 
               $data['products'][$product['product_id']]['description'] = $oc_product_description['description']; 
               $data['products'][$product['product_id']]['meta_title'] = $oc_product_description['meta_title']; 
               $data['products'][$product['product_id']]['meta_description'] = $oc_product_description['meta_description']; 
               $data['products'][$product['product_id']]['meta_keyword'] = $oc_product_description['meta_keyword']; 
               $data['products'][$product['product_id']]['tag'] = $oc_product_description['tag']; 
            } 
            $oc_products = $this->db->query("SELECT * FROM oc_product where product_id = " . $product['product_id']);
            foreach ($oc_products->rows as $oc_product) {
                $data['products'][$product['product_id']]['model'] = $oc_product['model']; 
                $data['products'][$product['product_id']]['sku'] = $oc_product['sku']; 
                $data['products'][$product['product_id']]['upc'] = $oc_product['upc']; 
                $data['products'][$product['product_id']]['ean'] = $oc_product['ean']; 
                $data['products'][$product['product_id']]['jan'] = $oc_product['jan']; 
                $data['products'][$product['product_id']]['isbn'] = $oc_product['isbn']; 
                $data['products'][$product['product_id']]['mpn'] = $oc_product['mpn']; 
                $data['products'][$product['product_id']]['location'] = $oc_product['location']; 
                $data['products'][$product['product_id']]['quantity'] = $oc_product['quantity'];
                $oc_stock_status = $this->db->query("SELECT * FROM oc_stock_status where stock_status_id = " . $oc_product['stock_status_id']);
                foreach ($oc_stock_status->rows as $status) {
                  $data['products'][$product['product_id']]['stock_status'] = $status['stock_status_id']; 
                }
                $data['products'][$product['product_id']]['image'] = $oc_product['image'];
                $data['products'][$product['product_id']]['big_image'] = $oc_product['image'];
                $data['products'][$product['product_id']]['manufacturer_id'] = $oc_product['manufacturer_id'];

                $oc_manufacturers = $this->db->query("SELECT * FROM oc_manufacturer where manufacturer_id = " . $oc_product['manufacturer_id']);
                foreach ($oc_manufacturers->rows as $oc_manufacturer) {
                  $data['products'][$product['product_id']]['stock_status'] = $oc_manufacturer['name']; 
                }

                $data['products'][$product['product_id']]['price'] = $oc_product['price'];

                $oc_product_specials = $this->db->query("SELECT * FROM oc_product_special where product_id = " . $oc_product['product_id']);
                foreach ($oc_product_specials->rows as $oc_product_special) {
                  if(array_key_exists ("price", $oc_product_special)){
                      $data['products'][$product['product_id']]['special'] = $oc_product_special['price'];
                  }else{
                      $data['products'][$product['product_id']]['special'] = 'null';
                  }                   
                }

                $oc_product_rewards = $this->db->query("SELECT * FROM oc_product_reward where product_id = " . $oc_product['product_id']);
                foreach ($oc_product_rewards->rows as $oc_product_reward) {
                  if(array_key_exists ("points", $oc_product_reward)){
                      $data['products'][$product['product_id']]['reward'] = $oc_product_reward['points'];
                  }else{
                      $data['products'][$product['product_id']]['reward'] = 'null';
                  }                   
                }

                $data['products'][$product['product_id']]['points'] = $oc_product['points'];                
                $data['products'][$product['product_id']]['tax_class_id'] = $oc_product['tax_class_id'];
                $data['products'][$product['product_id']]['date_available'] = $oc_product['date_available'];
                $data['products'][$product['product_id']]['weight'] = $oc_product['weight'];
                $data['products'][$product['product_id']]['weight_class_id'] = $oc_product['weight_class_id'];
                $data['products'][$product['product_id']]['length'] = $oc_product['length'];
                $data['products'][$product['product_id']]['width'] = $oc_product['width'];
                $data['products'][$product['product_id']]['height'] = $oc_product['height'];
                $data['products'][$product['product_id']]['length_class_id'] = $oc_product['length_class_id'];
                $data['products'][$product['product_id']]['subtract'] = $oc_product['subtract'];

                $count_rating = 1;

                $oc_reviews = $this->db->query("SELECT * FROM oc_review where product_id = " . $oc_product['product_id']);
                foreach ($oc_reviews->rows as $oc_review) {
                  if(array_key_exists ("rating", $oc_review)){
                      $data['products'][$product['product_id']]['rating'] += $oc_review['rating'];
                      $data['products'][$product['product_id']]['rating'] = $data['products'][$product['product_id']]['rating'] / $count_rating;
                       $count_rating++;
                  }else{
                      $data['products'][$product['product_id']]['rating'] = '0';
                  }  
                  if(array_key_exists ("reviews", $oc_review)){
                      $data['products'][$product['product_id']]['reviews'] = $oc_review['reviews'];
                  }else{
                      $data['products'][$product['product_id']]['reviews'] = '0';
                  }                   
                }

                $data['products'][$product['product_id']]['minimum'] = $oc_product['minimum'];
                $data['products'][$product['product_id']]['sort_order'] = $oc_product['sort_order'];
                $data['products'][$product['product_id']]['status'] = $oc_product['status'];
                $data['products'][$product['product_id']]['date_added'] = $oc_product['date_added'];
                $data['products'][$product['product_id']]['date_modified'] = $oc_product['date_modified'];
                $data['products'][$product['product_id']]['viewed'] = $oc_product['viewed'];
            }

        }
      $this->response->setOutput(json_encode($data));     
    
  }
}