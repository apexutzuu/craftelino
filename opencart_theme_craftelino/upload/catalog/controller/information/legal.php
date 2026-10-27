<?php
namespace Opencart\Catalog\Controller\Information;
/**
 * Pagini legale statice (termeni, livrare, confidențialitate) + date firmă footer.
 */
class Legal extends \Opencart\System\Engine\Controller {
	public static function company(): array {
		if (is_file(DIR_EXTENSION . 'craftelino/system/library/craft/legal_content.php')) {
			require_once DIR_EXTENSION . 'craftelino/system/library/craft/legal_content.php';

			return \Opencart\System\Library\Craft\LegalContent::company();
		}

		return [
			'name'               => 'TOP UNDERGROUND GOODS SRL',
			'cui'                => '50982610',
			'reg'                => 'J2024046408004',
			'address'            => 'Str. Serg. Ion Nuțu nr. 8-10, Sector 5, București',
			'procurement_email'  => 'procurement@craftelino.ro',
		];
	}

	/**
	 * @param callable(string, string): string $link
	 *
	 * @return list<array{title: string, href: string}>
	 */
	public static function footerLinks(string $language, callable $link): array {
		$args = 'language=' . $language;

		return [
			[
				'title' => 'Informații livrare',
				'href'  => $link('information/legal.delivery', $args),
			],
			[
				'title' => 'Politica cookie',
				'href'  => $link('information/legal.cookies', $args),
			],
		];
	}

	public function index(): void {
		$this->response->redirect($this->url->link('common/home', 'language=' . $this->config->get('config_language')));
	}

	public function terms(): void {
		$this->response->redirect($this->url->link('common/home', 'language=' . $this->config->get('config_language')));
	}

	public function delivery(): void {
		if (is_file(DIR_EXTENSION . 'craftelino/system/library/craft/legal_content.php')) {
			require_once DIR_EXTENSION . 'craftelino/system/library/craft/legal_content.php';
			$pages = \Opencart\System\Library\Craft\LegalContent::pages(
				(string)$this->config->get('config_name'),
				(string)$this->config->get('config_email'),
				(string)$this->config->get('config_telephone')
			);
			$this->renderPage($pages['delivery']['title'], $pages['delivery']['description']);

			return;
		}

		$html = '<p>Livrăm în România prin curier rapid sau transport agreat pentru comenzi mari.</p>'
			. '<h2>Termene</h2>'
			. '<ul>'
			. '<li>Produse standard, fără personalizare: 2–5 zile lucrătoare de la confirmarea plății.</li>'
			. '<li>Produse personalizate (logo, broderie, gravură): 5–7 zile lucrătoare după aprobarea machetei și confirmarea comenzii.</li>'
			. '<li>Comenzi de volum mare / B2B: termen stabilit în ofertă (de obicei 10–15 zile lucrătoare).</li>'
			. '</ul>'
			. '<h2>Costuri transport</h2>'
			. '<p>Costul livrării este afișat în coșul de cumpărături înainte de finalizarea comenzii. Pentru comenzi peste pragul comunicat pe site, livrarea poate fi gratuită.</p>'
			. '<h2>Recepție</h2>'
			. '<p>La primire, verificați coletul în prezența curierului. Pentru deteriorări vizibile, notați observațiile pe documentele curierului și contactați-ne în maximum 48 de ore.</p>';

		$this->renderPage('Informații livrare', $html);
	}

	public function privacy(): void {
		$this->renderLegalSlug('privacy');
	}

	public function cookies(): void {
		$this->renderLegalSlug('cookies');
	}

	private function renderLegalSlug(string $slug): void {
		if (is_file(DIR_EXTENSION . 'craftelino/system/library/craft/legal_content.php')) {
			require_once DIR_EXTENSION . 'craftelino/system/library/craft/legal_content.php';
			$pages = \Opencart\System\Library\Craft\LegalContent::pages(
				(string)$this->config->get('config_name'),
				(string)$this->config->get('config_email'),
				(string)$this->config->get('config_telephone')
			);

			if (isset($pages[$slug])) {
				$this->renderPage($pages[$slug]['title'], $pages[$slug]['description']);

				return;
			}
		}

		$this->response->redirect($this->url->link('common/home', 'language=' . $this->config->get('config_language')));
	}

	private function renderPage(string $title, string $description): void {
		$this->load->language('information/information');

		$this->document->setTitle($title);
		$this->document->setDescription($title);

		$data['breadcrumbs'] = [];
		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home', 'language=' . $this->config->get('config_language'))
		];
		$data['breadcrumbs'][] = [
			'text' => $title,
			'href' => $this->url->link($this->request->get['route'], 'language=' . $this->config->get('config_language'))
		];

		$data['heading_title'] = $title;
		$data['description'] = $description;
		$data['continue'] = $this->url->link('common/home', 'language=' . $this->config->get('config_language'));

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('information/legal', $data));
	}
}
