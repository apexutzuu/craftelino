<?php
namespace Opencart\Catalog\Controller\Extension\Craftelino\Module;

use Opencart\System\Library\Extension\Craftelino\Cookie\Settings;

/**
 * Banner acceptare cookie-uri Craftelino.
 */
class CookieConsent extends \Opencart\System\Engine\Controller {
	public function index(): string {
		$s = Settings::read($this->registry);

		if (!$s['status'] || isset($this->request->cookie['policy'])) {
			return '';
		}

		$lang = 'language=' . $this->config->get('config_language');
		$policy_href = '';

		if (!empty($s['information_id'])) {
			$this->load->model('catalog/information');
			$info = $this->model_catalog_information->getInformation((int)$s['information_id']);

			if ($info) {
				$policy_href = $this->url->link(
					'information/information',
					$lang . '&information_id=' . (int)$s['information_id']
				);
			}
		}

		if ($policy_href === '') {
			$policy_href = $this->url->link('information/legal.cookies', $lang);
		}

		$data = [
			'message'       => $s['message'],
			'policy_href'   => $policy_href,
			'policy_label'  => $s['policy_label'],
			'button_accept' => $s['button_accept'],
			'button_reject' => $s['button_reject'],
			'show_reject'   => $s['show_reject'],
			'confirm_agree' => $this->url->link('extension/craftelino/module/cookie_consent.confirm', $lang . '&agree=1'),
			'confirm_reject'=> $this->url->link('extension/craftelino/module/cookie_consent.confirm', $lang . '&agree=0'),
		];

		return $this->load->view('extension/craftelino/module/cookie_consent', $data);
	}

	public function confirm(): void {
		$this->load->language('extension/craftelino/module/cookie_consent');
		$json = [];

		$agree = !empty($this->request->get['agree']) ? '1' : '0';
		$s = Settings::read($this->registry);
		$days = max(1, (int)$s['cookie_days']);

		$option = [
			'expires'  => time() + 60 * 60 * 24 * $days,
			'path'     => $this->config->get('session_path') ?: '/',
			'secure'   => !empty($this->request->server['HTTPS']),
			'SameSite' => $this->config->get('config_session_samesite') ?: 'Lax',
		];

		setcookie('policy', $agree, $option);

		$json['success'] = $this->language->get('text_success');

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
