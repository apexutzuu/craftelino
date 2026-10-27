<?php
class ControllerExtensionModuleHomeShopCategories extends Controller {
	public function index($setting) {
		if (!is_array($setting) || empty($setting['status'])) {
			return '';
		}

		$this->load->language('extension/module/home_shop_categories');

		$this->load->model('catalog/category');

		$ids = !empty($setting['category_row']) && is_array($setting['category_row']) ? $setting['category_row'] : array();

		$categories = array();

		foreach ($ids as $category_id) {
			$category_id = (int)$category_id;

			if ($category_id < 1) {
				continue;
			}

			$info = $this->model_catalog_category->getCategory($category_id);

			if (!$info) {
				continue;
			}

			$path = $this->getCategoryPathParam($category_id);

			$categories[] = array(
				'name' => $info['name'],
				'href' => $this->url->link('product/category', 'path=' . $path, true)
			);
		}

		if (!$categories) {
			return '';
		}

		$heading = isset($setting['heading']) ? trim((string)$setting['heading']) : '';

		if ($heading === '') {
			$heading = $this->language->get('text_default_heading');
		}

		$data['heading'] = $heading;
		$data['categories'] = $categories;

		$data['see_all_href'] = '';
		$data['see_all_text'] = '';

		if (!empty($setting['see_all'])) {
			$see_text = isset($setting['see_all_text']) ? trim((string)$setting['see_all_text']) : '';

			if ($see_text === '') {
				$see_text = $this->language->get('text_default_see_all');
			}

			$data['see_all_text'] = $see_text;

			$custom = isset($setting['see_all_href']) ? trim((string)$setting['see_all_href']) : '';

			if ($custom !== '') {
				if (preg_match('#^https?://#i', $custom)) {
					$data['see_all_href'] = $custom;
				} elseif ($custom[0] === '/') {
					$data['see_all_href'] = $custom;
				} else {
					$data['see_all_href'] = $this->url->link($custom, '', true);
				}
			} else {
				$roots = $this->model_catalog_category->getCategories(0);

				if ($roots) {
					$first_id = (int)$roots[0]['category_id'];
					$data['see_all_href'] = $this->url->link('product/category', 'path=' . $this->getCategoryPathParam($first_id), true);
				}
			}
		}

		return $this->load->view('extension/module/home_shop_categories', $data);
	}

	private function getCategoryPathParam($category_id) {
		$query = $this->db->query("SELECT `path_id` FROM `" . DB_PREFIX . "category_path` WHERE `category_id` = '" . (int)$category_id . "' ORDER BY `level` ASC");
		$ids = array();

		foreach ($query->rows as $row) {
			$ids[] = (int)$row['path_id'];
		}

		if (!$ids) {
			return (string)(int)$category_id;
		}

		return implode('_', $ids);
	}
}
