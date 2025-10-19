document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector(".form-submit-event");
    const commentsContainer = document.getElementById("event-comments");

    form.addEventListener("submit", function (e) {
        e.preventDefault();
        const formData = new FormData(form);

        fetch(form.action, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value,
            },
            body: formData,
        })
        .then((res) => res.json())
        .then((data) => {
            if (!data.error) {
                // ✅ Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById("commentModal"));
                modal.hide();

                Swal.fire({
                    icon: "success",
                    title: "Comment added!",
                    text: "Your comment has been successfully added.",
                    timer: 1200,
                    showConfirmButton: false,
                });

                // ✅ Get category title text
                const categorySelect = document.getElementById("category_id");
                const selectedCategoryTitle = categorySelect.options[categorySelect.selectedIndex].text;

                // ✅ Create new comment element
                const newComment = document.createElement("div");
                newComment.classList.add("comment-item");
                newComment.setAttribute("data-comment-id", data.comment.id);

                newComment.innerHTML = `
                    <div>
                        <span class="comment-text">${data.comment.comment}</span>
                        <small class="text-muted d-block mt-1">${selectedCategoryTitle}</small>
                    </div>
                    <div>
                        <small class="text-muted mb-0">${new Date().toLocaleDateString()}</small>
                        <small class="text-muted mb-0">${new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</small>
                        <button type="button" class="btn btn-sm btn-outline-danger delete-comment-btn" data-id="${data.comment.id}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                `;

                // ✅ Prepend new comment to the top
                commentsContainer.prepend(newComment);

                // ✅ Clear form
                form.reset();
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Failed!",
                    text: "Could not add your comment.",
                });
            }
        })
        .catch((err) => {
            console.error(err);
            Swal.fire({
                icon: "error",
                title: "Error!",
                text: "Something went wrong while adding comment.",
            });
        });
    });
});