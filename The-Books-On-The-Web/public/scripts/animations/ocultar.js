function alternarDiv() {
  var div = document.getElementById("minhaDiv");
  if (div.style.display === "none") {
    div.style.display = "block"; // Ou "flex", "grid", dependendo do seu layout
  } else {
    div.style.display = "none";
  }
}