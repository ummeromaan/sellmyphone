<?php
require_once 'admin/includes/db.php';
require_once 'includes/home-data.php';
/**@var mysqli $conn */

$testimonials = home_rows($conn, 'home_testimonials');
$tsn_pages    = array_chunk($testimonials, 3);
if (empty($tsn_pages)) { $tsn_pages = [[]]; }
?>
<!--testimonials section - dark navy/gold theme, 3-per-page carousel-->

<section class="tsn-section" id="testimonials">
    <div class="container-fluid tsn-container-fluid">

        <div class="tsn-header">
            <div class="tsn-tag-wrap">
                <span class="tsn-tag-text">
                    <i class="fa-solid fa-comment-dots"></i>
                TESTIMONIALS
                </span>
            </div>
            <h2 class="tsn-title">What Our<span class="cus"style="color:#ebb917;"> Customers</span> Say</h2>
        </div>

        <div class="tsn-carousel-wrap">

            <button type="button" class="tsn-arrow tsn-arrow-left" id="tsnPrevBtn" aria-label="Previous testimonials">
                <i class="fa-solid fa-chevron-left"></i>
            </button>

            <div class="tsn-track-wrap">
                <div class="tsn-track" id="tsnTrack">

                    <?php foreach ($tsn_pages as $page): ?>
                    <div class="tsn-page">
                        <?php foreach ($page as $t): ?>
                        <div class="tsn-card">
                            <div class="tsn-stars">
                                <?php for ($s = 0; $s < intval($t['rating']); $s++): ?><i class="fa-solid fa-star"></i><?php endfor; ?>
                            </div>
                            <p class="tsn-quote">"<?php echo $t['quote']; ?>"</p>
                            <div class="tsn-author">
                                <span class="tsn-avatar"><?php echo htmlspecialchars($t['avatar_letter']); ?></span>
                                <div class="tsn-author-info">
                                    <p><?php echo $t['author_name']; ?></p>
                                    <span><?php echo $t['author_location']; ?></span>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endforeach; ?>

                </div>
            </div>

            <button type="button" class="tsn-arrow tsn-arrow-right" id="tsnNextBtn" aria-label="Next testimonials">
                <i class="fa-solid fa-chevron-right"></i>
            </button>

        </div>

        <div class="tsn-dots" id="tsnDots">
            <?php foreach ($tsn_pages as $i => $page): ?>
            <span class="tsn-dot<?php echo $i === 0 ? ' active' : ''; ?>" data-index="<?php echo $i; ?>"></span>
            <?php endforeach; ?>
        </div>

    </div>
</section>

<script>
(function () {
    var track = document.getElementById('tsnTrack');
    var pages = track.querySelectorAll('.tsn-page');
    var totalPages = pages.length;
    var currentIndex = 0;

    var prevBtn = document.getElementById('tsnPrevBtn');
    var nextBtn = document.getElementById('tsnNextBtn');
    var dots = document.querySelectorAll('#tsnDots .tsn-dot');

    function updateCarousel() {
        track.style.transform = 'translateX(-' + (currentIndex * 100) + '%)';

        // First page: hide left arrow. Last page: hide right arrow. Middle: show both.
        prevBtn.style.display = (currentIndex === 0) ? 'none' : 'flex';
        nextBtn.style.display = (currentIndex === totalPages - 1) ? 'none' : 'flex';

        dots.forEach(function (dot, i) {
            dot.classList.toggle('active', i === currentIndex);
        });
    }

    prevBtn.addEventListener('click', function () {
        if (currentIndex > 0) {
            currentIndex--;
            updateCarousel();
        }
    });

    nextBtn.addEventListener('click', function () {
        if (currentIndex < totalPages - 1) {
            currentIndex++;
            updateCarousel();
        }
    });

    dots.forEach(function (dot) {
        dot.addEventListener('click', function () {
            currentIndex = parseInt(dot.getAttribute('data-index'), 10);
            updateCarousel();
        });
    });

    updateCarousel();
})();
</script>
