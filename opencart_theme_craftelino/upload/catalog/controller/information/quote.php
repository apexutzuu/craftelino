<?php
namespace Opencart\Catalog\Controller\Information;

/**
 * Cerere ofertă B2B — formular + e-mail către procurement + auto-reply client.
 */
class Quote extends \Opencart\System\Engine\Controller {
	public function index(): void {
		$this->load->language('information/quote');

		$this->document->setTitle($this->language->get('heading_title'));
		$this->document->setDescription($this->language->get('meta_description'));

		$lang = 'language=' . $this->config->get('config_language');

		$data['breadcrumbs'] = [];
		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home', $lang)
		];
		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('information/quote', $lang)
		];

		$data['heading_title'] = $this->language->get('heading_title');
		$data['text_intro'] = $this->language->get('text_intro');
		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_email'] = $this->language->get('entry_email');
		$data['entry_company'] = $this->language->get('entry_company');
		$data['entry_telephone'] = $this->language->get('entry_telephone');
		$data['entry_quantity'] = $this->language->get('entry_quantity');
		$data['entry_deadline'] = $this->language->get('entry_deadline');
		$data['entry_product'] = $this->language->get('entry_product');
		$data['entry_logo'] = $this->language->get('entry_logo');
		$data['entry_message'] = $this->language->get('entry_message');
		$data['entry_mockup'] = $this->language->get('entry_mockup');
		$data['button_submit'] = $this->language->get('button_submit');
		$data['send'] = $this->url->link('information/quote.send', $lang);

		$data['product_id'] = (int)($this->request->get['product_id'] ?? 0);
		$data['product_name'] = '';
		$data['product_model'] = '';
		$data['product_href'] = '';

		if ($data['product_id']) {
			$this->load->model('catalog/product');
			$product = $this->model_catalog_product->getProduct($data['product_id']);

			if ($product) {
				$data['product_name'] = $product['name'];
				$data['product_model'] = $product['model'];
				$data['product_href'] = $this->url->link('product/product', $lang . '&product_id=' . $data['product_id']);
			}
		}

		$data['name'] = $this->customer->getFirstName() ?: '';
		$data['email'] = $this->customer->getEmail() ?: '';
		$data['company_name'] = '';
		$data['quantity'] = (int)($this->request->get['quantity'] ?? 100) ?: 100;
		$data['deadline'] = '';
		$data['message'] = '';

		$company = Legal::company();
		$data['procurement_email'] = $company['procurement_email'];

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('information/quote', $data));
	}

	public function send(): void {
		$this->load->language('information/quote');

		$json = [];

		$post = $this->request->post + [
			'name'         => '',
			'email'        => '',
			'company_name' => '',
			'telephone'    => '',
			'quantity'     => '',
			'deadline'     => '',
			'message'      => '',
			'product_id'   => '0',
			'product_name' => '',
			'mockup'       => '0',
		];

		if (!oc_validate_length(trim($post['name']), 2, 64)) {
			$json['error']['name'] = $this->language->get('error_name');
		}

		if (!oc_validate_email($post['email'])) {
			$json['error']['email'] = $this->language->get('error_email');
		}

		$qty = (int)$post['quantity'];

		if ($qty < 1 || $qty > 100000) {
			$json['error']['quantity'] = $this->language->get('error_quantity');
		}

		if (!oc_validate_length(trim($post['message']), 0, 4000)) {
			$json['error']['message'] = $this->language->get('error_message');
		}

		$logo_note = '';
		$upload_name = '';

		if (!empty($this->request->files['logo']['name']) && is_uploaded_file($this->request->files['logo']['tmp_name'])) {
			$file = $this->request->files['logo'];
			$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
			$allowed = ['png', 'jpg', 'jpeg', 'gif', 'webp', 'pdf', 'ai', 'eps', 'svg'];

			if (!in_array($ext, $allowed, true)) {
				$json['error']['logo'] = $this->language->get('error_logo_type');
			} elseif ($file['size'] > 8 * 1024 * 1024) {
				$json['error']['logo'] = $this->language->get('error_logo_size');
			} else {
				$dir = DIR_IMAGE . 'catalog/quote/';

				if (!is_dir($dir)) {
					mkdir($dir, 0755, true);
				}

				$upload_name = 'quote-' . date('Ymd-His') . '-' . bin2hex(random_bytes(4)) . '.' . $ext;

				if (!move_uploaded_file($file['tmp_name'], $dir . $upload_name)) {
					$json['error']['logo'] = $this->language->get('error_logo_upload');
				} else {
					$logo_note = $this->config->get('config_url') . 'image/catalog/quote/' . $upload_name;
				}
			}
		}

		if (!$json) {
			$company = Legal::company();
			$to = $company['procurement_email'] ?? 'procurement@craftelino.ro';

			$product_id = (int)$post['product_id'];
			$product_line = trim($post['product_name']);

			if ($product_id) {
				$this->load->model('catalog/product');
				$info = $this->model_catalog_product->getProduct($product_id);

				if ($info) {
					$product_line = $info['name'] . ' (ID ' . $product_id . ', model ' . $info['model'] . ')';
				}
			}

			$body = "Cerere ofertă Craftelino\n"
				. "========================\n"
				. "Nume: " . trim($post['name']) . "\n"
				. "Firmă: " . trim($post['company_name']) . "\n"
				. "E-mail: " . trim($post['email']) . "\n"
				. "Telefon: " . trim($post['telephone']) . "\n"
				. "Cantitate: " . $qty . "\n"
				. "Deadline: " . trim($post['deadline']) . "\n"
				. "Produs: " . ($product_line ?: '—') . "\n"
				. "Machetă gratuită: " . (!empty($post['mockup']) ? 'DA' : 'nu') . "\n"
				. "Logo: " . ($logo_note ?: 'neatașat') . "\n\n"
				. "Mesaj:\n" . trim($post['message']) . "\n";

			if ($this->config->get('config_mail_engine')) {
				$mail_option = [
					'parameter'      => $this->config->get('config_mail_parameter'),
					'smtp_hostname'  => $this->config->get('config_mail_smtp_hostname'),
					'smtp_username' => $this->config->get('config_mail_smtp_username'),
					'smtp_password' => html_entity_decode((string)$this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8'),
					'smtp_port'      => $this->config->get('config_mail_smtp_port'),
					'smtp_timeout'  => $this->config->get('config_mail_smtp_timeout')
				];

				$mail = new \Opencart\System\Library\Mail($this->config->get('config_mail_engine'), $mail_option);
				$mail->setTo($to);
				$mail->setFrom($this->config->get('config_email') ?: $to);
				$mail->setReplyTo(trim($post['email']));
				$mail->setSender(html_entity_decode(trim($post['name']), ENT_QUOTES, 'UTF-8'));
				$mail->setSubject('Cerere ofertă — ' . trim($post['name']) . ($product_line ? ' / ' . mb_substr($product_line, 0, 60) : ''));
				$mail->setText($body);
				$mail->send();

				// Auto-reply client
				$reply = new \Opencart\System\Library\Mail($this->config->get('config_mail_engine'), $mail_option);
				$reply->setTo(trim($post['email']));
				$reply->setFrom($this->config->get('config_email') ?: $to);
				$reply->setSender($this->config->get('config_name') ?: 'Craftelino');
				$reply->setSubject($this->language->get('email_autoreply_subject'));
				$reply->setText(sprintf(
					$this->language->get('email_autoreply_body'),
					trim($post['name']),
					$to
				));
				$reply->send();
			}

			$json['success'] = $this->language->get('text_success');
			$json['redirect'] = $this->url->link('information/quote.success', 'language=' . $this->config->get('config_language'), true);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function success(): void {
		$this->load->language('information/quote');

		$this->document->setTitle($this->language->get('heading_success'));

		$lang = 'language=' . $this->config->get('config_language');

		$data['breadcrumbs'] = [];
		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home', $lang)
		];
		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_success'),
			'href' => $this->url->link('information/quote.success', $lang)
		];

		$data['heading_title'] = $this->language->get('heading_success');
		$data['text_message'] = $this->language->get('text_success_message');
		$data['continue'] = $this->url->link('common/home', $lang);
		$data['button_continue'] = $this->language->get('button_continue');

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('information/quote_success', $data));
	}
}
