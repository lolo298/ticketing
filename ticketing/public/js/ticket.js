const form = document.querySelector("#editForm");

form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const formData = new FormData(form);

    const [route, method] = window.getRoute("updateTicket");
    const response = await fetch(route, {
        method,
        body: formData,
    });

    if (response.ok) {
        console.log("Ticket updated");
    }
});
