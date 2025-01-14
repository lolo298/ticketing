function InitModals() {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        console.log("setting up modal for", modal);

        const openBtnAttr = modal.getAttribute('data-trigger');
        const closeBtnAttr = modal.getAttribute('data-close');

        console.log("openBtnAttr", openBtnAttr);
        console.log("closeBtnAttr", closeBtnAttr);

        
        const openBtn = document.querySelector(openBtnAttr);
        const closeBtn = document.querySelector(closeBtnAttr);

        console.log("openBtn", openBtn);
        console.log("closeBtn", closeBtn);

        openBtn.addEventListener('click', () => {
            modal.showModal();
        });

        closeBtn.addEventListener('click', () => {
            modal.close();
        });

        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.close();
            }
        });
    });
}

InitModals();