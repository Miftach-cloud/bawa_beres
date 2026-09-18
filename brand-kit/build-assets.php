<?php

declare(strict_types=1);

$sourcePath = __DIR__.'/source/cover-background.png';
$outputPath = __DIR__.'/exports';

if (! is_file($sourcePath)) {
    fwrite(STDERR, "Missing source image: {$sourcePath}\n");
    exit(1);
}

if (! is_dir($outputPath) && ! mkdir($outputPath, 0777, true) && ! is_dir($outputPath)) {
    fwrite(STDERR, "Unable to create export directory.\n");
    exit(1);
}

$fontRegular = 'C:/Windows/Fonts/segoeui.ttf';
$fontBold = 'C:/Windows/Fonts/seguisb.ttf';
$fontBlack = 'C:/Windows/Fonts/seguibl.ttf';
$source = imagecreatefrompng($sourcePath);

function canvas(int $width, int $height, string $hex): GdImage
{
    $image = imagecreatetruecolor($width, $height);
    imagefill($image, 0, 0, color($image, $hex));

    return $image;
}

function color(GdImage $image, string $hex, int $alpha = 0): int
{
    $hex = ltrim($hex, '#');

    return imagecolorallocatealpha(
        $image,
        hexdec(substr($hex, 0, 2)),
        hexdec(substr($hex, 2, 2)),
        hexdec(substr($hex, 4, 2)),
        $alpha,
    );
}

function coverImage(GdImage $source, int $width, int $height): GdImage
{
    $target = canvas($width, $height, '#FFFBEB');
    $sourceWidth = imagesx($source);
    $sourceHeight = imagesy($source);
    $scale = max($width / $sourceWidth, $height / $sourceHeight);
    $scaledWidth = (int) ceil($sourceWidth * $scale);
    $scaledHeight = (int) ceil($sourceHeight * $scale);
    $x = (int) (($width - $scaledWidth) / 2);
    $y = (int) (($height - $scaledHeight) / 2);
    imagecopyresampled($target, $source, $x, $y, 0, 0, $scaledWidth, $scaledHeight, $sourceWidth, $sourceHeight);

    return $target;
}

function roundedRectangle(GdImage $image, int $x1, int $y1, int $x2, int $y2, int $radius, int $color): void
{
    imagefilledrectangle($image, $x1 + $radius, $y1, $x2 - $radius, $y2, $color);
    imagefilledrectangle($image, $x1, $y1 + $radius, $x2, $y2 - $radius, $color);
    imagefilledellipse($image, $x1 + $radius, $y1 + $radius, $radius * 2, $radius * 2, $color);
    imagefilledellipse($image, $x2 - $radius, $y1 + $radius, $radius * 2, $radius * 2, $color);
    imagefilledellipse($image, $x1 + $radius, $y2 - $radius, $radius * 2, $radius * 2, $color);
    imagefilledellipse($image, $x2 - $radius, $y2 - $radius, $radius * 2, $radius * 2, $color);
}

function brandMark(GdImage $image, int $x, int $y, int $size, string $fontBlack): void
{
    $amber = color($image, '#F59E0B');
    $slate = color($image, '#0F172A');
    $white = color($image, '#FFFFFF');
    roundedRectangle($image, $x, $y, $x + $size, $y + $size, (int) ($size * .22), $slate);
    imagettftext($image, (int) ($size * .43), 0, $x + (int) ($size * .12), $y + (int) ($size * .67), $amber, $fontBlack, 'BB');
    imagefilledellipse($image, $x + (int) ($size * .75), $y + (int) ($size * .24), (int) ($size * .18), (int) ($size * .18), $white);
    imagefilledellipse($image, $x + (int) ($size * .75), $y + (int) ($size * .24), (int) ($size * .07), (int) ($size * .07), $amber);
}

function brandName(GdImage $image, int $x, int $y, int $size, string $fontBlack, bool $light = false): void
{
    $first = $light ? '#FFFFFF' : '#0F172A';
    imagettftext($image, $size, 0, $x, $y, color($image, $first), $fontBlack, 'Bawa');
    $box = imagettfbbox($size, 0, $fontBlack, 'Bawa');
    imagettftext($image, $size, 0, $x + ($box[2] - $box[0]), $y, color($image, '#F59E0B'), $fontBlack, 'Beres');
}

