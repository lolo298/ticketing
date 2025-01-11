
const form = document.querySelector('#editForm');

form.addEventListener('submit', async (e) => {
  e.preventDefault();

  const formData = new FormData(form);
/** @var routesJSON object<string, string> */

const route = Object.entries(routesJSON).find(([rpath, name]) => {
  return name === "updateTicket"

});

console.log(route);

const [rpath, method] = route[0].split('::');

///^\/api\/edit\/ticket\/(?<id>(\w+))$/

const parsedRoute = rpath.toString().replaceAll(/\\\//g, "*").replaceAll(/[/\\^$]/g, "").replaceAll("*", "/").replace(/(\(.+\))/g, dataJSON.id);

const response = await fetch(parsedRoute, {
  method,
  body: formData
});

if (response.ok) {
  console.log('Ticket updated');
} 

});