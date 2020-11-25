<?php
/**
 * rest_api.php
 *
 * Custom rest services
 *
 * @author     Makai Lajos
 * @copyright  2014
 * @license    License.txt
 * @version    2.0
 * @link       http://opencart-api.com/product/opencart-restful-api-pro-v2-0/
 * @see        http://webshop.opencart-api.com/schema_v2.0/
 */

class ControllerFeedRestApi extends Controller
{

    private static $productFieds = array(
        "model",
        "sku",
        "upc",
        "ean",
        "jan",
        "isbn",
        "mpn",
        "location",
        "quantity",
        "minimum",
        "subtract",
        "stock_status_id",
        "date_available",
        "manufacturer_id",
        "shipping",
        "price",
        "points",
        "weight",
        "weight_class_id",
        "length",
        "width",
        "height",
        "length_class_id",
        "status",
        "tax_class_id",
        "sort_order"
    );
    private $debugIt = false;

    public function getchecksum()
    {

        $this->checkPlugin();

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {

            $this->load->model('catalog/product');

            $checksum = $this->_getChecksum();

            $checksumArray = array();

            for ($i = 0; $i < count($checksum); $i++) {
                $checksumArray[] = array('table' => $checksum[$i]['Table'], 'checksum' => $checksum[$i]['Checksum']);
            }

            $json = array('success' => true, 'data' => $checksumArray);

            $this->sendResponse($json);
        }
    }

    /*check database modification*/

    private function checkPlugin()
    {

        $this->response->addHeader('Content-Type: application/json');

        $json = array("success" => false);

        /*check rest api is enabled*/
        if (!$this->config->get('rest_api_status')) {
            $json["error"] = 'API is disabled. Enable it!';
        }


        $headers = apache_request_headers();

        $key = "";

        if (isset($headers['X-Oc-Merchant-Id'])) {
            $key = $headers['X-Oc-Merchant-Id'];
        } else if (isset($headers['X-OC-MERCHANT-ID'])) {
            $key = $headers['X-OC-MERCHANT-ID'];
        }

        /*validate api security key*/
        if ($this->config->get('rest_api_key') && ($key != $this->config->get('rest_api_key'))) {
            $json["error"] = 'Invalid secret key';
        }

        if (isset($json["error"])) {
            echo(json_encode($json));
            exit;
        }
    }

    /*
    * PRODUCT FUNCTIONS
    */

    private function _getChecksum()
    {
        $query = $this->db->query("CHECKSUM TABLE " . DB_PREFIX . "product, "
            . DB_PREFIX . "category,"
            . DB_PREFIX . "product_to_category,"
            . DB_PREFIX . "product_description"

        );
        return $query->rows;
    }

    /*
    * Get products list
    */

    private function sendResponse($json)
    {
        if ($this->debugIt) {
            echo '<pre>';
            print_r($json);
            echo '</pre>';
        } else {
            $this->response->setOutput(json_encode($json));
        }
    }

    /*
    * Get product details
    */

