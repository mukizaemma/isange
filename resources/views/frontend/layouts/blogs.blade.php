<section class="blog-area pt-30 rpt-100 pb-50 rpb-70 rel z-1">
    <div class="container">
        <div class="row justify-content-center">
            @foreach ($blogs as $blog )
            <div class="col-xl-4 col-md-6">
                <div class="blog-grid-item wow fadeInUp delay-0-2s">
                    <div class="image">
                        <img src="assets/images/blog/blog1.jpg" alt="Blog">
                        <ul class="blog-meta">
                            <li>
                                <i class="far fa-user"></i>
                                <a href="#">{{ $blog->title }}</a>
                            </li>
                            <li>
                                <i class="far fa-calendar-alt"></i>
                                <a href="#">{{ optional($blog->published_at)->format('d M, Y') }}</a>
                            </li>
                        </ul>
                    </div>
                    <div class="blog-content">
                        <h4><a href="{{ route('blog', ['slug' => $blog->slug]) }}">{{ $blog->title }}</a></h4>
                        <p>Perspiciatis omniste voluptate accusantiume doloremque laudantium, totam aperiam</p>
                        <a class="read-more" href="{{ route('blog', ['slug' => $blog->slug]) }}">Read More <i class="fal fa-angle-right"></i></a>
                    </div>
                </div>
            </div>
            @endforeach

        </div>
    </div>
    <div class="bg-lines for-bg-white">
       <span></span><span></span>
       <span></span><span></span>
       <span></span><span></span>
       <span></span><span></span>
       <span></span><span></span>
    </div>
</section>