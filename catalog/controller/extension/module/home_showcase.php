<?php
class ControllerExtensionModuleHomeShowcase extends Controller {
	public function index($setting) {
		if (!is_array($setting) || empty($setting['status'])) {
			return '';
		}

		$this->load->language('extension/module/home_showcase');

		$this->load->model('catalog/product');
		$this->load->model('catalog/category');
		$this->load->model('tool/image');

		$theme = $this->config->get('config_theme');
		$w = (int)$this->config->get('theme_' . $theme . '_image_product_width');
		$h = (int)$this->config->get('theme_' . $theme . '_image_product_height');

		if ($w < 1) {
			$w = 228;
		}

		if ($h < 1) {
			$h = 228;
		}

		$rows = !empty($setting['section']) && is_array($setting['section']) ? $setting['section'] : array();

		$data['sections'] = array();

		foreach ($rows as $row) {
			if (empty($row['enabled'])) {
				continue;
			}

			$source = isset($row['source']) ? (string)$row['source'] : 'category';
			$limit = isset($row['limit']) ? max(1, min(50, (int)$row['limit'])) : 5;
			$heading_in = isset($row['heading']) ? trim((string)$row['heading']) : '';
			$category_id = isset($row['category_id']) ? (int)$row['category_id'] : 0;

			$products_map = array();
			$see_all_href = '';
			$heading = $heading_in;

			switch ($source) {
				case 'bestseller':
					$products_map = $this->model_catalog_product->getBestSellerProducts($limit);
					$see_all_href = '';

					if ($heading === '') {
						$heading = $this->language->get('text_default_bestseller');
					}

					break;

				case 'special':
					$products_map = $this->model_catalog_product->getProductSpecials(array('start' => 0, 'limit' => $limit));
					$see_all_href = $this->url->link('product/special', '', true);

					if ($heading === '') {
						$heading = $this->language->get('text_default_special');
					}

					break;

				case 'latest':
					$products_map = $this->model_catalog_product->getLatestProducts($limit);
					$see_all_href = '';

					if ($heading === '') {
						$heading = $this->language->get('text_default_latest');
					}

					break;

				case 'category':
				default:
					if ($category_id < 1) {
						continue 2;
					}

					$cat = $this->model_catalog_category->getCategory($category_id);

					if (!$cat) {
						continue 2;
					}

					$products_map = $this->model_catalog_product->getProducts(array(
						'filter_category_id'  => $category_id,
						'filter_sub_category' => true,
						'sort'                => 'p.sort_order',
						'order'               => 'ASC',
						'start'               => 0,
						'limit'               => $limit
					));

					$path = $this->getCategoryPathParam($category_id);
					$see_all_href = $this->url->link('product/category', 'path=' . $path, true);

					if ($heading === '') {
						$heading = $cat['name'];
					}

					break;
			}

			if (!$products_map) {
				continue;
			}

			$products = $this->formatProducts($products_map, $w, $h, $source === 'category' ? $category_id : 0);

			if (!$products) {
				continue;
			}

			$data['sections'][] = array(
				'heading'      => $heading,
				'see_all_href' => $see_all_href,
				'see_all_text' => $this->language->get('text_see_all'),
				'products'     => $products
			);
		}

		if (!$data['sections']) {
			return '';
		}

		$data['button_wishlist'] = $this->language->get('button_wishlist');
		$data['button_cart'] = $this->language->get('button_cart');
		$data['text_new'] = $this->language->get('text_new');
		$data['text_offer'] = $this->language->get('text_offer');

		return $this->load->view('extension/module/home_showcase', $data);
	}

	private function getCategoryPathParam($category_id) {
		$query = $this->db->query("SELECT `path_id` FROM `" . DB_PREFIX . "category_path` WHERE `category_id` = '" . (int)$category_id . "' ORDER BY `level` ASC");
		$ids = array();

		foreach ($query->rows as $row) {
			$ids[] = (int)$row['path_id'];
		}

		if (!$ids) {
			return (string)(int)$category_id;
		}

		return implode('_', $ids);
	}

	private function formatProducts($products_map, $w, $h, $category_id_for_path) {
		$path_param = '';

		if ($category_id_for_path > 0) {
			$path_param = $this->getCategoryPathParam($category_id_for_path);
		}

		$new_days = 30;
		$out = array();

		foreach ($products_map as $result) {
			if (!$result || !is_array($result) || empty($result['product_id'])) {
				continue;
			}

			if ($result['image']) {
				$image = $this->model_tool_image->resize($result['image'], $w, $h);
			} else {
				$image = $this->model_tool_image->resize('placeholder.png', $w, $h);
			}

			if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
				$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
			} else {
				$price = false;
			}

			if (!is_null($result['special']) && (float)$result['special'] >= 0) {
				$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				$tax_price = (float)$result['special'];
			} else {
				$special = false;
				$tax_price = (float)$result['price'];
			}

			if ($this->config->get('config_tax')) {
				$tax = $this->currency->format($tax_price, $this->session->data['currency']);
			} else {
				$tax = false;
			}

			if ($this->config->get('config_review_status')) {
				$rating = (int)$result['rating'];
			} else {
				$rating = false;
			}

			$discount_pct = false;

			if ($special && (float)$result['price'] > 0 && (float)$result['special'] >= 0 && (float)$result['special'] < (float)$result['price']) {
				$discount_pct = (int)round(100 - ((float)$result['special'] / (float)$result['price']) * 100);

				if ($discount_pct < 1) {
					$discount_pct = false;
				}
			}

			$is_new = false;

			if (!empty($result['date_added'])) {
				$ts = strtotime($result['date_added']);

				if ($ts && (time() - $ts) < ($new_days * 86400)) {
					$is_new = true;
				}
			}

			if ($path_param !== '') {
				$href = $this->url->link('product/product', 'path=' . $path_param . '&product_id=' . (int)$result['product_id'], true);
			} else {
				$href = $this->url->link('product/product', 'product_id=' . (int)$result['product_id'], true);
			}

			$out[] = array(
				'product_id'   => (int)$result['product_id'],
				'thumb'        => $image,
				'name'         => $result['name'],
				'manufacturer' => !empty($result['manufacturer']) ? $result['manufacturer'] : '',
				'price'        => $price,
				'special'      => $special,
				'tax'          => $tax,
				'rating'       => $rating,
				'href'         => $href,
				'minimum'      => isset($result['minimum']) && (int)$result['minimum'] > 0 ? (int)$result['minimum'] : 1,
				'discount_pct' => $discount_pct,
				'is_new'       => $is_new
			);
		}

		return $out;
	}
}