    public function products()
    {

        $this->checkPlugin();

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            //get product details
            if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])) {
                $this->getProduct($this->request->get['id']);
            } else {
                //get products list

                /*check category id parameter*/
                if (isset($this->request->get['category']) && ctype_digit($this->request->get['category'])) {
                    $category_id = $this->request->get['category'];
                } else {
                    $category_id = 0;
                }

                $this->listProducts($category_id, $this->request);
            }
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            //insert product
            $requestjson = file_get_contents('php://input');

            $requestjson = json_decode($requestjson, true);

            if (!empty($requestjson)) {
                $this->addProduct($requestjson);
            } else {
                $this->response->setOutput(json_encode(array('success' => false)));
            }

        } else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            //update product
            $requestjson = file_get_contents('php://input');

            $requestjson = json_decode($requestjson, true);

            if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])
                && !empty($requestjson)) {
                $this->updateProduct($this->request->get['id'], $requestjson);
            } else {
                $this->response->setOutput(json_encode(array('success' => false)));
            }

        } else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])) {
                $this->deleteProduct($this->request->get['id']);
            } else {
                $this->response->setOutput(json_encode(array('success' => false)));
            }
        }
    }

    public function getProduct($id)
    {

        $json = array('success' => true);

        $this->load->model('catalog/product');

        $products = $this->_getProductsByIds(array($id));
        if (!empty($products)) {
            $json["data"] = $this->getProductInfo(reset($products));
        } else {
            $json['success'] = false;
        }

        $this->sendResponse($json);
    }

    /*	Update product

    */

    private function _getProductsByIds($product_ids)
    {

        if (count($product_ids) == 0) {
            return false;
        }

        if ($this->customer->isLogged()) {
            $customer_group_id = $this->customer->getCustomerGroupId();
        } else {
            $customer_group_id = $this->config->get('config_customer_group_id');
        }

        $query = $this->db->query("SELECT DISTINCT *, pd.name AS name, p.image, m.name AS manufacturer, (SELECT price FROM " . DB_PREFIX . "product_discount pd2 WHERE pd2.product_id = p.product_id AND pd2.customer_group_id = '" . (int)$customer_group_id . "' AND pd2.quantity = '1' AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())) ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount, (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$customer_group_id . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special, (SELECT points FROM " . DB_PREFIX . "product_reward pr WHERE pr.product_id = p.product_id AND customer_group_id = '" . (int)$customer_group_id . "') AS reward, (SELECT ss.name FROM " . DB_PREFIX . "stock_status ss WHERE ss.stock_status_id = p.stock_status_id AND ss.language_id = '" . (int)$this->config->get('config_language_id') . "') AS stock_status, (SELECT wcd.unit FROM " . DB_PREFIX . "weight_class_description wcd WHERE p.weight_class_id = wcd.weight_class_id AND wcd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS weight_class, (SELECT lcd.unit FROM " . DB_PREFIX . "length_class_description lcd WHERE p.length_class_id = lcd.length_class_id AND lcd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS length_class, (SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating, (SELECT COUNT(*) AS total FROM " . DB_PREFIX . "review r2 WHERE r2.product_id = p.product_id AND r2.status = '1' GROUP BY r2.product_id) AS reviews, p.sort_order FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id) WHERE p.product_id IN (" . implode(',', $product_ids) . ") AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' ORDER BY p.product_id ASC");

        $product_data = array();
        if ($query->num_rows) {
            foreach ($query->rows as $result) {
                $product_data[$result['product_id']] = array(
                    'product_id' => $result['product_id'],
                    'name' => $result['name'],
                    'description' => $result['description'],
                    'meta_description' => $result['meta_description'],
                    'meta_keyword' => $result['meta_keyword'],
                    'tag' => $result['tag'],
                    'model' => $result['model'],
                    'sku' => $result['sku'],
                    'upc' => $result['upc'],
                    'ean' => $result['ean'],
                    'jan' => $result['jan'],
                    'isbn' => $result['isbn'],
                    'mpn' => $result['mpn'],
                    'location' => $result['location'],
                    'quantity' => $result['quantity'],
                    'stock_status' => $result['stock_status'],
                    'image' => $result['image'],
                    'manufacturer_id' => $result['manufacturer_id'],
                    'manufacturer' => $result['manufacturer'],
                    'price' => ($result['discount'] ? $result['discount'] : $result['price']),
                    'special' => $result['special'],
                    'reward' => $result['reward'],
                    'points' => $result['points'],
                    'tax_class_id' => $result['tax_class_id'],
                    'date_available' => $result['date_available'],
                    'weight' => $result['weight'],
                    'weight_class_id' => $result['weight_class_id'],
                    'length' => $result['length'],
                    'width' => $result['width'],
                    'height' => $result['height'],
                    'length_class_id' => $result['length_class_id'],
                    'subtract' => $result['subtract'],
                    'rating' => round($result['rating']),
                    'reviews' => $result['reviews'] ? $result['reviews'] : 0,
                    'minimum' => $result['minimum'],
                    'sort_order' => $result['sort_order'],
                    'status' => $result['status'],
                    'date_added' => $result['date_added'],
                    'date_modified' => $result['date_modified'],
                    'viewed' => $result['viewed'],
                    'weight_class' => $result['weight_class'],
                    'length_class' => $result['length_class']
                );
            }
            return $product_data;
        } else {
            return false;
        }
    }

    /*
	Insert product
    */

    private function getProductInfo($product)
    {

        $this->load->model('tool/image');
        $this->load->model('catalog/category');
        $this->load->model('catalog/product');


        //product image
        if (isset($product['image']) && file_exists(DIR_IMAGE . $product['image'])) {
            $image = $this->model_tool_image->resize($product['image'], 500, 500);
        } else {
            $image = $this->model_tool_image->resize('no_image.jpg', 500, 500);
        }

        //additional images
        $additional_images = $this->model_catalog_product->getProductImages($product['product_id']);

        $images = array();

        foreach ($additional_images as $additional_image) {
            if (isset($additional_image['image']) && file_exists(DIR_IMAGE . $additional_image['image'])) {
                $images[] = $this->model_tool_image->resize($additional_image['image'], 500, 500);
            } else {
                $images[] = $this->model_tool_image->resize('no_image.jpg', 500, 500);
            }
        }

        //special
        if ((float)$product['special']) {
            $special = $this->currency->format($this->tax->calculate($product['special'], $product['tax_class_id'], $this->config->get('config_tax')));
        } else {
            $special = "";
        }

        //discounts
        $discounts = array();
        $data_discounts = $this->model_catalog_product->getProductDiscounts($product['product_id']);

        foreach ($data_discounts as $discount) {
            $discounts[] = array(
                'quantity' => $discount['quantity'],
                'price' => $this->currency->format($this->tax->calculate($discount['price'], $product['tax_class_id'], $this->config->get('config_tax')))
            );
        }


        //options
        $options = array();

        foreach ($this->model_catalog_product->getProductOptions($product['product_id']) as $option) {
            if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox' || $option['type'] == 'image') {
                $option_value_data = array();
                if (!empty($option['product_option_value'])) {
                    foreach ($option['product_option_value'] as $option_value) {
                        if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
                            if ((($this->customer->isLogged() && $this->config->get('config_customer_price')) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
                                $price = $this->currency->format($this->tax->calculate($option_value['price'], $product['tax_class_id'], $this->config->get('config_tax')));
                            } else {
                                $price = false;
                            }

                            if (isset($option_value['image']) && file_exists(DIR_IMAGE . $option_value['image'])) {
                                $option_image = $this->model_tool_image->resize($option_value['image'], 100, 100);
                            } else {
                                $option_image = $this->model_tool_image->resize('no_image.jpg', 100, 100);
                            }

                            $option_value_data[] = array(
                                'image' => $option_image,
                                'price' => $price,
                                'price_prefix' => $option_value['price_prefix'],
                                'product_option_value_id' => $option_value['product_option_value_id'],
                                'option_value_id' => $option_value['option_value_id'],
                                'name' => $option_value['name'],
                                'quantity' => !empty($option_value['quantity']) ? $option_value['quantity'] : 0
                            );
                        }
                    }
                }
                $options[] = array(
                    'name' => $option['name'],
                    'type' => $option['type'],
                    'option_value' => $option_value_data,
                    'required' => $option['required'],
                    'product_option_id' => $option['product_option_id'],
                    'option_id' => $option['option_id'],

                );

            } elseif ($option['type'] == 'text' || $option['type'] == 'textarea' || $option['type'] == 'file' || $option['type'] == 'date' || $option['type'] == 'datetime' || $option['type'] == 'time') {
                $option_value = array();
                if (!empty($option['product_option_value'])) {
                    $option_value = $option['product_option_value'];
                }
                $options[] = array(
                    'name' => $option['name'],
                    'type' => $option['type'],
                    'option_value' => $option_value,
                    'required' => $option['required'],
                    'product_option_id' => $option['product_option_id'],
                    'option_id' => $option['option_id'],
                );
            }
        }


        $productCategories = array();
        $product_category = $this->model_catalog_product->getCategories($product['product_id']);

        foreach ($product_category as $prodcat) {
            $category_info = $this->model_catalog_category->getCategory($prodcat['category_id']);
            if ($category_info) {
                $productCategories[] = array(
                    'name' => $category_info['name'],
                    'id' => $category_info['category_id']
                );
            }
        }

        return array(
            'id' => $product['product_id'],
            'seo_h1' => (!empty($product['seo_h1']) ? $product['seo_h1'] : ""),
            'name' => $product['name'],
            'manufacturer' => $product['manufacturer'],
            'sku' => (!empty($product['sku']) ? $product['sku'] : ""),
            'model' => $product['model'],
            'image' => $image,
            'images' => $images,
            'price' => $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'))),
            'rating' => (int)$product['rating'],
            'description' => html_entity_decode($product['description'], ENT_QUOTES, 'UTF-8'),
            'tag' => html_entity_decode($product['tag'], ENT_QUOTES, 'UTF-8'),
            'meta_description' => html_entity_decode($product['meta_description'], ENT_QUOTES, 'UTF-8'),
            'meta_keyword' => html_entity_decode($product['meta_keyword'], ENT_QUOTES, 'UTF-8'),
//            'meta_title'                => html_entity_decode($product['meta_title'], ENT_QUOTES, 'UTF-8'),
            'attribute_groups' => $this->model_catalog_product->getProductAttributes($product['product_id']),
            'special' => $special,
            'discounts' => $discounts,
            'options' => $options,
            'minimum' => $product['minimum'] ? $product['minimum'] : 1,
            'tag' => $product['tag'],
            'upc' => $product['upc'],
            'ean' => $product['ean'],
            'jan' => $product['jan'],
            'isbn' => $product['isbn'],
            'mpn' => $product['mpn'],
            'location' => $product['location'],
            'stock_status' => $product['stock_status'],
            'manufacturer_id' => $product['manufacturer_id'],
            'tax_class_id' => $product['tax_class_id'],
            'date_available' => $product['date_available'],
            'weight' => $product['weight'],
            'weight_class_id' => $product['weight_class_id'],
            'length' => $product['length'],
            'width' => $product['width'],
            'height' => $product['height'],
            'length_class_id' => $product['length_class_id'],
            'subtract' => $product['subtract'],
            'reviews' => $product['reviews'],
            'sort_order' => $product['sort_order'],
            'status' => $product['status'],
            'date_added' => $product['date_added'],
            'date_modified' => $product['date_modified'],
            'viewed' => $product['viewed'],
            'weight_class' => $product['weight_class'],
            'length_class' => $product['length_class'],
            'reward' => $product['reward'],
            'points' => $product['points'],
            'category' => $productCategories,
            'date_available' => $product['date_available'],
            'quantity' => !empty($product['quantity']) ? $product['quantity'] : 0
        );
    }

    /*
    * Delete product
    */

    public function listProducts($category_id, $request)
    {

        $json = array('success' => false);

        $this->load->model('catalog/product');

        $parameters = array(
            "limit" => 100,
            "start" => 1,
            'filter_category_id' => $category_id
        );

        /*check limit parameter*/
        if (isset($request->get['limit']) && ctype_digit($request->get['limit'])) {
            $parameters["limit"] = $request->get['limit'];
        }

        /*check page parameter*/
        if (isset($request->get['page']) && ctype_digit($request->get['page'])) {
            $parameters["start"] = $request->get['page'];
        }

        $parameters["start"] = ($parameters["start"] - 1) * $parameters["limit"];

        $products = $this->_getProductsData($parameters);

        if (count($products) == 0 || empty($products)) {
            $json['success'] = false;
            $json['error'] = "No product found";
        } else {
            $json['success'] = true;
            foreach ($products as $product) {
                $json['data'][] = $this->getProductInfo($product);
            }
        }

        $this->sendResponse($json);
    }

    /*
    * OPTION FUNCTIONS
    */

    private function _getProductsData($data = array())
    {
        if ($this->customer->isLogged()) {
            $customer_group_id = $this->customer->getCustomerGroupId();
        } else {
            $customer_group_id = $this->config->get('config_customer_group_id');
        }

        $sql = "SELECT p.product_id, (SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating, (SELECT price FROM " . DB_PREFIX . "product_discount pd2 WHERE pd2.product_id = p.product_id AND pd2.customer_group_id = '" . (int)$customer_group_id . "' AND pd2.quantity = '1' AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())) ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount, (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$customer_group_id . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special";


        if (!empty($data['filter_category_id'])) {
            if (!empty($data['filter_sub_category'])) {
                $sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";
            } else {
                $sql .= " FROM " . DB_PREFIX . "product_to_category p2c";
            }
            if (!empty($data['filter_filter'])) {
                $sql .= " LEFT JOIN " . DB_PREFIX . "product_filter pf ON (p2c.product_id = pf.product_id) LEFT JOIN " . DB_PREFIX . "product p ON (pf.product_id = p.product_id)";
            } else {
                $sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)";
            }
        } else {
            $sql .= " FROM " . DB_PREFIX . "product p";
        }

        $sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

        if (!empty($data['filter_category_id'])) {
            if (!empty($data['filter_sub_category'])) {
                $sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";
            } else {
                $sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
            }
        }

        $sql .= " GROUP BY p.product_id";

        $sql .= " ORDER BY p.product_id";

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['limit'] < 1) {
                $limit = 20;
            } else {
                $limit = (int)$data['limit'];
            }

            $offset = 0;
            if ($data['start'] < 0) {
                $offset = 0;
            } else {
                $offset = (int)$data['start'];
            }

            $sql .= " LIMIT " . $offset . "," . $limit;
        }

        $product_data = array();

        $query = $this->db->query($sql);

        foreach ($query->rows as $result) {
            $product_data[$result['product_id']] = $result['product_id'];
        }

        return $this->_getProductsByIds(array_keys($product_data));
    }

    public function addProduct($data)
    {

        $json = array('success' => true);

        $this->load->model('catalog/product');

        if ($this->validateProductForm($data, true)) {
            $productId = $this->_addProduct($data);
            $json['product_id'] = $productId;
        } else {
            $json['success'] = false;
        }

        $this->sendResponse($json);
    }

    /*
    * BULK PRODUCT FUNCTIONS
    */

    private function validateProductForm(&$data, $validateSku = false)
    {

        $error = false;

        if ($validateSku) {
            if ((utf8_strlen($data['sku']) < 2) || (utf8_strlen($data['sku']) > 255)) {
                $error = true;
            }
        }

        if (!empty($data['date_available'])) {
            $date_available = date('Y-m-d', strtotime($data['date_available']));
            if ($this->validateDate($date_available, 'Y-m-d')) {
                $data['date_available'] = $date_available;
            } else {
                $data['date_available'] = null;
            }
        } else {
            $data['date_available'] = null;
        }

        foreach (self::$productFieds as $field) {
            if (!isset($data[$field])) {
                $data[$field] = "";
            }
        }

        if (!$error) {
            return true;
        } else {
            return false;
        }
    }

    /*
		Insert products
	*/

    private function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    /*	Update products

    */

    private function _addProduct($data)
    {

        if ($data['model'] == 'duplicate') {
            $proId = $data['manufacturer_id'];
            $product_id = $this->copyProduct2($proId, $data);

        } else {
            $this->db->query("INSERT INTO " . DB_PREFIX . "product
                        SET model = '" . $this->db->escape($data['model']) . "',
                        sku = '" . $this->db->escape($data['sku']) . "',
                        upc = '" . $this->db->escape($data['upc']) . "',
                        ean = '" . $this->db->escape($data['ean']) . "',
                        jan = '" . $this->db->escape($data['jan']) . "',
                        isbn = '" . $this->db->escape($data['isbn']) . "',
                        mpn = '" . $this->db->escape($data['mpn']) . "',
                        location = '" . $this->db->escape($data['location']) . "',
                        quantity = '" . (int)$data['quantity'] . "',
                        minimum = '" . (int)$data['minimum'] . "',
                        subtract = '" . (int)$data['subtract'] . "',
                        stock_status_id = '" . (int)$data['stock_status_id'] . "',
                        date_available = '" . $this->db->escape($data['date_available']) . "',
                        manufacturer_id = '" . (int)$data['manufacturer_id'] . "',
                        shipping = '" . (int)$data['shipping'] . "',
                        price = '" . (float)$data['price'] . "',
                        points = '" . (int)$data['points'] . "',
                        weight = '" . (float)$data['weight'] . "',
                        weight_class_id = '" . (int)$data['weight_class_id'] . "',
                        length = '" . (float)$data['length'] . "',
                        width = '" . (float)$data['width'] . "',
                        height = '" . (float)$data['height'] . "',
                        length_class_id = '" . (int)$data['length_class_id'] . "',
                        status = '" . (int)$data['status'] . "',
                        tax_class_id = '" . $this->db->escape($data['tax_class_id']) . "',
                        sort_order = '" . (int)$data['sort_order'] . "',
                        date_added = NOW()");

            $product_id = $this->db->getLastId();

            foreach ($data['product_description'] as $language_id => $value) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', description = '" . $this->db->escape($value['description']) . "'
						                     ,tag= '" . $this->db->escape($value['product_tags']) . "'");
            }

            if (isset($data['product_store'])) {
                foreach ($data['product_store'] as $store_id) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "'");
                }
            }

            if (isset($data['product_category'])) {
                foreach ($data['product_category'] as $category_id) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "'");
                }
            }

            if (isset($data['colors'])) {
                //$color_id = 14;oc_option
                $this->db->query("INSERT INTO " . DB_PREFIX . "option SET type ='" . 'select' . "' , sort_order=" . '1' . "");
                $color_id = $this->db->getLastId();
                $this->db->query("INSERT INTO " . DB_PREFIX . "option_description SET option_id='" . (int)$color_id . "' , language_id ='1', name='Color' ");
                $plus = '+';
                $this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id='" . $color_id . "', required='1' ");
                $color_product_option_id = $this->db->getLastId();
                foreach ($data['colors'] as $color) {

                    $this->db->query("INSERT INTO " . DB_PREFIX . "option_value SET option_id = '" . $color_id . "'");
                    $colors_option_id = $this->db->getLastId();
                    $this->db->query("INSERT INTO " . DB_PREFIX . "option_value_description SET option_value_id = '" . $colors_option_id . "',language_id=1, option_id='" . (int)$color_id . "', name='" . $color['option'] . "' ");
                    $this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_id= '" . $color_product_option_id . "', product_id='" . (int)$product_id . "', option_id='" . (int)$color_id . "', option_value_id='" . $colors_option_id . "', price='" . $color['plus_value'] . "', price_prefix='" . $plus . "' ");
                }
            }
            //add size option
            if (isset($data['sizes'])) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "option SET type ='select', sort_order='1' ");
                $size_id = $this->db->getLastId();
                $this->db->query("INSERT INTO " . DB_PREFIX . "option_description SET option_id='" . (int)$size_id . "' , language_id ='1', name='Size' ");
                $plus = '+';
                $this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id='" . (int)$size_id . "' , required='1'");
                $size_product_option_id = $this->db->getLastId();
                foreach ($data['sizes'] as $size) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "option_value SET option_id = '" . (int)$size_id . "'");
                    $size_option_id = $this->db->getLastId();
                    $this->db->query("INSERT INTO " . DB_PREFIX . "option_value_description SET option_value_id = '" . $size_option_id . "',language_id=1, option_id='" . (int)$size_id . "', name='" . $size['option'] . "' ");
                    $this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_id= '" . $size_product_option_id . "', product_id='" . (int)$product_id . "', option_id='" . (int)$size_id . "', option_value_id='" . $size_option_id . "', price='" . $size['plus_value'] . "', price_prefix='" . $plus . "' ");
                }
            }

            if (isset($data['product_option'])) {
                foreach ($data['product_option'] as $product_option) {
                    if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
                        $this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', required = '" . (int)$product_option['required'] . "'");

                        $product_option_id = $this->db->getLastId();

                        if (isset($product_option['product_option_value']) && count($product_option['product_option_value']) > 0) {
                            foreach ($product_option['product_option_value'] as $product_option_value) {
                                $this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_value_id = '" . (int)$product_option_value['product_option_value_id'] . "', product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value_id = '" . (int)$product_option_value['option_value_id'] . "', quantity = '" . (int)$product_option_value['quantity'] . "', subtract = '" . (isset($product_option_value['subtract']) ? (int)$product_option_value['subtract'] : "") . "', price = '" . (isset($product_option_value['price']) ? (float)$product_option_value['price'] : "") . "', price_prefix = '" . (isset($product_option_value['price_prefix']) ? $this->db->escape($product_option_value['price_prefix']) : "") . "', points = '" . (isset($product_option_value['points']) ? (int)$product_option_value['points'] : "") . "', points_prefix = '" . (isset($product_option_value['points_prefix']) ? $this->db->escape($product_option_value['points_prefix']) : "") . "', weight = '" . (isset($product_option_value['weight']) ? (float)$product_option_value['weight'] : "") . "', weight_prefix = '" . (isset($product_option_value['weight_prefix']) ? $this->db->escape($product_option_value['weight_prefix']) : "") . "'");
                            }
                        } else {
                            $this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_option_id = '" . $product_option_id . "'");
                        }
                    } else {
                        $this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value = '" . $this->db->escape($product_option['option_value']) . "', required = '" . (int)$product_option['required'] . "'");
                    }
                }
            }

            $this->cache->delete('product');


        }
        return (int)$product_id;
    }

    private function copyProduct2($product_id, $old_data)
    {
        $copyId;
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
        if ($query->num_rows) {
            $data = array();
            $data = $query->row;
            $data['sku'] = '';
            $data['upc'] = '';
            $data['viewed'] = '0';
            $data['keyword'] = '';
            $data['status'] = '0';
            $data = array_merge($data, array('product_attribute' => $this->getProductAttributes($product_id)));
            $data = array_merge($data, array('product_description' => $this->getProductDescriptions($product_id)));
            $data = array_merge($data, array('product_discount' => $this->getProductDiscounts($product_id)));
            $data = array_merge($data, array('product_filter' => $this->getProductFilters($product_id)));
            $data = array_merge($data, array('product_image' => $this->getProductImages($product_id)));
            $data = array_merge($data, array('product_option' => $this->getProductOptions2($product_id)));
            $data = array_merge($data, array('product_related' => $this->getProductRelated($product_id)));
            $data = array_merge($data, array('product_reward' => $this->getProductRewards($product_id)));
            //$data = array_merge($data, array('product_special' => $this->getProductSpecials($product_id)));
            $data = array_merge($data, array('product_category' => $this->getProductCategories($product_id)));
            $data = array_merge($data, array('product_download' => $this->getProductDownloads($product_id)));
            $data = array_merge($data, array('product_layout' => $this->getProductLayouts($product_id)));
            $data = array_merge($data, array('product_profiles' => $this->getProfiles($product_id)));
            $copyId = $this->duplicate($data, $old_data);
        }
        return $copyId;
    }

    private function getProductDescriptions($product_id)
    {
        $product_description_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "'");

        foreach ($query->rows as $result) {
            $product_description_data[$result['language_id']] = array(
                'name' => $result['name'],
                'description' => $result['description'],
                'meta_keyword' => $result['meta_keyword'],
                'meta_description' => $result['meta_description'],
                'tag' => $result['tag']
            );
        }

        return $product_description_data;
    }

    /*
    * CATEGORY FUNCTIONS
    */

    private function getProductFilters($product_id)
    {
        $product_filter_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_filter WHERE product_id = '" . (int)$product_id . "'");

        foreach ($query->rows as $result) {
            $product_filter_data[] = $result['filter_id'];
        }

        return $product_filter_data;
    }


    /*
    * Get categories list
    */

    private function getProductOptions2($product_id)
    {
        $product_option_data = array();

        $product_option_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_option` po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN `" . DB_PREFIX . "option_description` od ON (o.option_id = od.option_id) WHERE po.product_id = '" . (int)$product_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "'");

        foreach ($product_option_query->rows as $product_option) {
            $product_option_value_data = array();

            $product_option_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value WHERE product_option_id = '" . (int)$product_option['product_option_id'] . "'");

            foreach ($product_option_value_query->rows as $product_option_value) {
                $product_option_value_data[] = array(
                    'product_option_value_id' => $product_option_value['product_option_value_id'],
                    'option_value_id' => $product_option_value['option_value_id'],
                    'quantity' => $product_option_value['quantity'],
                    'subtract' => $product_option_value['subtract'],
                    'price' => $product_option_value['price'],
                    'price_prefix' => $product_option_value['price_prefix'],
                    'points' => $product_option_value['points'],
                    'points_prefix' => $product_option_value['points_prefix'],
                    'weight' => $product_option_value['weight'],
                    'weight_prefix' => $product_option_value['weight_prefix']
                );
            }

            $product_option_data[] = array(
                'product_option_id' => $product_option['product_option_id'],
                'product_option_value' => $product_option_value_data,
                'option_id' => $product_option['option_id'],
                'name' => $product_option['name'],
                'type' => $product_option['type'],
                'value' => $product_option['option_value'],
                'required' => $product_option['required']
            );
        }

        return $product_option_data;
    }

    /*
    * Get category details
    */

    private function getProductRewards($product_id)
    {
        $product_reward_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$product_id . "'");

        foreach ($query->rows as $result) {
            $product_reward_data[$result['customer_group_id']] = array('points' => $result['points']);
        }

        return $product_reward_data;
    }

    private function getProductCategories($product_id)
    {
        $product_category_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");

        foreach ($query->rows as $result) {
            $product_category_data[] = $result['category_id'];
        }

        return $product_category_data;
    }

    /*
    Insert category
    */

    private function getProductDownloads($product_id)
    {
        $product_download_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_download WHERE product_id = '" . (int)$product_id . "'");

        foreach ($query->rows as $result) {
            $product_download_data[] = $result['download_id'];
        }

        return $product_download_data;
    }

    /*
    Uppdate category
    */

    private function getProductLayouts($product_id)
    {
        $product_layout_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int)$product_id . "'");

        foreach ($query->rows as $result) {
            $product_layout_data[$result['store_id']] = $result['layout_id'];
        }

        return $product_layout_data;
    }

    /*
    * Delete category
    */

    private function duplicate($data, $old_data)
    {
        $this->db->query("INSERT INTO " . DB_PREFIX . "product SET model = '" . $this->db->escape($data['model']) . "', sku = '" . $this->db->escape($old_data['sku']) . "', upc = '" . $this->db->escape($data['upc']) . "', ean = '" . $this->db->escape($data['ean']) . "', jan = '" . $this->db->escape($data['jan']) . "', isbn = '" . $this->db->escape($data['isbn']) . "', mpn = '" . $this->db->escape($data['mpn']) . "', location = '" . $this->db->escape($data['location']) . "', quantity = '" . (int)$data['quantity'] . "', minimum = '" . (int)$data['minimum'] . "', subtract = '" . (int)$data['subtract'] . "', stock_status_id = '" . (int)$data['stock_status_id'] . "', date_available = '" . $this->db->escape($data['date_available']) . "', manufacturer_id = '" . (int)$data['manufacturer_id'] . "', shipping = '" . (int)$data['shipping'] . "', price = '" . (float)$data['price'] . "', points = '" . (int)$data['points'] . "', weight = '" . (float)$data['weight'] . "', weight_class_id = '" . (int)$data['weight_class_id'] . "', length = '" . (float)$data['length'] . "', width = '" . (float)$data['width'] . "', height = '" . (float)$data['height'] . "', length_class_id = '" . (int)$data['length_class_id'] . "', status = '" . (int)$data['status'] . "', tax_class_id = '" . $this->db->escape($data['tax_class_id']) . "', sort_order = '" . (int)$data['sort_order'] . "', date_added = NOW()");
        $product_id = $this->db->getLastId();
        $this->db->query("UPDATE " . DB_PREFIX . "product SET status=1 WHERE product_id = '" . (int)$product_id . "'");
        if ($data['image']) {
            $this->db->query("UPDATE " . DB_PREFIX . "product SET image = '" . $this->db->escape(html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8')) . "' WHERE product_id = '" . (int)$product_id . "'");
        }
        foreach ($old_data['product_description'] as $dt) {
            foreach ($data['product_description'] as $language_id => $value) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($dt['name']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', description = '" . $this->db->escape($value['description']) . "', tag = '" . $this->db->escape($value['tag']) . "'");
            }
        }


        if ($data['product_attribute']) {
            foreach ($data['product_attribute'] as $product_attribute) {
                if ($product_attribute['attribute_id']) {
                    $this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "'");

                    foreach ($product_attribute['product_attribute_description'] as $language_id => $product_attribute_description) {
                        $this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$product_attribute['attribute_id'] . "', language_id = '" . (int)$language_id . "', text = '" . $this->db->escape($product_attribute_description['text']) . "'");
                    }
                }
            }
        }

        if ($data['product_option']) {
            foreach ($data['product_option'] as $product_option) {
                if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', required = '" . (int)$product_option['required'] . "'");

                    $product_option_id = $this->db->getLastId();

                    if (isset($product_option['product_option_value']) && count($product_option['product_option_value']) > 0) {
                        foreach ($product_option['product_option_value'] as $product_option_value) {
                            $this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value_id = '" . (int)$product_option_value['option_value_id'] . "', quantity = '" . (int)$product_option_value['quantity'] . "', subtract = '" . (int)$product_option_value['subtract'] . "', price = '" . (float)$product_option_value['price'] . "', price_prefix = '" . $this->db->escape($product_option_value['price_prefix']) . "', points = '" . (int)$product_option_value['points'] . "', points_prefix = '" . $this->db->escape($product_option_value['points_prefix']) . "', weight = '" . (float)$product_option_value['weight'] . "', weight_prefix = '" . $this->db->escape($product_option_value['weight_prefix']) . "'");
                        }
                    } else {
                        $this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_option_id = '" . $product_option_id . "'");
                    }
                } else {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value = '" . $this->db->escape($product_option['option_value']) . "', required = '" . (int)$product_option['required'] . "'");
                }
            }
        }

        if ($data['product_discount']) {
            foreach ($data['product_discount'] as $product_discount) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "product_discount SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_discount['customer_group_id'] . "', quantity = '" . (int)$product_discount['quantity'] . "', priority = '" . (int)$product_discount['priority'] . "', price = '" . (float)$product_discount['price'] . "', date_start = '" . $this->db->escape($product_discount['date_start']) . "', date_end = '" . $this->db->escape($product_discount['date_end']) . "'");
            }
        }

//		if ($data['product_special']) {
//			foreach ($data['product_special'] as $product_special) {
//				$this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_special['customer_group_id'] . "', priority = '" . (int)$product_special['priority'] . "', price = '" . (float)$product_special['price'] . "', date_start = '" . $this->db->escape($product_special['date_start']) . "', date_end = '" . $this->db->escape($product_special['date_end']) . "'");
//			}
//		}
        if ($data['product_image']) {
            foreach ($data['product_image'] as $product_image) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape(html_entity_decode($product_image['image'], ENT_QUOTES, 'UTF-8')) . "', sort_order = '" . (int)$product_image['sort_order'] . "'");
            }
        }

        if ($data['product_download']) {
            foreach ($data['product_download'] as $download_id) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_download SET product_id = '" . (int)$product_id . "', download_id = '" . (int)$download_id . "'");
            }
        }

        if ($data['product_category']) {
            foreach ($data['product_category'] as $category_id) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "'");
            }
        }

        if ($data['product_filter']) {
            foreach ($data['product_filter'] as $filter_id) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "product_filter SET product_id = '" . (int)$product_id . "', filter_id = '" . (int)$filter_id . "'");
            }
        }

        if ($data['product_related']) {
            foreach ($data['product_related'] as $related_id) {
                $this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "' AND related_id = '" . (int)$related_id . "'");
                $this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$product_id . "', related_id = '" . (int)$related_id . "'");
                $this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$related_id . "' AND related_id = '" . (int)$product_id . "'");
                $this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$related_id . "', related_id = '" . (int)$product_id . "'");
            }
        }

        if ($data['product_reward']) {
            foreach ($data['product_reward'] as $customer_group_id => $product_reward) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "product_reward SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$customer_group_id . "', points = '" . (int)$product_reward['points'] . "'");
            }
        }

        if ($data['product_layout']) {
            foreach ($data['product_layout'] as $store_id => $layout) {
                if ($layout['layout_id']) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_layout SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout['layout_id'] . "'");
                }
            }
        }

        if ($data['keyword']) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'product_id=" . (int)$product_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
        }

        if ($data['product_profiles']) {
            foreach ($data['product_profiles'] as $profile) {
                $this->db->query("INSERT INTO `" . DB_PREFIX . "product_profile` SET `product_id` = " . (int)$product_id . ", customer_group_id = " . (int)$profile['customer_group_id'] . ", `profile_id` = " . (int)$profile['profile_id']);
            }
        }

        $this->cache->delete('product');

        return $product_id;
    }

    private function updateProduct($id, $data)
    {

        $json = array('success' => false);

        $this->load->model('catalog/product');

        if (ctype_digit($id)) {
            $product = $this->_getProduct2($id);

            if (!empty($product)) {
                $this->loadProductSavedData($data, $product);
                if ($this->validateProductForm($data)) {
                    $json['success'] = true;
                    $this->_editProductById($id, $data);
                } else {
                    $json['error'] = "Validation failed";
                    $json['success'] = false;
                }
            } else {
                $json['success'] = false;
                $json['error'] = "The specified product does not exist $product";
            }
        } else {
            $json['success'] = false;
            $json['error'] = "Invalid identifier.";
        }

        $this->sendResponse($json);
    }

    /*
    * MANUFACTURER FUNCTIONS
    */

    private function _getProduct2($product_id)
    {
        if ($this->customer->isLogged()) {
            $customer_group_id = $this->customer->getCustomerGroupId();
        } else {
            $customer_group_id = $this->config->get('config_customer_group_id');
        }
        $query = $this->db->query("SELECT DISTINCT * from " . DB_PREFIX . "product where product_id='" . $product_id . "'");
        $query2 = $this->db->query("SELECT DISTINCT * from " . DB_PREFIX . "product_description where product_id='" . $product_id . "'");

        if ($query->num_rows && $query2->num_rows) {
            return array(
                'product_id' => $query->row['product_id'],
                'name' => $query2->row['name'],
                'description' => $query2->row['description'],
                'tag' => $query2->row['tag'],
                'model' => $query->row['model'],
                'sku' => $query->row['sku'],
                'upc' => $query->row['upc'],
                'ean' => $query->row['ean'],
                'jan' => $query->row['jan'],
                'isbn' => $query->row['isbn'],
                'mpn' => $query->row['mpn'],
                'location' => $query->row['location'],
                'quantity' => $query->row['quantity'],
            );
        } else {
            return false;
        }
    }

    /*
    * Get manufacturers list
    */

    private function loadProductSavedData(&$data, $product)
    {
        foreach (self::$productFieds as $field) {
            if (!isset($data[$field])) {
                if (isset($product[$field])) {
                    $data[$field] = $product[$field];
                } else {
                    $data[$field] = "";
                }
            }
        }
    }

    /*
    * Get manufacturer details
    */

    private function _editProductById($product_id, $data)
    {
        $this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int)$product_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$product_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "'");
        if (isset($data['colors'])) {
            //$color_id = 14;oc_option
            $this->db->query("INSERT INTO " . DB_PREFIX . "option SET type ='" . 'select' . "' , sort_order=" . '1' . "");
            $color_id = $this->db->getLastId();
            $this->db->query("INSERT INTO " . DB_PREFIX . "option_description SET option_id='" . (int)$color_id . "' , language_id ='1', name='Color' ");
            $plus = '+';
            $this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id='" . $color_id . "', required='1' ");
            $color_product_option_id = $this->db->getLastId();
            foreach ($data['colors'] as $color) {

                $this->db->query("INSERT INTO " . DB_PREFIX . "option_value SET option_id = '" . $color_id . "'");
                $colors_option_id = $this->db->getLastId();
                $this->db->query("INSERT INTO " . DB_PREFIX . "option_value_description SET option_value_id = '" . $colors_option_id . "',language_id=1, option_id='" . (int)$color_id . "', name='" . $color['option'] . "' ");
                $this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_id= '" . $color_product_option_id . "', product_id='" . (int)$product_id . "', option_id='" . (int)$color_id . "', option_value_id='" . $colors_option_id . "', price='" . $color['plus_value'] . "', price_prefix='" . $plus . "' ");
            }
        }
        //add size option
        if (isset($data['sizes'])) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "option SET type ='select', sort_order='1' ");
            $size_id = $this->db->getLastId();
            $this->db->query("INSERT INTO " . DB_PREFIX . "option_description SET option_id='" . (int)$size_id . "' , language_id ='1', name='Size' ");
            $plus = '+';
            $this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id='" . (int)$size_id . "' , required='1'");
            $size_product_option_id = $this->db->getLastId();
            foreach ($data['sizes'] as $size) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "option_value SET option_id = '" . (int)$size_id . "'");
                $size_option_id = $this->db->getLastId();
                $this->db->query("INSERT INTO " . DB_PREFIX . "option_value_description SET option_value_id = '" . $size_option_id . "',language_id=1, option_id='" . (int)$size_id . "', name='" . $size['option'] . "' ");
                $this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_id= '" . $size_product_option_id . "', product_id='" . (int)$product_id . "', option_id='" . (int)$size_id . "', option_value_id='" . $size_option_id . "', price='" . $size['plus_value'] . "', price_prefix='" . $plus . "' ");
            }
        }
        $this->db->query("UPDATE " . DB_PREFIX . "product SET 
						model = '" . $this->db->escape($data['model']) . "', 
						sku = '" . $this->db->escape($data['sku']) . "', 
						upc = '" . $this->db->escape($data['upc']) . "', 
						ean = '" . $this->db->escape($data['ean']) . "', 
						jan = '" . $this->db->escape($data['jan']) . "', 
						isbn = '" . $this->db->escape($data['isbn']) . "', 
						mpn = '" . $this->db->escape($data['mpn']) . "', 
						location = '" . $this->db->escape($data['location']) . "', 
						quantity = '" . (int)$data['quantity'] . "', 
						minimum = '" . (int)$data['minimum'] . "', 
						subtract = '" . (int)$data['subtract'] . "', 
						stock_status_id = '" . (int)$data['stock_status_id'] . "', 
						date_available = '" . $this->db->escape($data['date_available']) . "', 
						manufacturer_id = '" . (int)$data['manufacturer_id'] . "', 
						shipping = '" . (int)$data['shipping'] . "', 
						price = '" . (float)$data['price'] . "', 
						points = '" . (int)$data['points'] . "', 
						weight = '" . (float)$data['weight'] . "', 
						weight_class_id = '" . (int)$data['weight_class_id'] . "', 
						length = '" . (float)$data['length'] . "', 
						width = '" . (float)$data['width'] . "', 
						height = '" . (float)$data['height'] . "', 
						length_class_id = '" . (int)$data['length_class_id'] . "', 
						status = '" . (int)$data['status'] . "', 
						tax_class_id = '" . $this->db->escape($data['tax_class_id']) . "', 
						sort_order = '" . (int)$data['sort_order'] . "', 
						date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");

        if (isset($data['product_description']) && !empty($data['product_description'])) {
            $this->db->query("DELETE FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "'");

            foreach ($data['product_description'] as $language_id => $value) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', description = '" . $this->db->escape($value['description']) . "'
						                     ,tag= '" . $this->db->escape($value['product_tags']) . "'");
            }
        }

        if (isset($data['product_store']) && !empty($data['product_description'])) {
            $this->db->query("DELETE FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "'");

            if (isset($data['product_store'])) {
                foreach ($data['product_store'] as $store_id) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "'");
                }
            }
        }

        if (isset($data['product_category']) && !empty($data['product_category'])) {
            $this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");

            if (isset($data['product_category'])) {
                foreach ($data['product_category'] as $category_id) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "'");
                }
            }
        }

        if (isset($data['product_option']) && isset($data['product_option_quantity_update']) && intval($data['product_option_quantity_update']) == 1) {
            foreach ($data['product_option'] as $product_option) {
                if (isset($product_option['product_option_value']) && count($product_option['product_option_value']) > 0) {
                    foreach ($product_option['product_option_value'] as $product_option_value) {
                        $this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET quantity = '" . (int)$product_option_value['quantity'] . "' WHERE product_id = '" . (int)$product_id . "' AND product_option_value_id = '" . (int)$product_option_value['product_option_value_id'] . "'");
                    }
                }
            }
        } elseif (isset($data['product_option'])) {
            $this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int)$product_id . "'");
            $this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$product_id . "'");

            foreach ($data['product_option'] as $product_option) {
                if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_option_id = '" . (int)$product_option['product_option_id'] . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', required = '" . (int)$product_option['required'] . "'");

                    $product_option_id = $this->db->getLastId();

                    if (isset($product_option['product_option_value']) && count($product_option['product_option_value']) > 0) {
                        foreach ($product_option['product_option_value'] as $product_option_value) {
                            $this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_value_id = '" . (int)$product_option_value['product_option_value_id'] . "', product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value_id = '" . (int)$product_option_value['option_value_id'] . "', quantity = '" . (int)$product_option_value['quantity'] . "', subtract = '" . (isset($product_option_value['subtract']) ? (int)$product_option_value['subtract'] : "") . "', price = '" . (isset($product_option_value['price']) ? (float)$product_option_value['price'] : "") . "', price_prefix = '" . (isset($product_option_value['price_prefix']) ? $this->db->escape($product_option_value['price_prefix']) : "") . "', points = '" . (isset($product_option_value['points']) ? (int)$product_option_value['points'] : "") . "', points_prefix = '" . (isset($product_option_value['points_prefix']) ? $this->db->escape($product_option_value['points_prefix']) : "") . "', weight = '" . (isset($product_option_value['weight']) ? (float)$product_option_value['weight'] : "") . "', weight_prefix = '" . (isset($product_option_value['weight_prefix']) ? $this->db->escape($product_option_value['weight_prefix']) : "") . "'");
                        }
                    } else {
                        $this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_option_id = '" . $product_option_id . "'");
                    }
                } else {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_option_id = '" . (int)$product_option['product_option_id'] . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value = '" . $this->db->escape($product_option['option_value']) . "', required = '" . (int)$product_option['required'] . "'");
                }
            }
        }

        $this->cache->delete('product');
    }

    public function deleteProduct($id)
    {

        $json['success'] = false;

        $this->load->model('catalog/product');

        if (ctype_digit($id)) {

            $product = $this->model_catalog_product->getProduct($id);

            if (!empty($product)) {
                $json['success'] = true;
                $this->_deleteProduct($id);
            } else {
                $json['success'] = false;
                $json['error'] = "The specified product does not exist.";
            }
        } else {
            $json['success'] = false;
        }

        $this->sendResponse($json);
    }

    /*
        Insert manufacturer
    */

    private function _deleteProduct($product_id)
    {
        $this->db->query("DELETE FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "'");

        if (VERSION != "1.5.4" && VERSION != "1.5.4.1" && VERSION != "1.5.3" && VERSION != "1.5.3.1") {
            $this->db->query("DELETE FROM " . DB_PREFIX . "product_filter WHERE product_id = '" . (int)$product_id . "'");
        }

        if (VERSION == "1.5.3" && VERSION == "1.5.3.1") {
            $this->db->query("DELETE FROM " . DB_PREFIX . "product_tag WHERE product_id='" . (int)$product_id . "'");
        }

        $this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int)$product_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$product_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE related_id = '" . (int)$product_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$product_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "product_to_download WHERE product_id = '" . (int)$product_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int)$product_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "'");

        if (VERSION != "1.5.4" && VERSION != "1.5.4.1" && VERSION != "1.5.3" && VERSION != "1.5.3.1" && VERSION != "1.5.5" && VERSION != "1.5.5.1") {
            $this->db->query("DELETE FROM `" . DB_PREFIX . "product_profile` WHERE `product_id` = " . (int)$product_id);
        }

        $this->db->query("DELETE FROM " . DB_PREFIX . "review WHERE product_id = '" . (int)$product_id . "'");

        $this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'product_id=" . (int)$product_id . "'");

        $this->cache->delete('product');
    }

    /*
        Update manufacturer

    */

    public function options()
    {
        $this->checkPlugin();
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            //get product details
            if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])) {
                $requestjson = file_get_contents('php://input');
                $productId = $this->request->get['id'];
                $option = json_decode($requestjson, true);
                $resp = $this->deleteOp($productId, $option);
                $json['data'] = $resp;

            }
        } else {
            $json['data'] = "Bad Request";
        }

    }

    /*Delete manufacturer*/

    private function deleteOp($productId, $option)
    {
        $json['success'] = false;
        $this->load->model('catalog/product');
        if (ctype_digit($productId)) {
            if (!empty($productId) && $option != '') {
                $json['success'] = true;
                $json['data'] = $this->_deleteOption($productId, $option);
            } else {
                $json['success'] = false;
                $json['error'] = "The specified product does not exist.";
            }
        } else {
            $json['success'] = false;
        }

        if ($this->debugIt) {
            echo '<pre>';
            print_r($json);
            echo '</pre>';
        } else {
            $this->response->setOutput(json_encode($json));
        }
    }

    private function _deleteOption($productId, $data)
    {
        if ($data['command'] == 'option') {
            $result = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_option_value` pov LEFT JOIN `" . DB_PREFIX . "option_value_description` ovd "
                . "ON (pov.option_value_id = ovd.option_value_id) "
                . "WHERE pov.product_id = '" . (int)$productId . "' "
                . "AND ovd.name = '" . $data['value'] . "'");
            if ($result->num_rows > 0) {
                $product_option_value_id = $result->row["product_option_value_id"];
                $option_value_id = $result->row['option_value_id'];
                $this->db->query("DELETE FROM `" . DB_PREFIX . "product_option_value` where product_option_value_id='" . (int)$product_option_value_id . "'");
                $this->db->query("DELETE FROM `" . DB_PREFIX . "option_value_description` where option_value_id='" . (int)$option_value_id . "'");

            }
        } else if ($data['command'] == 'product') {
            $this->db->query("UPDATE `" . DB_PREFIX . "product` set status='0' where product_id='" . (int)$productId . "'");
        }
    }

    public function bulkproducts()
    {

        $this->checkPlugin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            //insert products
            $requestjson = file_get_contents('php://input');

            $requestjson = json_decode($requestjson, true);

            if (!empty($requestjson) && count($requestjson) > 0) {

                $this->addProducts($requestjson);
            } else {
                $this->response->setOutput(json_encode(array('success' => false)));
            }

        } else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            //update products
            $requestjson = file_get_contents('php://input');
            $requestjson = json_decode($requestjson, true);

            if (!empty($requestjson) && count($requestjson) > 0) {
                $this->updateProducts($requestjson);
            } else {
                $this->response->setOutput(json_encode(array('success' => false)));
            }

        }
    }

    /*
    * ORDER FUNCTIONS
    */

    public function addProducts($products)
    {

        $json = array('success' => true);

        $this->load->model('catalog/product');

        foreach ($products as $product) {

            if ($this->validateProductForm($product, true)) {
                $this->_addProduct($product);
            } else {
                $json['success'] = false;
            }
        }

        $this->sendResponse($json);
    }

    /*
    * List orders
    */

    private function updateProducts($products)
    {

        $json = array('success' => true);

        $this->load->model('catalog/product');

        foreach ($products as $productItem) {

            $id = $productItem['product_id'];

            if (ctype_digit($id)) {

                $product = $this->model_catalog_product->getProduct($id);

                if (!empty($product)) {
                    $this->loadProductSavedData($productItem, $product);
                    if ($this->validateProductForm($productItem)) {
                        $this->_editProductById($id, $productItem);
                    } else {
                        $json['success'] = false;
                    }

                } else {
                    $json['success'] = false;
                    $json['error'] = "The specified product does not exist.";
                }

            } else {
                $json['success'] = false;
                $json['error'] = "Invalid identifier";
            }
        }

        $this->sendResponse($json);
    }

    /*
    * List orders whith details
    */

    public function categories()
    {

        $this->checkPlugin();

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            //get category details
            if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])) {
                $this->getCategory($this->request->get['id']);
            } else {
                //get category list

                /*check parent parameter*/
                if (isset($this->request->get['parent'])) {
                    $parent = $this->request->get['parent'];
                } else {
                    $parent = 0;
                }

                /*check level parameter*/
                if (isset($this->request->get['level'])) {
                    $level = $this->request->get['level'];
                } else {
                    $level = 1;
                }

                $this->listCategories($parent, $level);
            }
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            //insert category data
            $requestjson = file_get_contents('php://input');

            $requestjson = json_decode($requestjson, true);

            if (!empty($requestjson)) {
                $this->addCategory($requestjson);
            } else {
                $this->response->setOutput(json_encode(array('success' => false)));
            }

        } else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            //update category data
            $requestjson = file_get_contents('php://input');

            $requestjson = json_decode($requestjson, true);

            if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])
                && !empty($requestjson)) {
                $this->updateCategory($this->request->get['id'], $requestjson);
            } else {
                $this->response->setOutput(json_encode(array('success' => false)));
            }

        } else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])) {
                $this->deleteCategory($this->request->get['id']);
            } else {
                $this->response->setOutput(json_encode(array('success' => false)));
            }

        }

    }

    /*Get order details*/

    public function getCategory($id)
    {

        $json = array('success' => true);

        $this->load->model('catalog/category');
        $this->load->model('tool/image');

        if (ctype_digit($id)) {
            $category_id = $id;
        } else {
            $category_id = 0;
        }

        $category = $this->model_catalog_category->getCategory($category_id);

        if (isset($category['category_id'])) {

            $json['success'] = true;

            if (isset($category['image']) && file_exists(DIR_IMAGE . $category['image'])) {
                $image = $this->model_tool_image->resize($category['image'], 100, 100);
            } else {
                $image = $this->model_tool_image->resize('no_image.jpg', 100, 100);
            }

            $json['data'] = array(
                'id' => $category['category_id'],
                'name' => $category['name'],
                'description' => $category['description'],
                'image' => $image
            );
        } else {
            $json['success'] = false;
            $json['error'] = "The specified category does not exist.";

        }

        $this->sendResponse($json);
    }

    /*Get all orders of user */

    public function listCategories($parent, $level)
    {

        $json['success'] = true;

        $this->load->model('catalog/category');

        $data = $this->loadCatTree($parent, $level);

        if (count($data) == 0) {
            $json['success'] = false;
            $json['error'] = "No category found";
        } else {
            $json['data'] = $data;
        }

        $this->sendResponse($json);
    }

    public function loadCatTree($parent = 0, $level = 1)
    {

        $this->load->model('catalog/category');
        $this->load->model('tool/image');

        $result = array();

        $categories = $this->model_catalog_category->getCategories($parent);

        if ($categories && $level > 0) {
            $level--;

            foreach ($categories as $category) {

                if (isset($category['image']) && file_exists(DIR_IMAGE . $category['image'])) {
                    $image = $this->model_tool_image->resize($category['image'], 100, 100);
                } else {
                    $image = $this->model_tool_image->resize('no_image.jpg', 100, 100);
                }

                $result[] = array(
                    'category_id' => $category['category_id'],
                    'parent_id' => $category['parent_id'],
                    'name' => $category['name'],
                    'image' => $image,
                    'categories' => $this->loadCatTree($category['category_id'], $level)
                );
            }
            return $result;
        }
    }

    /*
        Update order status

    */

    public function addCategory($data)
    {

        $json = array('success' => true);

        $this->load->model('catalog/category');

        if ($this->validateCategoryForm($data)) {
            $categoryId = $this->_addCategory($data);
            $json['category_id'] = $categoryId;
        } else {
            $json['success'] = false;
        }

        $this->sendResponse($json);
    }

    /*Delete order*/

    protected function validateCategoryForm($data)
    {

        $error = false;

        foreach ($data['category_description'] as $language_id => $value) {
            if ((utf8_strlen($value['name']) < 2) || (utf8_strlen($value['name']) > 255)) {
                $error = true;
            }
        }
        if (!$error) {
            return true;
        } else {
            return false;
        }
    }

    /*
    * CUSTOMER FUNCTIONS
    */

    private function _addCategory($data)
    {
        $this->db->query("INSERT INTO " . DB_PREFIX . "category SET parent_id = '" . (int)$data['parent_id'] . "', `top` = '" . (isset($data['top']) ? (int)$data['top'] : 0) . "', `column` = '" . (int)$data['column'] . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "', date_modified = NOW(), date_added = NOW()");

        $category_id = $this->db->getLastId();

        if (isset($data['image'])) {
            $this->db->query("UPDATE " . DB_PREFIX . "category SET image = '" . $this->db->escape(html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8')) . "' WHERE category_id = '" . (int)$category_id . "'");
        }

        foreach ($data['category_description'] as $language_id => $value) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "category_description SET category_id = '" . (int)$category_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', description = '" . $this->db->escape($value['description']) . "'");
        }

        // MySQL Hierarchical Data Closure Table Pattern
        $level = 0;
        if (VERSION != "1.5.4" && VERSION != "1.5.4.1" && VERSION != "1.5.3" && VERSION != "1.5.3.1") {
            $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "category_path` WHERE category_id = '" . (int)$data['parent_id'] . "' ORDER BY `level` ASC");

            foreach ($query->rows as $result) {
                $this->db->query("INSERT INTO `" . DB_PREFIX . "category_path` SET `category_id` = '" . (int)$category_id . "', `path_id` = '" . (int)$result['path_id'] . "', `level` = '" . (int)$level . "'");

                $level++;
            }

            $this->db->query("INSERT INTO `" . DB_PREFIX . "category_path` SET `category_id` = '" . (int)$category_id . "', `path_id` = '" . (int)$category_id . "', `level` = '" . (int)$level . "'");
        }

        if (VERSION != "1.5.4" && VERSION != "1.5.4.1" && VERSION != "1.5.3" && VERSION != "1.5.3.1" && VERSION != "1.5.5" && VERSION != "1.5.5.1") {
            if (isset($data['category_filter'])) {
                foreach ($data['category_filter'] as $filter_id) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "category_filter SET category_id = '" . (int)$category_id . "', filter_id = '" . (int)$filter_id . "'");
                }
            }
        }

        if (isset($data['category_store'])) {
            foreach ($data['category_store'] as $store_id) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "category_to_store SET category_id = '" . (int)$category_id . "', store_id = '" . (int)$store_id . "'");
            }
        }

        // Set which layout to use with this category
        if (isset($data['category_layout'])) {
            foreach ($data['category_layout'] as $store_id => $layout) {
                if ($layout['layout_id']) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "category_to_layout SET category_id = '" . (int)$category_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout['layout_id'] . "'");
                }
            }
        }

        if ($data['keyword']) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'category_id=" . (int)$category_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
        }

        $this->cache->delete('category');

        return (int)$category_id;
    }

    /*
    * Get customers list
    */

    public function updateCategory($id, $data)
    {

        $json = array('success' => false);

        $this->load->model('catalog/category');

        if ($this->validateCategoryForm($data)) {
            if (ctype_digit($id)) {
                $category = $this->model_catalog_category->getCategory($id);

                if (!empty($category)) {
                    $json['success'] = true;
                    $this->_editCategory($id, $data);
                } else {
                    $json['success'] = false;
                    $json['error'] = "The specified category does not exist.";
                }

            } else {
                $json['success'] = false;
            }
        } else {
            $json['success'] = false;
        }

        $this->sendResponse($json);
    }

    /*
    * Get customer details
    */

    private function _editCategory($category_id, $data)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "category SET parent_id = '" . (int)$data['parent_id'] . "', `top` = '" . (isset($data['top']) ? (int)$data['top'] : 0) . "', `column` = '" . (int)$data['column'] . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "', date_modified = NOW() WHERE category_id = '" . (int)$category_id . "'");

        if (isset($data['image'])) {
            $this->db->query("UPDATE " . DB_PREFIX . "category SET image = '" . $this->db->escape(html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8')) . "' WHERE category_id = '" . (int)$category_id . "'");
        }

        $this->db->query("DELETE FROM " . DB_PREFIX . "category_description WHERE category_id = '" . (int)$category_id . "'");

        foreach ($data['category_description'] as $language_id => $value) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "category_description SET category_id = '" . (int)$category_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', description = '" . $this->db->escape($value['description']) . "'");
        }

        // MySQL Hierarchical Data Closure Table Pattern
        if (VERSION != "1.5.4" && VERSION != "1.5.4.1" && VERSION != "1.5.3" && VERSION != "1.5.3.1") {
            $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "category_path` WHERE path_id = '" . (int)$category_id . "' ORDER BY level ASC");

            if ($query->rows) {
                foreach ($query->rows as $category_path) {
                    // Delete the path below the current one
                    $this->db->query("DELETE FROM `" . DB_PREFIX . "category_path` WHERE category_id = '" . (int)$category_path['category_id'] . "' AND level < '" . (int)$category_path['level'] . "'");

                    $path = array();

                    // Get the nodes new parents
                    $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "category_path` WHERE category_id = '" . (int)$data['parent_id'] . "' ORDER BY level ASC");

                    foreach ($query->rows as $result) {
                        $path[] = $result['path_id'];
                    }

                    // Get whats left of the nodes current path
                    $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "category_path` WHERE category_id = '" . (int)$category_path['category_id'] . "' ORDER BY level ASC");

                    foreach ($query->rows as $result) {
                        $path[] = $result['path_id'];
                    }

                    // Combine the paths with a new level
                    $level = 0;

                    foreach ($path as $path_id) {
                        $this->db->query("REPLACE INTO `" . DB_PREFIX . "category_path` SET category_id = '" . (int)$category_path['category_id'] . "', `path_id` = '" . (int)$path_id . "', level = '" . (int)$level . "'");

                        $level++;
                    }
                }
            } else {
                // Delete the path below the current one
                $this->db->query("DELETE FROM `" . DB_PREFIX . "category_path` WHERE category_id = '" . (int)$category_id . "'");

                // Fix for records with no paths
                $level = 0;

                $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "category_path` WHERE category_id = '" . (int)$data['parent_id'] . "' ORDER BY level ASC");

                foreach ($query->rows as $result) {
                    $this->db->query("INSERT INTO `" . DB_PREFIX . "category_path` SET category_id = '" . (int)$category_id . "', `path_id` = '" . (int)$result['path_id'] . "', level = '" . (int)$level . "'");

                    $level++;
                }

                $this->db->query("REPLACE INTO `" . DB_PREFIX . "category_path` SET category_id = '" . (int)$category_id . "', `path_id` = '" . (int)$category_id . "', level = '" . (int)$level . "'");
            }
        }

        if (VERSION != "1.5.4" && VERSION != "1.5.4.1" && VERSION != "1.5.3" && VERSION != "1.5.3.1" && VERSION != "1.5.5" && VERSION != "1.5.5.1") {
            $this->db->query("DELETE FROM " . DB_PREFIX . "category_filter WHERE category_id = '" . (int)$category_id . "'");

            if (isset($data['category_filter'])) {
                foreach ($data['category_filter'] as $filter_id) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "category_filter SET category_id = '" . (int)$category_id . "', filter_id = '" . (int)$filter_id . "'");
                }
            }
        }

        $this->db->query("DELETE FROM " . DB_PREFIX . "category_to_store WHERE category_id = '" . (int)$category_id . "'");

        if (isset($data['category_store'])) {
            foreach ($data['category_store'] as $store_id) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "category_to_store SET category_id = '" . (int)$category_id . "', store_id = '" . (int)$store_id . "'");
            }
        }

        $this->db->query("DELETE FROM " . DB_PREFIX . "category_to_layout WHERE category_id = '" . (int)$category_id . "'");

        if (isset($data['category_layout'])) {
            foreach ($data['category_layout'] as $store_id => $layout) {
                if ($layout['layout_id']) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "category_to_layout SET category_id = '" . (int)$category_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout['layout_id'] . "'");
                }
            }
        }

        $this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'category_id=" . (int)$category_id . "'");

        if ($data['keyword']) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'category_id=" . (int)$category_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
        }

        $this->cache->delete('category');
    }

    public function deleteCategory($id)
    {

        $json['success'] = false;

        $this->load->model('catalog/category');

        if (ctype_digit($id)) {

            $category = $this->model_catalog_category->getCategory($id);

            if (!empty($category)) {
                $json['success'] = true;
                $this->_deleteCategory($id);
            } else {
                $json['success'] = false;
                $json['error'] = "The specified product does not exist.";
            }
        } else {
            $json['success'] = false;
        }

        $this->sendResponse($json);
    }

    /*
	Update customer
   */

    private function _deleteCategory($category_id)
    {

        if (VERSION != "1.5.4" && VERSION != "1.5.4.1" && VERSION != "1.5.3" && VERSION != "1.5.3.1") {

            $this->db->query("DELETE FROM " . DB_PREFIX . "category_path WHERE category_id = '" . (int)$category_id . "'");

            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category_path WHERE path_id = '" . (int)$category_id . "'");

            foreach ($query->rows as $result) {
                $this->_deleteCategory($result['category_id']);
            }
        }
        $this->db->query("DELETE FROM " . DB_PREFIX . "category WHERE category_id = '" . (int)$category_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "category_description WHERE category_id = '" . (int)$category_id . "'");

        if (VERSION != "1.5.4" && VERSION != "1.5.4.1" && VERSION != "1.5.3" && VERSION != "1.5.3.1" && VERSION != "1.5.5" && VERSION != "1.5.5.1") {
            $this->db->query("DELETE FROM " . DB_PREFIX . "category_filter WHERE category_id = '" . (int)$category_id . "'");
        }

        $this->db->query("DELETE FROM " . DB_PREFIX . "category_to_store WHERE category_id = '" . (int)$category_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "category_to_layout WHERE category_id = '" . (int)$category_id . "'");

        $this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE category_id = '" . (int)$category_id . "'");

        $this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'category_id=" . (int)$category_id . "'");

        $this->cache->delete('category');
    }

    /*Delete customer*/

    public function manufacturers()
    {

        $this->checkPlugin();

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            //get manufacturer details
            if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])) {
                $this->getManufacturer($this->request->get['id']);
            } else {
                //get manufacturers list
                $this->listManufacturers();
            }
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            //insert manufacturer
            $requestjson = file_get_contents('php://input');

            $requestjson = json_decode($requestjson, true);

            if (!empty($requestjson)) {
                $this->addManufacturer($requestjson);
            } else {
                $this->response->setOutput(json_encode(array('success' => false)));
            }

        } else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            //update manufacturer
            $requestjson = file_get_contents('php://input');

            $requestjson = json_decode($requestjson, true);

            if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])
                && !empty($requestjson)) {
                $this->updateManufacturer($this->request->get['id'], $requestjson);
            } else {
                $this->response->setOutput(json_encode(array('success' => false)));
            }

        } else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            //delete manufacturer
            if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])) {
                $this->deleteManufacturer($this->request->get['id']);
            } else {
                $this->response->setOutput(json_encode(array('success' => false)));
            }

        }
    }

    public function getManufacturer($id)
    {

        $json = array('success' => true);

        $this->load->model('catalog/manufacturer');
        $this->load->model('tool/image');

        if (ctype_digit($id)) {
            $manufacturer = $this->model_catalog_manufacturer->getManufacturer($id);
            if ($manufacturer) {
                $json['data'] = $this->getManufacturerInfo($manufacturer);
            } else {
                $json['success'] = false;
                $json['error'] = "The specified manufacturer does not exist.";
            }
        } else {
            $json['success'] = false;
        }

        $this->sendResponse($json);
    }

    /*
    * LANGUAGE FUNCTIONS
    */

    private function getManufacturerInfo($manufacturer)
    {
        if (isset($manufacturer['image']) && file_exists(DIR_IMAGE . $manufacturer['image'])) {
            $image = $this->model_tool_image->resize($manufacturer['image'], 100, 100);
        } else {
            $image = $this->model_tool_image->resize('no_image.jpg', 100, 100);
        }

        return array(
            'manufacturer_id' => $manufacturer['manufacturer_id'],
            'name' => $manufacturer['name'],
            'image' => $image,
            'sort_order' => $manufacturer['sort_order']
        );
    }

    /*
* ORDER STATUSES FUNCTIONS
*/

    public function listManufacturers()
    {

        $this->load->model('catalog/manufacturer');
        $this->load->model('tool/image');
        $json = array('success' => true);

        $data['start'] = 0;
        $data['limit'] = 1000;

        $results = $this->model_catalog_manufacturer->getManufacturers($data);

        $manufacturers = array();

        foreach ($results as $manufacturer) {
            $manufacturers[] = $this->getManufacturerInfo($manufacturer);
        }

        if (empty($manufacturers)) {
            $json['success'] = false;
            $json['error'] = "No manufacturer found";
        } else {
            $json['data'] = $manufacturers;
        }

        $this->sendResponse($json);
    }

    /*
    * Get order statuses list
    */

    public function addManufacturer($data)
    {

        $json = array('success' => true);

        $this->load->model('catalog/manufacturer');

        if ($this->validateManufacturerForm($data)) {
            $manufacturerId = $this->_addManufacturer($data);
            $json['manufacturer_id'] = $manufacturerId;
        } else {
            $json['success'] = false;
        }

        $this->sendResponse($json);
    }

    /*
    * Get languages list
    */

    protected function validateManufacturerForm($data)
    {

        $error = false;

        if (isset($data["name"])) {
            if ((utf8_strlen($data["name"]) < 2) || (utf8_strlen($data["name"]) > 255)) {
                $error = true;
            }
        } else {
            $error = true;
        }

        if (isset($data["sort_order"])) {
            if ((utf8_strlen($data["sort_order"]) < 1) || (utf8_strlen($data["sort_order"]) > 255)) {
                $error = true;
            }
        } else {
            $error = true;
        }

        if (!$error) {
            return true;
        } else {
            return false;
        }
    }

    /*
    * Get language details
    */

    private function _addManufacturer($data)
    {
        $this->db->query("INSERT INTO " . DB_PREFIX . "manufacturer SET name = '" . $this->db->escape($data['name']) . "', sort_order = '" . (int)$data['sort_order'] . "'");

        $manufacturer_id = $this->db->getLastId();

        if (isset($data['image'])) {
            $this->db->query("UPDATE " . DB_PREFIX . "manufacturer SET image = '" . $this->db->escape(html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8')) . "' WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
        }

        if (isset($data['manufacturer_store'])) {
            foreach ($data['manufacturer_store'] as $store_id) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "manufacturer_to_store SET manufacturer_id = '" . (int)$manufacturer_id . "', store_id = '" . (int)$store_id . "'");
            }
        }

        if ($data['keyword']) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'manufacturer_id=" . (int)$manufacturer_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
        }

        $this->cache->delete('manufacturer');

        return (int)$manufacturer_id;
    }

    /*
    * STORE FUNCTIONS
    */

    public function updateManufacturer($id, $data)
    {

        $json = array('success' => false);

        $this->load->model('catalog/manufacturer');


        if (ctype_digit($id)) {
            if ($this->validateManufacturerForm($data)) {
                $result = $this->model_catalog_manufacturer->getManufacturer($id);

                if (!empty($result)) {
                    $json['success'] = true;
                    $this->_editManufacturer($id, $data);
                } else {
                    $json['success'] = false;
                    $json['error'] = "The specified manufacturer does not exist.";
                }

            } else {
                $json['success'] = false;
            }
        } else {
            $json['success'] = false;
        }

        $this->sendResponse($json);
    }

    /*
    * Get stores list
    */

    private function _editManufacturer($manufacturer_id, $data)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "manufacturer SET name = '" . $this->db->escape($data['name']) . "', sort_order = '" . (int)$data['sort_order'] . "' WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");

        if (isset($data['image'])) {
            $this->db->query("UPDATE " . DB_PREFIX . "manufacturer SET image = '" . $this->db->escape(html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8')) . "' WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
        }

        $this->db->query("DELETE FROM " . DB_PREFIX . "manufacturer_to_store WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");

        if (isset($data['manufacturer_store'])) {
            foreach ($data['manufacturer_store'] as $store_id) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "manufacturer_to_store SET manufacturer_id = '" . (int)$manufacturer_id . "', store_id = '" . (int)$store_id . "'");
            }
        }

        $this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'manufacturer_id=" . (int)$manufacturer_id . "'");

        if ($data['keyword']) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'manufacturer_id=" . (int)$manufacturer_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
        }

        $this->cache->delete('manufacturer');
    }

    public function deleteManufacturer($id)
    {

        $json['success'] = false;

        $this->load->model('catalog/manufacturer');

        if (ctype_digit($id)) {
            if ($this->validateManufacturerDelete($id)) {

                $result = $this->model_catalog_manufacturer->getManufacturer($id);

                if (!empty($result)) {
                    $json['success'] = true;
                    $this->_deleteManufacturer($id);
                } else {
                    $json['success'] = false;
                    $json['error'] = "The specified manufacturer does not exist.";
                }

            } else {
                $json['success'] = false;
                $json['error'] = "Some products belong to this manufacturer";
            }
        } else {
            $json['success'] = false;
        }

        $this->sendResponse($json);
    }

    /*
    * Get store details
    */

    protected function validateManufacturerDelete($manufacturer_id)
    {

        $error = false;

        $this->load->model('catalog/product');

        $product_total = $this->_getTotalProductsByManufacturerId($manufacturer_id);

        if ($product_total) {
            $error = true;
        }

        if (!$error) {
            return true;
        } else {
            return false;
        }
    }


    /*
    * COUNTRY FUNCTIONS
    */

    private function _getTotalProductsByManufacturerId($manufacturer_id)
    {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
        return $query->row['total'];
    }

    /*
    * Get countries
    */

    private function _deleteManufacturer($manufacturer_id)
    {
        $this->db->query("DELETE FROM " . DB_PREFIX . "manufacturer WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "manufacturer_to_store WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'manufacturer_id=" . (int)$manufacturer_id . "'");

        $this->cache->delete('manufacturer');
    }

    /*
    * Get country details
    */

    public function orders()
    {

        $this->checkPlugin();

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            //get order details
            if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])) {
                $this->getOrder($this->request->get['id']);
            } else {
                //get orders list
                $this->listOrders();
            }
        } else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            //update order data
            $requestjson = file_get_contents('php://input');

            $requestjson = json_decode($requestjson, true);

            if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])
                && !empty($requestjson)) {
                $this->updateOrder($this->request->get['id'], $requestjson);
            } else {
                $this->response->setOutput(json_encode(array('success' => false)));
            }


        } else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            //delete order
            if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])) {
                $this->deleteOrder($this->request->get['id']);
            } else {
                $this->response->setOutput(json_encode(array('success' => false)));
            }
        }
    }

    public function getOrder($order_id)
    {

        $this->load->model('checkout/order');
        $this->load->model('account/order');

        $json = array('success' => true);

        if (ctype_digit($order_id)) {
            $order_info = $this->model_checkout_order->getOrder($order_id);

            if (!empty($order_info)) {
                $json['success'] = true;
                $json['data'] = $this->getOrderDetailsToOrder($order_info);

            } else {
                $json['success'] = false;
                $json['error'] = "The specified order does not exist.";

            }
        } else {
            $json['success'] = false;
            $json['error'] = "Invalid order id";

        }

        $this->sendResponse($json);
    }

    /*
    * SESSION FUNCTIONS
    */

    private function getOrderDetailsToOrder($order_info)
    {

        $this->load->model('catalog/product');

        $orderData = array();

        if (!empty($order_info)) {
            foreach ($order_info as $key => $value) {
                $orderData[$key] = $value;
            }

            $orderData['products'] = array();

            $products = $this->model_account_order->getOrderProducts($orderData['order_id']);

            foreach ($products as $product) {
                $option_data = array();

                $options = $this->_getOrderOptionsMod($orderData['order_id'], $product['order_product_id']);

                foreach ($options as $option) {
                    if ($option['type'] != 'file') {
                        $option_data[] = array(
                            'name' => $option['name'],
                            'value' => $option['value'],
                            'type' => $option['type'],
                            'product_option_id' => isset($option['product_option_id']) ? $option['product_option_id'] : "",
                            'product_option_value_id' => isset($option['product_option_value_id']) ? $option['product_option_value_id'] : "",
                            'option_id' => isset($option['option_id']) ? $option['option_id'] : "",
                            'option_value_id' => isset($option['option_value_id']) ? $option['option_value_id'] : ""
                        );
                    } else {
                        $option_data[] = array(
                            'name' => $option['name'],
                            'value' => utf8_substr($option['value'], 0, utf8_strrpos($option['value'], '.')),
                            'type' => $option['type']
                        );
                    }
                }

                $origProduct = $this->model_catalog_product->getProduct($product['product_id']);

                $orderData['products'][] = array(
                    'order_product_id' => $product['order_product_id'],
                    'product_id' => $product['product_id'],
                    'name' => $product['name'],
                    'model' => $product['model'],
                    'sku' => (!empty($origProduct['sku']) ? $origProduct['sku'] : ""),
                    'option' => $option_data,
                    'quantity' => $product['quantity'],
                    'price' => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
                    'total' => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value'])
                );
            }
        }

        return $orderData;
    }

    /*
    * Get current session id
    */

    private function _getOrderOptionsMod($order_id, $order_product_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option 
 LEFT JOIN " . DB_PREFIX . "product_option_value pov ON (" . DB_PREFIX . "order_option.product_option_value_id = pov.product_option_value_id)
WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product_id . "'");

        return $query->rows;
    }


    /*
    * FEATURED PRODUCTS FUNCTIONS
    */

    public function listOrders()
    {

        $json = array('success' => true);


        $this->load->model('account/order');

        /*check offset parameter*/
        if (isset($this->request->get['offset']) && $this->request->get['offset'] != "" && ctype_digit($this->request->get['offset'])) {
            $offset = $this->request->get['offset'];
        } else {
            $offset = 0;
        }

        /*check limit parameter*/
        if (isset($this->request->get['limit']) && $this->request->get['limit'] != "" && ctype_digit($this->request->get['limit'])) {
            $limit = $this->request->get['limit'];
        } else {
            $limit = 10000;
        }

        /*get all orders of user*/
        $results = $this->_getAllOrders($offset, $limit);

        $orders = array();

        if (count($results)) {
            foreach ($results as $result) {

                $product_total = $this->model_account_order->getTotalOrderProductsByOrderId($result['order_id']);
                $voucher_total = $this->model_account_order->getTotalOrderVouchersByOrderId($result['order_id']);

                $orders[] = array(
                    'order_id' => $result['order_id'],
                    'name' => $result['firstname'] . ' ' . $result['lastname'],
                    'status' => $result['status'],
                    'date_added' => $result['date_added'],
                    'products' => ($product_total + $voucher_total),
                    'total' => $result['total'],
                    'currency_code' => $result['currency_code'],
                    'currency_value' => $result['currency_value'],
                );
            }

            if (count($orders) == 0) {
                $json['success'] = false;
                $json['error'] = "No orders found";
            } else {
                $json['data'] = $orders;
            }

        } else {
            $json['error'] = "No orders found";
            $json['success'] = false;
        }

        $this->sendResponse($json);
    }

    /*
    * Get featured products
    */

    private function _getAllOrders($start = 0, $limit = 20)
    {
        if ($start < 0) {
            $start = 0;
        }

        if ($limit < 1) {
            $limit = 1;
        }

        $query = $this->db->query("SELECT o.order_id, o.firstname, o.lastname, os.name as status, o.date_added, o.total, o.currency_code, o.currency_value FROM `" . DB_PREFIX . "order` o LEFT JOIN " . DB_PREFIX . "order_status os ON (o.order_status_id = os.order_status_id) WHERE o.order_status_id > '0' AND os.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY o.order_id DESC LIMIT " . (int)$start . "," . (int)$limit);

        return $query->rows;
    }

    /*
    * PRODUCT IMAGE MANAGEMENT FUNCTIONS
    */
