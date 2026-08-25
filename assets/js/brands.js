function showbrand(brand) {

    // brand-header (tag + heading + paragraph) ab hide nahi hoga, sirf brand cards hidden hongi
    $('#brand-buttons').hide();

    $('#brand-content').load(brand + '-content.php');

    setActiveStep(2); 
}
function backToBrands() {
    $('#brand-content').html('');
    $('#brand-header').show();
    $('#brand-buttons').show();

    setActiveStep(1);
}


function goToTab(tabId) {
    var x = document.getElementById(tabId);
    if (x) {
        x.click();
    }
}


// ==================== Progress stepper (1 Brand,2 Model,3 Storage,4 Condition,5 Accessories) ====================

var tabStepMap = {
    'pills-model-tab':       2,
    'pills-storage-tab':     3,
    'pills-condition-tab':   4,
    'pills-accessories-tab': 5,
    'disabled-tab':          5
};

function setActiveStep(stepNumber) {
    $('#price-stepper .step-item').each(function () {
        var step = parseInt($(this).data('step'), 10);
        $(this).removeClass('active done');

        if (step === stepNumber) {
            $(this).addClass('active');
        } else if (step < stepNumber) {
            $(this).addClass('done');
        }
    });
}


$(document).on('shown.bs.tab', '[data-bs-toggle="pill"]', function (e) {
    var stepNum = tabStepMap[e.target.id];
    if (stepNum) {
        setActiveStep(stepNum);
    }
});


// ==================== Selection tracking ====================
// when mode/storage/condition/accessories are selected..these are stored here
let selectedModel = null;
let selectedStorage = null;
let selectedCondition = null;
let selectedAccessories = [];


// ---------- Model card click ----------
$(document).on('click', '#pills-model .fon-card', function () {
    selectedModel = $(this).data('model');

    // .render the storage cards and accessories prices of this model
    renderStorageCards();
    renderAccessoryPrices();

    goToTab('pills-storage-tab');
});


// ---------- make Storage cards dynamically (only those which are available for this model) ----------
function renderStorageCards() {
    const container = $('#storage-cards-container');
    container.empty();

    if (!selectedModel || typeof phoneData === 'undefined' || !phoneData[selectedModel]) return;

    const variants = phoneData[selectedModel].variants;

    Object.keys(variants).forEach(function (storage) {
        const card = $(
            '<div class="col"><div class="card st-card" data-storage="' + storage + '">' +
            '<div class="card-body"><h5 class="card-title fs-4 text-center mt-2">' + storage + '</h5></div>' +
            '</div></div>'
        );
        container.append(card);
    });
}


// ---------- Storage card click ----------
$(document).on('click', '#storage-cards-container .st-card', function () {
    selectedStorage = $(this).data('storage');
    goToTab('pills-condition-tab');
});


// ---------- Condition card click ----------
$(document).on('click', '#pills-condition .con-card', function () {
    selectedCondition = $(this).data('condition');
    goToTab('pills-accessories-tab');
});


// ---------- Accessories: display prices of this model with checkboxes----------
function renderAccessoryPrices() {
    if (!selectedModel || typeof accessoriesData === 'undefined' || !accessoriesData[selectedModel]) return;

    const acc = accessoriesData[selectedModel];
    $('#price-charger').text(acc.charger);
    $('#price-earphones').text(acc.earphones);
    $('#price-box').text(acc.box);
    $('#price-bill').text(acc.bill);
}


// ---------- Accessory checkbox tick/untick ----------
$(document).on('change', '.accessory-check', function () {
    const key = $(this).data('key');

    if (this.checked) {
        if (selectedAccessories.indexOf(key) === -1) {
            selectedAccessories.push(key);
        }
    } else {
        selectedAccessories = selectedAccessories.filter(function (k) { return k !== key; });
    }
});


// ---------- "Get Final Price" button ----------
$(document).on('click', '#finalPriceBtn', function () {
    calculateFinalPrice();
});

function calculateFinalPrice() {
    if (!selectedModel || !selectedStorage || !selectedCondition) {
        alert('First select model,storage and condition.');
        return;
    }

    const base = phoneData[selectedModel].variants[selectedStorage][selectedCondition] || 0;

    let accTotal = 0;
    selectedAccessories.forEach(function (key) {
        accTotal += accessoriesData[selectedModel][key] || 0;
    });

    const total = base + accTotal;

    $('#final-price').text('AED ' + total);

    let summary = 'Based on: ' + selectedModel + ' ' + selectedStorage + ', ' + selectedCondition + ' condition';
    if (selectedAccessories.length > 0) {
        summary += ' + ' + selectedAccessories.join(', ');
    }
    $('#final-summary').text(summary);

    //fill the hideen feilds of pickup form(useful for submission)
    $('#pf-model').val(selectedModel);
    $('#pf-storage').val(selectedStorage);
    $('#pf-condition').val(selectedCondition);
    $('#pf-price').val(total);
    $('#pf-accessories').val(selectedAccessories.join(', '));
}


