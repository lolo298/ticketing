const routes = document.querySelector("script#routes");
const routesJSON = JSON.parse(routes.textContent);

const data = document.querySelector("script#data");
if (data){
  window.dataJSON = JSON.parse(data.textContent);
}