document.addEventListener("DOMContentLoaded", function () {
  const searchInput = document.getElementById("search-input");
  const searchBtn = document.getElementById("search-btn");
  const clearSearchBtn = document.getElementById("clear-search-btn");
  const addProductBtn = document.getElementById("add-product-btn");
  const productForm = document.getElementById("product-form");
  const productFormElement = document.getElementById("product-form-element");
  const cancelBtn = document.getElementById("cancel-btn");
  const productsList = document.getElementById("products-list");
  const formTitle = document.getElementById("form-title");

  const productIdField = document.getElementById("product-id");
  const nameField = document.getElementById("name");
  const descriptionField = document.getElementById("description");
  const priceField = document.getElementById("price");

  loadProducts();

  searchBtn.addEventListener("click", function () {
    loadProducts(searchInput.value);
  });

  clearSearchBtn.addEventListener("click", function () {
    searchInput.value = "";
    loadProducts();
  });

  addProductBtn.addEventListener("click", function () {
    showForm("add");
  });

  cancelBtn.addEventListener("click", function () {
    hideForm();
  });

  productFormElement.addEventListener("submit", function (e) {
    e.preventDefault();
    const isEdit = productIdField.value !== "";

    const productData = {
      name: nameField.value,
      description: descriptionField.value,
      price: parseFloat(priceField.value),
    };

    if (isEdit) {
      productData.id = parseInt(productIdField.value);
      updateProduct(productData);
    } else {
      createProduct(productData);
    }
  });

  function loadProducts(search = "") {
    let url = "../api/index.php";
    if (search) {
      url += "?search=" + encodeURIComponent(search);
    }

    fetch(url)
      .then((response) => response.json())
      .then((data) => {
        displayProducts(data.records);
      })
      .catch((error) => {
        console.error("Error:", error);
      });
  }

  function displayProducts(products) {
    productsList.innerHTML = "";

    if (products.length === 0) {
      productsList.innerHTML = "<p>No products found.</p>";
      return;
    }

    products.forEach((product) => {
      const productCard = document.createElement("div");
      productCard.className = "product-card";

      productCard.innerHTML = `
                <h3>${product.name}</h3>
                <p>${product.description}</p>
                <p><strong>Price:</strong> $${parseFloat(product.price).toFixed(
                  2
                )}</p>
                <div class="product-actions">
                    <button class="btn edit-btn" data-id="${
                      product.id
                    }">Edit</button>
                    <button class="btn delete-btn" data-id="${
                      product.id
                    }">Delete</button>
                </div>
            `;

      productsList.appendChild(productCard);

      productCard
        .querySelector(".edit-btn")
        .addEventListener("click", function () {
          const productId = this.getAttribute("data-id");
          loadProductDetails(productId);
        });

      productCard
        .querySelector(".delete-btn")
        .addEventListener("click", function () {
          const productId = this.getAttribute("data-id");
          if (confirm("Are you sure you want to delete this product?")) {
            deleteProduct(productId);
          }
        });
    });
  }

  function loadProductDetails(productId) {
    fetch(`../api/index.php?endpoint=product&id=${productId}`, {
      method: "GET",
    })
      .then((response) => response.json())
      .then((product) => {
        productIdField.value = product.id;
        nameField.value = product.name;
        descriptionField.value = product.description;
        priceField.value = product.price;

        showForm("edit");
      })
      .catch((error) => {
        console.error("Error:", error);
      });
  }

  function createProduct(productData) {
    fetch("../api/index.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(productData),
    })
      .then((response) => response.json())
      .then((data) => {
        alert(data.message);
        resetForm();
        hideForm();
        loadProducts();
      })
      .catch((error) => {
        console.error("Error:", error);
      });
  }

  function updateProduct(productData) {
    fetch("../api/index.php", {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(productData),
    })
      .then((response) => response.json())
      .then((data) => {
        alert(data.message);
        resetForm();
        hideForm();
        loadProducts();
      })
      .catch((error) => {
        console.error("Error:", error);
      });
  }

  function deleteProduct(productId) {
    fetch("../api/index.php", {
      method: "DELETE",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ id: productId }),
    })
      .then((response) => response.json())
      .then((data) => {
        alert(data.message);
        loadProducts();
      })
      .catch((error) => {
        console.error("Error:", error);
      });
  }

  function showForm(mode) {
    formTitle.textContent = mode === "add" ? "Add Product" : "Edit Product";
    productForm.style.display = "block";
  }

  function hideForm() {
    productForm.style.display = "none";
    resetForm();
  }

  function resetForm() {
    productFormElement.reset();
    productIdField.value = "";
  }
});
