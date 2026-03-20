const apiUrl = "/api/penerima-manfaat";

let dataTable;
let deleteModal;
let recordIdToDelete = null;

function showAlert(message, type = "success") {
  $("#alertContainer").html(`
    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
      ${message}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
  `);
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
        reject(error);
      }
    });
  });
}

async function loadStats() {
  try {
    const stats = await requestJson("/api/statistik");
    $("#statTotal").text(stats.total);
    $("#statLaki").text(stats.lakiLaki);
    $("#statPerempuan").text(stats.perempuan);
    $("#navTotal").text(stats.total);
  } catch (_) { }
}

function reloadTable() {
  dataTable.ajax.reload(null, false);
  loadStats();
}

function openDeleteModal(id) {
  recordIdToDelete = id;
  deleteModal.show();
}

async function deleteRecord() {
  if (!recordIdToDelete) return;
  try {
    const result = await requestJson(`${apiUrl}/${recordIdToDelete}`, { method: "DELETE" });
    deleteModal.hide();
    recordIdToDelete = null;
    showAlert(result.message, "success");
    reloadTable();
  } catch (error) {
    deleteModal.hide();
    showAlert(error.message, "danger");
  }
}

function initDataTable() {
  dataTable = $("#penerimaTable").DataTable({
    serverSide: true,
    processing: true,
    ajax: {
      url: apiUrl + "/datatable",
      type: "POST",
      dataSrc: "data"
    },
    columns: [
      {
        data: null,
        render: (_data, _type, _row, meta) => meta.settings._iDisplayStart + meta.row + 1,
        className: "text-center",
        width: "50px",
      },
      { data: "namaLengkap", render: (data) => `<strong>${data}</strong>` },
      { data: "alamat" },
      {
        data: "jenisKelamin",
        render: (data) => {
          const cls = data === "Laki-laki" ? "text-bg-success" : "text-bg-danger";
          return `<span class="badge ${cls}">${data}</span>`;
        },
      },
      { data: "alergi" },
      { data: "sekolah" },
      {
        data: null,
        orderable: false,
        searchable: false,
        className: "text-center",
        render: (data) => `
          <div class="btn-group btn-group-sm">
            <a class="btn btn-warning" href="/edit/${data.id}">
              <i class="bi bi-pencil"></i> Edit
            </a>
            <button class="btn btn-outline-danger btn-delete" data-id="${data.id}">
              <i class="bi bi-trash3"></i> Hapus
            </button>
          </div>
        `,
      },
    ],
    language: {
      search: "Cari:",
      lengthMenu: "Tampilkan _MENU_ data",
      info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
      infoEmpty: "Belum ada data",
      zeroRecords: "Data tidak ditemukan",
      paginate: { first: "Awal", last: "Akhir", next: "Berikutnya", previous: "Sebelumnya" },
    },
  });
}

$(() => {
  deleteModal = new bootstrap.Modal($("#deleteModal")[0]);
  initDataTable();
  loadStats();

  $("#confirmDeleteButton").on("click", deleteRecord);

  $("#penerimaTable").on("click", ".btn-delete", function () {
  
    openDeleteModal($(this).data("id"));
  });

  $("#deleteModal").on("hidden.bs.modal", () => {
    recordIdToDelete = null;
  });
});