// ==================== Image upload (drag & drop, max 4, accumulate) ====================
let selectedImages = [];

// ---------- file dialog opened on click ----------
$(document).on('click', '#image-drop-zone', function () {
    $('#pf-image-input')[0].click();
});

// ----------choose img from File dialog ----------
$(document).on('change', '#pf-image-input', function () {
    handleNewImages(this.files);
    this.value = ''; // reset, so that file can be selected again if it was removed
});

// ---------- Drag & Drop ----------
$(document).on('dragover', '#image-drop-zone', function (e) {
    e.preventDefault();
    $(this).addClass('drag-over');
});
$(document).on('dragleave', '#image-drop-zone', function () {
    $(this).removeClass('drag-over');
});
$(document).on('drop', '#image-drop-zone', function (e) {
    e.preventDefault();
    $(this).removeClass('drag-over');
    handleNewImages(e.originalEvent.dataTransfer.files);
});

function handleNewImages(fileList) {
    Array.from(fileList).forEach(function (file) {
        if (selectedImages.length >= 4) return; // max 4 
        selectedImages.push(file);
    });
    renderImagePreviews();
}

function renderImagePreviews() {
    const row = $('#image-preview-row');
    row.empty();

    selectedImages.forEach(function (file, index) {
        const url = URL.createObjectURL(file);
        const thumb = $(
            '<div class="image-preview-item">' +
            '<img src="' + url + '">' +
            '<button type="button" class="remove-image-btn" data-index="' + index + '">&times;</button>' +
            '</div>'
        );
        row.append(thumb);
    });
}

// ---------- Remove img from preview ----------
$(document).on('click', '.remove-image-btn', function () {
    const idx = $(this).data('index');
    selectedImages.splice(idx, 1);
    renderImagePreviews();
});

function resetImageUploader() {
    selectedImages = [];
    $('#image-preview-row').empty();
}
function showPickupForm() {
    $('#estimated-value-content').hide();
    $('#pickup-success').hide();
    $('#pickup-form-inner').show();
    $('#pickup-form-wrap').show();
    resetImageUploader();
}

// ----------go back to estimated value from bickup form ----------
function hidePickupForm() {
    $('#pickup-form-wrap').hide();
    $('#estimated-value-content').show();
}


// ---------- Pickup form submit (AJAX, page doesnot reload) ----------
$(document).on('submit', '#pickupForm', function (e) {
    e.preventDefault();

    if (selectedImages.length === 0) {
        $('#pickup-msg').html('<div class="alert alert-danger mt-2">Atleast upload 1 image.</div>');
        return;
    }

    const formData = new FormData(this);

    //put files from selectedimages array to formdata (original file input is separate)
    selectedImages.forEach(function (file) {
        formData.append('images[]', file);
    });

    const submitBtn = $(this).find('button[type=submit]');
    submitBtn.prop('disabled', true).text('Submitting...');

    $.ajax({
        url: 'submit-order',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
            let res;
            try {
                res = JSON.parse(response);
            } catch (err) {
                res = { success: false, message: 'Unexpected server response.' };
            }

            if (res.success) {
                //hide the form and show success msg 
                $('#pickup-form-inner').hide();
                $('#pickup-success').show();
                document.getElementById('pickupForm').reset();
                resetImageUploader();
            } else {
                $('#pickup-msg').html('<div class="alert alert-danger mt-2">' + res.message + '</div>');
                submitBtn.prop('disabled', false).text('Submit Request');
            }
        },
        error: function () {
            $('#pickup-msg').html('<div class="alert alert-danger mt-2">Error occur,try again.</div>');
            submitBtn.prop('disabled', false).text('Submit Request');
        }
    });
});



document.addEventListener("DOMContentLoaded", function () {
    const revealEls = document.querySelectorAll(".reveal-on-scroll");

    const observer = new IntersectionObserver(
        (entries, obs) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add("active");
                    obs.unobserve(entry.target); // animate once only
                }
            });
        },
        {
            threshold: 0.15,        // 15% of section visible triggers it
            rootMargin: "0px 0px -50px 0px"
        }
    );

    revealEls.forEach((el) => observer.observe(el));
});