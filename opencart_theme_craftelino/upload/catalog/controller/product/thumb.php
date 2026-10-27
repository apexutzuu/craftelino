<?php
namespace Opencart\Catalog\Controller\Product;
/**
 * Class Thumb
 *
 * Can be loaded using $this->load->controller('product/thumb', $product_data);
 *
 * @example
 *
 * $product_data = [
 *     'description' => '',
 *     'thumb'       => '',
 *     'price'       => 1.00,
 *     'special'     => 0.00,
 *     'tax'         => 0.00,
 *     'minimum'     => 1,
 *     'href'        => ''
 * ];
 *
 * @package Opencart\Catalog\Controller\Product
 */
class Thumb extends \Opencart\System\Engine\Controller {
	/**
	 * Index
	 *
	 * @param array<string, mixed> $data array of data
	 *
	 * @return string
	 */
	public function index(array $data): string {
		$this->load->language('product/thumb');

		$data['cart'] = $this->url->link('common/cart.info', 'language=' . $this->config->get('config_language'));

		$data['cart_add'] = $this->url->link('checkout/cart.add', 'language=' . $this->config->get('config_language'));
		$data['wishlist_add'] = $this->url->link('account/wishlist.add', 'language=' . $this->config->get('config_language'));
		$data['compare_add'] = $this->url->link('product/compare.add', 'language=' . $this->config->get('config_language'));

		$data['review_status'] = (int)$this->config->get('config_review_status');

		$data['discount_percent'] = 0;

		$data['manufacturer_name'] = '';

		if (!empty($data['manufacturer_id'])) {
			$this->load->model('catalog/manufacturer');

			$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer((int)$data['manufacturer_id']);

			if ($manufacturer_info) {
				$data['manufacturer_name'] = $manufacturer_info['name'];
			}
		}

		$data['badge_new'] = false;

		if (!empty($data['date_added']) && strtotime($data['date_added']) > strtotime('-40 days')) {
			$data['badge_new'] = true;
		}

		if (!empty($data['product_id']) && !empty($data['special'])) {
			$pid = (int)$data['product_id'];
			$product_query = $this->db->query("SELECT `price` FROM `" . DB_PREFIX . "product` WHERE `product_id` = '" . $pid . "'");

			if ($product_query->num_rows) {
				$catalog_price = (float)$product_query->row['price'];
				$this->load->model('catalog/product');
				$product_info = $this->model_catalog_product->getProduct($pid);
				$deal_price = isset($product_info['special']) && $product_info['special'] !== '' ? (float)$product_info['special'] : 0.0;

				if ($catalog_price > 0 && $deal_price > 0 && $deal_price < $catalog_price) {
					$pct = (int)round(100 * (1 - $deal_price / $catalog_price));

					if ($pct > 0 && $pct < 100) {
						$data['discount_percent'] = $pct;
					}
				}
			}
		}

		$data['button_add'] = $this->language->get('button_add');
		$data['text_sale_badge'] = $this->language->get('text_sale_badge');

		return $this->load->view('product/thumb', $data);
	}
}
