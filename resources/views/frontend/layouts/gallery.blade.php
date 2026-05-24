<section class="gallery-area mt-120 mb-130 rmb-30">
    <div class="gallery-active">
        @foreach ($gallery as $index => $image)
        <div class="gallery-item wow fadeInUp delay-0-2s">
            <div class="image">
                <img src="{{ asset('storage/images/gallery') . $image->image }}" 
                     alt="Gallery"
                     style="height: 300px; object-fit:cover; cursor:pointer;"
                     data-index="{{ $index }}"
                     data-src="{{ asset('storage/images/gallery') . $image->image }}"
                     class="galleryImage">
            </div>
            <div class="over-content">
                <h2>{{ $image->caption }}</h2>
            </div>
        </div>
        @endforeach
    </div>

    <div class="col-lg-4 text-lg-end mt-5 text-center">
        <a class="theme-btn style-three mb-30 wow fadeInRight delay-0-2s" href="{{ route('gallery') }}">Explore More Images<i class="fal fa-angle-right"></i></a>
    </div>
</section>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        let images = document.querySelectorAll('.galleryImage');
        let modalImage = document.getElementById('modalImage');
        let currentIndex = 0;

        images.forEach((img, index) => {
            img.addEventListener('click', function () {
                currentIndex = index;
                updateModalImage();
                new bootstrap.Modal(document.getElementById('imageModal')).show();
            });
        });

        document.getElementById('prevImage').addEventListener('click', function () {
            currentIndex = (currentIndex - 1 + images.length) % images.length;
            updateModalImage();
        });

        document.getElementById('nextImage').addEventListener('click', function () {
            currentIndex = (currentIndex + 1) % images.length;
            updateModalImage();
        });

        function updateModalImage() {
            modalImage.src = images[currentIndex].getAttribute('data-src');
        }
    });
</script>
