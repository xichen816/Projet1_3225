<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "../config/config.php";
require_once "../includes/auth.php";
require_once "../src/review.php";
$review = new Review($pdo);
$userId = $_SESSION['id'];
$userFeed = $review->fetchFeed($userId);
$userReviews = $review->fetchById($userId);
$stmt = $pdo->query("SELECT id, nom FROM cafes");
$cafes = $stmt->fetchAll(PDO::FETCH_ASSOC);
$catstmt = $pdo->query("SELECT id, nom FROM categories");
$categories = $catstmt->fetchAll(PDO::FETCH_ASSOC);

requireAuth();
$userId = $_SESSION['id'];
$userName = $_SESSION['username'] ?? 'My Profile';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - Cafe Run</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>

<body>
    <?php include '../includes/navbar.php'; ?>

    <div class="profile-header p-4">
        <h2><?= htmlspecialchars($userName) ?></h2>
        <button class="btn-create-review" id="createReviewBtn">Add a Review</button>
    </div>
    <div class="container m-10">
        <div id="user-review-grid"></div>
    </div>

    <div class="pagination-hint">
        <button class="page-nav" id="profile-prev-page" disabled>Previous</button>
        <div class="page-indicator">
            <div class="page-dot active"></div>
            <div class="page-dot"></div>
            <div class="page-dot"></div>
            <div class="page-dot"></div>
            <div class="page-dot"></div>
        </div>
        <div class="page-info">Page <span id="profile-current-page">1</span> of <span id="profile-total-pages">1</span>
        </div>
        <button class="page-nav" id="profile-next-page">Next</button>
    </div>


    <!-- Modal for Create/Edit Review -->
    <div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reviewModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body" id="reviewModalBody"></div>
                <div class="modal-footer" id="reviewModalFooter"></div>
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
                        <h5 class="modal-title" id="createReviewModalLabel">Créer une Revue</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="cafeSelect" class="form-label">Sélectionner un Café</label>
                            <select class="form-select" id="cafeSelect" name="id_cafe" required>
                                <option value="">Choisir un café</option>
                                <?php foreach ($cafes as $cafe): ?>
                                    <option value="<?= $cafe['id'] ?>"><?= htmlspecialchars($cafe['nom']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="categoryInput" class="form-label">Catégories</label>
                            <input type="text" class="form-control" id="categoryInput"
                                placeholder="Ajouter une catégorie" readonly />
                            <select name="categories[]" id="categorySelect" class="form-select hidden" multiple>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nom']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div id="selectedCategoryTags" class="mt-2 d-flex flex-wrap gap-2"></div>
                        </div>
                        <div class="mb-3">
                            <label for="reviewTitle" class="form-label">Titre de la Revue</label>
                            <input type="text" class="form-control" id="reviewTitle" name="titre" required>
                        </div>
                        <div class="mb-3">
                            <label for="reviewContent" class="form-label">Contenu de la Revue</label>
                            <textarea class="form-control" id="reviewContent" name="contenu" rows="4"
                                required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="reviewRating" class="form-label">Note (1 à 5)</label>
                            <input type="number" class="form-control" id="reviewRating" name="rating" min="1" max="5"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="reviewPhotos" class="form-label">Ajouter des Photos</label>
                            <input type="file" class="form-control" id="reviewPhotos" name="photos[]" multiple
                                accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Fermer</button>
                        <button type="submit" class="btn btn-primary">Créer la Revue</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="../assets/js/ajax.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.currentUserId = <?= json_encode($_SESSION['id']) ?>;
        let profileGridModule;

        async function loadUserReviews() {
            try {
                const reviews = await fetchReviewsByUser(window.currentUserId);
                if (!profileGridModule) {
                    profileGridModule = createPaginatedGrid({
                        gridId: "user-review-grid",
                        reviews,
                        createCardHtml: createReviewTile,
                        perPage: 15,
                        onCardClick: review => openReviewModal(review, "edit")
                    });
                    profileGridModule.render();
                } else {
                    profileGridModule.updateReviews(reviews);
                }

                document.querySelectorAll('.btn-edit').forEach(btn => {
                    btn.onclick = async function (e) {
                        e.stopPropagation();
                        const reviewId = btn.dataset.reviewId;
                        const review = await fetchReviewById(reviewId);
                        openReviewModal(review, "edit");
                    }
                });
                document.querySelectorAll('.btn-delete').forEach(btn => {
                    btn.onclick = function (e) {
                        e.stopPropagation();
                        const reviewId = btn.dataset.reviewId;
                        deleteAndUpdateGrid(reviewId, profileGridModule);
                    }
                });
            } catch (error) {
                console.error("Erreur lors du chargement des revues :", error);
                document.getElementById("user-review-grid").innerHTML =
                    '<div class="text-center text-danger">Erreur de chargement des revues.</div>';
            }
        }

        document.getElementById("createReviewBtn").onclick = function () {
            const modal = new bootstrap.Modal(document.getElementById("createReviewModal"));
            modal.show();
        };

        document.getElementById('logout').addEventListener('click', function (event) {
            event.preventDefault();
            logoutUser();
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
        document.addEventListener('DOMContentLoaded', loadUserReviews);
    </script>
</body>

</html>