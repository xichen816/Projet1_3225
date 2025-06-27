
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style-navbar.css">

<nav class="navbar navbar-expand-lg navbar-light bg-secondary">
<div class="container-fluid">
    <a class="navbar-brand" href="#">
    <!-- <img src="../assets/icon/cafe-run-icon.png" width="32" height="32"> -->
    Cafe Run
    </a>
    <div class="search-container d-flex align-items-center">
        <form class="search-form d-flex">
            <input class="search-input" type="search" placeholder="Recherche..." aria-label="Recherche">
            <button class="btn-search" type="submit">
            <i class="bi bi-search"></i>
            </button>
        </form>
    </div>
    <?php if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true): ?>
    <div class="d-flex">
        <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
            Menu
        </a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
            <?php
              $current_page = basename($_SERVER['PHP_SELF']);
              if ($current_page === 'profile.php'): ?>
                <li><a class="dropdown-item" href="index.php">Accueil</a></li>
                <li><a class="dropdown-item" href="feed.php">Feed</a></li>
            <?php elseif ($current_page === 'feed.php'): ?>
                <li><a class="dropdown-item" href="profile.php">Profil</a></li>
                <li><a class="dropdown-item" href="index.php">Accueil</a></li>
            <?php elseif ($current_page === 'index.php'): ?>
                <li><a class="dropdown-item" href="profile.php">Profil</a></li>
                <li><a class="dropdown-item" href="feed.php">Feed</a></li>
            <?php endif; ?>
            <li>
            <hr class="dropdown-divider">
            </li>
            <li><a class="dropdown-item" id="logout" href="../public/api/deconnexion.php">Déconnexion</a></li>
        </ul>
        </li>
    </div>
    <?php else: ?>
    <div class="d-flex">
        <button class="btn btn-primary me-2">
        <a href="inscription.php" class="text-white text-decoration-none">S'inscrire</a>
        </button>
        <button class="btn btn-primary">
        <a href="connexion.php" class="text-white text-decoration-none">Se connecter</a>
        </button>
    </div>
    <?php endif; ?>
</div>
</nav>