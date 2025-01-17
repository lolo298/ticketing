const closeTicketButtons = document.querySelectorAll(".closeTicket")

for (const closeTicketButton of closeTicketButtons) {
    closeTicketButton.addEventListener("click", async (e) => {
        e.preventDefault();
        const ticketId = closeTicketButton.dataset.ticketId;
        closeTicket(ticketId);
    });
}
