const closeTicketButtons = document.querySelectorAll(".closeTicket")
console.log(closeTicketButtons)

//href="{{ path("closeTicket", {'id':ticket.id}) }}"

for (const closeTicketButton of closeTicketButtons) {
    closeTicketButton.addEventListener("click", async (e) => {
        e.preventDefault();
        const [route, method] = window.getRoute("closeTicket", closeTicketButton.dataset.id);
        const response = await fetch(route, {
            method,
        });

        if (response.ok) {
            console.log("Ticket closed");
        }

        window.location.reload();
    });
}