function exportPng(GdImage $image, string $path): void
{
    imagepng($image, $path, 9);
    imagedestroy($image);
}

// Social profile — safe circular-crop composition.
$profile = canvas(1080, 1080, '#F59E0B');
imagefilledellipse($profile, 540, 540, 920, 920, color($profile, '#FFFBEB'));
brandMark($profile, 260, 170, 560, $fontBlack);
brandName($profile, 270, 865, 85, $fontBlack);
exportPng($profile, $outputPath.'/profile-1080.png');

/** @param array{width:int,height:int,titleSize:int,bodySize:int,markSize:int,x:int} $spec */
function createCover(GdImage $source, array $spec, string $path, string $fontRegular, string $fontBold, string $fontBlack): void
{
    $image = coverImage($source, $spec['width'], $spec['height']);
    $overlay = imagecolorallocatealpha($image, 15, 23, 42, 18);
    imagefilledrectangle($image, 0, 0, (int) ($spec['width'] * .56), $spec['height'], $overlay);
    brandMark($image, $spec['x'], (int) ($spec['height'] * .13), $spec['markSize'], $fontBlack);
    brandName($image, $spec['x'] + $spec['markSize'] + 24, (int) ($spec['height'] * .13) + (int) ($spec['markSize'] * .7), (int) ($spec['titleSize'] * .8), $fontBlack, true);
    imagettftext($image, $spec['titleSize'], 0, $spec['x'], (int) ($spec['height'] * .63), color($image, '#FFFFFF'), $fontBlack, 'Pindahan jadi beres.');
    imagettftext($image, $spec['bodySize'], 0, $spec['x'], (int) ($spec['height'] * .76), color($image, '#E2E8F0'), $fontBold, 'Moving • Storage • Delivery | Malang Raya');
    exportPng($image, $path);
}

createCover($source, ['width' => 1640, 'height' => 624, 'titleSize' => 62, 'bodySize' => 28, 'markSize' => 104, 'x' => 110], $outputPath.'/facebook-cover-1640x624.png', $fontRegular, $fontBold, $fontBlack);
createCover($source, ['width' => 1584, 'height' => 396, 'titleSize' => 44, 'bodySize' => 22, 'markSize' => 72, 'x' => 90], $outputPath.'/linkedin-cover-1584x396.png', $fontRegular, $fontBold, $fontBlack);

$post = coverImage($source, 1080, 1080);
imagefilledrectangle($post, 0, 0, 1080, 1080, imagecolorallocatealpha($post, 15, 23, 42, 42));
roundedRectangle($post, 64, 64, 1016, 1016, 40, imagecolorallocatealpha($post, 255, 251, 235, 12));
brandMark($post, 110, 110, 130, $fontBlack);
brandName($post, 265, 202, 52, $fontBlack);
imagettftext($post, 66, 0, 110, 735, color($post, '#0F172A'), $fontBlack, 'Bawa santai.');
imagettftext($post, 62, 0, 110, 820, color($post, '#D97706'), $fontBlack, 'Kami yang bereskan.');
imagettftext($post, 29, 0, 112, 900, color($post, '#334155'), $fontBold, 'Pindahan • Penitipan • Pengiriman');
exportPng($post, $outputPath.'/instagram-post-1080.png');

$story = coverImage($source, 1080, 1920);
imagefilledrectangle($story, 0, 0, 1080, 1920, imagecolorallocatealpha($story, 15, 23, 42, 38));
brandMark($story, 90, 100, 140, $fontBlack);
brandName($story, 260, 202, 56, $fontBlack);
imagettftext($story, 58, 0, 90, 1320, color($story, '#FFFFFF'), $fontBlack, 'Pindahan tanpa ribet.');
imagettftext($story, 34, 0, 94, 1400, color($story, '#E2E8F0'), $fontBold, 'Barang tercatat. Proses transparan.');
roundedRectangle($story, 90, 1480, 710, 1585, 30, color($story, '#F59E0B'));
imagettftext($story, 27, 0, 142, 1550, color($story, '#0F172A'), $fontBlack, 'KONSULTASI SEKARANG');
exportPng($story, $outputPath.'/instagram-story-1080x1920.png');

imagedestroy($source);

fwrite(STDOUT, "Brand assets exported to {$outputPath}\n");
