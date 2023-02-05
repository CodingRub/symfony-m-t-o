const adresse = document.querySelector(".adresse-input");
const ville = document.querySelector(".ville-input");
const cp = document.querySelector(".cp-input");
const latitude = document.querySelector(".latitude-input");
const longitude = document.querySelector(".longitude-input");
const ul = document.querySelector('.result')

var fetchData = function(event) {
    if (adresse.value.length > 2) {
        fetch("https://api-adresse.data.gouv.fr/search/?q=" + event.target.value+"&limit=10", {
            credentials: 'same-origin',
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            }
        })
            .then(handleResponse)
            .then(handleData)
    }
}

adresse.addEventListener('input', fetchData, false);
adresse.addEventListener('click', fetchData, false);

document.addEventListener('click', function() {
    ul.innerHTML = "";
})

function handleResponse(response) {
    return response.json().then(function (json) {
        ul.innerHTML = "";
        return response.ok ? json : Promise.reject(json);
    });
}

function handleData(data) {
    for (let i = 0; i < data["features"].length; i++) {
        const li = document.createElement("li");
        li.value = i;
        li.textContent = data["features"][i]["properties"]["label"];
        li.addEventListener('click', (element) => {
            const res = data["features"][element.target.value];
            adresse.value = res["properties"]["name"];
            ville.value = res["properties"]["city"];
            cp.value = res["properties"]["postcode"];
            latitude.value = res["geometry"]["coordinates"][1];
            longitude.value = res["geometry"]["coordinates"][0];
        })
        ul.appendChild(li);
    }
}