<?php
namespace Opencart\System\Library\Extension\Craftelino\Cookie;

class Settings {
	public const CODE = 'module_cookie_consent';

	/**
	 * @return array<string, mixed>
	 */
	public static function defaults(): array {
		return [
			'status'           => true,
			'message'          => 'Folosim cookie-uri pentru funcționarea site-ului și pentru a îmbunătăți experiența ta. Poți afla mai multe în politica de confidențialitate.',
			'information_id'   => 0,
			'policy_label'     => 'Politica de confidențialitate',
			'button_accept'    => 'Accept toate',
			'button_reject'    => 'Doar necesare',
			'show_reject'      => true,
			'cookie_days'      => 365,
		];
	}

	/**
	 * @return array<string, mixed>
	 */
	public static function read(\Opencart\System\Engine\Registry $registry): array {
		$registry->get('load')->model('setting/setting');
		$flat = $registry->get('model_setting_setting')->getSetting(self::CODE);
		$out = self::defaults();

		foreach ($out as $key => $default) {
			$store_key = self::CODE . '_' . $key;

			if (!array_key_exists($store_key, $flat)) {
				continue;
			}

			$val = $flat[$store_key];

			if (in_array($key, ['status', 'show_reject'], true)) {
				$out[$key] = (bool)$val;
			} elseif ($key === 'information_id' || $key === 'cookie_days') {
				$out[$key] = (int)$val;
			} else {
				$out[$key] = (string)$val;
			}
		}

		if (isset($flat[self::CODE . '_status'])) {
			$out['status'] = (bool)$flat[self::CODE . '_status'];
		}

		return $out;
	}

	/**
	 * @param array<string, mixed> $data
	 */
	public static function write(\Opencart\System\Engine\Registry $registry, array $data): void {
		$flat = [
			self::CODE . '_status'         => !empty($data['status']) ? 1 : 0,
			self::CODE . '_message'        => trim((string)($data['message'] ?? '')),
			self::CODE . '_information_id' => (int)($data['information_id'] ?? 0),
			self::CODE . '_policy_label'   => trim((string)($data['policy_label'] ?? '')),
			self::CODE . '_button_accept'  => trim((string)($data['button_accept'] ?? '')),
			self::CODE . '_button_reject'  => trim((string)($data['button_reject'] ?? '')),
			self::CODE . '_show_reject'    => !empty($data['show_reject']) ? 1 : 0,
			self::CODE . '_cookie_days'    => max(1, (int)($data['cookie_days'] ?? 365)),
		];

		$registry->get('load')->model('setting/setting');
		$registry->get('model_setting_setting')->editSetting(self::CODE, $flat);
	}
}
