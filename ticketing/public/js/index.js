/** @var routesJSON object<string, string> */
function InitModals() {
    const modals = document.querySelectorAll(".modal");
    modals.forEach((modal) => {
        const openBtnAttr = modal.getAttribute("data-trigger");
        const closeBtnAttr = modal.getAttribute("data-close");

        const openBtn = document.querySelector(openBtnAttr);
        const closeBtn = document.querySelector(closeBtnAttr);

        openBtn.addEventListener("click", () => {
            modal.showModal();
        });

        closeBtn.addEventListener("click", () => {
            modal.close();
        });

        modal.addEventListener("click", (e) => {
            if (e.target === modal) {
                modal.close();
            }
        });
    });
}

/**
 *
 * @param {string} routeName
 * @param {any} params
 * @returns {[string, string]}
 */

function getRoute(routeName, param) {
    const route = Object.entries(routesJSON).find(([_, name]) => {
        return name === routeName;
    });

    const [rpath, method] = route[0].split("::");

    const parsedRoute = rpath
        .toString()
        .replaceAll(/\\\//g, "*")
        .replaceAll(/[/\\^$]/g, "")
        .replaceAll("*", "/")
        .replace(/(\(.+\))/g, param);

    return [parsedRoute, method];
}

function closeTicket(id) {
    const [route, method] = window.getRoute("closeTicket", id);
    fetch(route, {
        method,
    }).then(() => {
        console.log("Ticket closed");
        window.location.reload();
    });
}

function initTableActions() {
    const actionsButtons = document.querySelectorAll(".action-menu-trigger");
    const actionMenu = document.querySelector(".action-menu");

    if (!actionsButtons || !actionMenu) {
        return;
    }

    console.log("actionsButtons", actionsButtons);

    actionsButtons.forEach((action) => {
        action.addEventListener("click", (e) => {
            const { x, y } = e.currentTarget.getBoundingClientRect();

            const id = e.currentTarget.getAttribute("data-id");
            const state = e.currentTarget.getAttribute("data-state");

            actionMenu.style.display = "block";
            actionMenu.style.top = `${y}px`;
            actionMenu.style.left = `${x}px`;
            actionMenu.style.transform = "translateX(-50%) translateY(50%)";

            actionMenu.setAttribute("data-id", id);
            if (state === "CLOSED") {
                actionMenu.querySelector(".closeTicket").style.display = "none";
            } else {
                actionMenu.querySelector(".closeTicket").style.display = "block";
            }
        });
    });

    document.body.addEventListener("click", (e) => {
        if (e.target !== actionMenu && !Array.from(actionsButtons).includes(e.target)) {
            actionMenu.style.display = "none";
        }
    });

    actionMenu.querySelector('.seeTicket').addEventListener('click', (e) => {
        const id = actionMenu.getAttribute('data-id');
        const path = getRoute('ticket', id)[0];

        window.location.href = path;
    });

    actionMenu.querySelector('.closeTicket').addEventListener('click', (e) => {
        const id = actionMenu.getAttribute('data-id');
        closeTicket(id);
    });
}

function initSelects() {
    const selectTemplate = document.querySelector('#select-template');
    const selectChoiceTemplate = document.querySelector('#select-choice-template');
    const selectItemTemplate = document.querySelector('#select-item-template');

    document.querySelectorAll('select').forEach((select) => {
        const options = select.querySelectorAll('option');

        const selectClone = selectTemplate.content.cloneNode(true).querySelector('.select');
        const selectChoiceClone = selectChoiceTemplate.content.cloneNode(true).querySelector('.select-choice');

        options.forEach((option) => {
            const optionClone = selectItemTemplate.content.cloneNode(true);
            const optionElement = optionClone.querySelector('.select-item');

            optionElement.textContent = option.textContent;
            optionElement.setAttribute('data-value', option.value);

            if (option.selected) {
                selectClone.querySelector('.select-container .select-choosen').textContent = option.textContent;
            }
            
            selectChoiceClone.appendChild(optionElement);
        });


        selectClone.appendChild(selectChoiceClone);

        selectClone.querySelector('.select-container').addEventListener('click', (e) => {
            e.stopPropagation();
            selectChoiceClone.classList.toggle('open');
        });

        selectChoiceClone.addEventListener('click', (e) => {
            const target = e.target;
            if (target.classList.contains('select-item')) {
                const value = target.getAttribute('data-value');
                select.value = value;
                selectChoiceClone.classList.remove('open');
                selectClone.querySelector('.select-container .select-choosen').textContent = target.textContent
            }
        });


        document.body.addEventListener('click', (e) => {
            if (e.target !== selectChoiceClone && !Array.from(selectChoiceClone.children).includes(e.target)) {
                selectChoiceClone.classList.remove('open');
            }
        });



        select.insertAdjacentElement('afterend', selectClone);


    });
}

InitModals();
initTableActions();
initSelects();