<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "../config/config.php";
require_once "../includes/auth.php";
requireAuth();
$userId = $_SESSION['user_id'];
$userName = $_SESSION['username'] ?? 'Votre profil';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - Cafe Run</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style-profile.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Cafe Run</a>
            <div class="d-flex">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userMenu" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        Menu
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                        <li><a class="dropdown-item" href="profile.php">Profil</a></li>
                        <li><a class="dropdown-item" href="../public/index.php">Explorer</a></li>
                        <li><a class="dropdown-item" href="../public/feed.php">Feed</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" id="logout" href="../public/decconnexion.php">Déconnexion</a></li>
                    </ul>
                </li>
            </div>
        </div>
    </nav>

    <div class="profile-header p-4">
        <h2><?= htmlspecialchars($userName) ?></h2>
        <button class="btn-create-review" id="createReviewBtn">+ Nouvelle Revue</button>
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
                        if (confirm("Supprimer cette revue ?")) {
                            deleteReview(reviewId).then(res => {
                                if (res.success || res.deleted) {
                                    loadUserReviews();
                                } else {
                                    alert("Erreur lors de la suppression");
                                }
                            });
                        }
                    }
                });
            } catch (error) {
                console.error("Erreur lors du chargement des revues :", error);
                document.getElementById("user-review-grid").innerHTML =
                    '<div class="text-center text-danger">Erreur de chargement des revues.</div>';
            }
        }

        document.getElementById("createReviewBtn").onclick = function () {
            openReviewModal({}, "create");
        };

        document.getElementById('logout').addEventListener('click', function (event) {
            event.preventDefault();
            logoutUser();
        });

        document.addEventListener('DOMContentLoaded', loadUserReviews);
    </script>
</body>

</html>