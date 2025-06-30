<?php

declare(strict_types=1);

namespace Hochwarth;

use ProcessWire\ModuleConfig;

use function ProcessWire\__;

class SecureHeadersConfig extends ModuleConfig
{
    public function __construct()
    {
        $this->add([
            // --- HSTS Fieldset ---
            [
                'type' => 'fieldset',
                'label' => 'HTTP Strict Transport Security (HSTS)',
                'icon' => 'lock',
                'description' => __("Erzwingt sichere (HTTPS) Verbindungen zum Server. Der `Strict-Transport-Security`-Header teilt dem Browser mit, dass dieser ausschließlich über HTTPS mit der Seite kommunizieren soll.\n**Warnung:** Nur aktivieren, wenn die Website und ALLE Subdomains vollständig über HTTPS erreichbar sind. Eine Fehlkonfiguration kann die Website unzugänglich machen.\n[MDN-Dokumentation](https://developer.mozilla.org/de/docs/Web/HTTP/Headers/Strict-Transport-Security)"),
                'collapsed' => true,
                'children' => [
                    [
                        'name' => 'hsts_enabled',
                        'type' => 'checkbox',
                        'label' => __('HSTS aktivieren'),
                        'label2' => __('Den Strict-Transport-Security-Header senden'),
                        'value' => false,
                    ],
                    [
                        'name' => 'hsts_max_age',
                        'type' => 'text',
                        'label' => __('max-age (in Sekunden)'),
                        'description' => __('Die Zeit in Sekunden, die der Browser die Anweisung speichern soll.'),
                        'notes' => __('Empfohlen: 31536000 (1 Jahr).'),
                        'value' => '31536000',
                        'columnWidth' => 50,
                        'showIf' => 'hsts_enabled=1',
                    ],
                    [
                        'name' => 'hsts_include_subdomains',
                        'type' => 'checkbox',
                        'label' => 'includeSubDomains',
                        'description' => __('Wenn gesetzt, gilt diese Regel auch für alle Subdomains.'),
                        'value' => false,
                        'columnWidth' => 25,
                        'showIf' => 'hsts_enabled=1',
                    ],
                    [
                        'name' => 'hsts_preload',
                        'type' => 'checkbox',
                        'label' => 'preload',
                        'description' => __('Erlaubt die Aufnahme in die "Preload"-Listen der Browser.'),
                        'notes' => __('Siehe [hstspreload.org](https://hstspreload.org/) vor der Aktivierung.'),
                        'value' => false,
                        'columnWidth' => 25,
                        'showIf' => 'hsts_enabled=1',
                    ],
                ]
            ],
            // --- Content-Security-Policy Fieldset ---
            [
                'type' => 'fieldset',
                'label' => 'Content Security Policy (CSP)',
                'icon' => 'shield',
                'description' => __("Der mächtigste Header zur Abwehr von Cross-Site-Scripting (XSS) und Dateneinschleusungs-Angriffen. Er legt fest, welche Ressourcen (Skripte, Bilder, etc.) von welchen Quellen geladen werden dürfen.\n**Warnung:** Eine fehlerhafte Konfiguration kann die Website unzugänglich machen. Neue Richtlinien sollten ggf. im 'Report-Only'-Modus getestet werden.\n[MDN-Dokumentation](https://developer.mozilla.org/de/docs/Web/HTTP/Headers/Content-Security-Policy)"),
                'collapsed' => false,
                'children' => [
                    [
                        'name' => 'csp_enabled',
                        'type' => 'checkbox',
                        'label' => __('CSP aktivieren'),
                        'value' => false,
                    ],
                    [
                        'name' => 'csp_report_only',
                        'type' => 'checkbox',
                        'label' => __('Report-Only Modus'),
                        'label2' => __('Verstöße nur protokollieren, nicht blockieren'),
                        'notes' => __('Empfohlen zum Testen neuer Richtlinien.'),
                        'value' => false,
                        'columnWidth' => 100,
                        'showIf' => 'csp_enabled=1',
                    ],
                    [
                        'name' => 'csp_policy',
                        'type' => 'textarea',
                        'label' => __('CSP-Richtlinien'),
                        'description' => __('Hier sollte das vollständige CSP-Regelwerk eingegeben werden. Der Platzhalter `nonce-proxy` sollte dort verwendet werden, wo die pro Request generierte Nonce eingefügt werden soll.'),
                        'notes' => __("Beispiel für eine strikte CSP: `default-src 'self' nonce-proxy; img-src 'self' data:; connect-src 'self'; form-action 'self'; upgrade-insecure-requests; block-all-mixed-content;`"),
                        'value' => "default-src 'self' nonce-proxy; img-src 'self' data:; connect-src 'self'; form-action 'self'; upgrade-insecure-requests; block-all-mixed-content;",
                        'rows' => 8,
                        'showIf' => 'csp_enabled=1',
                    ],
                    [
                        'name' => 'csp_report_to',
                        'type' => 'text',
                        'label' => __('Report to-URI'),
                        'description' => __('Eine URL sollte angegeben werden, an die Berichte über Verstöße gesendet werden sollen (setzt die `report-to`-Richtlinie). [MDN zu report-to](https://developer.mozilla.org/de/docs/Web/HTTP/Headers/Content-Security-Policy/report-to)'),
                        'value' => '',
                        'showIf' => 'csp_enabled=1',
                    ],
                ]
            ],
            // --- Framing Policy Fieldset ---
            [
                'type' => 'fieldset',
                'label' => 'Framing Policy (Clickjacking-Schutz)',
                'icon' => 'clone',
                'description' => __("Steuert, ob die Website auf einer anderen, fremden Webseite in einem `<iframe>` oder `<object>` eingebettet werden darf. Setzt die `frame-ancestors`-Richtlinie im CSP-Regelwerk (falls nicht bereits vorhanden) und den veralteten `X-Frame-Options`-Header für maximale Kompatibilität.\n[MDN zu frame-ancestors](https://developer.mozilla.org/de/docs/Web/HTTP/Headers/Content-Security-Policy/frame-ancestors) | [MDN zu X-Frame-Options](https://developer.mozilla.org/de/docs/Web/HTTP/Headers/X-Frame-Options)"),
                'collapsed' => true,
                'children' => [
                    [
                        'name' => 'framing_policy',
                        'type' => 'radios',
                        'label' => __('Framing-Regel'),
                        'options' => [
                            'none' => __("Jegliches Framing verbieten (`'none'`)"),
                            'self' => __("Nur von derselben Herkunft erlauben (`'self'`) – Empfohlen"),
                            'custom' => __('Von bestimmten Herkünften erlauben'),
                        ],
                        'value' => 'self',
                    ],
                    [
                        'name' => 'framing_custom_origins',
                        'type' => 'textarea',
                        'label' => __('Benutzerdefinierte, erlaubte Herkünfte'),
                        'description' => __("Eine durch Leerzeichen getrennte Liste von erlaubten Herkünften sollte eingegeben werden (z.B. `https://partner.com https://andere.site`).\n*Hinweis: Diese Herkünfte gelten nur für die CSP-Richtlinien `frame-ancestors`. Der veraltete `X-Frame-Options`-Header wird bei dieser Auswahl aus Sicherheitsgründen auf `DENY` gesetzt.*"),
                        'value' => '',
                        'showIf' => 'framing_policy=custom',
                    ],
                ]
            ],
            // --- Cross-Origin Policies Fieldset ---
            [
                'type' => 'fieldset',
                'label' => 'Cross-Origin Policies (COOP, COEP, CORP)',
                'icon' => 'globe',
                'description' => __('Diese Header bieten Schutz gegen spekulative Ausführungsangriffe wie Spectre, indem sie die Cross-Origin-Isolation aktivieren. Dies ist ein fortgeschrittenes Sicherheitsfeature. [Artikel auf web.dev](https://web.dev/why-coop-coep/)'),
                'collapsed' => true,
                'children' => [
                    [
                        'name' => 'coop_policy',
                        'type' => 'select',
                        'label' => 'Cross-Origin-Opener-Policy (COOP)',
                        'description' => __('Schützt vor Cross-Origin-Angriffen, indem Dokumente in einem eigenen Kontext isoliert werden. [MDN-Dokumentation](https://developer.mozilla.org/de/docs/Web/HTTP/Headers/Cross-Origin-Opener-Policy)'),
                        'options' => [
                            '' => __('Nicht gesetzt'),
                            'unsafe-none' => 'unsafe-none',
                            'same-origin-allow-popups' => 'same-origin-allow-popups (' . __('Empfohlen') . ')',
                            'same-origin' => 'same-origin',
                        ],
                        'value' => 'same-origin-allow-popups',
                    ],
                    [
                        'name' => 'coep_policy',
                        'type' => 'select',
                        'label' => 'Cross-Origin-Embedder-Policy (COEP)',
                        'description' => __('Verhindert, dass ein Dokument Cross-Origin-Ressourcen lädt, die nicht explizit die Erlaubnis dazu erteilen. [MDN-Dokumentation](https://developer.mozilla.org/de/docs/Web/HTTP/Headers/Cross-Origin-Embedder-Policy)'),
                        'options' => [
                            '' => __('Nicht gesetzt'),
                            'unsafe-none' => 'unsafe-none',
                            'require-corp' => 'require-corp',
                        ],
                        'value' => '',
                        'notes' => __('Warnung: `require-corp` kann das Laden von Bildern, Skripten und Stilen verhindern, wenn diese nicht die korrekten Cross-Origin-Header (CORP oder CORS) senden.'),
                    ],
                    [
                        'name' => 'corp_policy',
                        'type' => 'select',
                        'label' => 'Cross-Origin-Resource-Policy (CORP)',
                        'description' => __('Kontrolliert, welche Cross-Origin-Seiten eigene Ressourcen einbetten dürfen. Dies ist ein Header, den *diese Seite* sendet, um sich vor Einbettung durch andere zu schützen. [MDN-Dokumentation](https://developer.mozilla.org/de/docs/Web/HTTP/Headers/Cross-Origin-Resource-Policy)'),
                        'options' => [
                            '' => __('Nicht gesetzt'),
                            'same-site' => 'same-site',
                            'same-origin' => 'same-origin (' . __('Empfohlen') . ')',
                            'cross-origin' => 'cross-origin',
                        ],
                        'value' => 'same-origin',
                    ],
                ]
            ],
            // --- Other Headers Fieldset ---
            [
                'type' => 'fieldset',
                'label' => __('Weitere Sicherheits-Header'),
                'icon' => 'cogs',
                'collapsed' => true,
                'children' => [
                    [
                        'name' => 'x_content_type_options_enabled',
                        'type' => 'checkbox',
                        'label' => 'X-Content-Type-Options',
                        'label2' => __('`nosniff` aktivieren, um MIME-Sniffing-Angriffe zu verhindern.'),
                        'description' => __("Verhindert, dass der Browser versucht, den Inhaltstyp einer Ressource zu 'erraten', was eine Sicherheitslücke sein kann. [MDN-Dokumentation](https://developer.mozilla.org/de/docs/Web/HTTP/Headers/X-Content-Type-Options)"),
                        'notes' => __('Dringend empfohlen. Standardmäßig aktiviert.'),
                        'value' => true,
                    ],
                    [
                        'name' => 'referrer_policy',
                        'type' => 'select',
                        'label' => 'Referrer-Policy',
                        'description' => __('Steuert, wie viele Referrer-Informationen (die Herkunftsseite) mit Anfragen gesendet werden. [MDN-Dokumentation](https://developer.mozilla.org/de/docs/Web/HTTP/Headers/Referrer-Policy)'),
                        'options' => [
                            'no-referrer' => 'no-referrer',
                            'no-referrer-when-downgrade' => 'no-referrer-when-downgrade',
                            'origin' => 'origin',
                            'origin-when-cross-origin' => 'origin-when-cross-origin',
                            'same-origin' => 'same-origin',
                            'strict-origin' => 'strict-origin',
                            'strict-origin-when-cross-origin' => 'strict-origin-when-cross-origin (' . __('Empfohlen') . ')',
                            'unsafe-url' => 'unsafe-url',
                        ],
                        'value' => 'strict-origin-when-cross-origin',
                    ],
                ]
            ],
            // --- Permissions-Policy Fieldset ---
            [
                'type' => 'fieldset',
                'label' => 'Permissions Policy',
                'icon' => 'camera-retro',
                'description' => __('Kontrolliert den Zugriff auf Browser-Funktionen und APIs (Kamera, Mikrofon, Geolokalisierung etc.). Ersetzt den älteren `Feature-Policy`-Header. [MDN-Dokumentation](https://developer.mozilla.org/de/docs/Web/HTTP/Headers/Permissions-Policy)'),
                'collapsed' => true,
                'children' => [
                    [
                        'name' => 'permissions_policy_enabled',
                        'type' => 'checkbox',
                        'label' => __('Permissions-Policy aktivieren'),
                        'value' => false,
                    ],
                    [
                        'name' => 'permissions_policy',
                        'type' => 'textarea',
                        'label' => __('Permissions-Policy Richtlinien'),
                        'description' => __('Das vollständige Permissions-Policy-Regelwerk sollte eingegeben werden. `()` kann verwendet werden, um ein Feature zu deaktivieren, `(self)` um es für dieselbe Herkunft zu erlauben, oder `(self "https://example.com")` für spezifische Herkünfte.'),
                        'notes' => __('Beispiel, um den Zugriff auf gängige Features zu beschränken: `geolocation=(), camera=(), microphone=(), usb=()`'),
                        'value' => 'geolocation=(), camera=(), microphone=(), usb=()',
                        'rows' => 5,
                        'showIf' => 'permissions_policy_enabled=1',
                    ],
                ]
            ],
        ]);
    }
}
