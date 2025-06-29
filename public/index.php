<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

require_once "../config/config.php";
require_once "../includes/auth.php";
// if (!empty($_SESSION['authenticated']) && $_SESSION["authenticated"] === true) {
//   header("Location: feed.php");
//   exit();
// }
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Explore - Cafe Run</title>
  <!-- TODO : Add CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <!-- <link rel="icon" href="../assets/icon/cafe-run-icon.png"> -->
  <link rel="stylesheet" href="../assets/css/styles.css">

</head>

<body>
  <?php
  require_once "../includes/navbar.php";
  require_once "../config/config.php";
  if (session_status() === PHP_SESSION_NONE) {
    session_start();
  }
  ?>

  <div class="main-container">
    <header class="header">
      <h1>Explore</h1>
      <p>Discover other's runs</p>
    </header>

    <div class="content-area">
      <div id="review-grid">
        <div class="loading-state">
          <div class="spinner"></div>
          Loading reviews...
        </div>
      </div>

      <div class="pagination-hint">
        <button class="page-nav" id="prev-page" disabled>Previous</button>
        <div class="page-indicator">
          <div class="page-dot active"></div>
          <div class="page-dot"></div>
          <div class="page-dot"></div>
          <div class="page-dot"></div>
          <div class="page-dot"></div>
        </div>
        <div class="page-info">Page <span id="current-page">1</span> of <span id="total-pages">5</span></div>
        <button class="page-nav" id="next-page">Next <button>
      </div>
    </div>
    <!-- Review Modal -->
    <div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="reviewModalLabel"></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body" id="reviewModalBody"></div>
          <div class="modal-footer" id="reviewModalFooter"></div>
        </div>
      </div>
    </div>

  </div>

  <script src="../assets/js/ajax.js"></script>
  <script>
    window.currentUserId = <?= json_encode($_SESSION['id']) ?>;
    window.currentUserRole = <?= isset($_SESSION['role']) ? json_encode($_SESSION['role']) : 'null' ?>;
    window.isAdmin = false;

    let allReviews = [];
    let exploreGridModule;

    document.addEventListener('DOMContentLoaded', async () => {
      try {
        allReviews = await fetchReviews();
        exploreGridModule = createPaginatedGrid({
          gridId: "review-grid",
          reviews: allReviews,
          createCardHtml: createReviewTile,
          perPage: 15,
          onCardClick: review => fetchReviewById(review.id).then(openReviewModal)
        });
        exploreGridModule.render();
        exploreGridModule.setupPagination(
          { prev: '#prev-page', next: '#next-page' },
          '.page-dot',
          { current: '#current-page', total: '#total-pages' }
        );
        exploreGridModule.updatePaginationUI(
          { prev: '#prev-page', next: '#next-page' },
          '.page-dot',
          { current: '#current-page', total: '#total-pages' }
        );
      } catch (error) {
        document.getElementById('review-grid').innerHTML = `
            <div style="grid-column: 1 / -1; text-align: center; color: var(--text);">
                <h3>Oops! Something went wrong</h3>
                <p>Unable to load cafe reviews. Please refresh the page.</p>
            </div>
        `;
      }
    });

  </script>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q"
    crossorigin="anonymous"></script>
</body>

</html>