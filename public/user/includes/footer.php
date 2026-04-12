<footer class="footer bg-dark py-5 py-lg-11 py-xl-12">
  <div class="container">
    <div class="row">
      <div class="col-xl-5 mb-8 mb-xl-0">
        <div class="d-flex flex-column gap-8 pe-xl-5">
          <h2 class="mb-0 text-white">Build something together?</h2>
          <div class="d-flex flex-column gap-2">
            <a href="https://www.wrappixel.com/" target="_blank" class="link-hover hstack gap-3 text-white fs-5">
              <iconify-icon icon="lucide:arrow-up-right" class="fs-7 text-primary"></iconify-icon>
              info@wrappixel.com
            </a>
            <a href="https://maps.app.goo.gl/hpDp81fqzGt5y4bC8" target="_blank"
              class="link-hover hstack gap-3 text-white fs-5">
              <iconify-icon icon="lucide:map-pin" class="fs-7 text-primary"></iconify-icon>
              info@wrappixel.com
            </a>
          </div>
        </div>
      </div>
      <div class="col-md-4 col-xl-2 mb-8 mb-xl-0">
        <ul class="footer-menu list-unstyled mb-0 d-flex flex-column gap-2">
          <li><a class="link-hover fs-5 text-white" href="index.html">Home</a></li>
          <li><a class="link-hover fs-5 text-white" href="about-us.html">About</a></li>
          <li><a class="link-hover fs-5 text-white" id="services" href="#services">Services</a></li>
          <li><a class="link-hover fs-5 text-white" href="projects.html">Work</a></li>
          <li><a class="link-hover fs-5 text-white" href="terms-and-conditions.html">Terms</a></li>
          <li><a class="link-hover fs-5 text-white" href="privacy-policy.html">Privacy Policy</a></li>
          <li><a class="link-hover fs-5 text-white" href="404.html">Error 404</a></li>
        </ul>
      </div>
      <div class="col-md-4 col-xl-2 mb-8 mb-xl-0">
        <ul class="footer-menu list-unstyled mb-0 d-flex flex-column gap-2">
          <li><a class="link-hover fs-5 text-white" href="https://www.facebook.com/">Facebook</a></li>
          <li><a class="link-hover fs-5 text-white" href="https://www.instagram.com/">Instagram</a></li>
          <li><a class="link-hover fs-5 text-white" href="https://x.com/">Twitter</a></li>
        </ul>
      </div>
      <div class="col-md-4 col-xl-3 mb-8 mb-xl-0">
        <p class="mb-0 text-white text-opacity-70 text-md-end">© Studiova copyright 2025</p>
      </div>
    </div>
  </div>
</footer>

<div class="get-template hstack gap-2">
  <!-- <a class="bg-primary px-3 py-2 rounded fs-3 fw-semibold text-dark" target="_blank"
      href="https://www.wrappixel.com/templates/">Get This Template</a> -->
  <button class="btn bg-primary p-2 round-52 rounded-circle hstack justify-content-center flex-shrink-0"
    id="scrollToTopBtn">
    <iconify-icon icon="lucide:arrow-up" class="fs-7 text-dark"></iconify-icon>
  </button>
</div>


<script src="../assets/libs/jquery/dist/jquery.min.js"></script>
<script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/libs/owl.carousel/dist/owl.carousel.min.js"></script>
<script src="../assets/libs/aos-master/dist/aos.js"></script>
<script src="../assets/js/custom.js"></script>

<!-- solar icons -->
<script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php
if (isset($_SESSION['message']) && $_SESSION['code'] != '') {
  ?>
  <script>
    const Toast = Swal.mixin({
      toast: true,
      position: "top-end",
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true,
      didOpen: (toast) => {
        toast.onmouseenter = Swal.stopTimer;
        toast.onmouseleave = Swal.resumeTimer;
      }
    });

    Toast.fire({
      icon: "<?php echo $_SESSION['code']; ?>",
      title: "<?php echo $_SESSION['message']; ?>"
    });
  </script>
  <?php
  unset($_SESSION['message']);
  unset($_SESSION['code']);
}
?>


</body>

</html>