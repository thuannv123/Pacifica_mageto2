document.addEventListener("DOMContentLoaded", function () {
    var reviewForm = document.getElementById("review-form");
    if (reviewForm) {
        reviewForm.style.display = "none";
    }

    var reviewTextLink = document.getElementById("review-text");
    if (reviewTextLink) {
        reviewTextLink.addEventListener("click", function () {
            reviewForm.style.display = "block";
            reviewTextLink.style.display = "none";
        });
    }
});