//    public function productimages() {
//
//        $this->checkPlugin();
//
//        if ( $_SERVER['REQUEST_METHOD'] === 'POST' ){
//            //upload and save image
//            if (!empty($this->request->get['other']) && $this->request->get['other'] == 1) {
//                $this->addProductImage($this->request);
//            } else {
//                $this->updateProductImage($this->request);
//            }
//        }
//    }

    public function updateOrder($id, $data)
    {


        $json = array('success' => false);

        $this->load->model('checkout/order');
        $this->load->model('sale/order');

        if (ctype_digit($id)) {

            if (isset($data['status']) && ctype_digit($data['status'])) {

                $result = $this->model_checkout_order->getOrder($id);
                if (!empty($result)) {
                    $json['success'] = true;
                    $this->model_sale_order->addOrderHistory($id, $data['status']);
                } else {
                    $json['success'] = false;
                    $json['error'] = "The specified order does not exist.";
                }

            } else {
                $json['success'] = false;
            }
        } else {
            $json['success'] = false;
        }

        $this->sendResponse($json);
    }

    /*
    *  Upload and save product image
    */

    public function deleteOrder($id)
    {

        $json['success'] = false;

        $this->load->model('checkout/order');

        if (ctype_digit($id)) {
            $result = $this->model_checkout_order->getOrder($id);

            if (!empty($result)) {
                $json['success'] = true;
                $this->_deleteOrder($id);
            } else {
                $json['success'] = false;
                $json['error'] = "The specified order does not exist.";
            }

        } else {
            $json['success'] = false;
        }

        $this->sendResponse($json);
    }

    /*
    * Upload and save product image
    */

    private function _deleteOrder($order_id)
    {
        $order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE order_status_id > '0' AND order_id = '" . (int)$order_id . "'");

        if ($order_query->num_rows) {
            $product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");

            foreach ($product_query->rows as $product) {
                $this->db->query("UPDATE `" . DB_PREFIX . "product` SET quantity = (quantity + " . (int)$product['quantity'] . ") WHERE product_id = '" . (int)$product['product_id'] . "' AND subtract = '1'");

                $option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$product['order_product_id'] . "'");

                foreach ($option_query->rows as $option) {
                    $this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET quantity = (quantity + " . (int)$product['quantity'] . ") WHERE product_option_value_id = '" . (int)$option['product_option_value_id'] . "' AND subtract = '1'");
                }
            }
        }

        $this->db->query("DELETE FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$order_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "order_download WHERE order_id = '" . (int)$order_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "order_voucher WHERE order_id = '" . (int)$order_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "order_history WHERE order_id = '" . (int)$order_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "order_fraud WHERE order_id = '" . (int)$order_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "customer_transaction WHERE order_id = '" . (int)$order_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "customer_reward WHERE order_id = '" . (int)$order_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "affiliate_transaction WHERE order_id = '" . (int)$order_id . "'");

        if (VERSION != "1.5.4" && VERSION != "1.5.4.1" && VERSION != "1.5.3" && VERSION != "1.5.3.1" && VERSION != "1.5.5" && VERSION != "1.5.5.1") {
            $this->db->query("DELETE `or`, ort FROM " . DB_PREFIX . "order_recurring `or`, " . DB_PREFIX . "order_recurring_transaction ort WHERE order_id = '" . (int)$order_id . "' AND ort.order_recurring_id = `or`.order_recurring_id");
        }
    }

    /*
    * Upload and update product image
    */

    public function listorderswithdetails()
    {

        $this->checkPlugin();

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {

            $json = array('success' => true);


            $this->load->model('account/order');

            /*check limit parameter*/
            if (isset($this->request->get['limit']) && $this->request->get['limit'] != "" && ctype_digit($this->request->get['limit'])) {
                $limit = $this->request->get['limit'];
            } else {
                $limit = 100000;
            }

            if (isset($this->request->get['filter_date_added_from'])) {
                $date_added_from = date('Y-m-d H:i:s', strtotime($this->request->get['filter_date_added_from']));
                if ($this->validateDate($date_added_from)) {
                    $filter_date_added_from = $date_added_from;
                }
            } else {
                $filter_date_added_from = null;
            }

            if (isset($this->request->get['filter_date_added_on'])) {
                $date_added_on = date('Y-m-d', strtotime($this->request->get['filter_date_added_on']));
                if ($this->validateDate($date_added_on, 'Y-m-d')) {
                    $filter_date_added_on = $date_added_on;
                }
            } else {
                $filter_date_added_on = null;
            }


            if (isset($this->request->get['filter_date_added_to'])) {
                $date_added_to = date('Y-m-d H:i:s', strtotime($this->request->get['filter_date_added_to']));
                if ($this->validateDate($date_added_to)) {
                    $filter_date_added_to = $date_added_to;
                }
            } else {
                $filter_date_added_to = null;
            }

            if (isset($this->request->get['filter_date_modified_on'])) {
                $date_modified_on = date('Y-m-d', strtotime($this->request->get['filter_date_modified_on']));
                if ($this->validateDate($date_modified_on, 'Y-m-d')) {
                    $filter_date_modified_on = $date_modified_on;
                }
            } else {
                $filter_date_modified_on = null;
            }

            if (isset($this->request->get['filter_date_modified_from'])) {
                $date_modified_from = date('Y-m-d H:i:s', strtotime($this->request->get['filter_date_modified_from']));
                if ($this->validateDate($date_modified_from)) {
                    $filter_date_modified_from = $date_modified_from;
                }
            } else {
                $filter_date_modified_from = null;
            }

            if (isset($this->request->get['filter_date_modified_to'])) {
                $date_modified_to = date('Y-m-d H:i:s', strtotime($this->request->get['filter_date_modified_to']));
                if ($this->validateDate($date_modified_to)) {
                    $filter_date_modified_to = $date_modified_to;
                }
            } else {
                $filter_date_modified_to = null;
            }

            if (isset($this->request->get['page'])) {
                $page = $this->request->get['page'];
            } else {
                $page = 1;
            }

            if (isset($this->request->get['filter_order_status_id'])) {
                $filter_order_status_id = $this->request->get['filter_order_status_id'];
            } else {
                $filter_order_status_id = null;
            }

            $data = array(
                'filter_date_added_on' => $filter_date_added_on,
                'filter_date_added_from' => $filter_date_added_from,
                'filter_date_added_to' => $filter_date_added_to,
                'filter_date_modified_on' => $filter_date_modified_on,
                'filter_date_modified_from' => $filter_date_modified_from,
                'filter_date_modified_to' => $filter_date_modified_to,
                'filter_order_status_id' => $filter_order_status_id,
                'start' => ($page - 1) * $limit,
                'limit' => $limit
            );


            $results = $this->_getOrdersByFilter($data);
            /*get all orders*/
            //$results = $this->model_account_order->getAllOrders($offset, $limit);

            $orders = array();

            if (count($results)) {

                foreach ($results as $result) {

                    $orderData = $this->getOrderDetailsToOrder($result);

                    if (!empty($orderData)) {
                        $orders[] = $orderData;
                    }
                }

                if (count($orders) == 0) {
                    $json['success'] = false;
                    $json['error'] = "No orders found";
                } else {
                    $json['data'] = $orders;
                }

            } else {
                $json['error'] = "No orders found";
                $json['success'] = false;
            }
        } else {
            $json['success'] = false;
        }

        $this->sendResponse($json);
    }


    /*
    * CATEGORY IMAGE MANAGEMENT FUNCTIONS
    */

    private function _getOrdersByFilter($data = array())
    {
        $sql = "SELECT o.*, CONCAT(o.firstname, ' ', o.lastname) AS customer,
				            payment_country.iso_code_2 as pc_iso_code_2,
				            payment_country.iso_code_3 as pc_iso_code_3,
                            shipping_country.iso_code_2 as sc_iso_code_2,
				            shipping_country.iso_code_3 as sc_iso_code_3,
				            payment_zone.code as payment_zone_code,
				            shipping_zone.code as shipping_zone_code

				        FROM `" . DB_PREFIX . "order` o
				        LEFT JOIN `" . DB_PREFIX . "country` payment_country ON ( payment_country.country_id = o.payment_country_id)
				        LEFT JOIN `" . DB_PREFIX . "country` shipping_country ON ( shipping_country.country_id = o.shipping_country_id)
				        LEFT JOIN `" . DB_PREFIX . "zone` payment_zone ON ( payment_zone.zone_id = o.payment_zone_id)
				        LEFT JOIN `" . DB_PREFIX . "zone` shipping_zone ON ( shipping_zone.zone_id = o.shipping_zone_id)
				                    ";

        if (isset($data['filter_order_status_id']) && !is_null($data['filter_order_status_id'])) {
            $sql .= " WHERE o.order_status_id IN ( " . $this->db->escape(rtrim($data['filter_order_status_id'], ",")) . ")";
        } else {
            $sql .= " WHERE o.order_status_id > '0'";
        }

        if (!empty($data['filter_order_id'])) {
            $sql .= " AND o.order_id = '" . (int)$data['filter_order_id'] . "'";
        }

        if (!empty($data['filter_customer'])) {
            $sql .= " AND CONCAT(o.firstname, ' ', o.lastname) LIKE '%" . $this->db->escape($data['filter_customer']) . "%'";
        }

        if (!empty($data['filter_date_added_to']) && !empty($data['filter_date_added_from'])) {

            $sql .= " AND o.date_added BETWEEN STR_TO_DATE('" . $this->db->escape($data['filter_date_added_from']) . "','%Y-%m-%d %H:%i:%s') AND STR_TO_DATE('" . $this->db->escape($data['filter_date_added_to']) . "','%Y-%m-%d %H:%i:%s')";

        } elseif (!empty($data['filter_date_added_from'])) {

            $sql .= " AND o.date_added >= STR_TO_DATE('" . $this->db->escape($data['filter_date_added_from']) . "','%Y-%m-%d %H:%i:%s')";

        } elseif (!empty($data['filter_date_added_on'])) {

            $sql .= " AND DATE(o.date_added) = DATE('" . $this->db->escape($data['filter_date_added_on']) . "')";
        }

        if (!empty($data['filter_date_modified_to']) && !empty($data['filter_date_modified_from'])) {

            $sql .= " AND o.date_modified BETWEEN STR_TO_DATE('" . $this->db->escape($data['filter_date_modified_from']) . "','%Y-%m-%d %H:%i:%s') AND STR_TO_DATE('" . $this->db->escape($data['filter_date_modified_to']) . "','%Y-%m-%d %H:%i:%s')";

        } elseif (!empty($data['filter_date_modified_from'])) {

            $sql .= " AND o.date_modified >= STR_TO_DATE('" . $this->db->escape($data['filter_date_modified_from']) . "','%Y-%m-%d %H:%i:%s')";

        } elseif (!empty($data['filter_date_modified_on'])) {

            $sql .= " AND DATE(o.date_modified) = DATE('" . $this->db->escape($data['filter_date_modified_on']) . "')";
        }


        if (!empty($data['filter_total'])) {
            $sql .= " AND o.total = '" . (float)$data['filter_total'] . "'";
        }

        $sort_data = array(
            'o.order_id',
            'customer',
            'o.date_added',
            'o.date_modified',
            'o.total'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY o.order_id";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $orders_query = $this->db->query($sql);
        $orders = array();
        $index = 0;

        $this->load->model('localisation/language');

        foreach ($orders_query->rows as $order) {

            $payment_iso_code_2 = '';
            $payment_iso_code_3 = '';

            if (isset($order["pc_iso_code_2"])) {
                $payment_iso_code_2 = $order["pc_iso_code_2"];
            }

            if (isset($order["pc_iso_code_3"])) {
                $payment_iso_code_3 = $order["pc_iso_code_3"];
            }

            $shipping_iso_code_2 = '';
            $shipping_iso_code_3 = '';

            if (isset($order["sc_iso_code_2"])) {
                $shipping_iso_code_2 = $order["sc_iso_code_2"];
            }

            if (isset($order["sc_iso_code_3"])) {
                $shipping_iso_code_3 = $order["sc_iso_code_3"];
            }

            if (isset($order["payment_zone_code"])) {
                $payment_zone_code = $order["payment_zone_code"];
            } else {
                $payment_zone_code = '';
            }

            if (isset($order["shipping_zone_code"])) {
                $shipping_zone_code = $order["shipping_zone_code"];
            } else {
                $shipping_zone_code = '';
            }


            $language_info = $this->model_localisation_language->getLanguage($order['language_id']);

            if ($language_info) {
                $language_code = $language_info['code'];
                $language_filename = $language_info['filename'];
                $language_directory = $language_info['directory'];
            } else {
                $language_code = '';
                $language_filename = '';
                $language_directory = '';
            }

            $orders[$index] = array(
                'order_id' => $order['order_id'],
                'invoice_no' => $order['invoice_no'],
                'invoice_prefix' => $order['invoice_prefix'],
                'store_id' => $order['store_id'],
                'store_name' => $order['store_name'],
                'store_url' => $order['store_url'],
                'customer_id' => $order['customer_id'],
                'firstname' => $order['firstname'],
                'lastname' => $order['lastname'],
                'telephone' => $order['telephone'],
                'fax' => $order['fax'],
                'email' => $order['email'],
                'payment_firstname' => $order['payment_firstname'],
                'payment_lastname' => $order['payment_lastname'],
                'payment_company' => $order['payment_company'],
                'payment_company_id' => $order['payment_company_id'],
                'payment_tax_id' => $order['payment_tax_id'],
                'payment_address_1' => $order['payment_address_1'],
                'payment_address_2' => $order['payment_address_2'],
                'payment_postcode' => $order['payment_postcode'],
                'payment_city' => $order['payment_city'],
                'payment_zone_id' => $order['payment_zone_id'],
                'payment_zone' => $order['payment_zone'],
                'payment_zone_code' => $payment_zone_code,
                'payment_country_id' => $order['payment_country_id'],
                'payment_country' => $order['payment_country'],
                'payment_iso_code_2' => $payment_iso_code_2,
                'payment_iso_code_3' => $payment_iso_code_3,
                'payment_address_format' => $order['payment_address_format'],
                'payment_method' => $order['payment_method'],
                'payment_code' => $order['payment_code'],
                'shipping_firstname' => $order['shipping_firstname'],
                'shipping_lastname' => $order['shipping_lastname'],
                'shipping_company' => $order['shipping_company'],
                'shipping_address_1' => $order['shipping_address_1'],
                'shipping_address_2' => $order['shipping_address_2'],
                'shipping_postcode' => $order['shipping_postcode'],
                'shipping_city' => $order['shipping_city'],
                'shipping_zone_id' => $order['shipping_zone_id'],
                'shipping_zone' => $order['shipping_zone'],
                'shipping_zone_code' => $shipping_zone_code,
                'shipping_country_id' => $order['shipping_country_id'],
                'shipping_country' => $order['shipping_country'],
                'shipping_iso_code_2' => $shipping_iso_code_2,
                'shipping_iso_code_3' => $shipping_iso_code_3,
                'shipping_address_format' => $order['shipping_address_format'],
                'shipping_method' => $order['shipping_method'],
                'shipping_code' => $order['shipping_code'],
                'comment' => $order['comment'],
                'total' => $order['total'],
                'order_status_id' => $order['order_status_id'],
                'language_id' => $order['language_id'],
                'language_code' => $language_code,
                'language_filename' => $language_filename,
                'language_directory' => $language_directory,
                'currency_id' => $order['currency_id'],
                'currency_code' => $order['currency_code'],
                'currency_value' => $order['currency_value'],
                'ip' => $order['ip'],
                'forwarded_ip' => $order['forwarded_ip'],
                'user_agent' => $order['user_agent'],
                'accept_language' => $order['accept_language'],
                'date_modified' => $order['date_modified'],
                'date_added' => $order['date_added']
            );
            $index++;
        }

        return $orders;
    }

    /*
    * Upload and save category image
    */

    public function userorders()
    {

        $this->checkPlugin();

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {

            $json = array('success' => true);

            $user = null;

            /*check user parameter*/
            if (isset($this->request->get['user']) && $this->request->get['user'] != "" && ctype_digit($this->request->get['user'])) {
                $user = $this->request->get['user'];
            } else {
                $json['success'] = false;
            }

            if ($json['success'] == true) {
                $orderData['orders'] = array();

                $this->load->model('account/order');

                /*get all orders of user*/
                $results = $this->_getOrdersByUser($user);

                $orders = array();

                foreach ($results as $result) {

                    $product_total = $this->model_account_order->getTotalOrderProductsByOrderId($result['order_id']);
                    $voucher_total = $this->model_account_order->getTotalOrderVouchersByOrderId($result['order_id']);

                    $orders[] = array(
                        'order_id' => $result['order_id'],
                        'name' => $result['firstname'] . ' ' . $result['lastname'],
                        'status' => $result['status'],
                        'date_added' => $result['date_added'],
                        'products' => ($product_total + $voucher_total),
                        'total' => $result['total'],
                        'currency_code' => $result['currency_code'],
                        'currency_value' => $result['currency_value'],
                    );
                }

                if (count($orders) == 0) {
                    $json['success'] = false;
                    $json['error'] = "No orders found";
                } else {
                    $json['data'] = $orders;
                }
            } else {
                $json['success'] = false;
            }
        }

        $this->sendResponse($json);
    }

    /*
* GET UTC AND LOCAL TIME DIFFERENCE
    * returns offset in seconds
*/

    private function _getOrdersByUser($customer_id)
    {

        $query = $this->db->query("SELECT o.order_id, o.firstname, o.lastname, os.name as status, o.date_added, o.total, o.currency_code, o.currency_value FROM `" . DB_PREFIX . "order` o LEFT JOIN " . DB_PREFIX . "order_status os ON (o.order_status_id = os.order_status_id) WHERE o.customer_id = '" . (int)$customer_id . "' AND o.order_status_id > '0' AND os.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY o.order_id DESC");

        return $query->rows;
    }

    /*
    * MANUFACTURER IMAGE MANAGEMENT FUNCTIONS
    */

    public function customers()
    {

        $this->checkPlugin();

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            //get customer details
            if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])) {
                $this->getCustomer($this->request->get['id']);
            } else {
                //get customers list
                $this->listCustomers();
            }
        } else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            //update customer
            $requestjson = file_get_contents('php://input');

            $requestjson = json_decode($requestjson, true);

            if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])
                && !empty($requestjson)) {
                $this->updateCustomer($this->request->get['id'], $requestjson);
            } else {
                $this->response->setOutput(json_encode(array('success' => false)));
            }

        } else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            //delete customer
            if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])) {
                $this->deleteCustomer($this->request->get['id']);
            } else {
                $this->response->setOutput(json_encode(array('success' => false)));
            }
        }
    }

    /*
    * Upload and save manufacturer image
    */

    private function getCustomer($id)
    {

        $json = array('success' => true);

        $this->load->model('account/customer');

        if (ctype_digit($id)) {
            $customer = $this->model_account_customer->getCustomer($id);
            if (!empty($customer['customer_id'])) {
                $json['data'] = $this->getCustomerInfo($customer);
            } else {
                $json['success'] = false;
                $json['error'] = "The specified customer does not exist.";
            }
        } else {
            $json['success'] = false;
        }

        $this->sendResponse($json);
    }


    /*
    * Update products quantity
    */

    private function getCustomerInfo($customer)
    {
        // Custom Fields
        $this->load->model('account/custom_field');

        $custom_fields = $this->model_account_custom_field->getCustomFields($this->config->get('config_customer_group_id'));
        $account_custom_field = unserialize($customer['custom_field']);

        return array(
            'store_id' => $customer['store_id'],
            'customer_id' => $customer['customer_id'],
            'firstname' => $customer['firstname'],
            'lastname' => $customer['lastname'],
            'telephone' => $customer['telephone'],
            'fax' => $customer['fax'],
            'email' => $customer['email'],
            'account_custom_field' => $account_custom_field,
            'custom_fields' => $custom_fields

        );
    }

    /*
    * Update products quantity
    */

    private function listCustomers()
    {

        $json = array('success' => true);

        $this->load->model('account/customer');

        $results = $this->_getCustomersMod();

        $customers = array();

        foreach ($results as $customer) {
            $customers[] = $this->getCustomerInfo($customer);
        }

        if (count($customers) == 0) {
            $json['success'] = false;
            $json['error'] = "No customers found";
        } else {
            $json['data'] = $customers;
        }

        $this->sendResponse($json);
    }


    /*
    * Update order status by status name
    */

    private function _getCustomersMod($data = array())
    {
        $sql = "SELECT *, CONCAT(c.firstname, ' ', c.lastname) AS name, cgd.name AS customer_group FROM " . DB_PREFIX . "customer c LEFT JOIN " . DB_PREFIX . "customer_group_description cgd ON (c.customer_group_id = cgd.customer_group_id) WHERE cgd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

        $implode = array();

        if (!empty($data['filter_name'])) {
            $implode[] = "CONCAT(c.firstname, ' ', c.lastname) LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
        }

        if (!empty($data['filter_email'])) {
            $implode[] = "c.email LIKE '" . $this->db->escape($data['filter_email']) . "%'";
        }

        if (isset($data['filter_newsletter']) && !is_null($data['filter_newsletter'])) {
            $implode[] = "c.newsletter = '" . (int)$data['filter_newsletter'] . "'";
        }

        if (!empty($data['filter_customer_group_id'])) {
            $implode[] = "c.customer_group_id = '" . (int)$data['filter_customer_group_id'] . "'";
        }

        if (!empty($data['filter_ip'])) {
            $implode[] = "c.customer_id IN (SELECT customer_id FROM " . DB_PREFIX . "customer_ip WHERE ip = '" . $this->db->escape($data['filter_ip']) . "')";
        }

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $implode[] = "c.status = '" . (int)$data['filter_status'] . "'";
        }

        if (isset($data['filter_approved']) && !is_null($data['filter_approved'])) {
            $implode[] = "c.approved = '" . (int)$data['filter_approved'] . "'";
        }

        if (!empty($data['filter_date_added'])) {
            $implode[] = "DATE(c.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
        }

        if ($implode) {
            $sql .= " AND " . implode(" AND ", $implode);
        }

        $sort_data = array(
            'name',
            'c.email',
            'customer_group',
            'c.status',
            'c.approved',
            'c.ip',
            'c.date_added'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY name";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    /*
     *   Update order status by status name
    */

    private function updateCustomer($id, $data)
    {

        $json = array('success' => false);

        $this->load->model('account/customer');

        if ($this->validateCustomerForm($data)) {
            if (ctype_digit($id)) {
                $result = $this->model_account_customer->getCustomer($id);
                if (!empty($result)) {
                    $enableModification = true;

                    //if user wanted to change current password, we need to check not in use
                    if ($result['email'] != strtolower($data['email'])) {
                        $email_query = $this->db->query("SELECT `email` FROM " . DB_PREFIX . "customer WHERE LOWER(email) = '" . $this->db->escape(strtolower($data['email'])) . "'");
                        /*check email not used*/
                        if ($email_query->num_rows > 0) {
                            $enableModification = false;
                            $json['error'] = "The email is already used";
                        }
                    }
                    if ($enableModification) {
                        $json['success'] = true;
                        $this->_editCustomerById($id, $data);
                    }
                } else {
                    $json['success'] = false;
                    $json['error'] = "The specified customer does not exist.";
                }
            } else {
                $json['success'] = false;
            }
        } else {
            $json['success'] = false;
        }

        $this->sendResponse($json);
    }

    private function validateCustomerForm($data)
    {

        $error = false;

        if ((utf8_strlen($data['firstname']) < 2) || (utf8_strlen($data['firstname']) > 255)) {
            $error = true;
        }

        if ((utf8_strlen($data['lastname']) < 2) || (utf8_strlen($data['lastname']) > 255)) {
            $error = true;
        }

        if ((utf8_strlen($data['email']) < 2) || (utf8_strlen($data['email']) > 255) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $error = true;
        }

        if (!$error) {
            return true;
        } else {
            return false;
        }
    }

    //Image upload

    private function _editCustomerById($customer_id, $data)
    {

        $this->db->query("UPDATE " . DB_PREFIX . "customer SET firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', fax = '" . $this->db->escape($data['fax']) . "' WHERE customer_id = '" . (int)$customer_id . "'");
    }

    public function deleteCustomer($id)
    {

        $json['success'] = false;

        $this->load->model('account/customer');

        if (ctype_digit($id)) {
            $result = $this->model_account_customer->getCustomer($id);
            if (!empty($result)) {
                $json['success'] = true;
                $this->_deleteCustomer($id);
            } else {
                $json['success'] = false;
                $json['error'] = "The specified customer does not exist.";
            }
        } else {
            $json['success'] = false;
            $json['error'] = "Invalid id";
        }

        if ($this->debugIt) {
            echo '<pre>';
            print_r($json);
            echo '</pre>';
        } else {
            $this->response->setOutput(json_encode($json));
        }
    }

    /*
     * Makes directory and returns BOOL(TRUE) if exists OR made.
     */

    private function _deleteCustomer($customer_id)
    {
        $this->db->query("DELETE FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$customer_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "customer_reward WHERE customer_id = '" . (int)$customer_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "customer_transaction WHERE customer_id = '" . (int)$customer_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "customer_ip WHERE customer_id = '" . (int)$customer_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "address WHERE customer_id = '" . (int)$customer_id . "'");
    }

    //date format validator

    public function languages()
    {

        $this->checkPlugin();

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            //get language details
            if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])) {
                $this->getLanguage($this->request->get['id']);
            } else {
                //get languages list
                $this->listLanguages();
            }
        }
    }

    //Product functions

    private function getLanguage($id)
    {

        $json = array('success' => true);

        $this->load->model('localisation/language');

        if (ctype_digit($id)) {
            $result = $this->model_localisation_language->getLanguage($id);
        } else {
            $json['success'] = false;
            $json['error'] = "Not valid id";
        }

        if (!empty($result)) {
            $json['data'] = array(
                'language_id' => $result['language_id'],
                'name' => $result['name'],
                'code' => $result['code'],
                'locale' => $result['locale'],
                'image' => $result['image'],
                'directory' => $result['directory'],
                'filename' => $result['filename'],
                'sort_order' => $result['sort_order'],
                'status' => $result['status']
            );
        } else {
            $json['success'] = false;
            $json['error'] = "The specified language does not exist.";
        }

        $this->sendResponse($json);
    }

    private function listLanguages()
    {

        $json = array('success' => true);

        $this->load->model('localisation/language');

        $languages = $this->model_localisation_language->getLanguages();

        if (count($languages) == 0) {
            $json['success'] = false;
            $json['error'] = "No language found";
        } else {
            $json['data'] = $languages;
        }

        $this->sendResponse($json);
    }

    public function order_statuses()
    {

        $this->checkPlugin();

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            //get order statuses list
            $this->listOrderStatuses();
        }
    }

    private function listOrderStatuses()
    {

        $json = array('success' => true);

        $this->load->model('account/order');

        $statuses = $this->_getOrderStatuses();

        if (count($statuses) == 0) {
            $json['success'] = false;
            $json['error'] = "No order status found";
        } else {
            $json['data'] = $statuses;
        }

        if ($this->debugIt) {
            echo '<pre>';
            print_r($json);
        } else {
            $this->response->setOutput(json_encode($json));
        }
    }

    private function _getOrderStatuses()
    {

        $query = $this->db->query("SELECT order_status_id, name FROM " . DB_PREFIX . "order_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY name");

        return $query->rows;
    }

    public function stores()
    {

        $this->checkPlugin();

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            //get store details
            if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])) {
                $this->getStore($this->request->get['id']);
            } else {
                //get stores list
                $this->listStores();
            }
        }
    }

    private function getStore($id)
    {

        $json = array('success' => true);

        $this->load->model('checkout/order');

        if (ctype_digit($id)) {
            $result = $this->_getStore($id);
        } else {
            $json['success'] = false;
        }

        if (isset($result['store_id'])) {
            $json['data'] = array(
                'store_id' => $result['store_id'],
                'name' => $result['name']
            );
        } else {
            $json['success'] = false;
            $json['error'] = "The specified store does not exist.";
        }

        $this->sendResponse($json);
    }

    private function _getStore($store_id)
    {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "store WHERE store_id = '" . (int)$store_id . "'");

        return $query->row;
    }

    private function listStores()
    {

        $json = array('success' => true);

        $this->load->model('checkout/order');

        $results = $this->_getStores();

        $stores = array();

        foreach ($results as $result) {
            $stores[] = array(
                'store_id' => $result['store_id'],
                'name' => $result['name']
            );
        }

        $default_store = array(
            'store_id' => 0,
            'name' => $this->config->get('config_name')
        );

        $data = array_merge($default_store, $stores);

        if (count($data) == 0) {
            $json['success'] = false;
            $json['error'] = "No store found";
        } else {
            $json['data'] = $data;
        }

        $this->sendResponse($json);
    }

    private function _getStores($data = array())
    {
        $store_data = $this->cache->get('store');

        if (!$store_data) {
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "store ORDER BY url");

            $store_data = $query->rows;

            $this->cache->set('store', $store_data);
        }

        return $store_data;
    }

    public function get_store_info()
    {
        $json = array('success' => true);
        $json['data'] = array(
            'module_version' => 1.9,
            'opencart_version' => VERSION,
            'name' => $this->config->get('config_name'),
            'owner' => $this->config->get('config_owner'),
            'email' => $this->config->get('config_email'),
        );
        $this->sendResponse($json);
    }

    public function countries()
    {

        $this->checkPlugin();

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            //get country details
            if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])) {
                $this->getCountry($this->request->get['id']);
            } else {
                $this->listCountries();
            }
        }
    }

    public function getCountry($country_id)
    {

        $json = array('success' => true);

        $this->load->model('localisation/country');

        $country_info = $this->model_localisation_country->getCountry($country_id);

        if (!empty($country_info)) {
            $json["data"] = $this->getCountryInfo($country_info);
        } else {
            $json['success'] = false;
            $json['error'] = "The specified country does not exist.";
        }

        $this->sendResponse($json);
    }

    private function getCountryInfo($country_info, $addZone = true)
    {
        $this->load->model('localisation/zone');
        $info = array(
            'country_id' => $country_info['country_id'],
            'name' => $country_info['name'],
            'iso_code_2' => $country_info['iso_code_2'],
            'iso_code_3' => $country_info['iso_code_3'],
            'address_format' => $country_info['address_format'],
            'postcode_required' => $country_info['postcode_required'],
            'status' => $country_info['status']
        );
        if ($addZone) {
            $info['zone'] = $this->model_localisation_zone->getZonesByCountryId($country_info['country_id']);
        }

        return $info;
    }

    private function listCountries()
    {

        $json = array('success' => true);

        $this->load->model('localisation/country');

        $results = $this->model_localisation_country->getCountries();

        $data = array();

        foreach ($results as $country) {
            $data[] = $this->getCountryInfo($country, false);
        }

        if (count($results) == 0) {
            $json['success'] = false;
            $json['error'] = "No country found";
        } else {
            $json['data'] = $data;
        }

        $this->sendResponse($json);
    }

    public function session()
    {

        $this->checkPlugin();

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            //get session details
            $this->getSessionId();
        }
    }

    public function getSessionId()
    {

        $json = array('success' => true);

        $json['data'] = array('session' => session_id());

        $this->sendResponse($json);
    }

    public function featured()
    {

        $this->checkPlugin();

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            //get featured products
            $limit = 0;

            if (isset($this->request->get['limit']) && ctype_digit($this->request->get['limit']) && $this->request->get['limit'] > 0) {
                $limit = $this->request->get['limit'];
            }

            $this->getFeaturedProducts($limit);
        }
    }

    public function getFeaturedProducts($limit)
    {

        $json = array('success' => true);

        $this->load->model('catalog/product');

        $this->load->model('tool/image');

        $products = explode(',', $this->config->get('featured_product'));

        if ($limit) {
            $products = array_slice($products, 0, (int)$limit);
        }

        foreach ($products as $product_id) {
            $product_info = $this->model_catalog_product->getProduct($product_id);

            if ($product_info) {
                if ($product_info['image']) {
                    $image = $this->model_tool_image->resize($product_info['image'], 500, 500);
                } else {
                    $image = false;
                }

                if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                    $price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')));
                } else {
                    $price = false;
                }

                if ((float)$product_info['special']) {
                    $special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')));
                } else {
                    $special = false;
                }

                if ($this->config->get('config_review_status')) {
                    $rating = $product_info['rating'];
                } else {
                    $rating = false;
                }

                $json['data'][] = array(
                    'product_id' => $product_info['product_id'],
                    'thumb' => $image,
                    'name' => $product_info['name'],
                    'price' => $price,
                    'special' => $special,
                    'rating' => $rating
                );
            }
        }

        $this->sendResponse($json);
    }

    public function productimages()
    {

        $this->checkPlugin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            //upload and save image
            $this->saveProductImage($this->request);
        }
    }

    public function saveProductImage($request)
    {

        $json = array('success' => false);
        $this->load->model('catalog/product');
        if (ctype_digit($request->get['id'])) {
            $product = $this->model_catalog_product->getProduct((int)$request->get['id']);
            //check product exists
            if (!empty($product)) {
                if (!isset($request->files['file']) && !isset($request->post['src']))
                    $json['error'] = "File is required";
                else {
                    $from_url = 0;
                    if (isset($request->post['src'])) {
                        $from_url = 1;
                        $temp_path = dirname(realpath(__FILE__)) . "/temp_images";
                        if (!file_exists($temp_path))
                            mkdir($temp_path);
                        $file_url = $request->post['src'];
                        $path_file = $temp_path . '/' . basename($file_url);
                        copy($file_url, $path_file);
                        $request_file = array('name' => basename($file_url), 'type' => 'image/png', 'tmp_name' => $path_file, 'error' => '0');
                    } elseif (isset($request->files['file']))
                        $request_file = $request->files['file'];

                    $uploadResult = $this->upload($request_file, "products");
                    if ($from_url == 1)
                        unlink($path_file);
                    if (!isset($uploadResult['error'])) {
                        $json['success'] = true;
                        if ($request->post['default'] == '1')
                            $json['data'] = $this->_setProductImage($request->get['id'], $uploadResult['file_path']);
                        else
                            $json['data'] = $this->_addProductImages($request->get['id'], $uploadResult['file_path']);
                    } else
                        $json['error'] = $uploadResult['error'];
                }
            } else {
                $json['success'] = false;
                $json['error'] = "The specified product does not exist.";
            }
        } else {
            $json['success'] = false;
        }

        if ($this->debugIt) {
            echo '<pre>';
            print_r($json);
            echo '</pre>';
        } else {
            $this->response->setOutput(json_encode($json));
        }
    }

    public function upload($uploadedFile, $subdirectory)
    {
        $this->language->load('product/product');

        $result = array();

        if (!empty($uploadedFile['name'])) {
            $filename = basename(preg_replace('/[^a-zA-Z0-9\.\-\s+]/', '', html_entity_decode($uploadedFile['name'], ENT_QUOTES, 'UTF-8')));

            // Allowed file extension types
            $allowed = array();

            $filetypes = explode("\n", $this->config->get('config_file_extension_allowed'));

            foreach ($filetypes as $filetype) {
                $allowed[] = trim($filetype);
            }
            $allowed = array('txt', 'png', 'jpe', 'jpeg', 'jpg', 'gif', 'bmp', 'ico', 'tiff', 'tif', 'svg', 'svgz', 'zip', 'rar', 'msi', 'cab', 'mp3', 'qt', 'mov', 'pdf', 'psd', 'ai', 'eps', 'ps', 'doc', 'rtf', 'xls', 'ppt', 'odt', 'ods');

            if (!in_array(substr(strrchr($filename, '.'), 1), $allowed)) {
                $result['error'] = $this->language->get('error_filetype');
            }

            // Allowed file mime types
            $allowed = array();

            $filetypes = explode("\n", $this->config->get('config_file_mime_allowed'));

            foreach ($filetypes as $filetype) {
                $allowed[] = trim($filetype);
            }

            if (!in_array($uploadedFile['type'], $allowed)) {
                $result['error'] = $this->language->get('error_filetype');
            }

            if ($uploadedFile['error'] != UPLOAD_ERR_OK) {
                $result['error'] = $this->language->get('error_upload_' . $uploadedFile['error']);
            }
        } else {
            $result['error'] = $this->language->get('error_upload');
        }

        if (!$result && file_exists($uploadedFile['tmp_name'])) {
            $file = basename($filename) . '.' . md5(mt_rand());

            // Hide the uploaded file name so people can not link to it directly.
            $result['file'] = $this->encryption->encrypt($file);

            $result['file_path'] = "data/" . $subdirectory . "/" . $filename;
            if ($this->rmkdir(DIR_IMAGE . "data/" . $subdirectory)) {
                if (copy($uploadedFile['tmp_name'], DIR_IMAGE . $result['file_path']))
                    $result['success'] = $this->language->get('text_upload');
                else
                    $result['error'] = "error with move file image";
            } else
                $result['error'] = "Could not create directory or directory is not writeable: " . DIR_IMAGE . "data/" . $subdirectory;
        }
        return $result;
    }

    function rmkdir($path, $mode = 0755)
    {

        if (!file_exists($path)) {
            $path = rtrim(preg_replace(array("/\\\\/", "/\/{2,}/"), "/", $path), "/");
            $e = explode("/", ltrim($path, "/"));
            if (substr($path, 0, 1) == "/") {
                $e[0] = "/" . $e[0];
            }
            $c = count($e);
            $cp = $e[0];
            for ($i = 1; $i < $c; $i++) {
                if (!is_dir($cp)) {
                    @mkdir($cp, $mode);
                }
                $cp .= "/" . $e[$i];
            }
            return @mkdir($path, $mode);
        }

        if (is_writable($path)) {
            return true;
        } else {
            return false;
        }
    }

    private function _setProductImage($product_id, $image)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "product SET image = '" . $this->db->escape(html_entity_decode($image, ENT_QUOTES, 'UTF-8')) . "',  date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");
    }


