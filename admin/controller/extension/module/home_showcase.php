<?php
class ControllerExtensionModuleHomeShowcase extends Controller {
	private $error = array();

	public function install() {
	}

	public function uninstall() {
	}

	public function index() {
		$this->load->language('extension/module/home_showcase');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/module');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$post = $this->request->post;
			$post['section'] = $this->normalizeSections(isset($post['section']) ? $post['section'] : array());

			if (!isset($this->request->get['module_id'])) {
				$this->model_setting_module->addModule('home_showcase', $post);
			} else {
				$this->model_setting_module->editModule($this->request->get['module_id'], $post);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		if (!isset($this->request->get['module_id'])) {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/home_showcase', 'user_token=' . $this->session->data['user_token'], true)
			);
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/home_showcase', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
			);
		}

		if (!isset($this->request->get['module_id'])) {
			$data['action'] = $this->url->link('extension/module/home_showcase', 'user_token=' . $this->session->data['user_token'], true);
		} else {
			$data['action'] = $this->url->link('extension/module/home_showcase', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
		}

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$module_info = $this->model_setting_module->getModule($this->request->get['module_id']);
		} else {
			$module_info = array();
		}

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($module_info['name'])) {
			$data['name'] = $module_info['name'];
		} else {
			$data['name'] = '';
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (isset($module_info['status'])) {
			$data['status'] = $module_info['status'];
		} else {
			$data['status'] = 1;
		}

		if (isset($this->request->post['section']) && ($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$sections = $this->normalizeSections($this->request->post['section']);
		} elseif (!empty($module_info['section']) && is_array($module_info['section'])) {
			$sections = $module_info['section'];
		} else {
			$sections = array();
		}

		$this->load->model('catalog/category');

		$category_rows = $this->model_catalog_category->getCategories(array(
			'sort'  => 'name',
			'order' => 'ASC',
			'start' => 0,
			'limit' => 5000
		));

		$data['category_options'] = array();

		foreach ($category_rows as $row) {
			$data['category_options'][] = array(
				'category_id' => (int)$row['category_id'],
				'name'        => strip_tags(html_entity_decode($row['name'], ENT_QUOTES, 'UTF-8'))
			);
		}

		$data['sections'] = array();

		foreach ($sections as $s) {
			if (!is_array($s)) {
				continue;
			}

			$data['sections'][] = array(
				'enabled'      => !empty($s['enabled']) ? 1 : 0,
				'source'       => isset($s['source']) ? (string)$s['source'] : 'category',
				'category_id'  => isset($s['category_id']) ? (int)$s['category_id'] : 0,
				'heading'      => isset($s['heading']) ? (string)$s['heading'] : '',
				'limit'        => isset($s['limit']) ? (int)$s['limit'] : 5
			);
		}

		if (!$data['sections']) {
			$data['sections'][] = array(
				'enabled'      => 1,
				'source'       => 'category',
				'category_id'  => 0,
				'heading'      => '',
				'limit'        => 5
			);
		}

		$data['source_options'] = array(
			array('value' => 'category',   'label' => $this->language->get('text_source_category')),
			array('value' => 'bestseller', 'label' => $this->language->get('text_source_bestseller')),
			array('value' => 'special',    'label' => $this->language->get('text_source_special')),
			array('value' => 'latest',     'label' => $this->language->get('text_source_latest'))
		);

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/home_showcase', $data));
	}

	private function normalizeSections($sections) {
		$allowed = array('category', 'bestseller', 'special', 'latest');
		$out = array();

		if (!is_array($sections)) {
			return $out;
		}

		foreach ($sections as $row) {
			if (!is_array($row)) {
				continue;
			}

			$source = isset($row['source']) ? (string)$row['source'] : 'category';

			if (!in_array($source, $allowed, true)) {
				$source = 'category';
			}

			$out[] = array(
				'enabled'     => !empty($row['enabled']) ? 1 : 0,
				'source'      => $source,
				'category_id' => isset($row['category_id']) ? (int)$row['category_id'] : 0,
				'heading'     => isset($row['heading']) ? trim((string)$row['heading']) : '',
				'limit'       => max(1, min(50, isset($row['limit']) ? (int)$row['limit'] : 5))
			);
		}

		return $out;
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/home_showcase')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!isset($this->request->post['name']) || (utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}

		return !$this->error;
	}
}
