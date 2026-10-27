<?php
namespace Opencart\Catalog\Controller\Information;

/**
 * Catalog promoțional — PDF dacă există, altfel pagină HTML tipărită.
 */
class Catalog extends \Opencart\System\Engine\Controller {
	public function index(): void {
		$lang = 'language=' . $this->config->get('config_language');
		$pdf_rel = 'catalog/catalog.pdf';
		$pdf_path = DIR_IMAGE . $pdf_rel;

		if (is_file($pdf_path)) {
			$this->response->redirect($this->config->get('config_url') . 'image/' . $pdf_rel);
			return;
		}

		$title = 'Catalog produse promoționale';
		$this->document->setTitle($title);
		$this->document->setDescription('Catalog Craftelino — produse promoționale personalizate pentru firme.');

		$data['breadcrumbs'] = [];
		$data['breadcrumbs'][] = [
			'text' => 'Acasă',
			'href' => $this->url->link('common/home', $lang)
		];
		$data['breadcrumbs'][] = [
			'text' => $title,
			'href' => $this->url->link('information/catalog', $lang)
		];

		$data['heading_title'] = $title;
		$data['link_quote'] = $this->url->link('information/quote', $lang);
		$data['link_special'] = $this->url->link('product/special', $lang);
		$data['link_sitemap'] = $this->url->link('information/sitemap', $lang);
		$data['pdf_missing'] = true;
		$data['procurement_email'] = Legal::company()['procurement_email'];

		$this->load->model('catalog/category');
		$data['categories'] = [];

		foreach ($this->model_catalog_category->getCategories(0) as $category) {
			if (count($data['categories']) >= 24) {
				break;
			}

			$data['categories'][] = [
				'name' => $category['name'],
				'href' => $this->url->link('product/category', $lang . '&path=' . $category['category_id']),
			];
		}

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('information/catalog', $data));
	}
}
