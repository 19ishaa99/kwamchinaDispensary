// Render chart
const ctx = document.getElementById("expenseChart").getContext("2d");
new Chart(ctx, {
  type: "line",
  data: {
    labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
    datasets: [
      {
        label: "Expenses",
        data: [400, 600, 500, 650, 550, 760],
        borderColor: "red",
        fill: false
      },
      {
        label: "Income",
        data: [600, 850, 700, 900, 750, 1250],
        borderColor: "green",
        fill: false
      }
    ]
  }
});

// Medicine form logic
let medIndex = 1;
document.getElementById("medicineForm").addEventListener("submit", function (e) {
  e.preventDefault();
  const name = document.getElementById("medName").value;
  const cat = document.getElementById("medCategory").value;
  const qty = document.getElementById("medQty").value;
  const price = document.getElementById("medPrice").value;

  const row = document.createElement("tr");
  row.innerHTML = `<td>${medIndex++}</td><td>${name}</td><td>${cat}</td><td>${qty}</td><td>${price}</td>`;
  document.querySelector("#medicineTable tbody").appendChild(row);

  this.reset();
});
