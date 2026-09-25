    <footer class="site-footer">
      <div class="container footer-wrap">
        <p>&copy; 2026 CreativeConnect. ICT312 Advanced Web Information Systems.</p>
        <ul aria-label="Footer links">
          <li><a href="services.php">Services</a></li>
          <li><a href="contact.php">New Request</a></li>
          <?php if (logged_in()): ?>
            <li><a href="dashboard.php">Dashboard</a></li>
          <?php else: ?>
            <li><a href="login.php">Login</a></li>
          <?php endif; ?>
        </ul>
      </div>
    </footer>
    <script src="js/main.js"></script>
  </body>
</html>
