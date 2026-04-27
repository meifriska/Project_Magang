CREATE DATABASE db_permohonan_rekomendasi;
USE db_permohonan_rekomendasi;

-- =====================
-- TABEL INSTANSI
-- =====================
CREATE TABLE instansi (
    id_instansi INT AUTO_INCREMENT PRIMARY KEY,
    nama_instansi VARCHAR(45),
    jenis_instansi VARCHAR(45),
    alamat_instansi VARCHAR(45)
);

-- =====================
-- TABEL USER
-- =====================
CREATE TABLE USER (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    nama_user VARCHAR(45),
    email_user VARCHAR(45),
    password_user VARCHAR(45),
    id_instansi INT,
    FOREIGN KEY (id_instansi) REFERENCES instansi(id_instansi)
);

-- =====================
-- TABEL PENYELENGGARA
-- =====================
CREATE TABLE penyelenggara (
    id_penyelenggara INT AUTO_INCREMENT PRIMARY KEY,
    nama_penyelenggara VARCHAR(45),
    email_penyelenggara VARCHAR(45),
    password_penyelenggara VARCHAR(45)
);

-- =====================
-- TABEL JENIS KEGIATAN
-- =====================
CREATE TABLE jenis_kegiatan (
    id_jenis_kegiatan INT AUTO_INCREMENT PRIMARY KEY,
    nama_kegiatan VARCHAR(45)
);

-- =====================
-- TABEL PERMOHONAN
-- =====================
CREATE TABLE permohonan (
    id_permohonan INT AUTO_INCREMENT PRIMARY KEY,
    judul_tema VARCHAR(45),
    jumlah_peserta INT,
    tanggal_pelaksanaan DATETIME,
    tempat_pelaksanaan VARCHAR(45),
    tanggal_permohonan DATETIME,
    status_permohonan VARCHAR(45),
    
    id_user INT,
    id_penyelenggara INT,
    id_jenis_kegiatan INT,

    FOREIGN KEY (id_user) REFERENCES USER(id_user),
    FOREIGN KEY (id_penyelenggara) REFERENCES penyelenggara(id_penyelenggara),
    FOREIGN KEY (id_jenis_kegiatan) REFERENCES jenis_kegiatan(id_jenis_kegiatan)
);