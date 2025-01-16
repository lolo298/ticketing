const usersRows = document.querySelectorAll('.user');


for (const userRow of usersRows) {
  const roleSelect = userRow.querySelector('.role');
  const actifCheckbox = userRow.querySelector('.actif');
  const userId = userRow.dataset.id;
  
  
  roleSelect.addEventListener('change', async (e) => {
    try {
      await updateField('role', e.target.value, userId);
    } catch (err) {
      console.error(err);
    }
  });
  
  actifCheckbox.addEventListener('change', async (e) => {
    try {
      await updateField('actif', e.target.checked ? 1 : 0, userId);
    } catch (err) {
      console.error(err);
    }
  });
  
}


async function updateField(name, val, id) {
  const route = Object.entries(routesJSON).find(([rpath, name]) => {
    return name === "editUser"
  });
  
  console.log(route);
  
  const [rpath, method] = route[0].split('::');
  
  const parsedRoute = rpath.toString().replaceAll(/\\\//g, "*").replaceAll(/[/\\^$]/g, "").replaceAll("*", "/").replace(/(\(.+\))/g, id);
  console.log(parsedRoute);

  const res = await fetch(parsedRoute, {
    method: 'PATCH',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      [name]: val,
    }),
  });

  if (!res.ok) {
    throw new Error('Failed to update user');
  }
}