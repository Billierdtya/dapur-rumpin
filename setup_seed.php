<?php
// ============================================================
//  setup_seed.php — Import menu lama ke database
//  PENTING: Hapus file ini setelah berhasil dijalankan!
//  Akses: http://localhost/dapur-rumpin/setup_seed.php
// ============================================================
require_once 'config/db.php';

// Cek apakah sudah pernah dijalankan
$sudahAda = $pdo->query("SELECT COUNT(*) FROM menu")->fetchColumn();
if ($sudahAda > 0) {
    die('<p style="font-family:sans-serif;color:orange;">⚠️ Database sudah berisi ' . $sudahAda . ' menu. Seed tidak dijalankan ulang. <a href="index.php">Kembali</a></p>');
}

$menus = [
  // MAKANAN
  ['Nasi Timbel Sunda',      'makanan', 42000, 'Paket nasi timbel dengan lauk ayam goreng, sambal lalapan dan tahu tempe khas Sunda. Disajikan dalam bungkusan daun pisang yang harum dan menggugah selera.',                         'Favorit',     'img/Nasi timbel Sunda add.webp'],
  ['Nasi Goreng Sereh',      'makanan', 24000, 'Nama lain Lemongrass Fried Rice, olahan nasi goreng spesial dengan serai yang harum dan menggugah selera. Menu andalan Dapur Rumpin yang paling banyak dipesan.',                      'Favorit #1',  'img/NASI GORENG SEREH add.webp'],
  ['Nasi Pecel Jowo',        'makanan', 38000, 'Sajian khas Jawa dengan paket nasi lauk ayam dan sayuran masak saus kacang yang gurih.',                                                                                               'Khas Jawa',   'img/Nasi Pecel Jowo add.webp'],
  ['Gurame Asam Manis',      'makanan', 98000, 'Olahan ikan gurame tawar asli Indonesia dengan sauce asam manis yang khas. Terdapat juga varian Bakar Madu dan Goreng sesuai selera Anda.',                                           'Premium',     'img/Gurame Asam Manis add.webp'],
  ['Gurame Bakar Madu',      'makanan', 98000, 'Ikan gurame pilihan dibakar dengan olesan madu dan rempah spesial hingga harum kecoklatan.',                                                                                          'Premium',     'img/Gurame Asam Manis add.webp'],
  ['Gurame Goreng',          'makanan', 94000, 'Ikan gurame segar digoreng garing hingga renyah di luar namun tetap lembut di dalam.',                                                                                                'Premium',     'img/Gurame Asam Manis add.webp'],
  ['Udang Asam Manis',       'makanan', 30000, 'Olahan udang dengan sauce asam manis yang memanjakan lidah. Terdapat varian goreng tepung.',                                                                                          'Seafood',     'img/Udang Asam Manis.webp'],
  ['Cumi Asam Manis',        'makanan', 26000, 'Olahan seafood cumi dengan sauce asam manis. Ada varian goreng tepung.',                                                                                                              'Seafood',     'img/Cumi asam manis add.webp'],
  ['Mie Goreng GTR',         'makanan', 22000, 'Sajian mie goreng spesial dengan bumbu khas Dapur Rumpin, lezat dan mengenyangkan.',                                                                                                  'Favorit',     'img/Mie Goreng GTR add.webp'],
  ['Pecak Ikan Mas',         'makanan', 26000, 'Sajian khas olahan ikan Mas dengan bumbu pecak yang kaya rempah dan aromatik.',                                                                                                       'Tradisional', 'img/Pecak Ikan Mas add.webp'],
  ['Nila Bakar + Sambal',    'makanan', 25000, 'Hidangan ikan bakar dengan nila pilihan dan sambal segar. Ada varian goreng juga.',                                                                                                   'Bakar',       'img/Nila bakar+sambal add.webp'],
  ['Nila Goreng',            'makanan', 25000, 'Ikan nila segar digoreng hingga garing dengan bumbu rempah pilihan yang gurih.',                                                                                                      'Gurih',       'img/Nila bakar+sambal add.webp'],
  ['Pecak Nila',             'makanan', 27000, 'Ikan nila pilihan dimasak dengan bumbu pecak khas yang kaya rempah dan pedas.',                                                                                                       'Pedas',       'img/Pecak Ikan Mas add.webp'],
  ['Sayur Asem',             'makanan', 12000, 'Khas Indonesia banget. Dengan variant macam sayur seperti jagung manis, asam, melinjo dan lainnya.',                                                                                  'Sehat',       'img/Sayur Asam~Khas Indonesia banget,dengan varian macam sayur seperti jagung manis,asam,melinjo dl.webp'],
  ['Pecel Sayur',            'makanan', 10000, 'Varian sayuran rebus khas Jawa dengan sambal bumbu kacang yang gurih dan lezat.',                                                                                                     'Vegetarian',  'img/Pecel Sayur add.webp'],
  ['Karedok',                'makanan', 10000, 'Salad sayuran mentah khas Sunda dengan bumbu kacang segar yang pedas dan segar.',                                                                                                     'Segar',       'img/Karedok add.webp'],
  ['Ca Tauge',               'makanan', 10000, 'Tauge segar ditumis dengan bumbu bawang dan cabai, sederhana namun lezat.',                                                                                                           'Sederhana',   'img/Ca Tauge add.webp'],
  ['Ca Kangkung',            'makanan', 10000, 'Kangkung segar ditumis dengan bumbu terasi dan cabai, renyah dan menggugah selera.',                                                                                                  'Sederhana',   'img/Ca Kangkung add.webp'],
  ['Sambal',                 'makanan',  5000, 'Pilihan sambal segar berbagai varian: tomat, dadak, terasi, dan kecap untuk pelengkap hidangan.',                                                                                    'Pedas',       'img/sambal.webp'],
  ['Tahu & Tempe Goreng',    'makanan',  6000, 'Tahu dan tempe pilihan digoreng garing dengan bumbu sederhana, cocok sebagai lauk pelengkap.',                                                                                       'Sederhana',   'img/Tahu baso add.webp'],
  ['Chicken Teriyaki',       'makanan', 26000, 'Nasi dengan topping ayam fillet dengan sauce teriyaki manis dan gurih yang lezat.',                                                                                                   'Ricebowl',    'img/Ricebowl chicken teriyaki add.webp'],
  ['Chicken Blackpepper',    'makanan', 26000, 'Nasi dengan topping ayam fillet sauce blackpepper spesial yang pedas dan gurih.',                                                                                                     'Ricebowl',    'img/Ricebowl chicken blackpaper add.webp'],
  ['Korean Spicy Chicken',   'makanan', 26000, 'Nasi dengan topping olahan daging ayam slice sauce pedas ala Korean yang menggugah selera.',                                                                                          'Ricebowl',    'img/Ricebowl Korean spicy chicken add.webp'],
  ['Cumi Balado',            'makanan', 26000, 'Nasi dengan topping baby cumi masak sauce balado pedas yang menggugah selera.',                                                                                                       'Ricebowl',    'img/Ricebowl cumi balado add.webp'],
  // CEMILAN
  ['Risol Mayo',             'cemilan', 14000, 'Risoles goreng dengan isian telur dengan mayonaise di dalamnya, crispy dan creamy.',                                                                                                  'Cemilan',     'img/Risol Mayo add.webp'],
  ['French Fries',           'cemilan', 10000, 'Snack dengan bahan dasar kentang yang diiris tipis dan digoreng crunchy sempurna.',                                                                                                   'Cemilan',     'img/French fries add.webp'],
  ['Potato Wedges',          'cemilan', 17000, 'Kentang potong baji digoreng atau dipanggang, pas disajikan dengan saus spesial.',                                                                                                    'Cemilan',     'img/Potato wedges add.webp'],
  ['Rujak Cireng',           'cemilan', 10000, 'Jajanan aci digoreng kering dengan sauce rujak untuk cocolan yang pedas dan segar.',                                                                                                  'Cemilan',     'img/Rujak Cireng add.webp'],
  ['Tahu Baso',              'cemilan', 10000, 'Cemilan enak khas Semarang – tahu isi baso yang digoreng garing dan lezat.',                                                                                                          'Cemilan',     'img/Tahu baso add.webp'],
  // MINUMAN
  ['Iced Lhcyee Tea',        'minuman', 15000, 'Sajian varian minuman segar dari teh dan leci yang menyegarkan di siang hari.',                                                                                                       'Segar',       'img/Iced lhcyee tea add.webp'],
  ['Iced Green Tea',         'minuman', 14000, 'Minuman dari teh hijau yang segar dengan tambahan creamer yang creamy.',                                                                                                              'Segar',       'img/Iced green tea add.webp'],
  ['Es Kuwut Melon',         'minuman', 15000, 'Minuman segar favorit pelanggan dari varian buah melon dan timun yang diserut, sangat menyegarkan di siang hari.',                                                                    'Favorit #1',  'img/Es kuwut melon add.webp'],
  ['Iced Lemon Tea',         'minuman', 10000, 'Minuman teh dingin dengan tambahan lemon segar yang menyegarkan dan ringan.',                                                                                                         'Segar',       'img/Iced lemon tea add.webp'],
  ['Lemongrass Squash',      'minuman', 17000, 'Minuman mix antara serai dan lemon ditambah kesegaran soda yang menyegarkan.',                                                                                                        'Soda',        'img/Lemongrass squash  add.webp'],
  ['Strawberry Squash',      'minuman', 18000, 'Minuman dominan strawberry ditambah dengan kesegaran lemon dan soda.',                                                                                                                'Soda',        'img/Strawberry squash  add.webp'],
  ['Juice Melon',            'minuman', 12000, 'Kesegaran buah melon sangat terasa saat penghilang dahaga yang menyehatkan.',                                                                                                         'Jus',         'img/Juices Melon  add.webp'],
  ['Juice Tomat',            'minuman', 12000, 'Minuman segar dari tomat asli yang menyehatkan dan menyegarkan alami.',                                                                                                               'Jus',         'img/Juices Tomat addr.webp'],
  ['Jus Alpukat',            'minuman', 16000, 'Buah favorit juices dengan lelehan cream coklat yang lezat dan kaya nutrisi.',                                                                                                        'Jus',         'img/Juices Alpukat  add.webp'],
  ['Juice Orange',           'minuman', 12000, 'Dengan jeruk pilihan dan segar yang banyak mengandung vitamin C.',                                                                                                                    'Jus',         'img/Juices Orange add.webp'],
  ['Milkshake Strawberry',   'minuman', 18000, 'Minuman shake susu dan ice cream dengan rasa strawberry, extra topping ice cream strawberry.',                                                                                        'Milkshake',   'img/Milkshake Strawberry add.webp'],
  ['Milkshake Chocolate',    'minuman', 18000, 'Minuman shake susu dan ice cream dengan rasa coklat, extra topping ice cream coklat.',                                                                                                'Milkshake',   'img/Milkshake chocolate top add.webp'],
  ['Milkshake Vanilla',      'minuman', 18000, 'Minuman shake susu dan ice cream dengan rasa vanilla yang lembut dan creamy.',                                                                                                        'Milkshake',   'img/Milkshake vanilla top add.webp'],
  ['Ice Cream',              'minuman', 12000, 'Ice cream berbagai macam rasa dengan topping buah segar yang menyegarkan dan lezat.',                                                                                                 'Dessert',     'img/Ice cream add.webp'],
];

