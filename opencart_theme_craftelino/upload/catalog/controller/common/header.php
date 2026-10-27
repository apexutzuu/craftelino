<?php
namespace Opencart\Catalog\Controller\Common;
/**
 * Class Header
 *
 * Can be called from $this->load->controller('common/header');
 *
 * @package Opencart\Catalog\Controller\Common
 */
class Header extends \Opencart\System\Engine\Controller {
	/**
	 * Index
	 *
	 * @return string
	 */
	public function index(): string {
		// Analytics
		$data['analytics'] = [];

		if (!$this->config->get('config_cookie_id') || (isset($this->request->cookie['policy']) && $this->request->cookie['policy'])) {
			// Extension
			$this->load->model('setting/extension');

			$analytics = $this->model_setting_extension->getExtensionsByType('analytics');

			foreach ($analytics as $analytic) {
				if ($this->config->get('analytics_' . $analytic['code'] . '_status')) {
					$data['analytics'][] = $this->load->controller('extension/' . $analytic['extension'] . '/analytics/' . $analytic['code'], $this->config->get('analytics_' . $analytic['code'] . '_status'));
				}
			}
		}

		$data['lang'] = $this->language->get('code');
		$data['direction'] = $this->language->get('direction');

		$data['title'] = $this->document->getTitle();
		$data['base'] = $this->config->get('config_url');
		$data['description'] = $this->document->getDescription();
		$data['keywords'] = $this->document->getKeywords();

		// Hard coding css, so they can be replaced via the event's system.
		$data['bootstrap'] = 'catalog/view/stylesheet/bootstrap.css';
		$data['icons'] = 'catalog/view/stylesheet/fonts/fontawesome/css/all.min.css';
		$data['stylesheet'] = 'catalog/view/stylesheet/stylesheet.css';

		// Hard coding scripts, so they can be replaced via the event's system.
		$data['jquery'] = 'catalog/view/javascript/jquery/jquery-3.7.1.min.js';

		$data['links'] = $this->document->getLinks();
		$data['styles'] = $this->document->getStyles();
		$data['scripts'] = $this->document->getScripts('header');

		$data['name'] = $this->config->get('config_name');

		$this->load->model('tool/image');

		// Fav icon (decode path like admin — HTML entities in DB break is_file)
		$icon_file = $this->config->get('config_icon') ? html_entity_decode((string)$this->config->get('config_icon'), ENT_QUOTES, 'UTF-8') : '';

		if ($icon_file && is_file(DIR_IMAGE . $icon_file)) {
			$data['icon'] = $this->config->get('config_url') . 'image/' . str_replace(' ', '%20', $icon_file);
		} elseif (is_file(DIR_IMAGE . 'catalog/craftelino-favicon.svg')) {
			$data['icon'] = $this->config->get('config_url') . 'image/catalog/craftelino-favicon.svg';
		} else {
			$data['icon'] = '';
		}

		$logo_file = $this->config->get('config_logo') ? html_entity_decode((string)$this->config->get('config_logo'), ENT_QUOTES, 'UTF-8') : '';
		$data['logo'] = '';

		// Prefer store logo; fall back to Craftelino brand assets in image/catalog/
		if (!$logo_file || !is_file(DIR_IMAGE . $logo_file)) {
			foreach (['catalog/craftelino-logo.png', 'catalog/craftelino-logo.svg'] as $fallback) {
				if (is_file(DIR_IMAGE . $fallback)) {
					$logo_file = $fallback;
					break;
				}
			}
		}

		if ($logo_file && is_file(DIR_IMAGE . $logo_file)) {
			$extension = strtolower(pathinfo($logo_file, PATHINFO_EXTENSION));

			if ($extension === 'svg') {
				$data['logo'] = $this->config->get('config_url') . 'image/' . str_replace(' ', '%20', $logo_file);
			} else {
				$w = (int)$this->config->get('config_image_default_width');
				$h = (int)$this->config->get('config_image_default_height');
				$resized = $this->model_tool_image->resize($logo_file, $w > 0 ? $w : 200, $h > 0 ? $h : 56, '');

				$data['logo'] = $resized ?: ($this->config->get('config_url') . 'image/' . str_replace(' ', '%20', $logo_file));
			}
		}

		$this->load->language('common/header');

		// Wishlist
		if ($this->customer->isLogged()) {
			$this->load->model('account/wishlist');

			$data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), $this->model_account_wishlist->getTotalWishlist($this->customer->getId()));
		} else {
			$data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0));
		}

		$data['home'] = $this->url->link('common/home', 'language=' . $this->config->get('config_language'));
		$data['wishlist'] = $this->url->link('account/wishlist', 'language=' . $this->config->get('config_language') . (isset($this->session->data['customer_token']) ? '&customer_token=' . $this->session->data['customer_token'] : ''));
		$data['logged'] = $this->customer->isLogged();

		if (!$this->customer->isLogged()) {
			$data['register'] = $this->url->link('account/register', 'language=' . $this->config->get('config_language'));
			$data['login'] = $this->url->link('account/login', 'language=' . $this->config->get('config_language'));
		} else {
			$data['account'] = $this->url->link('account/account', 'language=' . $this->config->get('config_language') . '&customer_token=' . $this->session->data['customer_token']);
			$data['order'] = $this->url->link('account/order', 'language=' . $this->config->get('config_language') . '&customer_token=' . $this->session->data['customer_token']);
			$data['transaction'] = $this->url->link('account/transaction', 'language=' . $this->config->get('config_language') . '&customer_token=' . $this->session->data['customer_token']);
			$data['download'] = $this->url->link('account/download', 'language=' . $this->config->get('config_language') . '&customer_token=' . $this->session->data['customer_token']);
			$data['logout'] = $this->url->link('account/logout', 'language=' . $this->config->get('config_language'));
		}

		$data['shopping_cart'] = $this->url->link('checkout/cart', 'language=' . $this->config->get('config_language'));
		$data['checkout'] = $this->url->link('checkout/checkout', 'language=' . $this->config->get('config_language'));
		$data['contact'] = $this->url->link('information/contact', 'language=' . $this->config->get('config_language'));
		$company = \Opencart\Catalog\Controller\Information\Legal::company();
		$data['procurement_email'] = $company['procurement_email'];
		$data['procurement_mailto'] = 'mailto:' . $data['procurement_email'];
		$lang = 'language=' . $this->config->get('config_language');
		$data['special'] = $this->url->link('product/special', $lang);
		$data['manufacturer'] = $this->url->link('product/manufacturer', $lang);
		$data['link_quote'] = $this->url->link('information/quote', $lang);
		$data['link_personalizare'] = $this->url->link('information/personalizare', $lang);
		$data['link_catalog'] = $this->url->link('information/catalog', $lang);
		$data['og_url'] = $this->config->get('config_url');
		$data['og_image'] = '';

		if (!empty($data['logo'])) {
			$data['og_image'] = $data['logo'];
		} elseif (is_file(DIR_IMAGE . 'catalog/craftelino-logo.png')) {
			$data['og_image'] = $this->config->get('config_url') . 'image/catalog/craftelino-logo.png';
		} elseif (is_file(DIR_IMAGE . 'catalog/craftelino-logo.svg')) {
			$data['og_image'] = $this->config->get('config_url') . 'image/catalog/craftelino-logo.svg';
		}

		$data['language'] = $this->load->controller('common/language');
		$data['currency'] = $this->load->controller('common/currency');
		$data['search'] = $this->load->controller('common/search');
		$data['cart'] = $this->load->controller('common/cart');
		$data['menu'] = $this->load->controller('common/menu');

		$search_template = $this->url->link('product/search', 'language=' . $this->config->get('config_language') . '&search={search_term_string}', true);
		$schema = [
			'@context' => 'https://schema.org',
			'@graph'   => [
				[
					'@type' => 'Organization',
					'name'  => $this->config->get('config_name'),
					'url'   => $this->config->get('config_url'),
					'email' => $data['procurement_email'],
				],
				[
					'@type'     => 'WebSite',
					'name'      => $this->config->get('config_name'),
					'url'       => $this->config->get('config_url'),
					'publisher' => [
						'@type' => 'Organization',
						'name'  => $this->config->get('config_name'),
					],
					'potentialAction' => [
						'@type'       => 'SearchAction',
						'target'      => [
							'@type'        => 'EntryPoint',
							'urlTemplate'  => $search_template,
						],
						'query-input' => 'required name=search_term_string',
					],
				],
			],
		];
		$data['schema_markup'] = json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

		$craftelino_css = DIR_APPLICATION . 'view/theme/craftelino/stylesheet/craftelino.css';
		$craftelino_js = DIR_APPLICATION . 'view/theme/craftelino/javascript/craftelino.js';

		$data['craftelino_css'] = 'catalog/view/theme/craftelino/stylesheet/craftelino.css';
		$data['craftelino_js'] = 'catalog/view/theme/craftelino/javascript/craftelino.js';

		if (is_file($craftelino_css)) {
			$data['craftelino_css'] .= '?v=' . filemtime($craftelino_css);
		}

		if (is_file($craftelino_js)) {
			$data['craftelino_js'] .= '?v=' . filemtime($craftelino_js);
		}

		return $this->load->view('common/header', $data);
	}
}
