<?php

/** ตรวจ MIME ของไฟล์อัปโหลด (รองรับ Windows ที่ไม่มี mime_content_type) */
function hug_detect_upload_mime(string $tmpPath, string $originalName = '', string $clientType = ''): string
{
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo) {
            $mime = finfo_file($finfo, $tmpPath);
            finfo_close($finfo);
            if (is_string($mime) && $mime !== '' && $mime !== 'application/octet-stream') {
                return $mime;
            }
        }
    }

    if (function_exists('mime_content_type')) {
        $mime = mime_content_type($tmpPath);
        if (is_string($mime) && $mime !== '') {
            return $mime;
        }
    }

    $imageInfo = @getimagesize($tmpPath);
    if (is_array($imageInfo) && !empty($imageInfo['mime'])) {
        return (string) $imageInfo['mime'];
    }

    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $byExt = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'webp' => 'image/webp',
    ];
    if (isset($byExt[$ext])) {
        return $byExt[$ext];
    }

    return $clientType;
}

/** @return list<string> */
function hug_upload_subdirs(): array
{
    return ['gallery', 'news', 'residents'];
}

function hug_upload_dir(string $subdir): string
{
    $subdir = preg_replace('/[^a-z0-9_-]/', '', strtolower($subdir)) ?: 'gallery';
    $base = dirname(__DIR__) . '/assets/uploads/' . $subdir;
    if (!is_dir($base)) {
        mkdir($base, 0755, true);
    }

    return $base;
}

/**
 * บันทึกรูปจาก $_FILES — คืน path สำหรับเว็บ (/assets/uploads/...) หรือ null ถ้าไม่ได้เลือกไฟล์
 *
 * @param array<string,mixed>|null $file
 */
function hug_save_image_upload(?array $file, string $subdir, array &$errors): ?string
{
    if ($file === null || empty($file['name'])) {
        return null;
    }

    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        $errors[] = 'อัปโหลดไฟล์ไม่สำเร็จ';

        return null;
    }

    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $mime = hug_detect_upload_mime($file['tmp_name'], $file['name'] ?? '', $file['type'] ?? '');
    if (!isset($allowed[$mime])) {
        $errors[] = 'รองรับเฉพาะ JPG, PNG, WEBP';

        return null;
    }
    if (($file['size'] ?? 0) > 5 * 1024 * 1024) {
        $errors[] = 'ไฟล์ใหญ่เกิน 5 MB';

        return null;
    }

    $name = bin2hex(random_bytes(8)) . '.' . $allowed[$mime];
    $dest = hug_upload_dir($subdir) . '/' . $name;
    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        $errors[] = 'บันทึกไฟล์ไม่สำเร็จ';

        return null;
    }

    return '/assets/uploads/' . preg_replace('/[^a-z0-9_-]/', '', strtolower($subdir)) . '/' . $name;
}

/** รวม path จากอัปโหลด / ช่องข้อความ / ค่าเดิมตอนแก้ไข */
function hug_resolve_image_path(?array $file, string $subdir, string $textPath, ?string $existing, array &$errors): string
{
    $uploaded = hug_save_image_upload($file, $subdir, $errors);
    if ($uploaded !== null) {
        return $uploaded;
    }
    $textPath = trim($textPath);
    if ($textPath !== '') {
        return $textPath;
    }

    return trim($existing ?? '');
}