$stmt = $pdo->prepare("
    INSERT INTO menu (nama, kategori, harga, deskripsi, badge, gambar, is_active)
    VALUES (?, ?, ?, ?, ?, ?, 1)
");

$berhasil = 0;
$gagal    = [];

foreach ($menus as $m) {
    try {
        $stmt->execute($m);
        $berhasil++;
    } catch (PDOException $e) {
        $gagal[] = $m[0] . ' – ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Seed Database – DapurRumpin</title>
  <style>
    body { font-family: sans-serif; max-width: 600px; margin: 60px auto; padding: 20px; }
    .ok  { color: #15803d; background: #dcfce7; padding: 16px; border-radius: 8px; }
    .err { color: #dc2626; background: #fee2e2; padding: 16px; border-radius: 8px; margin-top: 12px; }
    a    { color: #2d6a4f; font-weight: bold; }
  </style>
</head>
<body>
  <h2>🌱 Setup Seed Database</h2>
  <div class="ok">
    ✅ <strong><?= $berhasil ?> menu</strong> berhasil diimport ke database.
  </div>

  <?php if (!empty($gagal)): ?>
    <div class="err">
      <strong>⚠️ <?= count($gagal) ?> item gagal:</strong>
      <ul>
        <?php foreach ($gagal as $g): ?>
          <li><?= htmlspecialchars($g) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <p style="margin-top:20px;padding:12px;background:#fef9c3;border-radius:8px;">
    ⚠️ <strong>PENTING:</strong> Segera hapus file <code>setup_seed.php</code> ini dari server setelah selesai!
  </p>

  <p style="margin-top:16px;">
    <a href="index.php">← Kembali ke Website</a> &nbsp;|&nbsp;
    <a href="admin/menu_list.php">Lihat Data Menu di Admin →</a>
  </p>
</body>
</html>