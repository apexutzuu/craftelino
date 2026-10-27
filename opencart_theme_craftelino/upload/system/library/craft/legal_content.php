<?php
namespace Opencart\System\Library\Craft;

/**
 * Conținut HTML pagini legale (termeni, livrare, confidențialitate).
 */
class LegalContent {
	/**
	 * @return array{name: string, cui: string, reg: string, address: string}
	 */
	public static function company(): array {
		return [
			'name'               => 'TOP UNDERGROUND GOODS SRL',
			'cui'                => '50982610',
			'reg'                => 'J2024046408004',
			'address'            => 'Str. Serg. Ion Nuțu nr. 8-10, Sector 5, București',
			'procurement_email'  => 'procurement@craftelino.ro',
		];
	}

	public static function slugForTitle(string $title): ?string {
		$t = mb_strtolower(trim($title));

		foreach (self::titleNeedles() as $slug => $needles) {
			foreach ($needles as $needle) {
				if (str_contains($t, $needle)) {
					return $slug;
				}
			}
		}

		return null;
	}

	/**
	 * @return array<string, list<string>>
	 */
	public static function titleNeedles(): array {
		return [
			'terms'    => ['terms & conditions', 'terms and conditions', 'termeni și condiții', 'termeni si conditii', 'termeni'],
			'delivery' => ['delivery information', 'delivery', 'informații livrare', 'informatii livrare', 'livrare'],
			'privacy'  => ['privacy policy', 'privacy', 'politica de confidențialitate', 'politica de confidentialitate', 'confidențialitate', 'confidentialitate'],
			'cookies'  => ['cookie', 'cookies', 'politica cookie', 'politică cookie', 'politica de cookie'],
		];
	}

