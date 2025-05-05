// moduloExportar.js

export function toDataURL(url) {
  return new Promise((resolve, reject) => {
    const xhr = new XMLHttpRequest();
    xhr.onload = function () {
      const reader = new FileReader();
      reader.onloadend = function () {
        resolve(reader.result);
      };
      reader.onerror = reject;
      reader.readAsDataURL(xhr.response);
    };
    xhr.onerror = reject;
    xhr.open("GET", url);
    xhr.responseType = "blob";
    xhr.send();
  });
}

export async function getHeaderContent() {
  const logoBase64 = await toDataURL("images/home/logo.png").catch(() => null);

  return {
    table: {
      widths: ["*", "*"],
      body: [
        [
          logoBase64
            ? {
                image: logoBase64,
                width: 100,
                margin: [10, 10, 0, 10],
              }
            : { text: "Logo", margin: [10, 10, 0, 10] },
          {
            alignment: "right",
            stack: [
              {
                text: "+2 95 01 88 821",
                fontSize: 10,
                margin: [0, 10, 10, 0],
              },
              {
                text: "info@domain.com",
                fontSize: 10,
                margin: [0, 2, 10, 0],
              },
              {
                text: "Facebook | Twitter | LinkedIn | Dribbble | Google+",
                fontSize: 9,
                color: "gray",
                margin: [0, 5, 10, 10],
              },
            ],
          },
        ],
      ],
    },
    layout: {
      fillColor: "#d9d6ce",
      hLineWidth: () => 0,
      vLineWidth: () => 0,
      paddingLeft: () => 35,
      paddingRight: () => 35,
      paddingTop: () => 20,
      paddingBottom: () => 25,
    },
    margin: [-40, -40, -40, 20],
  };
}

export function getFooterDefinition() {
  return function (currentPage, pageCount) {
    return {
      columns: [
        {
          text: "© 2025 E-SHOPPER Inc. Todos los derechos reservados.",
          alignment: "left",
          fontSize: 9,
          margin: [40, 0, 0, 10],
          color: "#FFA500",
        },
        {
          text: `Diseñado por Themeum | Página ${currentPage} de ${pageCount}`,
          alignment: "right",
          fontSize: 9,
          margin: [0, 0, 40, 10],
          color: "#FFA500",
        },
      ],
    };
  };
}
