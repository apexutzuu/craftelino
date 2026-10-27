<?php
namespace Opencart\Catalog\Controller\Information;

/**
 * Landing: personalizare logo gratuită la comenzi peste 500 lei.
 */
class Personalizare extends \Opencart\System\Engine\Controller {
	public function index(): void {
		$lang = 'language=' . $this->config->get('config_language');

		$title = 'Personalizare logo gratuită peste 500 lei';
		$desc = 'La comenzi Craftelino peste 500 lei, personalizarea cu logo este gratuită: print, broderie sau gravură. Solicită ofertă și machetă.';

		$this->document->setTitle($title);
		$this->document->setDescription($desc);

		$data['breadcrumbs'] = [];
		$data['breadcrumbs'][] = [
			'text' => 'Acasă',
			'href' => $this->url->link('common/home', $lang)
		];
		$data['breadcrumbs'][] = [
			'text' => $title,
			'href' => $this->url->link('information/personalizare', $lang)
		];

		$data['heading_title'] = $title;
		$data['link_quote'] = $this->url->link('information/quote', $lang);
		$data['link_special'] = $this->url->link('product/special', $lang);
		$data['link_catalog'] = $this->url->link('information/catalog', $lang);
		$data['hero_img'] = $this->config->get('config_url') . 'image/catalog/hero/hero-tricou.svg';

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('information/personalizare', $data));
	}
}
