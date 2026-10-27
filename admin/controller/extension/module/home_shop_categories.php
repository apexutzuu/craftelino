<?php
class ControllerExtensionModuleHomeShopCategories extends Controller {
	private $error = array();

	public function install() {
	}

	public function uninstall() {
	}

	public function index() {
		$this->load->language('extension/module/home_shop_categories');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/module');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$post = $this->request->post;
			$post['see_all'] = !empty($post['see_all']) ? 1 : 0;
			$post['see_all_text'] = isset($post['see_all_text']) ? trim((string)$post['see_all_text']) : '';
			$post['see_all_href'] = isset($post['see_all_href']) ? trim((string)$post['see_all_href']) : '';
			$post['heading'] = isset($post['heading']) ? trim((string)$post['heading']) : '';
			$post['category_row'] = $this->normalizeCategoryRows(isset($post['category_row']) ? $post['category_row'] : array());

			if (!isset($this->request->get['module_id'])) {
				$this->model_setting_module->addModule('home_shop_categories', $post);
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

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => !isset($this->request->get['module_id'])
				? $this->url->link('extension/module/home_shop_categories', 'user_token=' . $this->session->data['user_token'], true)
				: $this->url->link('extension/module/home_shop_categories', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
		);

		$data['action'] = !isset($this->request->get['module_id'])
			? $this->url->link('extension/module/home_shop_categories', 'user_token=' . $this->session->data['user_token'], true)
			: $this->url->link('extension/module/home_shop_categories', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);

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

		if (isset($this->request->post['heading'])) {
			$data['heading'] = $this->request->post['heading'];
		} elseif (isset($module_info['heading'])) {
			$data['heading'] = $module_info['heading'];
		} else {
			$data['heading'] = '';
		}

		if (isset($this->request->post['see_all'])) {
			$data['see_all'] = $this->request->post['see_all'];
		} elseif (isset($module_info['see_all'])) {
			$data['see_all'] = $module_info['see_all'];
		} else {
			$data['see_all'] = 1;
		}

		if (isset($this->request->post['see_all_text'])) {
			$data['see_all_text'] = $this->request->post['see_all_text'];
		} elseif (isset($module_info['see_all_text'])) {
			$data['see_all_text'] = $module_info['see_all_text'];
		} else {
			$data['see_all_text'] = '';
		}

		if (isset($this->request->post['see_all_href'])) {
			$data['see_all_href'] = $this->request->post['see_all_href'];
		} elseif (isset($module_info['see_all_href'])) {
			$data['see_all_href'] = $module_info['see_all_href'];
		} else {
			$data['see_all_href'] = '';
		}

		if (isset($this->request->post['category_row']) && ($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$category_rows = $this->normalizeCategoryRows($this->request->post['category_row']);
		} elseif (!empty($module_info['category_row']) && is_array($module_info['category_row'])) {
			$category_rows = $module_info['category_row'];
		} else {
			$category_rows = array();
		}

		$this->load->model('catalog/category');

		$category_options = $this->model_catalog_category->getCategories(array(
			'sort'  => 'name',
			'order' => 'ASC',
			'start' => 0,
			'limit' => 5000
		));

		$data['category_options'] = array();

		foreach ($category_options as $row) {
			$data['category_options'][] = array(
				'category_id' => (int)$row['category_id'],
				'name'        => strip_tags(html_entity_decode($row['name'], ENT_QUOTES, 'UTF-8'))
			);
		}

		$data['category_rows'] = array();

		foreach ($category_rows as $cid) {
			$data['category_rows'][] = array('category_id' => (int)$cid);
		}

		if (!$data['category_rows']) {
			$data['category_rows'][] = array('category_id' => 0);
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/home_shop_categories', $data));
	}

	private function normalizeCategoryRows($rows) {
		$out = array();

		if (!is_array($rows)) {
			return $out;
		}

		foreach ($rows as $row) {
			if (!is_array($row)) {
				continue;
			}

			$id = isset($row['category_id']) ? (int)$row['category_id'] : 0;

			if ($id > 0) {
				$out[] = $id;
			}
		}

		return $out;
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/home_shop_categories')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!isset($this->request->post['name']) || (utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}

		return !$this->error;
	}
}
