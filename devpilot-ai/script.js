async function analyzeCode() {

    const code =
        document.getElementById("codeInput").value;

    const response = await fetch("./analyze.php", {

        method: "POST",

        headers: {
            "Content-Type": "application/json"
        },

        body: JSON.stringify({
            code: code
        })

    });

    const data = await response.json();

    document.getElementById("output")
        .innerText = data.result;
}