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

		$lang = 'language=' . $this->config->get('config_language');
		$data['link_special'] = $this->url->link('product/special', $lang);
		$data['link_sitemap'] = $this->url->link('information/sitemap', $lang);
		$data['link_manufacturer'] = $this->url->link('product/manufacturer', $lang);
		$data['link_quote'] = $this->url->link('information/quote', $lang);
		$data['link_personalizare'] = $this->url->link('information/personalizare', $lang);
		$data['link_catalog'] = $this->url->link('information/catalog', $lang);

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

		$data['how_steps'] = [
			['icon' => 'fa-cart-shopping', 'title' => 'Alegi produsul', 'text' => 'Cantitate și tip personalizare (print, broderie, gravură).'],
			['icon' => 'fa-upload', 'title' => 'Trimiți logo-ul', 'text' => 'Cerere ofertă cu fișier sau e-mail către procurement.'],
			['icon' => 'fa-eye', 'title' => 'Aprobi macheta', 'text' => 'Previzualizare gratuită înainte de producție.'],
			['icon' => 'fa-truck-fast', 'title' => 'Livrare', 'text' => '5–7 zile lucrătoare după confirmare.'],
		];
		$data['examples'] = [
			['img' => $img_base . 'hero-tricou.svg', 'title' => 'Textile brodate', 'text' => 'Tricouri & hanorace cu logo firmă'],
			['img' => $img_base . 'hero-termos.svg', 'title' => 'Drinkware', 'text' => 'Termosuri și sticle gravate'],
			['img' => $img_base . 'hero-agenda.svg', 'title' => 'Office kit', 'text' => 'Agende și seturi corporate'],
			['img' => $img_base . 'hero-pix.svg', 'title' => 'Writing', 'text' => 'Pixuri metal / eco cu logo'],
		];
		$data['bundles'] = [
			['title' => 'Set birou starter', 'text' => 'Agendă + pix + sticky notes — ideal onboarding.', 'href' => $data['link_quote'], 'badge' => 'B2B'],
			['title' => 'Set eveniment', 'text' => 'Tricou + sticlă + tote bag pentru activări.', 'href' => $data['link_special'], 'badge' => '-25%'],
			['title' => 'Set cadou corporate', 'text' => 'Termos + agendă + pix premium.', 'href' => $data['link_quote'], 'badge' => 'Top'],
		];
		$data['trust_badges'] = [
			['icon' => 'fa-palette', 'title' => 'Personalizare', 'text' => 'Print · broderie · gravură'],
			['icon' => 'fa-clock', 'title' => '5–7 zile', 'text' => 'După aprobarea machetei'],
			['icon' => 'fa-building', 'title' => 'CUI 50982610', 'text' => 'TOP UNDERGROUND GOODS SRL'],
			['icon' => 'fa-envelope', 'title' => 'procurement@craftelino.ro', 'text' => 'Răspuns ofertă în 24h'],
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
