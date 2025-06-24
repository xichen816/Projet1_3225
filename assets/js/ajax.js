// Toast Helper
function showToast(message, type = "info") {
  const container = document.getElementById("message");
  if (container) {
    container.innerHTML = `
      <div class="alert alert-${type} alert-dismissible fade show" role="alert">
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>`;
  } else {
    console[type === "error" ? "error" : "log"](message);
  }
}

// Escape Helper
function escapeHtml(text) {
  return String(text).replace(
    /[&<>"']/g,
    (s) =>
      ({
        "&": "&amp;",
        "<": "&lt;",
        ">": "&gt;",
        '"': "&quot;",
        "'": "&#39;",
      }[s])
  );
}

// Date Helper
function formatDate(dateString) {
  if (!dateString) return "";
  const date = new Date(dateString);
  return date.toLocaleDateString("en-US", {
    month: "short",
    day: "numeric",
    year: "numeric",
  });
}

// Fetch
async function apiFetch(url, options = {}) {
  options.credentials = "same-origin";
  // If sending FormData, do NOT set Content-Type!
  if (options.body instanceof FormData) {
    delete options.headers?.["Content-Type"];
  }
  try {
    const response = await fetch(url, options);
    if (!response.ok) throw new Error(`HTTP Error: ${response.status}`);
    return await response.json();
  } catch (err) {
    showToast(`Erreur API: ${err.message}`, "error");
    throw err;
  }
}

// Auth
async function loginUser(email, password) {
  const formData = new FormData();
  formData.append("email", email);
  formData.append("password", password);

  try {
    const data = await apiFetch("api/connexion.php", {
      method: "POST",
      body: formData,
    });
    if (data.success) {
      showToast("Bienvenue, " + data.nom + "!");
      window.location.href = "index.php";
    } else showToast(`Erreur : ${data.message}`);
  } catch {}
}

async function logoutUser() {
  try {
    const data = await apiFetch("api/deconnexion.php", { method: "POST" });
    if (data.success) {
      showToast("Déconnexion réussie!");
      window.location.href = "index.php";
    } else showToast(`Erreur : ${data.message}`);
  } catch {}
}

async function signupUser(nom, email, password) {
  const formData = new FormData();
  formData.append("nom", nom);
  formData.append("email", email);
  formData.append("password", password);

  try {
    const data = await apiFetch("api/inscription.php", {
      method: "POST",
      body: formData,
    });
    if (data.success) {
      showToast("Inscription réussie!");
      window.location.href = "connexion.php";
    } else showToast(`Erreur : ${data.message}`);
  } catch {}
}

// Review CRUD
async function createReview(formData) {
  return await apiFetch("api/reviews.php", { method: "POST", body: formData });
}

async function updateReview(reviewId, formData) {
  return await apiFetch(`api/reviews.php?id=${reviewId}`, {
    method: "PUT",
    body: formData,
  });
}

async function deleteReview(reviewId) {
  return await apiFetch(`api/reviews.php?id=${reviewId}`, { method: "DELETE" });
}

async function fetchReviews() {
  return await apiFetch("api/reviews.php");
}

async function fetchReviewById(reviewId) {
  return await apiFetch(`api/reviews.php?id=${reviewId}`);
}

async function fetchReviewsByUser(userId) {
  return await apiFetch(`api/reviews.php?user_id=${userId}`);
}

async function fetchFeedReviews(userId) {
  return await apiFetch(`api/reviews.php?feed=1&user_id=${userId}`);
}

// UI
function createReviewCard(review, readOnly = false) {
  let imgHtml = "";
  if (review.thumbnail) {
    imgHtml = `<img src="${escapeHtml(
      review.thumbnail
    )}" class="card-img-top" alt="Thumbnail">`;
  } else if (review.photos && review.photos.length > 0) {
    imgHtml = `<img src="${escapeHtml(
      review.photos[0].filepath
    )}" class="card-img-top" alt="Review photo">`;
  }

  let controls = "";
  if (
    !readOnly &&
    String(review.id_utilisateur) === String(window.currentUserId)
  ) {
    controls = `<button class="btn btn-danger btn-sm float-end" onclick="deleteReview(${review.id})">Supprimer</button>`;
  }

  const author = review.auteur_nom || review.username || review.nom || "";
  const cafe = review.cafe_nom || review.cafename || "";

  // rating row at bottom
  return `
    <div class="card review-card rectangle-card h-100" id="review-${review.id}">
      ${imgHtml}
      <div class="card-body d-flex flex-column">
        <h5 class="card-title mb-1">${escapeHtml(review.titre)}</h5>
        <div class="mb-1 text-muted small">
          par ${escapeHtml(author)} | ${escapeHtml(cafe)}
        </div>
        <div class="mb-2 text-truncate">${escapeHtml(
          review.description || review.contenu || ""
        )}</div>
        <div class="mt-auto d-flex align-items-center justify-content-between">
          <span class="badge bg-primary">${review.rating}/5</span>
          <button class="btn btn-sm btn-primary open-review-modal" data-review-id="${
            review.id
          }">Voir plus</button>
          ${controls}
        </div>
      </div>
    </div>
  `;
}

function createReviewTile(review) {
  const imgSrc =
    review.thumbnail ||
    (review.photos && review.photos[0] && review.photos[0].filepath);
  const title = escapeHtml(review.titre || review.title || "Untitled Review");
  const cafeName = escapeHtml(
    review.cafe_nom ||
      review.cafename ||
      review.cafe_name ||
      review.cafe ||
      "Unknown Cafe"
  );
  const authorName = escapeHtml(
    review.auteur_nom ||
      review.username ||
      review.nom ||
      review.author ||
      review.author_name ||
      "Anonymous"
  );
  const description = escapeHtml(
    review.description || review.contenu || review.content || ""
  );
  const rating = review.rating || 0;
  const reviewDate = formatDate(
    review.created_at || review.date || review.createdAt || ""
  );
  const contentClass = imgSrc ? "card-content-overlay" : "card-content-solid";
  const noImagePlaceholder = !imgSrc
    ? '<div class="no-image-placeholder"></div>'
    : "";
  return `
    <div class="rating-badge">★ ${rating}/5</div>
    ${noImagePlaceholder}
    <div class="${contentClass}">
        <div class="review-title">${title}</div>
        <div class="cafe-info">
            <div class="cafe-name">${cafeName}</div>
            <div class="author-name">by ${authorName}</div>
        </div>
        <div class="review-description">${description}</div>
        <div class="card-footer">
            <div class="review-date">${reviewDate}</div>
            <a href="#" class="read-more">Read More</a>
        </div>
    </div>
  `;
}

function updateFeedList(reviews) {
  const list = document.querySelector("#feed-list .review-cards-row");
  if (!list) {
    return;
  }

  list.innerHTML = "";

  if (reviews.length === 0) {
    list.innerHTML = `<div class="alert alert-info" role="alert">
                            Aucune revue trouvée. Commencez à écrire vos revues !
                        </div>`;
    return;
  }

  const feedCount = reviews.length;
  const odd = feedCount % 2 !== 0;
  reviews.forEach((r, i) => {
    let cardHtml;
    if (i === 0 && odd) {
      cardHtml = `<div class="col-12">${createReviewCard(r)}</div>`;
    } else {
      cardHtml = `<div class="col-6">${createReviewCard(r)}</div>`;
    }
    list.insertAdjacentHTML("beforeend", cardHtml);
  });
}

function updateUserReviewList(reviews) {
  const list = document.getElementById("user-review-list");
  if (!list) return;

  list.innerHTML = "";

  if (!Array.isArray(reviews) || reviews.length === 0) {
    list.innerHTML = `<div class="alert alert-info" role="alert">
                        Aucune revue trouvée. Commencez à écrire vos revues !
                      </div>`;
    return;
  }

  reviews.forEach((r) => {
    list.insertAdjacentHTML("beforeend", createReviewCard(r));
  });
}

function prependReviewCard(review) {
  const reviewList = document.getElementById("feed-list");
  if (reviewList) {
    reviewList.insertAdjacentHTML("afterbegin", createReviewCard(review));
  }
}

// Review Modal
function openReviewModal(review) {
  const modalTitle = document.getElementById("reviewModalLabel");
  const modalBody = document.getElementById("reviewModalBody");
  const modalFooter = document.getElementById("reviewModalFooter");
  const currentUserId = window.currentUserId;

  modalTitle.textContent = review.titre;

  modalBody.innerHTML = `
    ${getReviewPhotosCarouselHtml(review.photos)}
    <p>${escapeHtml(review.contenu || "")}</p>
    <span class="badge bg-primary">${review.rating}/5</span>
  `;

  if (String(review.id_utilisateur) === String(currentUserId)) {
    modalFooter.innerHTML = `
      <button type="button" class="btn btn-warning" id="editReviewBtn">Modifier</button>
      <button type="button" class="btn btn-danger" id="deleteReviewBtn">Supprimer</button>
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
    `;
    document.getElementById("editReviewBtn").onclick = () =>
      switchToEditMode(review);
    document.getElementById("deleteReviewBtn").onclick = () =>
      handleDeleteReview(review.id);
  } else {
    modalFooter.innerHTML = `<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>`;
  }

  new bootstrap.Modal(document.getElementById("reviewModal")).show();
}

// Edit Mode
function switchToEditMode(review) {
  const modalBody = document.getElementById("reviewModalBody");
  const modalFooter = document.getElementById("reviewModalFooter");
  modalBody.innerHTML = `
    <form id="editReviewForm" enctype="multipart/form-data">
      <div class="mb-3">
        <label for="editReviewTitle" class="form-label">Titre</label>
        <input type="text" class="form-control" id="editReviewTitle" name="titre" value="${escapeHtml(
          review.titre
        )}" required>
      </div>
      <div class="mb-3">
        <label for="editReviewContent" class="form-label">Contenu</label>
        <textarea class="form-control" id="editReviewContent" name="contenu" required>${escapeHtml(
          review.contenu
        )}</textarea>
      </div>
      <div class="mb-3">
        <label for="editReviewRating" class="form-label">Note (1-5)</label>
        <input type="number" class="form-control" id="editReviewRating" name="rating" min="1" max="5" value="${
          review.rating
        }" required>
      </div>
      <div class="mb-3">
        <label for="editReviewPhotos" class="form-label">Ajouter de nouvelles photos (max 5)</label>
        <input type="file" class="form-control" id="editReviewPhotos" name="photos[]" multiple accept="image/*">
      </div>
      <button type="submit" class="btn btn-success">Enregistrer</button>
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
    </form>
  `;
  modalFooter.innerHTML = "";

  document.getElementById("editReviewForm").onsubmit = async function (e) {
    const userId = window.currentUserId;
    e.preventDefault();
    const formData = new FormData(this);
    try {
      const res = await updateReview(review.id, formData);
      if (res.success || res.updated) {
        showToast("Revue modifiée !");
        fetchReviewsByUser(userId).then(updateUserReviewList);
        fetchFeedReviews(userId).then(updateFeedList);
        bootstrap.Modal.getInstance(
          document.getElementById("reviewModal")
        ).hide();
      } else {
        showToast(res.message || "Erreur lors de la modification", "error");
      }
    } catch (err) {
      showToast(`Erreur lors de la modification : ${err.message}`, "error");
    }
  };
}

// Delete Review
function handleDeleteReview(reviewId) {
  const userId = window.currentUserId;
  if (confirm("Êtes-vous sûr de vouloir supprimer cette revue ?")) {
    deleteReview(reviewId)
      .then((res) => {
        if (res.deleted || res.success) {
          showToast("Revue supprimée !");
          fetchReviewsByUser(userId).then(updateUserReviewList);
          fetchFeedReviews(userId).then(updateFeedList);
          bootstrap.Modal.getInstance(
            document.getElementById("reviewModal")
          ).hide();
        } else {
          showToast(res.message || "Erreur lors de la suppression", "error");
        }
      })
      .catch((err) =>
        showToast(`Erreur lors de la suppression : ${err.message}`, "error")
      );
  }
}

// Carousel
function getReviewPhotosCarouselHtml(photos) {
  if (!photos || photos.length === 0) return "";
  if (photos.length === 1) {
    return `<img src="${photos[0].filepath}" class="img-fluid mb-2" alt="Photo de la revue">`;
  }
  const carouselId = "reviewPhotosCarousel";
  return `
    <div id="${carouselId}" class="carousel slide mb-2" data-bs-ride="carousel">
      <div class="carousel-inner">
        ${photos
          .map(
            (photo, i) => `
          <div class="carousel-item${i === 0 ? " active" : ""}">
            <img src="${photo.filepath}" class="d-block w-100" alt="Photo ${
              i + 1
            }">
          </div>
        `
          )
          .join("")}
      </div>
      <button class="carousel-control-prev" type="button" data-bs-target="#${carouselId}" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Précédent</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#${carouselId}" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Suivant</span>
      </button>
    </div>
  `;
}