//Category functions 

    private function _addProductImages($product_id, $image)
    {
        $this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image ='" . $this->db->escape(html_entity_decode($image, ENT_QUOTES, 'UTF-8')) . "' ");
        return $this->db->getLastId();
    }

    public function addProductImage($request)
    {

        $json = array('success' => false);

        $this->load->model('catalog/product');

        if (ctype_digit($request->get['id'])) {
            $product = $this->model_catalog_product->getProduct($request->get['id']);
            //check product exists
            if (!empty($product)) {
                if (isset($request->files['file'])) {
                    $uploadResult = $this->upload($request->files['file'], "products");
                    if (!isset($uploadResult['error'])) {
                        $json['success'] = true;
                        $this->_addProductImage($request->get['id'], $uploadResult['file_path']);
                    } else {
                        $json['error'] = $uploadResult['error'];
                    }
                } else {
                    $json['error'] = "File is required!";
                }
            } else {
                $json['success'] = false;
                $json['error'] = "The specified product does not exist.";
            }
        } else {
            $json['success'] = false;
        }

        $this->sendResponse($json);
    }

    private function _addProductImage($product_id, $image)
    {
        $this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape(html_entity_decode($image, ENT_QUOTES, 'UTF-8')) . "'");
    }

    public function updateProductImage($request)
    {

        $json = array('success' => false);

        $this->load->model('catalog/product');

        if (ctype_digit($request->get['id'])) {
            $product = $this->model_catalog_product->getProduct($request->get['id']);
            //check product exists
            if (!empty($product)) {
                if (isset($request->files['file'])) {
                    $uploadResult = $this->upload($request->files['file'], "products");
                    if (!isset($uploadResult['error'])) {
                        $json['success'] = true;
                        $this->_setProductImage($request->get['id'], $uploadResult['file_path']);
                    } else {
                        $json['error'] = $uploadResult['error'];
                    }
                } else {
                    $json['error'] = "File is required!";
                }
            } else {
                $json['success'] = false;
                $json['error'] = "The specified product does not exist.";
            }
        } else {
            $json['success'] = false;
        }

        $this->sendResponse($json);
    }

