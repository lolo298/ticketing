
const newTicketBtn = document.getElementById('newTicketBtn');
const newTicketModal = document.getElementById('newTicketModal');
const resetNewTicketModal = document.querySelector('#newTicketModal input[type="reset"]');

newTicketBtn.addEventListener('click', () => {
    newTicketModal.showModal();
});

resetNewTicketModal.addEventListener('click', () => {
  newTicketModal.close();
});

newTicketModal.addEventListener('click', (e) => {
    if (e.target === newTicketModal) {
        newTicketModal.close();
    }
});