function createPaginatedGrid({
  gridId,
  reviews,
  createCardHtml,
  perPage = 15,
  onCardClick = null,
}) {
  let currentPage = 1;
  let totalPages = Math.ceil(reviews.length / perPage);

  function renderGrid() {
    const grid = document.getElementById(gridId);
    if (!grid) return;
    grid.innerHTML = "";

    const start = (currentPage - 1) * perPage;
    const items = reviews.slice(start, start + perPage);

    items.forEach((review) => {
      const card = document.createElement("div");
      card.className = "review-card";
      card.innerHTML = createCardHtml(review);

      if (onCardClick) {
        card.onclick = (e) => {
          e.preventDefault();
          onCardClick(review);
        };
      }

      const imgSrc =
        review.thumbnail ||
        (review.photos && review.photos[0] && review.photos[0].filepath);
      if (imgSrc) card.classList.add("has-image");
      else card.classList.add("no-image");

      grid.appendChild(card);
    });

    // Fill empty slots for grid structure
    const emptySlots = perPage - items.length;
    for (let i = 0; i < emptySlots; i++) {
      const card = document.createElement("div");
      card.className = "review-card empty";
      grid.appendChild(card);
    }
  }

  // TODO
  function updatePaginationUI(pageSelector, dotSelector, infoSelector) {
    // pageSelector: {prev: '#prev-page', next: '#next-page'}
    // dotSelector: '.page-dot'
    // infoSelector: {current: '#current-page', total: '#total-pages'}

    if (dotSelector) {
      const dots = document.querySelectorAll(dotSelector);
      dots.forEach((dot, idx) => {
        dot.classList.toggle("active", idx === currentPage - 1);
        dot.style.display = idx < totalPages ? "block" : "none";
      });
    }

    if (infoSelector) {
      if (infoSelector.current)
        document.querySelector(infoSelector.current).textContent = currentPage;
      if (infoSelector.total)
        document.querySelector(infoSelector.total).textContent = totalPages;
    }

    if (pageSelector) {
      if (pageSelector.prev)
        document.querySelector(pageSelector.prev).disabled = currentPage === 1;
      if (pageSelector.next)
        document.querySelector(pageSelector.next).disabled =
          currentPage === totalPages;
    }
  }

  function setupPagination(pageSelector, dotSelector, infoSelector) {

    if (pageSelector.prev) {
      document.querySelector(pageSelector.prev).onclick = () => {
        if (currentPage > 1) {
          currentPage--;
          renderGrid();
          updatePaginationUI(pageSelector, dotSelector, infoSelector);
        }
      };
    }
    if (pageSelector.next) {
      document.querySelector(pageSelector.next).onclick = () => {
        if (currentPage < totalPages) {
          currentPage++;
          renderGrid();
          updatePaginationUI(pageSelector, dotSelector, infoSelector);
        }
      };
    }
    if (dotSelector) {
      document.querySelectorAll(dotSelector).forEach((dot, idx) => {
        dot.onclick = () => {
          if (idx < totalPages) {
            currentPage = idx + 1;
            renderGrid();
            updatePaginationUI(pageSelector, dotSelector, infoSelector);
          }
        };
      });
    }
  }

  return {
    render: renderGrid,
    setupPagination: setupPagination,
    updatePaginationUI: updatePaginationUI,
    updateReviews: function (newReviews) {
      reviews = newReviews;
      totalPages = Math.ceil(reviews.length / perPage);
      currentPage = 1;
      renderGrid();
    },
    getCurrentPage: () => currentPage,
  };
}