//Manufacturer Functions

    public function categoryimages()
    {

        $this->checkPlugin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            //upload and save image
            $this->saveCategoryImage($this->request);
        }
    }

    public function saveCategoryImage($request)
    {
        $json = array('success' => false);

        $this->load->model('catalog/category');

        if (ctype_digit($request->get['id'])) {
            $category = $this->model_catalog_category->getCategory($request->get['id']);
            //check category exists
            if (!empty($category)) {
                if (isset($request->files['file'])) {
                    $uploadResult = $this->upload($request->files['file'], "categories");
                    if (!isset($uploadResult['error'])) {
                        $json['success'] = true;
                        $this->_setCategoryImage($request->get['id'], $uploadResult['file_path']);
                    } else {
                        $json['error'] = $uploadResult['error'];
                    }
                } else {
                    $json['error'] = "File is required!";
                }
            } else {
                $json['success'] = false;
                $json['error'] = "The specified category does not exist.";
            }
        } else {
            $json['success'] = false;
        }

        $this->sendResponse($json);
    }

    private function _setCategoryImage($category_id, $image)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "category SET image = '" . $this->db->escape($image) . "', date_modified = NOW() WHERE category_id = '" . (int)$category_id . "'");
    }

    public function utc_offset()
    {

        $this->checkPlugin();

        $json = array('success' => false);

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $serverTimeZone = date_default_timezone_get();
            $timezone = new DateTimeZone($serverTimeZone);
            $now = new DateTime("now", $timezone);
            $offset = $timezone->getOffset($now);

            $json['data'] = array('offset' => $offset);
            $json['success'] = true;
        }

        $this->sendResponse($json);
    }

