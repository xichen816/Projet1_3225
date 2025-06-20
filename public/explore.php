<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../config/config.php";
// TODO : Navbar (Login, Sign up, Search bar? Filters?)
// TODO : Review tiles : Explore section with tiles of reviews
// TODO : Pagination, Load more, page buttons, etc...

$stmt = $pdo->query("SELECT * FROM revues ORDER BY date DESC LIMIT 15");
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<section id="explore" class="container mt-4">
    <h2>Explore</h2>
    <div class="row">
        <?php if (count($reviews) > 0): ?>
            <?php foreach ($reviews as $review): ?>
                <div class="card h-100 mb-4 col-md-4">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($review['title']) ?></h5>
                        <h6 class="card-subtitle mb-2 text-muted">
                            par <?= htmlspecialchars($review['utilisateur_nom']) ?> |
                            Rating : <?= intval($review['rating']) ?>/5
                        </h6>
                        <small class="text-muted"><?= htmlspecialchars($review['date']) ?></small>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info" role="alert">
                    Aucune revue trouvée. Essayez de modifier vos critères de recherche.
                </div>
            </div>
        <?php endif; ?>
        <!-- TODO : Pagination -->
        <!-- <div class="col-12">
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <li class="page-item">
                        <a class="page-link" href="#" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    <li class="page-item"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div> -->
    </div>
</section>