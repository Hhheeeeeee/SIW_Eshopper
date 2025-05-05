// generarPDF.js
import {
  toDataURL,
  getHeaderContent,
  getFooterDefinition,
} from "./moduloExportar.js";

$(document).ready(function () {
  $("#verMenuExportar").click(function () {
    verificarSesion(() => {
      $("#submenuExportar").toggle();
    });
  });
});

$(document).click(function (e) {
  if ($("#submenuExportar").is(":visible")) {
    if (
      !$(e.target).closest("#submenuExportar").length &&
      !$(e.target).closest("#verMenuExportar").length
    ) {
      $("#submenuExportar").hide();
    }
  }
});

/* Añadir Listeners abajos */

const botones = [
  { id: "exportarCategorias", handler: generarPDFConCategorias },
  { id: "exportarProductos", handler: generarPDFConProductos },
  { id: "exportarOfertas", handler: generarPDFConOfertas },
  { id: "exportarPedidos", handler: generarPDFConPedidos },
  { id: "exportarOpiniones", handler: generarPDFConOpiniones },
];

botones.forEach(({ id, handler }) => {
  const boton = document.getElementById(id);
  if (boton) {
    boton.addEventListener("click", handler);
  }
});

async function generarPDFConProductos() {
  console.log("Exportar productos ..");
  try {
    const headerContent = await getHeaderContent();
    const footerContent = getFooterDefinition();
    const response = await fetch("get_products_by_category.php?category=all");
    const data = await response.json();

    if (!data.success) {
      alert("Error al cargar productos.");
      return;
    }

    const products = data.products;

    const productsWithBase64 = await Promise.all(
      products.map(async (product) => {
        const base64Image = await toDataURL(product.image).catch(() => null);
        return {
          id: product.id,
          name: product.name,
          price: product.price,
          image: base64Image,
        };
      })
    );

    const bodyTable = [
      [
        { text: "ID", bold: true },
        { text: "Imagen", bold: true },
        { text: "Nombre", bold: true },
        { text: "Precio", bold: true },
      ],
    ];

    productsWithBase64.forEach((product) => {
      bodyTable.push([
        { text: product.id.toString() },
        product.image
          ? { image: product.image, width: 50, height: 50 }
          : { text: "No disponible" },
        { text: product.name },
        { text: `$${parseFloat(product.price).toFixed(2)}` },
      ]);
    });

    const docDefinition = {
      content: [
        headerContent,
        {
          text: "Listado de Productos",
          style: "header",
          margin: [0, 0, 0, 20],
        },
        {
          table: {
            headerRows: 1,
            widths: [50, 70, "*", 60],
            body: bodyTable,
          },
        },
      ],
      styles: {
        header: {
          fontSize: 18,
          bold: true,
        },
      },
      footer: footerContent,
    };

    pdfMake.createPdf(docDefinition).download("productos.pdf");
  } catch (error) {
    console.error("Error al generar el PDF:", error);
    alert("Ocurrió un error al generar el PDF.");
  }
}

async function generarPDFConCategorias() {
  console.log("Exportar categorías ..");
  try {
    const headerContent = await getHeaderContent();
    const footerContent = getFooterDefinition();
    const response = await fetch("get_categories.php");
    const data = await response.json();

    if (!data || Object.keys(data).length === 0) {
      alert("Error al cargar categorías.");
      return;
    }

    const bodyTable = [
      [
        { text: "Categoría Principal", bold: true },
        { text: "Subcategoría", bold: true },
      ],
    ];

    for (const mainCategory in data) {
      if (data.hasOwnProperty(mainCategory)) {
        let subCategories = data[mainCategory];

        subCategories.forEach((subCategory, index) => {
          bodyTable.push([
            index === 0
              ? { text: mainCategory, rowSpan: subCategories.length }
              : {},
            { text: subCategory },
          ]);
        });
      }
    }

    const docDefinition = {
      content: [
        headerContent,
        {
          text: "Listado de Categorías",
          style: "header",
          margin: [0, 0, 0, 20],
        },
        {
          table: {
            headerRows: 1,
            widths: ["*", "*"],
            body: bodyTable,
          },
        },
      ],
      styles: {
        header: {
          fontSize: 18,
          bold: true,
        },
      },
      footer: footerContent,
    };

    pdfMake.createPdf(docDefinition).download("categorias.pdf");
  } catch (error) {
    console.error("Error al generar el PDF:", error);
    alert("Ocurrió un error al generar el PDF.");
  }
}

