<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../config/config.php";
// TODO : Navbar (Login, Sign up, Search bar? Filters?)
// TODO : Review tiles : Explore section with tiles of reviews
// TODO : Pagination, Load more, page buttons, etc...


?>
<section id="explore" class="container mt-4">
    <h2>Explore</h2>
    <div id="review-list" class="row">
    </div>
</section>
<script src="../assets/js/ajax.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', async () => {
      const reviews = await fetchReviews();
      updateReviewList(reviews, true);
    });
    document.getElementById('review-list').addEventListener('click', async function (e) {
            const card = e.target.closest('.card');
            if (card) {
                const reviewId = card.id.replace('review-', '');
                const review = await fetchReviewById(reviewId);
                openReviewModal(review);
            }
        });
</script>