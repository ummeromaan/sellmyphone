<?php
require_once 'admin/includes/db.php';
/**@var mysqli $conn */

$brand = 'Samsung';
$brand_esc = mysqli_real_escape_string($conn, $brand);

// ----------  fetch the prices of models,storage and prices of this model----------
$result = mysqli_query($conn, "SELECT * FROM phones WHERE brand='$brand_esc' ORDER BY phone_model DESC, storage DESC");

$phoneData = [];
while ($row = mysqli_fetch_assoc($result)) {
    $model = $row['phone_model'];

    if (!isset($phoneData[$model])) {
        $phoneData[$model] = [
            'image'    => $row['image'],
            'variants' => [],
        ];
    }

    $phoneData[$model]['variants'][$row['storage']] = [
        'Flawless' => (float) $row['flawless_price'],
        'Good'     => (float) $row['good_price'],
        'Fair'     => (float) $row['fair_price'],
    ];
}

// ----------fetch the accessories prices of all models of this brand ----------
$acc_result = mysqli_query($conn, "SELECT * FROM model_accessories WHERE brand='$brand_esc'");
$accessoriesData = [];
while ($row = mysqli_fetch_assoc($acc_result)) {
    $accessoriesData[$row['phone_model']] = [
        'charger'   => (float) $row['charger_price'],
        'earphones' => (float) $row['earphones_price'],
        'box'       => (float) $row['box_price'],
        'bill'      => (float) $row['bill_price'],
    ];
}
?>

<div class="samsung" id="samsung">

<ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
  <li class="nav-item" role="presentation">
    <button class="nav-link active" id="pills-model-tab" data-bs-toggle="pill" data-bs-target="#pills-model" type="button" role="tab" aria-controls="pills-model" aria-selected="true">Model</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="pills-storage-tab" data-bs-toggle="pill" data-bs-target="#pills-storage" type="button" role="tab" aria-controls="pills-storage" aria-selected="false">Storage</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="pills-condition-tab" data-bs-toggle="pill" data-bs-target="#pills-condition" type="button" role="tab" aria-controls="pills-condition" aria-selected="false">Condition</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="pills-accessories-tab" data-bs-toggle="pill" data-bs-target="#pills-accessories" type="button" role="tab" aria-controls="pills-accessories" aria-selected="false">Accessories</button>
  </li>

<li class="nav-item d-none" id="final-tab-item">
    <button class="nav-link"
            id="disabled-tab"
            data-bs-toggle="pill"
            data-bs-target="#disabled-tab-pane"
            type="button">

    </button>
</li>

</ul>
<div class="tab-content" id="pills-tabContent">

  <!-- ==================== MODEL TAB (from DB) ==================== -->
  <div class="tab-pane fade show active" id="pills-model" role="tabpanel" aria-labelledby="pills-model-tab">

<div class="row row-cols-1 row-cols-md-4 g-3">
  <?php foreach ($phoneData as $model_name => $data): ?>
  <div class="col">
    <div class="card fon-card" data-model="<?php echo htmlspecialchars($model_name); ?>">
      <div><img src="assets/images/<?php echo htmlspecialchars($data['image'] ?? ''); ?>" class="card-img-top" alt="..."></div>
      <div class="card-body">
        <h5 class="card-title"><?php echo htmlspecialchars($model_name); ?></h5>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div><!--cards-->

<div class="text-center mt-4">
     <button type="button" class="btn-proceed" onclick="backToBrands()">
        <i class="fa-solid fa-arrow-left"></i> Back to brands
    </button></div>
  </div>

  <!-- ==================== STORAGE TAB (filled through js after model is selected) ==================== -->
  <div class="tab-pane fade" id="pills-storage" role="tabpanel" aria-labelledby="pills-storage-tab">

  <div class="row row-cols-1 row-cols-md-4 row1 mt-4" id="storage-cards-container">
    <!--JS storage card appear here -->
  </div>