// Page Load
document.addEventListener("DOMContentLoaded", function () {
  const userId = window.currentUserId;
  fetchReviewsByUser(userId).then(updateUserReviewList);
  fetchFeedReviews(userId).then(updateFeedList);
  // Create review modal
  const createForm = document.getElementById("createReviewForm");
  if (createForm) {
    createForm.onsubmit = async function (e) {
      e.preventDefault();
      const formData = new FormData(this);
      formData.append("id_utilisateur", window.currentUserId);
      try {
        const result = await createReview(formData);
        if (result.success) {
          showToast("Review créée !");
          fetchReviewsByUser(userId).then(updateUserReviewList);
          prependReviewCard(result.review);
          fetchFeedReviews(userId).then(updateFeedList);
          bootstrap.Modal.getInstance(
            document.getElementById("createReviewModal")
          ).hide();
        } else {
          showToast(result.message || "Erreur lors de la création", "error");
        }
      } catch (err) {
        showToast("Erreur lors de la création : " + err.message, "error");
      }
    };
  }
});

setInterval(async () => {
  try {
    const latestReviews = await fetchFeedReviews(window.currentUserId);
    if (!latestReviews || latestReviews.length === 0) {
      console.log("Aucune nouvelle revue dans le flux.");
      return;
    }
    console.log("Nouvelles revues récupérées :", latestReviews);
    updateFeedList(latestReviews);
  } catch (err) {
    console.error("Erreur lors de la récupération des dernières revues :", err);
    showToast("Erreur lors de la récupération des dernières revues", "error");
  }
}, 60000);
