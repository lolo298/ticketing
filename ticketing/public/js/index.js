/** @var routesJSON object<string, string> */
function InitModals() {
    const modals = document.querySelectorAll(".modal");
    modals.forEach((modal) => {
        console.log("setting up modal for", modal);

        const openBtnAttr = modal.getAttribute("data-trigger");
        const closeBtnAttr = modal.getAttribute("data-close");

        console.log("openBtnAttr", openBtnAttr);
        console.log("closeBtnAttr", closeBtnAttr);

        const openBtn = document.querySelector(openBtnAttr);
        const closeBtn = document.querySelector(closeBtnAttr);

        console.log("openBtn", openBtn);
        console.log("closeBtn", closeBtn);

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

InitModals();
