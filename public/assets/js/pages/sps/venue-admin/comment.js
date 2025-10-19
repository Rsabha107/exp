document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector(".form_submit_event");
    const commentsContainer = document.getElementById("event-comments");

    form.addEventListener("submit", function (e) {
        e.preventDefault();
        const formData = new FormData(form);

        fetch(form.action, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('input[name="_token"]')
                    .value,
                Accept: "application/json",
            },
            body: formData,
        })
            .then((response) => response.json())
            .then((data) => {
                if (!data.error) {
                    // check if error is false
                    const categoryText =
                        document.getElementById("category_id")
                            .selectedOptions[0].text;
                    const div = document.createElement("div");
                    div.className = "mb-1";
                    div.innerText =
                        data.comment.comment + " (" + categoryText + ")";
                    commentsContainer.appendChild(div);

                    form.reset(); // clear form
                    bootstrap.Modal.getInstance(
                        document.getElementById("commentModal")
                    ).hide();
                    alert(data.message); // show success message
                } else {
                    alert("Something went wrong!");
                }
            })

            .catch((err) => {
                console.error(err);
                alert("Error submitting comment!");
            });
    });
});
