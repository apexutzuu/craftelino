<?php
namespace Opencart\Catalog\Controller\Common;
/**
 * Class Home
 *
 * Can be called from $this->load->controller('common/home');
 *
 * @package Opencart\Catalog\Controller\Common
 */
class Home extends \Opencart\System\Engine\Controller {
	/**
	 * Index
	 *
	 * @return void
	 */
	public function index(): void {
		$description = $this->config->get('config_description');
		$language_id = $this->config->get('config_language_id');

		if (isset($description[$language_id])) {
			$this->document->setTitle($description[$language_id]['meta_title']);
			$this->document->setDescription($description[$language_id]['meta_description']);
			$this->document->setKeywords($description[$language_id]['meta_keyword']);
		}

		$this->load->model('catalog/category');
		$this->load->model('catalog/manufacturer');

		$icons = ['fa-bottle-water', 'fa-book', 'fa-shirt', 'fa-pen', 'fa-bag-shopping', 'fa-gift', 'fa-umbrella', 'fa-mug-hot', 'fa-print', 'fa-clock'];

		$data['home_categories'] = [];
		$icon_i = 0;

		foreach ($this->model_catalog_category->getCategories(0) as $category) {
			if (count($data['home_categories']) >= 7) {
				break;
			}

			$data['home_categories'][] = [
				'name' => $category['name'],
				'href' => $this->url->link('product/category', 'language=' . $this->config->get('config_language') . '&path=' . $category['category_id']),
				'icon' => $icons[$icon_i % count($icons)]
			];

			$icon_i++;
		}

		$data['home_manufacturers'] = [];

		foreach ($this->model_catalog_manufacturer->getManufacturers(['start' => 0, 'limit' => 20]) as $manufacturer) {
			$data['home_manufacturers'][] = [
				'name' => $manufacturer['name'],
				'href' => $this->url->link('product/manufacturer.info', 'language=' . $this->config->get('config_language') . '&manufacturer_id=' . $manufacturer['manufacturer_id'])
			];
		}

		$data['link_special'] = $this->url->link('product/special', 'language=' . $this->config->get('config_language'));
		$data['link_sitemap'] = $this->url->link('information/sitemap', 'language=' . $this->config->get('config_language'));
		$data['link_manufacturer'] = $this->url->link('product/manufacturer', 'language=' . $this->config->get('config_language'));

		$hero_end = new \DateTime('last day of this month 23:59:59');
		$data['hero_campaign_end'] = $hero_end->format('c');
		$diff = max(0, $hero_end->getTimestamp() - time());
		$data['hero_cd_d'] = sprintf('%02d', (int)floor($diff / 86400));
		$data['hero_cd_h'] = sprintf('%02d', (int)floor(($diff % 86400) / 3600));
		$data['hero_cd_m'] = sprintf('%02d', (int)floor(($diff % 3600) / 60));
		$data['hero_cd_s'] = sprintf('%02d', (int)($diff % 60));

		$img_base = $this->config->get('config_url') . 'image/catalog/hero/';
		$data['hero_images'] = [
			['src' => $img_base . 'hero-termos.svg', 'label' => 'Termos'],
			['src' => $img_base . 'hero-tricou.svg', 'label' => 'Tricou'],
			['src' => $img_base . 'hero-agenda.svg', 'label' => 'Agendă'],
			['src' => $img_base . 'hero-pix.svg', 'label' => 'Pix'],
		];
		$data['hero_side_images'] = [
			'side1' => $img_base . 'hero-tricou.svg',
			'side2' => $img_base . 'hero-agenda.svg',
		];

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('common/home', $data));
	}
}
