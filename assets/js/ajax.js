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

async function createReview(formData) {
  return await apiFetch("/api/reviews", {
    method: "POST",
    body: formData,
  });
}

async function updateReview(reviewId, formData) {
  return await apiFetch(`/api/reviews/${reviewId}`, {
    method: "PUT",
    body: formData,
  });
}

async function deleteReview(reviewId) {
  return await apiFetch(`/api/reviews/${reviewId}`, {
    method: "DELETE",
  });
}

async function fetchReviews() {
  return await apiFetch("/api/reviews");
}

// Usage with async/await from the notes
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

// Polling Example from the notes (every 60 seconds)
setInterval(async () => {
  try {
    const latestReviews = await fetchReviews();
    updateReviewList(latestReviews);
  } catch (err) {
    console.error("Polling error:", err);
  }
}, 60000);
