function searchArticles() {
    let query = document.getElementById("search").value;

    fetch("/api/search.php?q=" + query)
        .then(res => res.text())
        .then(data => {
            document.getElementById("results").innerHTML = data;
        });
}