//Orders Functions 

    public function manufacturerimages()
    {

        $this->checkPlugin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            //upload and save manufacturer image
            $this->saveManufacturerImage($this->request);
        }
    }

    public function saveManufacturerImage($request)
    {

        $json = array('success' => false);

        $this->load->model('catalog/manufacturer');

        if (ctype_digit($request->get['id'])) {
            $manufacturer = $this->model_catalog_manufacturer->getManufacturer($request->get['id']);
            //check manufacturer exists
            if (!empty($manufacturer)) {
                if (isset($request->files['file'])) {
                    $uploadResult = $this->upload($request->files['file'], "manufacturers");
                    if (!isset($uploadResult['error'])) {
                        $json['success'] = true;
                        $this->_setManufacturerImage($request->get['id'], $uploadResult['file_path']);
                    } else {
                        $json['error'] = $uploadResult['error'];
                    }
                } else {
                    $json['error'] = "File is required!";
                }
            } else {
                $json['success'] = false;
                $json['error'] = "The specified manufacturer does not exist.";
            }
        } else {
            $json['success'] = false;
        }

        $this->sendResponse($json);
    }

    private function _setManufacturerImage($manufacturer_id, $image)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "manufacturer SET image = '" . $this->db->escape($image) . "' WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
    }

    public function productquantity()
    {

        $this->checkPlugin();

        if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            //update products
            $requestjson = file_get_contents('php://input');
            $requestjson = json_decode($requestjson, true);

            if (!empty($requestjson) && count($requestjson) > 0) {
                $this->updateProductsQuantity($requestjson);
            } else {
                $this->response->setOutput(json_encode(array('success' => false)));
            }
        } else {
            $json['success'] = false;
            $json['error'] = "Invalid request method, use PUT method.";
            $this->sendResponse($json);
        }
    }

    private function updateProductsQuantity($products)
    {

        $json = array('success' => true);

        $this->load->model('catalog/product');

        foreach ($products as $productItem) {

            if (isset($productItem['product_id']) && ctype_digit($productItem['product_id'])) {
                //if don't update product option quantity, product quantity must be set
                if (!isset($productItem['product_option'])) {
                    if (!isset($productItem['quantity']) || !ctype_digit($productItem['quantity'])) {
                        $json['success'] = false;
                        $json['error'] = "Invalid quantity:" . $productItem['quantity'] . ", product id:" . $productItem['product_id'];
                    }
                } else {
                    foreach ($productItem['product_option'][0]['product_option_value'] as $option) {
                        if (!isset($option['quantity']) || !ctype_digit($option['quantity'])) {
                            $json['success'] = false;
                            $json['error'] = "Invalid quantity:" . $option['quantity'] . ", product id:" . $productItem['product_id'];
                            break;
                        }
                    }
                }

                if ($json['success']) {
                    $id = $productItem['product_id'];

                    $product = $this->model_catalog_product->getProduct($id);

                    if (!empty($product)) {
                        $this->_editProductQuantity($id, $productItem);
                    } else {
                        $json['success'] = false;
                        $json['error'] = "The specified product does not exist, id: " . $productItem['product_id'];
                    }
                }
            } else {
                $json['success'] = false;
                $json['error'] = "Invalid product id:" . $productItem['product_id'];
            }
        }

        $this->sendResponse($json);
    }

