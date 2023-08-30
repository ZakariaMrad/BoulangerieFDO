$(document).ready(() => {

    var modal = document.getElementById("myModal");
    $(".card-modal").click(async (event) => {
        event.preventDefault();

        const href = event.currentTarget.href;

        const response = await axios.get(href);
        if (response.status === 200) {
            modal.innerHTML = response.data;
            modal.style.display = "block";
            loadModalJs(modal);
        }
    })

    var eX=0;
    $(window).on('mousemove', function(e){
        const scroll = $('.container').scrollLeft()
        if (e.clientX < 150 && e.clientX < eX) {
            $('.container').scrollLeft(scroll-((150-e.clientX)/2))
        }
        if (e.clientX > window.innerWidth-150 && e.clientX > eX) {
            $('.container').scrollLeft(scroll + (150-(window.innerWidth-e.clientX))/2)
        }
        

        eX = e.clientX;
      });
})
// Get the <span> element that closes the modal
function loadModalJs(modal) {

    var span = document.getElementsByClassName("close")[0];
    span.onclick = function () {
        modal.style.display = "none";
    }

    window.onclick = function (event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    var carouselImages = document.querySelector("#carousel-images");
    var prevButton = document.getElementById("prev-button");
    var nextButton = document.getElementById("next-button");

    nextButton.onclick = () => {
        let imgN = carouselImages.dataset.imgd;
        let imgMax = carouselImages.children.length;
        if (imgMax === 1) return;


        for (let i = 0; i < imgMax; i++) {
            if (i != imgN) {
                removeClasses(carouselImages, i);
                carouselImages.children[i].classList.add('img-right')
            }
        }
        carouselImages.children[imgN].classList.remove('img-center')
        carouselImages.children[imgN].classList.add('img-left')
        carouselImages.children[imgN].classList.add('t')
        imgN++;
        if (imgN >= imgMax)
            imgN = 0;
        carouselImages.dataset.imgd = imgN;
        carouselImages.children[imgN].classList.remove('img-right')
        carouselImages.children[imgN].classList.add('img-center')
    }
    prevButton.onclick = () => {
        let imgN = carouselImages.dataset.imgd;
        let imgMax = carouselImages.children.length;
        if (imgMax === 1) return;

        for (let i = 0; i < imgMax; i++) {
            if (i != imgN) {
                removeClasses(carouselImages, i);
                carouselImages.children[i].classList.add('img-left')
            }
        }
        carouselImages.children[imgN].classList.remove('img-center')
        carouselImages.children[imgN].classList.add('img-right')
        carouselImages.children[imgN].classList.add('t')
        imgN--;
        if (imgN < 0)
            imgN = imgMax - 1;
        carouselImages.dataset.imgd = imgN;
        carouselImages.children[imgN].classList.remove('img-left')
        carouselImages.children[imgN].classList.add('img-center')
    }
}
function removeClasses(carouselImages, i) {
    carouselImages.children[i].classList.remove('img-center')
    carouselImages.children[i].classList.remove('img-left')
    carouselImages.children[i].classList.remove('img-right')
    carouselImages.children[i].classList.remove('t')
}
