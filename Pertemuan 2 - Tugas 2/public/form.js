const apiUrl = "/api/penerima-manfaat";

function showAlert(message, type = "success") {
  $("#alertContainer").html(`
    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
      ${message}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
  `);
}

function clearErrors() {
  $.each(["namaLengkap", "alamat", "jenisKelamin", "alergi", "sekolah"], (i, field) => {
    $(`#${field}`).removeClass("is-invalid");
    $(`#error-${field}`).text("");
  });
}

function setErrors(errors = {}) {
  clearErrors();
  $.each(errors, (field, message) => {
    $(`#${field}`).addClass("is-invalid");
    $(`#error-${field}`).text(message);
  });
}

function requestJson(url, options = {}) {
  return new Promise((resolve, reject) => {
    $.ajax({
      url: url,
      type: options.method || 'GET',
      contentType: "application/json",
      data: options.body,
      headers: options.headers,
      success: function (response) {
        resolve(response);
      },
      error: function (xhr) {
        let result = {};
        try {
          result = JSON.parse(xhr.responseText);
        } catch (e) { }
        const error = new Error(result.message || "Terjadi kesalahan.");
        error.status = xhr.status;
        error.errors = result.errors || {};
        reject(error);
      }
    });
  });
}

function getEditId() {
  const segments = window.location.pathname.split("/").filter(Boolean);
  if (segments[0] === "edit" && segments[1]) return segments[1];
  return null;
}

async function loadEditData(id) {
  const record = await requestJson(`${apiUrl}/${id}`);
  $("#namaLengkap").val(record.namaLengkap);
  $("#alamat").val(record.alamat);
  $("#jenisKelamin").val(record.jenisKelamin).trigger('change');


  if (record.alergi) {
    const alergiOption = new Option(record.alergi, record.alergi, true, true);
    $("#alergi").append(alergiOption).trigger('change');
  }

  $("#sekolah").val(record.sekolah);
}

async function handleSubmit(event) {
  event.preventDefault();
  clearErrors();

  const editId = getEditId();
  const payload = {
    namaLengkap: $("#namaLengkap").val(),
    alamat: $("#alamat").val(),
    jenisKelamin: $("#jenisKelamin").val(),
    alergi: $("#alergi").val(),
    sekolah: $("#sekolah").val(),
  };

  try {
    const result = await requestJson(editId ? `${apiUrl}/${editId}` : apiUrl, {
      method: editId ? "PUT" : "POST",
      body: JSON.stringify(payload),
    });
    showAlert(result.message, "success");
    setTimeout(() => { window.location.href = "/"; }, 800);
  } catch (error) {
    if (error.status === 422) {
      setErrors(error.errors);
      return;
    }
    showAlert(error.message, "danger");
  }
}

$(async () => {
  $("#jenisKelamin").select2({
    theme: "bootstrap-5",
    width: '100%',
    placeholder: "Pilih jenis kelamin",
  });

  $("#alergi").select2({
    theme: "bootstrap-5",
    width: '100%',
    placeholder: "Pilih atau ketik alergi",
    tags: true,
    ajax: {
      url: "/api/alergi",
      dataType: "json",
      delay: 250,
      data: function (params) {
        return { q: params.term };
      },
      processResults: function (response) {
        return { results: response.data };
      },
      cache: true,
    },
  });

  const editId = getEditId();

  if (editId) {
    $("#pageTitle").text("Edit Data Penerima Manfaat");
    $("#pageDescription").text("Perbarui data penerima manfaat program Makan Bergizi Gratis pada form berikut.");
    $("#submitButton").html('<i class="bi bi-arrow-repeat"></i> Update Data');

    try {
      await loadEditData(editId);
    } catch (error) {
      showAlert(error.message, "danger");
    }
  }

  $("#penerimaForm").on("submit", handleSubmit);
});