//checkout order Functions

    private function _editProductQuantity($product_id, $data)
    {
        if (isset($data['product_option'])) {
            foreach ($data['product_option'] as $product_option) {
                if (isset($product_option['product_option_value']) && count($product_option['product_option_value']) > 0) {
                    foreach ($product_option['product_option_value'] as $product_option_value) {
                        $this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET quantity = '" . (int)$product_option_value['quantity'] . "' WHERE product_id = '" . (int)$product_id . "' AND product_option_value_id = '" . (int)$product_option_value['product_option_value_id'] . "'");
                    }
                }
            }
        }
        if (isset($data['quantity'])) {
            $this->db->query("UPDATE " . DB_PREFIX . "product SET quantity = '" . (int)$data['quantity'] . "', date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");
        }

        $this->cache->delete('product');
    }

    public function orderstatus()
    {

        $this->checkPlugin();
        if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])
            ) {
                $requestjson = file_get_contents('php://input');

                $requestjson = json_decode($requestjson, true);

                $this->updateOrderStatusByName($this->request->get['id'], $requestjson);
            } else {
                $json['success'] = false;
                $json['error'] = "Invalid request, please set order id and order status";

                $this->sendResponse($json);
            }
        }
    }

    public function updateOrderStatusByName($id, $data)
    {

        $json = array('success' => false);

        $this->load->model('checkout/order');
        $this->load->model('sale/order');

        if (ctype_digit($id)) {
            if (isset($data['status']) && ($data['status']) != "") {

                $status = $this->findStatusByName($data['status']);

                if ($status) {
                    $result = $this->model_checkout_order->getOrder($id);
                    if (!empty($result)) {
                        $json['success'] = true;
                        $history_id = $this->model_sale_order->addOrderHistory($id, $status);
                        if ($data['tracking'] != "") {
                            $json['history_id'] = $this->_updateOrderTracking($history_id, $id, $data['tracking'], $data['Notify']);
                        }
                    } else {
                        $json['success'] = false;
                        $json['error'] = "The specified order does not exist.";
                    }
                } else {
                    $json['success'] = false;
                    $json['error'] = "The specified status does not exist.";
                }
            } else {
                $json['success'] = false;
                $json['error'] = "Invalid status id";
            }
        } else {
            $json['success'] = false;
            $json['error'] = "Invalid order id";
        }

        $this->sendResponse($json);

    }

