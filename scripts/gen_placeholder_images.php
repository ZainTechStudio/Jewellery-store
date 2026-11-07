<?php
/**
 * One-off local script (not part of the Laravel app) used only to generate
 * placeholder image assets for the static image paths referenced by the
 * frontend (img/p1.jpg ... img/p8.jpg, img/hero-fallback.jpg) since no real
 * product photography was provided. Replace these with real photos in
 * public/img/ whenever available — nothing in the app depends on this
 * script at runtime.
 */

$targets = [
    'p1.jpg' => 'Solitaire Éclat Ring',
    'p2.jpg' => 'Champagne Halo Pendant',
    'p3.jpg' => 'Lumière Drop Earrings',
    'p4.jpg' => 'Aurelia Tennis Bracelet',
    'p5.jpg' => 'Emerald Royale Ring',
    'p6.jpg' => 'Pearl & Gold Heirloom Set',
    'hero-fallback.jpg' => 'AURELIA — Fine Jewellery',
];

$outDir = __DIR__ . '/../public/img';
if (!is_dir($outDir)) {
    mkdir($outDir, 0755, true);
}

foreach ($targets as $file => $label) {
    $w = ($file === 'hero-fallback.jpg') ? 1920 : 900;
    $h = ($file === 'hero-fallback.jpg') ? 1080 : 900;

    $img = imagecreatetruecolor($w, $h);
    $ink = imagecolorallocate($img, 10, 10, 12);
    $gold = imagecolorallocate($img, 212, 175, 55);
    $goldDim = imagecolorallocate($img, 90, 76, 30);

    imagefilledrectangle($img, 0, 0, $w, $h, $ink);

    // simple radial-ish glow using concentric rectangles
    for ($i = 0; $i < 40; $i++) {
        $alpha = 100 - (int) ($i * 2.2);
        $c = imagecolorallocatealpha($img, 212, 175, 55, max(0, min(127, 127 - $alpha)));
        $pad = $i * ($w / 90);
        imagefilledellipse($img, (int) ($w * 0.7), (int) ($h * 0.4), (int) ($w * 0.9 - $pad), (int) ($w * 0.9 - $pad), $c);
    }

    // border frame
    imagerectangle($img, 20, 20, $w - 21, $h - 21, $goldDim);

    // centered label text
    $font = 5;
    $text = $label;
    $textW = imagefontwidth($font) * strlen($text);
    $x = (int) (($w - $textW) / 2);
    $y = (int) ($h / 2);
    imagestring($img, $font, $x, $y, $text, $gold);

    $sub = 'AURELIA — placeholder image';
    $subW = imagefontwidth(3) * strlen($sub);
    imagestring($img, 3, (int) (($w - $subW) / 2), $y + 26, $sub, $goldDim);

    imagejpeg($img, $outDir . '/' . $file, 82);
    imagedestroy($img);
    echo "generated {$file}\n";
}