async function generarPDFConOfertas() {
  console.log("Exportar ofertas ..");
  try {
    const headerContent = await getHeaderContent();
    const footerContent = getFooterDefinition();
    const response = await fetch("get_ofertas.php");
    const data = await response.json();

    if (!data.success) {
      alert("Error al cargar ofertas.");
      return;
    }

    const offers = data.offers;

    const offersWithBase64 = await Promise.all(
      offers.map(async (offer) => {
        const base64Image = await toDataURL(offer.imagen).catch(() => null);
        return {
          id: offer.id,
          titulo: offer.titulo,
          precio_original: offer.precio_original,
          precio_oferta: offer.precio_oferta,
          imagen: base64Image,
        };
      })
    );

    const bodyTable = [
      [
        { text: "ID", bold: true },
        { text: "Imagen", bold: true },
        { text: "Nombre", bold: true },
        { text: "Precio Original", bold: true },
        { text: "Precio Oferta", bold: true },
      ],
    ];

    offersWithBase64.forEach((offer) => {
      bodyTable.push([
        { text: offer.id.toString() },
        offer.imagen
          ? { image: offer.imagen, width: 50, height: 50 }
          : { text: "No disponible" },
        { text: offer.titulo },
        { text: `$${parseFloat(offer.precio_original).toFixed(2)}` },
        { text: `$${parseFloat(offer.precio_oferta).toFixed(2)}` },
      ]);
    });

    const logoBase64 = await toDataURL("images/home/logo.png").catch(
      () => null
    );

    const docDefinition = {
      content: [
        headerContent,
        {
          text: "Listado de Ofertas",
          style: "header",
          margin: [0, 0, 0, 20],
        },
        {
          table: {
            headerRows: 1,
            widths: [50, 70, "*", "*", "*"],
            body: bodyTable,
          },
        },
      ],
      styles: {
        header: {
          fontSize: 18,
          bold: true,
        },
      },
      footer: footerContent,
    };

    pdfMake.createPdf(docDefinition).download("ofertas.pdf");
  } catch (error) {
    console.error("Error al generar el PDF:", error);
    alert("Ocurrió un error al generar el PDF.");
  }
}

async function generarPDFConPedidos() {
  console.log("Exportar pedidos ..");
  try {
    // 1. Obtener pedidos
    const headerContent = await getHeaderContent();
    const footerContent = getFooterDefinition();
    const response = await fetch("get_pedidos.php");
    const data = await response.json();

    console.log("Respuesta de get_pedidos.php:", data);

    if (!data.success) {
      alert("Error al cargar pedidos.");
      return;
    }

    const pedidos = data.pedidos;

    // 2. Crear estructura del PDF
    const bodyTable = [
      [
        { text: "ID", bold: true },
        { text: "Título", bold: true },
        { text: "Estado", bold: true },
        { text: "Precio Unidad", bold: true },
        { text: "Total", bold: true },
      ],
    ];

    pedidos.forEach((pedido) => {
      bodyTable.push([
        { text: pedido.id.toString() },
        { text: pedido.titulo },
        { text: pedido.estado },
        { text: `$${parseFloat(pedido.precio_unidad).toFixed(2)}` },
        { text: `$${parseFloat(pedido.total).toFixed(2)}` },
      ]);
    });

    const logoBase64 = await toDataURL("images/home/logo.png").catch(
      () => null
    );

    const docDefinition = {
      content: [
        headerContent,
        {
          text: "Listado de Pedidos",
          style: "header",
          margin: [0, 0, 0, 20],
        },
        {
          table: {
            headerRows: 1,
            widths: [50, "*", "*", "*", "*"],
            body: bodyTable,
          },
        },
      ],
      styles: {
        header: {
          fontSize: 18,
          bold: true,
        },
      },
      footer: footerContent,
    };

    // 4. Generar PDF
    pdfMake.createPdf(docDefinition).download("pedidos.pdf");
  } catch (error) {
    console.error("Error al generar el PDF:", error);
    alert("Ocurrió un error al generar el PDF.");
  }
}

async function generarPDFConOpiniones() {
  console.log("Exportar opiniones...");
  try {
    const headerContent = await getHeaderContent();
    const footerContent = getFooterDefinition();

    const idText = document.querySelector(
      ".product-information p:nth-of-type(1)"
    ).textContent;
    const match = idText.match(/Web ID:\s*(.+)/); // Captura cualquier texto después de "Web ID:"
    if (!match) {
      alert("No se pudo obtener el ID del producto.");
      return;
    }
    const idProducto = match[1].trim(); // Esto puede ser string tipo "PROD-45" o número tipo "123"
    console.log("ID del producto:", idProducto);

    const response = await fetch(`get_reviews.php?id=${idProducto}`);
    const data = await response.json();

    if (!data.success) {
      alert("Error al cargar opiniones.");
      return;
    }

    const opiniones = data.reviews;

    const bodyTable = [
      [
        { text: "Usuario", bold: true },
        { text: "Comentario", bold: true },
        { text: "Calificación", bold: true },
        { text: "Fecha", bold: true },
      ],
    ];

    opiniones.forEach((opinion) => {
      bodyTable.push([
        { text: opinion.usuario },
        { text: opinion.comentario },
        { text: opinion.rating.toString() },
        { text: opinion.fecha },
      ]);
    });

    // 4. Definir el PDF
    const docDefinition = {
      content: [
        headerContent,
        {
          text: "Opiniones del Producto",
          style: "header",
          margin: [0, 0, 0, 20],
        },

        {
          table: {
            headerRows: 1,
            widths: ["*", "*", 60, 60],
            body: bodyTable,
          },
        },
      ],
      styles: {
        header: {
          fontSize: 18,
          bold: true,
        },
      },
      footer: footerContent,
    };

    // 5. Generar PDF
    pdfMake.createPdf(docDefinition).download("opiniones.pdf");
  } catch (error) {
    console.error("Error al generar el PDF:", error);
    alert("Ocurrió un error al generar el PDF.");
  }
}
