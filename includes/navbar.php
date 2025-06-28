
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style-navbar.css">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Cafe Run</a>
        
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav">
                <?php $currentPage = basename($_SERVER['PHP_SELF']); ?>
                    <a class="nav-link <?= ($currentPage == 'index.php') ? 'active text-white' : '' ?>" href="index.php">Explore</a>
                    <a class="nav-link <?= ($currentPage == 'feed.php') ? 'active text-white' : '' ?>" href="feed.php">Feed</a>
                    <a class="nav-link <?= ($currentPage == 'profile.php') ? 'active text-white' : '' ?>" href="profile.php">Profile</a>
            </div>
        </div>
        
        <div class="d-flex ms-auto">
            <div class="search-container d-flex align-items-center">
                <form class="search-form d-flex">
                    <input class="search-input" type="search" placeholder="Search for..." aria-label="Recherche">
                    <button class="btn-search" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </form>
            </div>
        </div>
        

        <?php if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true): ?>
        <div class="d-flex ms-auto">
            <div class="nav-item dropdown d-none d-lg-block">
                <a class="nav-link dropdown-toggle text-white" href="#" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                    Menu
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                    <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                    <li><a class="dropdown-item" href="feed.php">Feed</a></li>
                    <li><a class="dropdown-item" href="index.php">Explore</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" id="logout" href="../public/api/deconnexion.php">Log Out</a></li>
                </ul>
            </div>
        </div>
        <?php else: ?>
        <div class="d-flex ms-auto">
            <button class="btn btn-primary me-2">
                <a href="inscription.php" class="text-white text-decoration-none">Sign In</a>
            </button>
            <button class="btn btn-primary">
                <a href="connexion.php" class="text-white text-decoration-none">Log In</a>
            </button>
        </div>
        <?php endif; ?>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>
</nav>