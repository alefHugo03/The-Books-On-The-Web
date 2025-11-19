// Config Worker
pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.14.305/pdf.worker.min.js';

document.addEventListener("DOMContentLoaded", function() {
    const canvases = document.querySelectorAll('.pdf-thumb');

    canvases.forEach(canvas => {
        const url = canvas.getAttribute('data-url');
        if (url && url.trim() !== '') {
            renderizarCapa(url, canvas);
        }
    });
});

function renderizarCapa(url, canvas) {
    pdfjsLib.getDocument(url).promise.then(pdf => {
        pdf.getPage(1).then(page => {
            var scale = 0.8;
            var viewport = page.getViewport({ scale: scale });
            var context = canvas.getContext('2d');

            canvas.height = viewport.height;
            canvas.width = viewport.width;

            page.render({
                canvasContext: context,
                viewport: viewport
            });
        });
    }).catch(error => {
        console.error("Erro PDF:", url, error);
        // Desenha um erro visual no canvas
        var ctx = canvas.getContext("2d");
        canvas.width = 120; canvas.height = 170;
        ctx.fillStyle = "#f8d7da";
        ctx.fillRect(0, 0, 120, 170);
        ctx.fillStyle = "red";
        ctx.fillText("Erro PDF", 35, 85);
    });
}