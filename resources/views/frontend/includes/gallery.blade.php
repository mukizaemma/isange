<section class="gallery-page-area pt-70 rpt-60 pb-100 rpb-0">
    <div class="container">
        <!-- Dynamic Filter Tabs -->
        <ul class="gallery-filter filter-btns-one justify-content-center pb-50 wow fadeInUp delay-0-2s">
            {{-- <li data-filter="*" class="current">Show All</li> --}}
            @foreach ($categories as $category)
                <li data-filter=".{{ Str::slug($category) }}">{{ ucfirst($category) }}</li>
            @endforeach
        </ul>

        <div class="row gallery-masonry-active">
            @foreach ($images as $image)
                <div class="col-xl-4 col-md-6 item {{ Str::slug($image->category) }}">
                    <div class="gallery-item style-two wow fadeInUp delay-0-2s">
                        <div class="image">
                            <img src="{{ asset('storage/images/gallery/'.$image->image) }}" 
                                 alt="{{ $image->category }}" 
                                 class="gallery-img"
                                 data-index="{{ $loop->index }}"
                                 data-src="{{ asset('storage/images/gallery/'.$image->image) }}"
                                 style="height:300px; object-fit: cover; cursor: pointer;"
                                 onclick="openModal({{ $loop->index }})">
                        </div>
                        <div class="over-content">
                            <h2>{{ $image->caption ?? '' }}</h2>
                            <span class="eye-icon" onclick="openModal({{ $loop->index }})">
                                👁️
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Modal Structure -->
<div id="imageModal" class="modal" style="display: none;">
    <span class="close" onclick="closeModal()">&times;</span>
    <img class="modal-content" id="modalImg">
    
    <!-- Navigation Controls -->
    <span class="prev" onclick="changeImage(-1)">&#10094;</span>
    <span class="next" onclick="changeImage(1)">&#10095;</span>
</div>

<!-- JavaScript -->
<script>
    let currentIndex = 0;
    let images = [];

    // Store all images in an array
    document.addEventListener("DOMContentLoaded", function () {
        images = Array.from(document.querySelectorAll('.gallery-img')).map(img => img.dataset.src);
    });

    function openModal(index) {
        currentIndex = index;
        let modal = document.getElementById("imageModal");
        let modalImg = document.getElementById("modalImg");
        modal.style.display = "flex";
        modalImg.src = images[currentIndex];
    }

    function closeModal() {
        document.getElementById("imageModal").style.display = "none";
    }

    function changeImage(step) {
        currentIndex += step;
        if (currentIndex < 0) currentIndex = images.length - 1;
        if (currentIndex >= images.length) currentIndex = 0;
        document.getElementById("modalImg").src = images[currentIndex];
    }

    // Close modal when clicking outside the image
    document.getElementById("imageModal").addEventListener("click", function (event) {
        if (event.target === this) closeModal();
    });

    // Keyboard Navigation
    document.addEventListener("keydown", function (event) {
        if (document.getElementById("imageModal").style.display === "flex") {
            if (event.key === "ArrowRight") changeImage(1);
            if (event.key === "ArrowLeft") changeImage(-1);
            if (event.key === "Escape") closeModal();
        }
    });
</script>