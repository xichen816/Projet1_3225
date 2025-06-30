<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
error_reporting(E_ALL);
ini_set('display_errors', 1);

// TODO : Navbar (Search bar, Filters, Hamburger menu (profile, settings, logout)
// TODO : Content (Two columns (user feed (recent reviews, followed profiles), user reviews))
// TODO : Loading/Pagination (Load more, page buttons, etc...)
// TODO : Add styling
require_once "../config/config.php";
require_once "../includes/auth.php";
requireAuth();

require_once "../src/review.php";
$review = new Review($pdo);
$userId = $_SESSION['id'];
$userFeed = $review->fetchFeed($userId);
$userReviews = $review->fetchById($userId);
$stmt = $pdo->query("SELECT id, nom FROM cafes");
$cafes = $stmt->fetchAll(PDO::FETCH_ASSOC);
$catstmt = $pdo->query("SELECT id, nom FROM categories");
$categories = $catstmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <title>Feed - Cafe Run</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>

<body class="scrollable-page">
    <?php require_once "../includes/navbar.php"; ?>

    <div class="container-fluid main-content px-4 pt-4">
        <div class="feed">
            <h2>My Feed</h2>
            <p>Check Out the latest updates</p>
            <div id="feed-list" class="mb-5">
                <div class="review-cards-row row gx-3 gy-3"></div>
                <div class="d-flex justify-content-center my-3">
                    <button class="btn btn-load-more" id="loadMoreButton">Load More</button>
                </div>
            </div>

            <h2>My Reviews</h2>
            <p>See all your runs</p>
            <div id="user-review-list" class="mb-5"></div>
        </div>
    </div>

    <button id="createReviewButton" class="btn btn-lg btn-create rounded-circle shadow position-fixed p-0">
    <div class="create-button-icon">+</div>
    </button>

    <!-- Review Modal -->
    <div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="reviewModalForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="reviewModalLabel"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="reviewModalBody"></div>
                    <div class="modal-footer" id="reviewModalFooter"></div>
                </form>
            </div>
        </div>
    </div>

    <!-- Review Creation Modal -->

    <div class="modal fade" id="createReviewModal" tabindex="-1" aria-labelledby="createReviewModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg create-modal">
            <div class="modal-content">
                <form id="createReviewForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createReviewModalLabel">New Review</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="cafeSelect" class="form-label">Cafe</label>
                            <select class="form-select" id="cafeSelect" name="id_cafe" required>
                                <option value="">Select your cafe</option>
                                <?php foreach ($cafes as $cafe): ?>
                                    <option value="<?= $cafe['id'] ?>"><?= htmlspecialchars($cafe['nom']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="reviewTitle" class="form-label">Title</label>
                            <input type="text" class="form-control" id="reviewTitle" name="titre" required>
                        </div>
                        <div class="mb-3">
                            <label for="reviewContent" class="form-label">Your Review</label>
                            <textarea class="form-control" id="reviewContent" name="contenu" rows="4"
                                required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="categoryInput" class="form-label">Categories</label>
                            <input type="text" class="form-control" id="categoryInput" placeholder="Apply one or more categories" readonly />
                            <select id="categorySelect" class="form-select hidden" multiple>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nom']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div id="selectedCategoryTags" class="mt-2 d-flex flex-wrap gap-2"></div>
                        </div>
                        <div class="mb-3">
                            <label for="reviewRating" class="form-label">Your Rating (1 to 5)</label>
                            <input type="number" class="form-control" id="reviewRating" name="rating" min="1" max="5"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="reviewPhotos" class="form-label">Your Pictures</label>
                            <input type="file" class="form-control" id="reviewPhotos" name="photos[]" multiple
                                accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Fermer</button>
                        <button type="submit" class="btn btn-primary">Publish</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q"
        crossorigin="anonymous"></script>
    <script src="../assets/js/ajax.js"></script>
    <script>
        window.currentUserId = <?= json_encode($_SESSION['id']) ?>;
        window.currentUserRole = <?= isset($_SESSION['role']) ? json_encode($_SESSION['role']) : 'null' ?>;
        window.isAdmin = false;

        document.getElementById('feed-list').addEventListener('click', async function (e) {
            const card = e.target.closest('.card');
            if (card) {
                const reviewId = card.id.replace('review-', '');
                const review = await fetchReviewById(reviewId);
                openReviewModal(review);
            }
        });

        document.getElementById('user-review-list').addEventListener('click', async function (e) {
            const card = e.target.closest('.card');
            if (card) {
                const reviewId = card.id.replace('review-', '');
                const review = await fetchReviewById(reviewId);
                openReviewModal(review);
            }
        });

        // Button for showing the modal
        document.getElementById("createReviewButton").addEventListener("click", function () {
            const modal = new bootstrap.Modal(document.getElementById("createReviewModal"));
            modal.show();
        });

        const categoryInput = document.getElementById('categoryInput');
        const categorySelect = document.getElementById('categorySelect');
        const selectedTagsContainer = document.getElementById('selectedCategoryTags');
        const selectedCategories = new Map();

        // Afficher le select au clic sur l'input
        categoryInput.addEventListener('click', () => {
            categorySelect.classList.remove('hidden');
            categorySelect.focus();
        });

        categorySelect.addEventListener('change', () => {
            Array.from(categorySelect.selectedOptions).forEach(option => {
                if (!selectedCategories.has(option.value)) {
                    selectedCategories.set(option.value, option.text);

                    const tag = document.createElement('span');
                    tag.className = 'badge bg-success category-tag';
                    tag.textContent = option.text;

                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'btn-close';
                    removeBtn.onclick = () => {
                        selectedCategories.delete(option.value);
                        tag.remove();
                        option.selected = false;
                        categoryInput.value = Array.from(selectedCategories.values()).join(', ');
                    };

                    tag.appendChild(removeBtn);
                    selectedTagsContainer.appendChild(tag);
                }
            });
            categorySelect.classList.add('hidden');
            categoryInput.value = Array.from(selectedCategories.values()).join(', ');
        });

        const form = document.getElementById('createReviewForm');
        form.addEventListener('submit', (event) => {
            const selectedCategoryIds = Array.from(selectedCategories.keys()).join(',');
            const selectedCategoriesInput = document.createElement('input');
            selectedCategoriesInput.type = 'hidden';
            selectedCategoriesInput.name = 'selectedCategories';
            selectedCategoriesInput.value = selectedCategoryIds;

            form.appendChild(selectedCategoriesInput);
        });
    
    </script>

    <div id="message"></div>
</body>

</html>