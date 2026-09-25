<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';
$pageTitle = 'CreativeConnect | Services';
$pageDescription = 'Service packages and frequently asked questions for CreativeConnect.';
$currentPage = 'services';
require __DIR__ . '/includes/header.php';
?>
    <main id="main-content">
      <section class="page-hero" aria-labelledby="services-title">
        <div class="container">
          <span class="detail-mark" aria-hidden="true"
            ><i></i><i></i><i></i
          ></span>
          <p class="eyebrow">What we deliver</p>
          <h1 id="services-title">Packages for every project.</h1>
        </div>
      </section>

      <section class="section" aria-labelledby="pricing-title">
        <div class="container">
          <div class="section-header">
            <h2 id="pricing-title">Choose a package</h2>
            <div
              class="billing-toggle"
              role="group"
              aria-label="Billing period"
            >
              <button
                class="toggle-btn is-active"
                data-billing="monthly"
                aria-pressed="true"
              >
                Monthly
              </button>
              <button
                class="toggle-btn"
                data-billing="yearly"
                aria-pressed="false"
              >
                Yearly
              </button>
            </div>
          </div>

          <div class="pricing-grid" aria-live="polite">
            <article class="pricing-card">
              <h3>Portfolio</h3>
              <p class="price" data-monthly="450" data-yearly="4,200">
                $450
              </p>
              <p class="price-note">One deliverable, start to finish.</p>
              <ul>
                <li>One creative deliverable</li>
                <li>Client portal request &amp; tracking</li>
                <li>Two rounds of revisions</li>
              </ul>
            </article>

            <article class="pricing-card featured">
              <p class="chip">Most chosen</p>
              <h3>Creative Team</h3>
              <p class="price" data-monthly="1,600" data-yearly="1,350">
                $1,600
              </p>
              <p class="price-note">Ongoing support for growing brands.</p>
              <ul>
                <li>Everything in Portfolio</li>
                <li>Assigned team member &amp; progress updates</li>
                <li>Priority feedback turnaround</li>
              </ul>
            </article>

            <article class="pricing-card">
              <h3>Full Delivery</h3>
              <p class="price" data-monthly="3,200" data-yearly="2,700">
                $3,200
              </p>
              <p class="price-note">Full builds, fully managed.</p>
              <ul>
                <li>Everything in Creative Team</li>
                <li>Custom website or web app build</li>
                <li>Dedicated project dashboard</li>
              </ul>
            </article>
          </div>
        </div>
      </section>

      <section class="section section-alt" aria-labelledby="faq-title">
        <div class="container">
          <span class="detail-mark" aria-hidden="true"
            ><i></i><i></i><i></i
          ></span>
          <h2 id="faq-title">Frequently asked questions</h2>
          <div class="faq-list">
            <article class="faq-item">
              <h3>
                <button
                  class="faq-btn"
                  aria-expanded="false"
                  aria-controls="faq-1"
                  id="faq-btn-1"
                >
                  How do I submit a project request?
                </button>
              </h3>
              <div
                id="faq-1"
                class="faq-panel"
                role="region"
                aria-labelledby="faq-btn-1"
                hidden
              >
                <p>Send a brief through Contact. We confirm within 48 hours.</p>
              </div>
            </article>

            <article class="faq-item">
              <h3>
                <button
                  class="faq-btn"
                  aria-expanded="false"
                  aria-controls="faq-2"
                  id="faq-btn-2"
                >
                  Can I track progress once my project starts?
                </button>
              </h3>
              <div
                id="faq-2"
                class="faq-panel"
                role="region"
                aria-labelledby="faq-btn-2"
                hidden
              >
                <p>Yes — see your assigned team member, status and deadlines anytime.</p>
              </div>
            </article>

            <article class="faq-item">
              <h3>
                <button
                  class="faq-btn"
                  aria-expanded="false"
                  aria-controls="faq-3"
                  id="faq-btn-3"
                >
                  How are feedback and revisions handled?
                </button>
              </h3>
              <div
                id="faq-3"
                class="faq-panel"
                role="region"
                aria-labelledby="faq-btn-3"
                hidden
              >
                <p>Every package includes a feedback channel — nothing gets lost in email.</p>
              </div>
            </article>
          </div>
        </div>
      </section>
    </main>

<?php require __DIR__ . '/includes/footer.php'; ?>
