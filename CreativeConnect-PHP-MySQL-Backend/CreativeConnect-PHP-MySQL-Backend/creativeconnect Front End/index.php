<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';
$pageTitle = 'CreativeConnect | Team Portfolio & Client Management';
$pageDescription = 'CreativeConnect is a team portfolio and client management platform for creative teams.';
$currentPage = 'home';
require __DIR__ . '/includes/header.php';
?>
    <main id="main-content">
      <section class="hero figma-hero" aria-labelledby="hero-title">
        <div class="container hero-grid">
          <article>
            <span class="detail-mark" aria-hidden="true"
              ><i></i><i></i><i></i
            ></span>
            <p class="eyebrow">Creative work. One platform.</p>
            <h1 id="hero-title">
              Your work. Your clients. One place.
            </h1>
            <p class="lead">
              Portfolios, requests, and progress tracking for creative
              teams — without the tool overload.
            </p>
            <div class="hero-actions">
              <a class="btn btn-primary" href="#portfolio">View Work</a>
              <a class="btn btn-ghost" href="contact.php">Get Started</a>
            </div>
          </article>
          <aside class="hero-phones" aria-label="Client portal app preview">
            <img
              src="assets/image/image 5.svg"
              alt="Mobile sign-in screen preview of the CreativeConnect client portal"
            />
          </aside>
        </div>
      </section>

      <section
        id="about"
        class="section who-section"
        aria-labelledby="who-title"
      >
        <div class="container">
          <div class="who-grid">
            <figure>
              <img
                src="assets/image/fahim-muntashir-v-FOvoL3onk-unsplash.svg"
                alt="Creative team members reviewing a project together"
              />
            </figure>
            <article>
              <span class="detail-mark" aria-hidden="true"
                ><i></i><i></i><i></i
              ></span>
              <p class="chip">Our story</p>
              <h2 id="who-title">Who are we?</h2>
              <p class="who-lead">Built for creative teams.</p>
              <p>
                CreativeConnect brings portfolios, requests, and feedback
                into one secure platform — so the team can focus on the work,
                not the admin.
              </p>
            </article>
          </div>
        </div>
      </section>

      <section id="services" class="section" aria-labelledby="services-title">
        <div class="container">
          <span class="detail-mark" aria-hidden="true"
            ><i></i><i></i><i></i
          ></span>
          <h2 id="services-title">What We Offer</h2>
          <p class="section-sub">Everything your project needs, in one place.</p>
          <div class="services-grid">
            <article class="service-card">
              <div class="service-icon" aria-hidden="true">
                <img src="assets/icons/Group 43.svg" alt="" />
              </div>
              <h3>Video &amp; Digital Editing</h3>
              <p>From raw footage to finished cut.</p>
            </article>
            <article class="service-card">
              <div class="service-icon" aria-hidden="true">
                <img src="assets/icons/Group 679.svg" alt="" />
              </div>
              <h3>Web Development</h3>
              <p>Custom sites and apps, built to last.</p>
            </article>
            <article class="service-card">
              <div class="service-icon" aria-hidden="true">
                <img src="assets/icons/Group 682.svg" alt="" />
              </div>
              <h3>Content Creation</h3>
              <p>Graphics and copy, made for your brand.</p>
            </article>
            <article class="service-card">
              <div class="service-icon" aria-hidden="true">
                <img src="assets/icons/Vector (3).svg" alt="" />
              </div>
              <h3>Client Portal Access</h3>
              <p>Track progress and deadlines in one dashboard.</p>
            </article>
            <article class="service-card">
              <div class="service-icon" aria-hidden="true">
                <img src="assets/icons/Frame.svg" alt="" />
              </div>
              <h3>Feedback &amp; Revisions</h3>
              <p>Comments and revisions, never lost in email.</p>
            </article>
            <article class="service-card">
              <div class="service-icon" aria-hidden="true">
                <img src="assets/icons/Group 43.svg" alt="" />
              </div>
              <h3>IT Support &amp; Consulting</h3>
              <p>Guidance on tooling, hosting and delivery.</p>
            </article>
          </div>
        </div>
      </section>

      <section
        id="portfolio"
        class="section portfolio"
        aria-labelledby="portfolio-title"
      >
        <div class="container portfolio-grid">
          <article>
            <span class="detail-mark" aria-hidden="true"
              ><i></i><i></i><i></i
            ></span>
            <h2 id="portfolio-title">Our Portfolio</h2>
            <p class="section-sub">Selected work from across the team.</p>
            <p>
              Every project is tracked start to finish — request,
              assignment, progress, feedback.
            </p>
            <a class="btn btn-primary" href="services.php">See Services</a>
          </article>
          <figure>
            <img
              src="assets/image/image 7.svg"
              alt="Mobile screens showing a client project status view"
            />
          </figure>
        </div>
      </section>

      <section
        id="team"
        class="section team-section"
        aria-labelledby="team-title"
      >
        <div class="container">
          <span class="detail-mark" aria-hidden="true"
            ><i></i><i></i><i></i
          ></span>
          <h2 id="team-title">Meet the Team</h2>
          <p class="section-sub">The people behind the work.</p>
          <div class="team-grid">
            <article class="team-card">
              <img
                src="assets/image/image 5.svg"
                alt="Portrait of Aries Joshua Andaya"
              />
              <h3>ARIES JOSHUA ANDAYA</h3>
              <p>WEB DEVELOPER &amp; SYSTEM ADMINISTRATOR</p>
            </article>
            <article class="team-card">
              <img
                src="assets/image/image 5.svg"
                alt="Portrait of Phommaxay Phimmavong"
              />
              <h3>PHOMMAXAY PHIMMAVONG</h3>
              <p>UI/UX DESIGNER</p>
            </article>
            <article class="team-card">
              <img
                src="assets/image/image 6.svg"
                alt="Portrait of Srijana Bhandari"
              />
              <h3>SRIJANA BHANDARI</h3>
              <p>CONTENT CREATOR</p>
            </article>
            <article class="team-card">
              <img
                src="assets/image/image 7.svg"
                alt="Portrait of Ishika"
              />
              <h3>ISHIKA</h3>
              <p>VIDEO &amp; DIGITAL EDITOR</p>
            </article>
          </div>
        </div>
      </section>

      <section class="section clients" aria-labelledby="clients-title">
        <div class="container">
          <span class="detail-mark" aria-hidden="true"
            ><i></i><i></i><i></i
          ></span>
          <h2 id="clients-title">What Clients Say</h2>
          <p class="section-sub">Real feedback, from real projects.</p>
          <div class="testimonial-slider" aria-label="Client testimonials">
            <button
              class="slider-btn"
              type="button"
              data-slide="prev"
              aria-label="Previous testimonials"
            >
              &#8249;
            </button>
            <div class="testimonial-track" data-slider-track>
              <article class="testimonial-card">
                <h3>Client A</h3>
                <p class="stars" aria-label="5 star rating">★★★★★</p>
                <p>
                  Being able to track our video project status without
                  chasing emails made the whole process far less stressful.
                </p>
              </article>
              <article class="testimonial-card">
                <h3>Client B</h3>
                <p class="stars" aria-label="5 star rating">★★★★★</p>
                <p>
                  Clear communication and a real portfolio to review before we
                  committed. The revision process was simple to follow.
                </p>
              </article>
              <article class="testimonial-card">
                <h3>Client C</h3>
                <p class="stars" aria-label="5 star rating">★★★★★</p>
                <p>
                  Our web project stayed on schedule and every update showed
                  up in the portal, exactly when they said it would.
                </p>
              </article>
              <article class="testimonial-card">
                <h3>Client D</h3>
                <p class="stars" aria-label="5 star rating">★★★★★</p>
                <p>
                  From the first enquiry to the final delivery, everything was
                  organised, documented and easy to follow.
                </p>
              </article>
            </div>
            <button
              class="slider-btn"
              type="button"
              data-slide="next"
              aria-label="Next testimonials"
            >
              &#8250;
            </button>
          </div>
        </div>
      </section>

      <section class="section section-alt cta" aria-labelledby="cta-title">
        <div class="container cta-grid">
          <article>
            <span class="detail-mark" aria-hidden="true"
              ><i></i><i></i><i></i
            ></span>
            <h2 id="cta-title">Got a project in mind?</h2>
            <p>Tell us your brief. We'll take it from there.</p>
            <a class="btn btn-primary" href="contact.php">Get Started</a>
          </article>
          <figure>
            <img
              src="assets/image/image 6.svg"
              alt="Hand holding a phone showing a project status update"
            />
          </figure>
        </div>
      </section>
    </main>

<?php require __DIR__ . '/includes/footer.php'; ?>