// Customer functions

    private function findStatusByName($status_name)
    {
        $this->load->model('catalog/product');

        $status_id = $this->_getOrderStatusByName($status_name);
        return ((count($status_id) > 0 && $status_id[0]['order_status_id']) ? $status_id[0]['order_status_id'] : false);
    }

    private function _getOrderStatusByName($status)
    {

        $query = $this->db->query("SELECT order_status_id FROM " . DB_PREFIX . "order_status WHERE LCASE(name) = '" . $this->db->escape(utf8_strtolower($status)) . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");

        return $query->rows;
    }

    private function _updateOrderTracking($history_id, $id, $tracking, $notify = '')
    {
        if ($notify == '') {
            $this->db->query("UPDATE " . DB_PREFIX . "order_history SET trackcode = '" . $tracking . "' where order_history_id='" . (int)$history_id . "' and order_id='" . (int)$id . "'");
        } else {
            var_dump("UPDATE " . DB_PREFIX . "order_history SET trackcode = '" . $tracking . "' and notify='" . (int)$notify . "' where order_history_id='" . (int)$history_id . "' and order_id='" . (int)$id . "'");
            $this->db->query("UPDATE " . DB_PREFIX . "order_history SET trackcode = '" . $tracking . "' ,notify='" . (int)$notify . "' where order_history_id='" . (int)$history_id . "' and order_id='" . (int)$id . "'");
        }
    }

    private function _editPasswordById($customer_id, $password)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "customer SET password = '" . $this->db->escape(md5($password)) . "' WHERE customer_id = '" . (int)$customer_id . "'");
    }

}

if (!function_exists('apache_request_headers')) {
    function apache_request_headers()
    {
        $arh = array();
        $rx_http = '/\AHTTP_/';

        foreach ($_SERVER as $key => $val) {
            if (preg_match($rx_http, $key)) {
                $arh_key = preg_replace($rx_http, '', $key);
                $rx_matches = array();
                // do some nasty string manipulations to restore the original letter case
                // this should work in most cases
                $rx_matches = explode('_', $arh_key);

                if (count($rx_matches) > 0 and strlen($arh_key) > 2) {
                    foreach ($rx_matches as $ak_key => $ak_val) {
                        $rx_matches[$ak_key] = ucfirst($ak_val);
                    }

                    $arh_key = implode('-', $rx_matches);
                }

                $arh[$arh_key] = $val;
            }
        }

        return ($arh);
    }
}