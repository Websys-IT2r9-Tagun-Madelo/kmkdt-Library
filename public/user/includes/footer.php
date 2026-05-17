<footer class="footer bg-dark py-5">
  <div class="container pt-8"> 
    <div class="row">
      <div class="col-xl-5 mb-8 mb-xl-0">
        <div class="d-flex flex-column gap-8 pe-xl-5">
          <h2 class="mb-0 text-white">Have a great day!</h2>
        </div>
      </div>
      
      <div class="col-md-4 col-xl-2 mb-8 mb-xl-0">
        <ul class="footer-menu list-unstyled mb-0 d-flex flex-column gap-2">
          <li><a class="link-hover fs-5 text-white" href="index">Home</a></li>
          <li><a class="link-hover fs-5 text-white" href="profile">Profile</a></li>
          <li><a class="link-hover fs-5 text-white" href="browseBooks">Browse Books</a></li>
          <li><a class="link-hover fs-5 text-white" href="MBB">My Borrowed Books</a></li>
          <li><a class="link-hover fs-5 text-white" href="contact">Contact Us</a></li>
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
<script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/modal-handler.js"></script>
<script src="assets/js/messenger.js"></script>



<!-- solar icons -->
<script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php
if (isset($_SESSION['message']) && !empty($_SESSION['code'])) {
  ?>
  <script>
    const Toast = Swal.mixin({
      toast: true,
      position: "top-end",
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true,
      didOpen: (toast) => {
        const progressBar = toast.querySelector('.swal2-timer-progress-bar');
        if (progressBar) {
            progressBar.style.backgroundColor = '#32cd32';
        }
        toast.onmouseenter = Swal.stopTimer;
        toast.onmouseleave = Swal.resumeTimer;
      }
    });

    Toast.fire({
      icon: "<?php echo $_SESSION['code']; ?>", 
      title: "<?php echo $_SESSION['message']; ?>",
      iconColor: '<?php echo ($_SESSION['code'] == "success") ? "#32cd32" : ""; ?>'
    });
  </script>
  <?php 
  unset($_SESSION['message']);
  unset($_SESSION['code']);
}
?>


</body>

</html>