const express = require("express");
const path = require("path");
const crypto = require("crypto");
const Database = require("better-sqlite3");
const fs = require("fs");

const app = express();
const PORT = process.env.PORT || 3000;
const HOST = process.env.HOST || "127.0.0.1";
const DB_PATH = path.join(__dirname, "data", "mbg.db");

app.use(express.json());
app.use(express.urlencoded({ extended: true }));
app.use(express.static(path.join(__dirname, "public")));

if (!fs.existsSync(path.join(__dirname, "data"))) {
  fs.mkdirSync(path.join(__dirname, "data"), { recursive: true });
}

const dbExists = fs.existsSync(DB_PATH);
const db = new Database(DB_PATH);
db.pragma("journal_mode = WAL");
db.pragma("foreign_keys = ON");

if (!dbExists) {
  db.exec(`
    CREATE TABLE IF NOT EXISTS penerima_manfaat (
      id           TEXT PRIMARY KEY,
      namaLengkap  TEXT NOT NULL,
      alamat       TEXT NOT NULL,
      jenisKelamin TEXT NOT NULL CHECK(jenisKelamin IN ('Laki-laki', 'Perempuan')),
      alergi       TEXT NOT NULL,
      sekolah      TEXT NOT NULL,
      createdAt    TEXT NOT NULL DEFAULT (datetime('now')),
      updatedAt    TEXT NOT NULL DEFAULT (datetime('now'))
    )
  `);
}

const stmts = {
  selectAll: db.prepare("SELECT * FROM penerima_manfaat ORDER BY createdAt DESC"),
  selectById: db.prepare("SELECT * FROM penerima_manfaat WHERE id = ?"),
  insert: db.prepare(`
    INSERT INTO penerima_manfaat (id, namaLengkap, alamat, jenisKelamin, alergi, sekolah, createdAt, updatedAt)
    VALUES (@id, @namaLengkap, @alamat, @jenisKelamin, @alergi, @sekolah, @createdAt, @updatedAt)
  `),
  update: db.prepare(`
    UPDATE penerima_manfaat
    SET namaLengkap  = @namaLengkap,
        alamat       = @alamat,
        jenisKelamin = @jenisKelamin,
        alergi       = @alergi,
        sekolah      = @sekolah,
        updatedAt    = @updatedAt
    WHERE id = @id
  `),
  delete: db.prepare("DELETE FROM penerima_manfaat WHERE id = ?"),
  count: db.prepare("SELECT COUNT(*) AS total FROM penerima_manfaat"),
  countLaki: db.prepare("SELECT COUNT(*) AS total FROM penerima_manfaat WHERE jenisKelamin = 'Laki-laki'"),
  countPerempuan: db.prepare("SELECT COUNT(*) AS total FROM penerima_manfaat WHERE jenisKelamin = 'Perempuan'"),
};

function validatePayload(body) {
  const payload = {
    namaLengkap: String(body.namaLengkap || "").trim(),
    alamat: String(body.alamat || "").trim(),
    jenisKelamin: String(body.jenisKelamin || "").trim(),
    alergi: String(body.alergi || "").trim(),
    sekolah: String(body.sekolah || "").trim(),
  };

  const errors = {};

  if (!payload.namaLengkap) errors.namaLengkap = "Nama lengkap wajib diisi.";
  if (!payload.alamat) errors.alamat = "Alamat wajib diisi.";
  if (!["Laki-laki", "Perempuan"].includes(payload.jenisKelamin)) {
    errors.jenisKelamin = "Jenis kelamin harus dipilih.";
  }
  if (!payload.alergi) errors.alergi = "Alergi wajib diisi. Isi '-' jika tidak ada.";
  if (!payload.sekolah) errors.sekolah = "Sekolah wajib diisi.";

  return { payload, errors, isValid: Object.keys(errors).length === 0 };
}

app.get("/api/penerima-manfaat", (_req, res) => {
  try {
    const records = stmts.selectAll.all();
    res.json({ data: records });
  } catch (error) {
    res.status(500).json({ message: "Gagal membaca data.", error: error.message });
  }
});

