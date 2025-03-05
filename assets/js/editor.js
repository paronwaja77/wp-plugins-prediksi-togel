jQuery(document).ready(function ($) {
  let currentPrediction = ""; // Variabel untuk menyimpan prediksi terbaru

  // Handle Generate Prediction Button
  $("#ptw-generate-button").on("click", function () {
    // Ambil pasaran yang dipilih
    const selectedPasaran = $("#ptw-pasaran-select").val();
    if (!selectedPasaran) {
      alert("Silakan pilih pasaran.");
      return;
    }

    // Ambil tanggal yang dipilih
    const selectedDate = $("#ptw-date-input").val();
    if (!selectedDate) {
      alert("Silakan pilih tanggal.");
      return;
    }

    // Kosongkan kontainer prediksi sebelum generate baru
    $("#ptw-prediction-result").empty();

    // Kirim request ke API
    $.ajax({
      url: wpApiSettings.apiUrl,
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-WP-Nonce": wpApiSettings.nonce,
      },
      data: JSON.stringify({ pasaran: selectedPasaran, date: selectedDate }),
      success: function (response) {
        if (response.status === "success") {
          // Tambahkan prediksi ke kontainer
          $("#ptw-prediction-result").html(response.prediction);
          currentPrediction = response.prediction; // Simpan prediksi terbaru

          // Tampilkan tombol-tombol aksi
          $("#ptw-prediction-actions").addClass("show");
        } else {
          alert("Gagal menghasilkan prediksi: " + response.message);
        }
      },
      error: function () {
        alert("Terjadi kesalahan saat menghubungi server.");
      },
    });
  });

  // Handle Copy Button (Teks Biasa)
  $(document).on("click", ".ptw-copy-button", function () {
    // Ekstrak teks biasa dari tabel HTML
    const table = $("#ptw-prediction-result").find("table");
    let predictionText = "";

    // Loop melalui setiap baris tabel
    table.find("tr").each(function () {
      const th = $(this).find("th").text(); // Ambil teks dari header
      const td = $(this).find("td").text(); // Ambil teks dari sel
      predictionText += `${th}: ${td}\n`; // Gabungkan header dan sel
    });

    // Salin teks biasa ke clipboard
    navigator.clipboard.writeText(predictionText).then(() => {
      alert("Prediksi berhasil disalin ke clipboard!");
    });
  });

  // Handle Insert to Editor Button (Teks Biasa)
  $(document).on("click", ".ptw-insert-text-button", function () {
    if (!currentPrediction) {
      alert("Tidak ada prediksi yang tersedia. Silakan generate prediksi terlebih dahulu.");
      return;
    }

    // Ekstrak teks biasa dari tabel HTML
    const table = $("#ptw-prediction-result").find("table");
    let predictionText = "";

    // Loop melalui setiap baris tabel
    table.find("tr").each(function () {
      const th = $(this).find("th").text(); // Ambil teks dari header
      const td = $(this).find("td").text(); // Ambil teks dari sel
      predictionText += `${th}: ${td}\n`; // Gabungkan header dan sel
    });

    // Cek apakah editor Gutenberg aktif
    if (typeof wp !== "undefined" && wp.data && wp.data.dispatch) {
      // Sisipkan prediksi sebagai teks biasa ke editor Gutenberg
      wp.data.dispatch("core/editor").insertBlocks(wp.blocks.createBlock("core/paragraph", { content: predictionText }));
      alert("Prediksi berhasil disisipkan ke editor Gutenberg (Teks Biasa)!");
    }
    // Cek apakah editor TinyMCE aktif
    else if (typeof tinymce !== "undefined" && tinymce.activeEditor && !tinymce.activeEditor.isHidden()) {
      tinymce.activeEditor.insertContent(predictionText);
      alert("Prediksi berhasil disisipkan ke editor TinyMCE (Teks Biasa)!");
    }
    // Jika editor teks biasa (Text mode), gunakan QTags
    else if (typeof QTags !== "undefined") {
      QTags.insertContent(predictionText);
      alert("Prediksi berhasil disisipkan ke editor teks (Teks Biasa)!");
    } else {
      alert("Editor tidak ditemukan!");
    }
  });

  // Handle Insert to Editor Button (Tabel HTML)
  $(document).on("click", ".ptw-insert-html-button", function () {
    if (!currentPrediction) {
      alert("Tidak ada prediksi yang tersedia. Silakan generate prediksi terlebih dahulu.");
      return;
    }

    // Cek apakah editor Gutenberg aktif
    if (typeof wp !== "undefined" && wp.data && wp.data.dispatch) {
      // Sisipkan prediksi sebagai blok HTML ke editor Gutenberg
      wp.data.dispatch("core/editor").insertBlocks(wp.blocks.createBlock("core/html", { content: currentPrediction }));
      alert("Prediksi berhasil disisipkan ke editor Gutenberg (Tabel HTML)!");
    }
    // Cek apakah editor TinyMCE aktif
    else if (typeof tinymce !== "undefined" && tinymce.activeEditor && !tinymce.activeEditor.isHidden()) {
      tinymce.activeEditor.insertContent(currentPrediction);
      alert("Prediksi berhasil disisipkan ke editor TinyMCE (Tabel HTML)!");
    }
    // Jika editor teks biasa (Text mode), gunakan QTags
    else if (typeof QTags !== "undefined") {
      QTags.insertContent(currentPrediction);
      alert("Prediksi berhasil disisipkan ke editor teks (Tabel HTML)!");
    } else {
      alert("Editor tidak ditemukan!");
    }
  });
});