<div class="text-center mt-4 display-flex g-3">
     
    <button type="button" class="btn-proceed" onclick="goToTab('pills-model-tab')">
        <i class="fa-solid fa-arrow-left"></i> Back to Model
    </button>

  </div>
</div>

  <!-- ==================== CONDITION TAB (labels same,add data-condition) ==================== -->
   <div class="tab-pane fade" id="pills-condition" role="tabpanel" aria-labelledby="pills-condition-tab">

   <div class="row row-cols-1 row-cols-md-4 row1 mt-4">
  <div class="col">
    <div class="card con-card" data-condition="Flawless">
      <div class="card-body">
        <h5 class="card-title fs-4 fw-bold text-center mb-2 mt-2">Flawless</h5>
        <p class="title">Looks brand new,no scratches or dents</p>
      </div>
    </div>
  </div>
 <div class="col">
    <div class="card con-card" data-condition="Good">
      <div class="card-body">
        <h5 class="card-title fs-4 fw-bold text-center mb-2 mt-2">Good</h5>
        <p class="title">Light signs of wear,fully working</p>
      </div>
    </div>
  </div>
  <div class="col">
    <div class="card con-card" data-condition="Fair">
      <div class="card-body">
        <h5 class="card-title fs-4 fw-bold text-center mb-2 mt-2">Fair/Cracked</h5>
        <p class="title">Heavy wear,cracked screen or back,but functional</p>
      </div>
    </div>
  </div>
</div>
  <div class="text-center mt-4 display-flex flex-direction-row g-3">
     
    <button type="button" class="btn-proceed" onclick="goToTab('pills-storage-tab')">
        <i class="fa-solid fa-arrow-left"></i> Back to Storage
    </button>
</div>

</div>

  <!-- ==================== ACCESSORIES TAB (prices are filled according to model through JS) ==================== -->
   <div class="tab-pane fade" id="pills-accessories" role="tabpanel" aria-labelledby="pills-accessories-tab">

   <div class="row row-cols-1 row-cols-md-4 row1 mt-4 g-2">

 <div class="col col-md-3">
<div class="custom-control custom-checkbox image-checkbox">
<input type="checkbox" class="custom-control-input accessory-check" id="ck1" data-key="charger">
<label class="custom-control-label" for="ck1">
<h2 class="h2con">Charger</h2>
<p class="title">+ AED <span id="price-charger">0</span></p>
</label>
</div>
</div>

<div class="col col-md-3">
<div class="custom-control custom-checkbox image-checkbox">
<input type="checkbox" class="custom-control-input accessory-check" id="ck2" data-key="earphones">
<label class="custom-control-label" for="ck2">
<h2 class="h2con">EarPhones</h2>
<p>+ AED <span id="price-earphones">0</span></p>
</label>
</div>
</div>
<div class="col col-md-3">
<div class="custom-control custom-checkbox image-checkbox">
<input type="checkbox" class="custom-control-input accessory-check" id="ck3" data-key="box">
<label class="custom-control-label" for="ck3">
<h2 class="h2con">Box</h2>
<p>+ AED <span id="price-box">0</span></p>
</label>
</div>
</div>
<div class="col col-md-3">
<div class="custom-control custom-checkbox image-checkbox">
<input type="checkbox" class="custom-control-input accessory-check" id="ck4" data-key="bill">
<label class="custom-control-label" for="ck4">
<h2 class="h2con">Valid Bill</h2>
<p>+ AED <span id="price-bill">0</span></p>
</label>
</div>
</div>

</div>
<div class="text-center mt-4">
    <button type="button" class="btn-proceed" onclick="goToTab('pills-condition-tab')">
        <i class="fa-solid fa-arrow-left"></i> Back to Condition
    </button>
    <button type="button" class="btn-proceed" id="finalPriceBtn" onclick="goToTab('disabled-tab')">
        Get Final Price <i class="fa-solid fa-arrow-right"></i>
    </button>