app.post("/api/penerima-manfaat/datatable", (req, res) => {
  try {
    const payload = req.body;
    const isDataTable = payload.draw !== undefined;

    if (isDataTable) {
      const draw = parseInt(payload.draw) || 1;
      const start = parseInt(payload.start) || 0;
      const length = parseInt(payload.length) || 10;

      const searchData = payload.search || {};
      const searchValue = searchData.value !== undefined ? searchData.value : payload["search[value]"] || '';

      let orderColumnIndex = 0;
      let orderDir = 'DESC';
      if (payload.order && payload.order[0]) {
        orderColumnIndex = payload.order[0].column;
        orderDir = payload.order[0].dir === 'asc' ? 'ASC' : 'DESC';
      }

      let orderBy = 'createdAt';
      if (payload.columns && payload.columns[orderColumnIndex]) {
        const colData = payload.columns[orderColumnIndex].data;
        const validColumns = ['namaLengkap', 'alamat', 'jenisKelamin', 'alergi', 'sekolah'];
        if (validColumns.includes(colData)) {
          orderBy = colData;
        }
      }

      let queryStr = "SELECT * FROM penerima_manfaat";
      let countStr = "SELECT COUNT(*) AS total FROM penerima_manfaat";
      let filterParams = [];

      if (searchValue) {
        const searchClause = " WHERE namaLengkap LIKE ? OR alamat LIKE ? OR jenisKelamin LIKE ? OR alergi LIKE ? OR sekolah LIKE ?";
        queryStr += searchClause;
        countStr += searchClause;
        const paramStr = `%${searchValue}%`;
        filterParams = [paramStr, paramStr, paramStr, paramStr, paramStr];
      }

      queryStr += ` ORDER BY ${orderBy} ${orderDir} LIMIT ? OFFSET ?`;

      const recordsFiltered = db.prepare(countStr).get(...filterParams).total;
      const recordsTotal = stmts.count.get().total;

      const records = db.prepare(queryStr).all(...filterParams, length, start);

      res.json({ draw, recordsTotal, recordsFiltered, data: records });
    } else {
      res.status(400).json({ message: "Format payload tidak sesuai." });
    }
  } catch (error) {
    res.status(500).json({ message: "Gagal membaca data server-side.", error: error.message });
  }
});

app.get("/api/penerima-manfaat/:id", (req, res) => {
  try {
    const record = stmts.selectById.get(req.params.id);
    if (!record) return res.status(404).json({ message: "Data tidak ditemukan." });
    res.json(record);
  } catch (error) {
    res.status(500).json({ message: "Gagal membaca detail data.", error: error.message });
  }
});

app.post("/api/penerima-manfaat", (req, res) => {
  const { payload, errors, isValid } = validatePayload(req.body);
  if (!isValid) return res.status(422).json({ message: "Validasi gagal.", errors });

  try {
    const now = new Date().toISOString();
    const newRecord = { id: crypto.randomUUID(), ...payload, createdAt: now, updatedAt: now };
    stmts.insert.run(newRecord);
    res.status(201).json({ message: "Data berhasil ditambahkan.", data: newRecord });
  } catch (error) {
    res.status(500).json({ message: "Gagal menambah data.", error: error.message });
  }
});

app.put("/api/penerima-manfaat/:id", (req, res) => {
  const { payload, errors, isValid } = validatePayload(req.body);
  if (!isValid) return res.status(422).json({ message: "Validasi gagal.", errors });

  try {
    const existing = stmts.selectById.get(req.params.id);
    if (!existing) return res.status(404).json({ message: "Data tidak ditemukan." });

    const updatedRecord = { id: req.params.id, ...payload, updatedAt: new Date().toISOString() };
    stmts.update.run(updatedRecord);
    res.json({ message: "Data berhasil diperbarui.", data: { ...existing, ...updatedRecord } });
  } catch (error) {
    res.status(500).json({ message: "Gagal memperbarui data.", error: error.message });
  }
});

app.delete("/api/penerima-manfaat/:id", (req, res) => {
  try {
    const result = stmts.delete.run(req.params.id);
    if (result.changes === 0) return res.status(404).json({ message: "Data tidak ditemukan." });
    res.json({ message: "Data berhasil dihapus." });
  } catch (error) {
    res.status(500).json({ message: "Gagal menghapus data.", error: error.message });
  }
});

app.get("/api/statistik", (_req, res) => {
  try {
    const total = stmts.count.get().total;
    const lakiLaki = stmts.countLaki.get().total;
    const perempuan = stmts.countPerempuan.get().total;
    res.json({ total, lakiLaki, perempuan });
  } catch (error) {
    res.status(500).json({ message: "Gagal mengambil statistik.", error: error.message });
  }
});

app.get("/api/alergi", (req, res) => {
  try {
    const q = String(req.query.q || "").trim();
    let query = "SELECT DISTINCT alergi FROM penerima_manfaat WHERE alergi != ''";
    const params = [];

    if (q) {
      query += " AND alergi LIKE ?";
      params.push(`%${q}%`);
    }

    query += " ORDER BY alergi ASC";

    const rows = db.prepare(query).all(...params);
    const data = rows.map((row) => ({ id: row.alergi, text: row.alergi }));
    res.json({ data });
  } catch (error) {
    res.status(500).json({ message: "Gagal mengambil data alergi.", error: error.message });
  }
});



app.get("/", (_req, res) => {
  res.sendFile(path.join(__dirname, "public", "index.html"));
});

app.get("/tambah", (_req, res) => {
  res.sendFile(path.join(__dirname, "public", "form.html"));
});

app.get("/edit/:id", (_req, res) => {
  res.sendFile(path.join(__dirname, "public", "form.html"));
});




app.use((req, res) => {
  if (req.path.startsWith("/api/")) {
    return res.status(404).json({ message: "Endpoint tidak ditemukan." });
  }
  res.status(404).send("Halaman tidak ditemukan.");
});

process.on("SIGINT", () => { db.close(); process.exit(0); });
process.on("SIGTERM", () => { db.close(); process.exit(0); });

app.listen(PORT, HOST, () => {
  console.log(`Server MBG berjalan di http://${HOST}:${PORT}`);
});