	/**
	 * @return array<string, array{title: string, description: string}>
	 */
	public static function pages(string $store = 'Craftelino', string $email = '', string $phone = ''): array {
		$c = self::company();
		$mail = $c['procurement_email'] ?? ($email ?: 'procurement@craftelino.ro');

		$terms = '<p>Prezentele Termeni și condiții reglementează utilizarea site-ului <strong>' . htmlspecialchars($store, ENT_QUOTES, 'UTF-8') . '</strong> și contractarea comenzilor de produse promoționale personalizate.</p>'
			. '<h2>1. Datele operatorului</h2><ul>'
			. '<li><strong>Denumire:</strong> ' . htmlspecialchars($c['name'], ENT_QUOTES, 'UTF-8') . '</li>'
			. '<li><strong>CUI:</strong> ' . htmlspecialchars($c['cui'], ENT_QUOTES, 'UTF-8') . '</li>'
			. '<li><strong>Nr. Reg. Comerțului:</strong> ' . htmlspecialchars($c['reg'], ENT_QUOTES, 'UTF-8') . '</li>'
			. '<li><strong>Sediu:</strong> ' . htmlspecialchars($c['address'], ENT_QUOTES, 'UTF-8') . '</li>'
			. '<li><strong>E-mail:</strong> <a href="mailto:' . htmlspecialchars($mail, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($mail, ENT_QUOTES, 'UTF-8') . '</a></li>'
			. '</ul>'
			. '<h2>2. Produse și personalizare</h2><p>Produsele pot fi personalizate (print, broderie, gravură etc.) conform specificațiilor confirmate de client.</p>'
			. '<h2>3. Comenzi și oferte</h2><p>Comanda devine fermă după confirmarea noastră (e-mail sau telefon) și, după caz, acceptarea machetei.</p>'
			. '<h2>4. Prețuri și plată</h2><p>Prețurile sunt exprimate în RON, conform mențiunilor de pe pagina produsului.</p>'
			. '<h2>5. Livrare</h2><p>Termene orientative: 5–7 zile lucrătoare după confirmarea comenzii și a graficii.</p>'
			. '<h2>6. Dreptul de retragere</h2><p>Consumatorii beneficiază de 14 zile pentru produse nepersonalizate. Produsele personalizate la comandă pot fi exceptate conform legii.</p>'
			. '<h2>7. Garanție și reclamații</h2><p>Reclamațiile se transmit în 48 de ore de la recepție, cu dovezi foto.</p>'
			. '<h2>8. Protecția datelor</h2><p>Prelucrarea datelor se face conform Politicii de confidențialitate.</p>'
			. '<h2>9. Legea aplicabilă</h2><p>Contractul este guvernat de legea română.</p>';

		$delivery = '<p>Livrăm în România prin curier rapid sau transport agreat pentru comenzi mari.</p>'
			. '<h2>Termene</h2><ul>'
			. '<li>Produse standard: 2–5 zile lucrătoare de la confirmarea plății.</li>'
			. '<li>Produse personalizate: 5–7 zile lucrătoare după aprobarea machetei.</li>'
			. '<li>Comenzi B2B: termen stabilit în ofertă (10–15 zile lucrătoare).</li>'
			. '</ul>'
			. '<h2>Costuri transport</h2><p>Costul livrării este afișat în coș înainte de finalizarea comenzii.</p>'
			. '<h2>Recepție</h2><p>Verificați coletul la primire. Notați deteriorările pe documentele curierului și contactați-ne în 48 de ore.</p>';

		$privacy = '<p>Operatorul datelor este <strong>' . htmlspecialchars($c['name'], ENT_QUOTES, 'UTF-8') . '</strong>, CUI ' . htmlspecialchars($c['cui'], ENT_QUOTES, 'UTF-8') . ', cu sediul în ' . htmlspecialchars($c['address'], ENT_QUOTES, 'UTF-8') . '.</p>'
			. '<h2>Date colectate</h2><p>Nume, contact, adresă, date comandă, comunicări și cookie-uri tehnice.</p>'
			. '<h2>Scopuri</h2><ul><li>procesarea comenzilor;</li><li>suport clienți;</li><li>obligații legale;</li><li>securitatea site-ului.</li></ul>'
			. '<h2>Drepturile persoanelor vizate</h2><p>Acces, rectificare, ștergere, restricționare, portabilitate, opoziție și plângere la ANSPDCP. Solicitările se trimit la <a href="mailto:' . htmlspecialchars($mail, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($mail, ENT_QUOTES, 'UTF-8') . '</a>.</p>';

		$cookies = '<p>Această politică explică modul în care <strong>' . htmlspecialchars($store, ENT_QUOTES, 'UTF-8') . '</strong> folosește cookie-uri pe craftelino.ro.</p>'
			. '<h2>Ce sunt cookie-urile</h2><p>Fișiere mici stocate în browser, necesare pentru funcționarea magazinului (coș, sesiune, preferințe) sau, cu acordul tău, pentru analiză.</p>'
			. '<h2>Tipuri folosite</h2><ul>'
			. '<li><strong>Esențiale</strong> — autentificare, coș, securitate; nu pot fi dezactivate.</li>'
			. '<li><strong>Preferințe</strong> — limbă, monedă, acceptarea politicii.</li>'
			. '<li><strong>Analitică</strong> — doar dacă accepți bannerul de cookie-uri.</li>'
			. '</ul>'
			. '<h2>Gestionare</h2><p>Poți accepta sau limita cookie-urile din bannerul site-ului. Poți șterge cookie-urile din setările browserului.</p>'
			. '<h2>Contact</h2><p><a href="mailto:' . htmlspecialchars($mail, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($mail, ENT_QUOTES, 'UTF-8') . '</a></p>';

		return [
			'terms' => [
				'title'       => 'Termeni și condiții',
				'description' => $terms,
			],
			'delivery' => [
				'title'       => 'Informații livrare',
				'description' => $delivery,
			],
			'privacy' => [
				'title'       => 'Politica de confidențialitate',
				'description' => $privacy,
			],
			'cookies' => [
				'title'       => 'Politica cookie',
				'description' => $cookies,
			],
		];
	}
}
