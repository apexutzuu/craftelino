<?php
namespace Opencart\Catalog\Controller\Information;
/**
 * Class Sitemap
 *
 * @package Opencart\Catalog\Controller\Information
 */
class Sitemap extends \Opencart\System\Engine\Controller {
	/**
	 * Index
	 *
	 * @return void
	 */
	public function index(): void {
		$this->load->language('information/sitemap');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home', 'language=' . $this->config->get('config_language'))
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('information/sitemap', 'language=' . $this->config->get('config_language'))
		];

		// Category
		$this->load->model('catalog/category');

		$data['categories'] = [];

		$categories_1 = $this->model_catalog_category->getCategories(0);
		$icons = ['fa-bottle-water', 'fa-book', 'fa-shirt', 'fa-pen', 'fa-bag-shopping', 'fa-gift', 'fa-umbrella', 'fa-mug-hot', 'fa-print', 'fa-clock'];
		$icon_i = 0;

		foreach ($categories_1 as $category_1) {
			$level_2_data = [];

			$categories_2 = $this->model_catalog_category->getCategories($category_1['category_id']);

			foreach ($categories_2 as $category_2) {
				$level_3_data = [];

				$categories_3 = $this->model_catalog_category->getCategories($category_2['category_id']);

				foreach ($categories_3 as $category_3) {
					$level_3_data[] = ['href' => $this->url->link('product/category', 'language=' . $this->config->get('config_language') . '&path=' . $category_1['category_id'] . '_' . $category_2['category_id'] . '_' . $category_3['category_id'])] + $category_3;
				}

				$level_2_data[] = [
					'children' => $level_3_data,
					'href'     => $this->url->link('product/category', 'language=' . $this->config->get('config_language') . '&path=' . $category_1['category_id'] . '_' . $category_2['category_id'])
				] + $category_2;
			}

			$data['categories'][] = [
				'children' => $level_2_data,
				'href'     => $this->url->link('product/category', 'language=' . $this->config->get('config_language') . '&path=' . $category_1['category_id']),
				'icon'     => $icons[$icon_i % count($icons)]
			] + $category_1;

			$icon_i++;
		}

		$data['manufacturer'] = $this->url->link('product/manufacturer', 'language=' . $this->config->get('config_language'));

		$data['special'] = $this->url->link('product/special', 'language=' . $this->config->get('config_language'));
		$data['account'] = $this->url->link('account/account', 'language=' . $this->config->get('config_language') . (isset($this->session->data['customer_token']) ? '&customer_token=' . $this->session->data['customer_token'] : ''));
		$data['edit'] = $this->url->link('account/edit', 'language=' . $this->config->get('config_language') . (isset($this->session->data['customer_token']) ? '&customer_token=' . $this->session->data['customer_token'] : ''));
		$data['password'] = $this->url->link('account/password', 'language=' . $this->config->get('config_language') . (isset($this->session->data['customer_token']) ? '&customer_token=' . $this->session->data['customer_token'] : ''));
		$data['address'] = $this->url->link('account/address', 'language=' . $this->config->get('config_language') . (isset($this->session->data['customer_token']) ? '&customer_token=' . $this->session->data['customer_token'] : ''));
		$data['history'] = $this->url->link('account/order', 'language=' . $this->config->get('config_language') . (isset($this->session->data['customer_token']) ? '&customer_token=' . $this->session->data['customer_token'] : ''));
		$data['download'] = $this->url->link('account/download', 'language=' . $this->config->get('config_language') . (isset($this->session->data['customer_token']) ? '&customer_token=' . $this->session->data['customer_token'] : ''));
		$data['cart'] = $this->url->link('checkout/cart', 'language=' . $this->config->get('config_language'));
		$data['checkout'] = $this->url->link('checkout/checkout', 'language=' . $this->config->get('config_language'));
		$data['search'] = $this->url->link('product/search', 'language=' . $this->config->get('config_language'));
		$data['contact'] = $this->url->link('information/contact', 'language=' . $this->config->get('config_language'));

		$lang = 'language=' . $this->config->get('config_language');
		$data['craft_pages'] = [
			['title' => 'Cerere ofertă B2B', 'href' => $this->url->link('information/quote', $lang)],
			['title' => 'Personalizare logo gratuită', 'href' => $this->url->link('information/personalizare', $lang)],
			['title' => 'Catalog', 'href' => $this->url->link('information/catalog', $lang)],
			['title' => 'Bestsellers', 'href' => $this->url->link('product/special', $lang)],
			['title' => 'Politica cookie', 'href' => $this->url->link('information/legal.cookies', $lang)],
			['title' => 'Informații livrare', 'href' => $this->url->link('information/legal.delivery', $lang)],
		];

		// Information
		$this->load->model('catalog/information');

		$data['informations'] = [];

		foreach ($this->model_catalog_information->getInformations() as $result) {
			$data['informations'][] = ['href' => $this->url->link('information/information', 'language=' . $this->config->get('config_language') . '&information_id=' . $result['information_id'])] + $result;
		}

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('information/sitemap', $data));
	}
}
