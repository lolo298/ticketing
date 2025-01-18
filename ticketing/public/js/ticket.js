const form = document.querySelector("#editForm");
const sendChatButton = document.querySelector("#sendChat");
const chatInput = document.querySelector("#chatInput");
const closeTicketButton = document.querySelector("#closeTicket");

const ticketId = dataJSON.id;

form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const formData = new FormData(form);

    const [route, method] = window.getRoute("updateTicket", ticketId);
    const response = await fetch(route, {
        method,
        body: formData,
    });

    if (response.ok) {
        console.log("Ticket updated");
    }
});

sendChatButton.addEventListener("click", async () => {
    const message = chatInput.value;

    const [route, method] = window.getRoute("sendChat", ticketId);
    const response = await fetch(route, {
        method,
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({ message }),
    });

    if (response.ok) {
        console.log("Chat sent");
        window.location.reload();
    }
})

closeTicketButton.addEventListener("click", async (e) => {
    e.preventDefault();
    const ticketId = closeTicketButton.dataset.id;
    closeTicket(ticketId);
});