<?php
namespace Opencart\Catalog\Controller\Information;
/**
 * Pagini legale statice (termeni, livrare, confidențialitate) + date firmă footer.
 */
class Legal extends \Opencart\System\Engine\Controller {
	public static function company(): array {
		return [
			'name'               => 'TOP UNDERGROUND GOODS SRL',
			'cui'                => '50982610',
			'reg'                => 'J2024046408004',
			'address'            => 'Str. Verzisori 6, Bl. D, Ap. B118',
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
				'title' => 'Termeni și condiții',
				'href'  => $link('information/legal/terms', $args),
			],
			[
				'title' => 'Informații livrare',
				'href'  => $link('information/legal/delivery', $args),
			],
			[
				'title' => 'Politica de confidențialitate',
				'href'  => $link('information/legal/privacy', $args),
			],
		];
	}

	public function terms(): void {
		$c = self::company();
		$store = $this->config->get('config_name');

		$html = '<p>Prezentele Termeni și condiții reglementează utilizarea site-ului <strong>' . htmlspecialchars($store, ENT_QUOTES, 'UTF-8') . '</strong> și contractarea comenzilor de produse promoționale personalizate.</p>'
			. '<h2>1. Datele operatorului</h2>'
			. '<ul>'
			. '<li><strong>Denumire:</strong> ' . htmlspecialchars($c['name'], ENT_QUOTES, 'UTF-8') . '</li>'
			. '<li><strong>CUI:</strong> ' . htmlspecialchars((string)$c['cui'], ENT_QUOTES, 'UTF-8') . '</li>'
			. '<li><strong>Nr. Reg. Comerțului:</strong> ' . htmlspecialchars($c['reg'], ENT_QUOTES, 'UTF-8') . '</li>'
			. '<li><strong>Sediu:</strong> ' . htmlspecialchars($c['address'], ENT_QUOTES, 'UTF-8') . '</li>'
			. '<li><strong>E-mail achiziții / oferte:</strong> <a href="mailto:' . htmlspecialchars($c['procurement_email'], ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($c['procurement_email'], ENT_QUOTES, 'UTF-8') . '</a></li>'
			. ($this->config->get('config_email') ? '<li><strong>E-mail general:</strong> ' . htmlspecialchars($this->config->get('config_email'), ENT_QUOTES, 'UTF-8') . '</li>' : '')
			. '</ul>'
			. '<h2>2. Produse și personalizare</h2>'
			. '<p>Produsele pot fi personalizate (print, broderie, gravură etc.) conform specificațiilor confirmate de client. Culorile afișate pe ecran pot varia ușor față de produsul final.</p>'
			. '<h2>3. Comenzi și oferte</h2>'
			. '<p>Comanda devine fermă după confirmarea noastră (e-mail sau telefon) și, după caz, acceptarea machetei. Pentru comenzi B2B sau volume mari, termenele și prețurile se stabilesc prin ofertă scrisă.</p>'
			. '<h2>4. Prețuri și plată</h2>'
			. '<p>Prețurile sunt exprimate în moneda afișată pe site și pot include sau exclude TVA, conform mențiunilor de pe pagina produsului. Metodele de plată disponibile sunt cele afișate la finalizarea comenzii.</p>'
			. '<h2>5. Livrare</h2>'
			. '<p>Termenele de livrare sunt orientative (de regulă 5–7 zile lucrătoare după confirmarea comenzii și a graficii). Detalii suplimentare în pagina <a href="' . htmlspecialchars($this->url->link('information/legal/delivery', 'language=' . $this->config->get('config_language')), ENT_QUOTES, 'UTF-8') . '">Informații livrare</a>.</p>'
			. '<h2>6. Dreptul de retragere</h2>'
			. '<p>Consumatorii beneficiază de dreptul de retragere în 14 zile calendaristice pentru produse nepersonalizate, conform legislației aplicabile. Produsele realizate la comandă sau personalizate conform specificațiilor clientului pot fi exceptate de la retragere, conform art. 16 lit. c) din OUG 34/2014.</p>'
			. '<h2>7. Garanție și reclamații</h2>'
			. '<p>Reclamațiile privind defecte sau neconformități se transmit în termen de 48 de ore de la recepție, pe e-mail sau telefon, cu dovezi foto. Vom remedia sau înlocui produsele defecte în limita posibilităților tehnice.</p>'
			. '<h2>8. Protecția datelor</h2>'
			. '<p>Prelucrarea datelor cu caracter personal se face conform <a href="' . htmlspecialchars($this->url->link('information/legal/privacy', 'language=' . $this->config->get('config_language')), ENT_QUOTES, 'UTF-8') . '">Politicii de confidențialitate</a>.</p>'
			. '<h2>9. Legea aplicabilă</h2>'
			. '<p>Prezentul contract este guvernat de legea română. Litigiile se soluționează pe cale amiabilă sau de instanțele competente din România.</p>';

		$this->renderPage('Termeni și condiții', $html);
	}

	public function delivery(): void {
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
		$c = self::company();

		$html = '<p>Operatorul datelor cu caracter personal este <strong>' . htmlspecialchars($c['name'], ENT_QUOTES, 'UTF-8') . '</strong>, CUI ' . htmlspecialchars((string)$c['cui'], ENT_QUOTES, 'UTF-8') . ', cu sediul în ' . htmlspecialchars($c['address'], ENT_QUOTES, 'UTF-8') . '.</p>'
			. '<h2>Date colectate</h2>'
			. '<p>Putem prelucra: nume, date de contact (e-mail, telefon), adresă de livrare/facturare, date despre comandă, comunicări și, tehnic, adresa IP și cookie-uri necesare funcționării site-ului.</p>'
			. '<h2>Scopuri</h2>'
			. '<ul>'
			. '<li>procesarea și livrarea comenzilor;</li>'
			. '<li>comunicare privind comenzi, oferte și suport;</li>'
			. '<li>îndeplinirea obligațiilor legale (facturare, contabilitate);</li>'
			. '<li>îmbunătățirea serviciilor și securitatea site-ului.</li>'
			. '</ul>'
			. '<h2>Temei legal</h2>'
			. '<p>Executarea contractului, obligații legale, interes legitim și, unde este cazul, consimțământul (ex. newsletter, dacă este activ).</p>'
			. '<h2>Durata stocării</h2>'
			. '<p>Păstrăm datele pe perioada necesară îndeplinirii scopurilor și conform termenelor legale de arhivare.</p>'
			. '<h2>Drepturile persoanelor vizate</h2>'
			. '<p>Aveți dreptul de acces, rectificare, ștergere, restricționare, portabilitate și opoziție, precum și dreptul de a depune plângere la ANSPDCP. Solicitările se trimit la <a href="mailto:' . htmlspecialchars($c['procurement_email'], ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($c['procurement_email'], ENT_QUOTES, 'UTF-8') . '</a>.</p>'
			. '<h2>Securitate</h2>'
			. '<p>Aplicăm măsuri tehnice și organizatorice rezonabile pentru protejarea datelor împotriva accesului neautorizat.</p>';

		$this->renderPage('Politica de confidențialitate', $html);
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