</div>
   </div>

  <!-- ==================== FINAL PRICE (pickup form now lives in a modal, see #pickupModal below) ==================== -->
<div class="tab-pane fade" id="disabled-tab-pane" role="tabpanel" aria-labelledby="disabled-tab" tabindex="0">

<div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="est-value text-center">

  <div id="estimated-value-content">
  <h5 class="est-head text-center mt-5">Your Estimated Value </h5>
  <h5 class="est-head text-center fw-bold fs-2 mt-3">Upto </h5>
  <h1 class="est-price fw-bold mt-4" id="final-price">AED 0</h1>
  <p class="text-muted fw-bold mt-4" id="final-summary">Based on: -</p>
<p class="text-center text-danger mt-3 mb-3" id="sell-now-note">
   Note: Actual payout is confirmed only after our expert evaluates your device.
</p>
    <div class="text-center mt-5 display-flex flex-direction-row g-3">
     <button type="button" class="btn-proceed" onclick="window.open('https://wa.me/971502166562', '_blank')">
      
        <i class="fa-brands fa-whatsapp fs-3"></i>Sell on Whatsapp
    </button>
    <button type="button" class="btn-proceed" id="bookPickupBtn" onclick="showPickupForm()">
        <i class="fa-solid fa-file-lines fs-2"></i>
        Book Free Pickup
    </button>
</div>

      <div class="text-center mt-4">
     <button type="button" class="btn-pro" onclick="backToBrands()">
        <i class="fa-solid fa-arrow-left"></i> Back to brands
    </button></div>
  </div><!--estimated-value-content-->

            </div>

    </div>

    </div>

</div>
</div><!--tab content-->

<!-- ==================== PICKUP DETAILS MODAL (opens on "Book Free Pickup") ==================== -->
<div class="modal fade pickup-modal" id="pickupModal" tabindex="-1" aria-labelledby="pickupModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <button type="button" class="btn-close pickup-modal-close" onclick="hidePickupForm()" aria-label="Close"></button>

      <div class="modal-body">

        <!-- form and success msg both are swapped under this div-->
        <div id="pickup-form-inner" class="pickup-form-container">
          <h5 class="fw-bold text-center mb-4 pickup-form-title">Your Details</h5>

          <form id="pickupForm" enctype="multipart/form-data">

            <input type="hidden" name="phone_model" id="pf-model">
            <input type="hidden" name="storage" id="pf-storage">
            <input type="hidden" name="phone_condition" id="pf-condition">
            <input type="hidden" name="offered_price" id="pf-price">
            <input type="hidden" name="accessories_selected" id="pf-accessories">

            <div class="mb-3">
              <label class="fw-bold mb-2">Full Name</label>
              <input type="text" name="customer_name" class="form-control" required>
            </div>

            <div class="mb-3">
              <label class="fw-bold mb-2">Email Address</label>
              <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
              <label class="fw-bold mb-2">Phone Number</label>
              <input type="text" name="phone_no" class="form-control" required>
            </div>

            <div class="mb-3">
              <label class="fw-bold mb-2">Location / Area</label>
              <input type="text" name="location" class="form-control" placeholder="e.g. Downtown Dubai" required>
            </div>

            <div class="mb-3">
              <label class="fw-bold mb-2">Full Address (Optional)</label>
              <textarea name="address" class="form-control"></textarea>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="fw-bold mb-2">Pickup Date</label>
                <input type="date" name="pickup_date" class="form-control" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="fw-bold mb-2">Time Slot</label>
                <select name="time_slot" class="form-control" required>
                  <option value="">Select Time</option>
                  <option value="9AM - 12PM">9AM - 12PM</option>
                  <option value="12PM - 3PM">12PM - 3PM</option>
                  <option value="3PM - 6PM">3PM - 6PM</option>
                  <option value="6PM - 9PM">6PM - 9PM</option>
                </select>
              </div>
            </div>

            <div class="mb-3">
              <label class="fw-bold mb-2">Upload Device Images (Required, Max 4)</label>

              <div id="image-drop-zone" class="image-drop-zone">
                <i class="fa-solid fa-cloud-arrow-up fs-1 drop-zone-icon"></i>
                <p class="mb-0 mt-2">Drag &amp; Drop images here or <span class="drop-zone-link">Click to select</span></p>
                <p class="text-muted mb-0" style="font-size:13px;">You can add up to 4 images</p>
              </div>
              <input type="file" id="pf-image-input" accept=".jpg,.jpeg,.png,.webp" multiple style="display:none;">

              <div id="image-preview-row" class="image-preview-row mt-2"></div>
            </div>

            <div id="pickup-msg"></div>

            <button type="submit" class="btn-proceed">Submit Request</button>

            <div class="text-center mt-3">
              <button type="button" class="btn-muted-back" onclick="hidePickupForm()">
                &larr; Back to Estimated Value
              </button>
            </div>
          </form>
        </div><!--pickup-form-inner-->

        <!-- ==================== SUCCESS MESSAGE (appear here after submission) ==================== -->
        <div id="pickup-success" class="pickup-success" style="display:none;">
          <div class="icon-circle"><i class="fa-solid fa-check"></i></div>
          <h4>Success!</h4>
          <p>Your information has been sent.</p>
          <p class="text-muted">Our team will contact you shortly to confirm the pickup time.</p>
          <button type="button" class="btn-proceed mt-3" onclick="hidePickupForm(); backToBrands();">Done</button>
        </div>

      </div><!--modal-body-->
    </div><!--modal-content-->
  </div><!--modal-dialog-->
</div><!--pickupModal-->

</div><!--section-->

<script>
// all models/storage/prices and accesories prices of this model....brand.js used this data
// assign on windows(not const),  so that error"already declared"may not occur on brand switch
window.phoneData = <?php echo json_encode($phoneData); ?>;
window.accessoriesData = <?php echo json_encode($accessoriesData); ?>;

/* ==================== Pickup form now opens as a modal ====================
   Overrides/defines showPickupForm() and hidePickupForm() so the existing
   "Book Free Pickup" / "Back to Estimated Value" / "Done" buttons (which already
   call these two functions) now open and close the Bootstrap modal instead of
   toggling display:block/none on an inline section. Any other JS in the project
   (e.g. the AJAX submit handler) that swaps #pickup-form-inner and #pickup-success
   by id keeps working unchanged, since the ids didn't move, only their container did. */
(function () {
    // IMPORTANT: the modal markup is injected inside .brand-section, which has
    // the "reveal-on-scroll" class (uses CSS transform for the scroll-in
    // animation). Any ancestor with a transform breaks Bootstrap's
    // position:fixed for modals/backdrops - the modal ends up scrolling with
    // the page instead of staying fixed, and the (truly fixed) backdrop then
    // sits on top of it, blocking clicks on the form fields. Moving the modal
    // to be a direct child of <body> fixes this.
    var pickupModalEl = document.getElementById('pickupModal');
    if (pickupModalEl) {
        document.body.querySelectorAll('#pickupModal').forEach(function (el) {
            if (el !== pickupModalEl) el.remove(); // drop any leftover copy from a previous brand load
        });
        if (pickupModalEl.parentElement !== document.body) {
            document.body.appendChild(pickupModalEl);
        }
    }

    window.showPickupForm = function () {
        var modalEl = document.getElementById('pickupModal');
        if (!modalEl) return;

        // Always open fresh on the form view, not on a leftover success screen
        var formInner = document.getElementById('pickup-form-inner');
        var success   = document.getElementById('pickup-success');
        if (formInner) formInner.style.display = 'block';
        if (success)   success.style.display = 'none';

        var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    };

    window.hidePickupForm = function () {
        var modalEl = document.getElementById('pickupModal');
        if (!modalEl) return;
        var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.hide();
    };
})();
</script>