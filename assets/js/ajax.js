// Handle AJAX requests (fetch API (async/await))

async function apiFetch(url, options = {}) {
  try {
    const response = await fetch(url, options);
    if (!response.ok) throw new Error(`HTTP Error: ${response.status}`);
    return await response.json();
  } catch (err) {
    console.error(`Error fetching ${url}:`, err);
    throw err;
  }
}

async function loginUser(email, password) {
  const formData = new FormData();
  formData.append("email", email);
  formData.append("password", password);

  try {
    const data = await apiFetch("/api/connexion.php", {
      method: "POST",
      body: formData,
    });

    if (data.success) {
      showToast("Bienvenue, " + data.nom + "!");
      window.location.href = "/index.php";
    } else {
      showToast(`Erreur : ${data.message}`);
    }
  } catch (err) {
    showToast(`Erreur lors de la connexion : ${err.message}`);
  }
}

async function logoutUser() {
  try {
    const response = await apiFetch("/api/deconnexion.php", {
      method: "POST",
    });
    if (response.success) {
      showToast("Déconnexion réussie!");
      window.location.href = "/index.php";
    } else {
      showToast(`Erreur : ${response.message}`);
    }
  } catch (err) {
    showToast(`Erreur lors de la déconnexion : ${err.message}`);
  }
}

async function signupUser(nom, email, password) {
  const formData = new FormData();
  formData.append("nom", nom);
  formData.append("email", email);
  formData.append("password", password);

  try {
    const data = await apiFetch("/api/inscription.php", {
      method: "POST",
      body: formData,
    });

    if (data.success) {
      showToast("Inscription réussie!");
      // Redirect to login page
      window.location.href = "/connexion.php";
    } else {
      showToast(`Erreur : ${data.message}`);
    }
  } catch (err) {
    showToast(`Erreur lors de l'inscription : ${err.message}`);
  }
}

async function createReview(formData) {
  return await apiFetch("/api/reviews.php", {
    method: "POST",
    body: formData,
  });
}

async function updateReview(reviewId, formData) {
  return await apiFetch(`/api/reviews/${reviewId}.php`, {
    method: "PUT",
    body: formData,
  });
}

async function deleteReview(reviewId) {
  return await apiFetch(`/api/reviews/${reviewId}.php`, {
    method: "DELETE",
  });
}

async function fetchReviews() {
  return await apiFetch("/api/reviews.php");
}

async function submitReview(e) {
  e.preventDefault();
  const formData = new FormData(e.target);
  try {
    const newReview = await createReview(formData);
    showToast("Review sent!");
    prependReviewCard(newReview);
  } catch (err) {
    showToast(`Error : ${err.message}`);
  }
}

setInterval(async () => {
  try {
    const latestReviews = await fetchReviews();
    updateReviewList(latestReviews);
  } catch (err) {
    console.error("Polling error:", err);
  }
}, 60000);
