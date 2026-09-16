<?php
/**
 * ไอคอนเส้นมินิมอล (stroke) — ใช้ทั้งหน้าเว็บและแอดมิน
 */
function hug_icon(string $name, int $size = 20): string
{
    $paths = [
        'heart' => '<path d="M12 20.5 4.2 12.9a4.6 4.6 0 0 1 6.5-6.5l1.3 1.3 1.3-1.3a4.6 4.6 0 0 1 6.5 6.5z"/>',
        'home' => '<path d="M4 11.5 12 5l8 6.5V19a1.5 1.5 0 0 1-1.5 1.5H15v-5.5H9V20.5H5.5A1.5 1.5 0 0 1 4 19z"/>',
        'elder' => '<circle cx="12" cy="7.5" r="3"/><path d="M6 19.5a6 6 0 0 1 12 0"/><path d="M16 8.5a2.5 2.5 0 0 1 0 4.5"/>',
        'user' => '<circle cx="12" cy="8" r="3.2"/><path d="M5.5 19.5a6.5 6.5 0 0 1 13 0"/>',
        'users' => '<circle cx="9" cy="8.5" r="2.8"/><path d="M3.5 19a5.5 5.5 0 0 1 11 0"/><path d="M16.2 7.2a2.5 2.5 0 0 1 0 4.6"/><path d="M19.5 19a4 4 0 0 0-3.5-3.9"/>',
        'medical' => '<path d="M12 4v16M4 12h16"/><circle cx="12" cy="12" r="9"/>',
        'service' => '<path d="M12 20.5 4.2 12.9a4.6 4.6 0 0 1 6.5-6.5l1.3 1.3 1.3-1.3a4.6 4.6 0 0 1 6.5 6.5z"/><path d="M3.5 12.5h4l1.5-3 2 6 2-4 1.5 1h6"/>',
        'calendar' => '<rect x="4" y="5.5" width="16" height="15" rx="2"/><path d="M8 4v3M16 4v3M4 10.5h16"/>',
        'news' => '<path d="M6 5.5h9a3 3 0 0 1 3 3v11H6z"/><path d="M6 5.5v14"/><path d="M9.5 11h5M9.5 14.5h5M9.5 18h3"/>',
        'image' => '<rect x="4" y="5" width="16" height="14" rx="2"/><circle cx="9" cy="10" r="1.5"/><path d="m5 17 5-5 3 3 2.5-2.5L19 17"/>',
        'chart' => '<path d="M5 19V9M10 19V5M15 19v-7M20 19V11"/>',
        'settings' => '<circle cx="12" cy="12" r="2.8"/><path d="M12 3v2M12 19v2M3 12h2M19 12h2M5.6 5.6l1.4 1.4M17 17l1.4 1.4M18.4 5.6 17 7M7 17l-1.4 1.4"/>',
        'check' => '<circle cx="12" cy="12" r="9"/><path d="M8 12.2 10.8 15 16 9.5"/>',
        'edit' => '<path d="M12 20h8M14.5 5.5l4 4L8 20l-4 1 1-4z"/>',
        'trash' => '<path d="M5 7h14M9 7V5.5h6V7M7.5 7l.8 11h7.4l.8-11"/>',
        'pin' => '<path d="M12 21s6-4.5 6-10a6 6 0 0 0-12 0c0 5.5 6 10 6 10z"/><circle cx="12" cy="11" r="2"/>',
        'phone' => '<path d="M6.5 4.5h3l1.5 3.5-2 1.5a11 11 0 0 0 5 5l1.5-2 3.5 1.5v3a1.5 1.5 0 0 1-1.5 1.5A13 13 0 0 1 5 6a1.5 1.5 0 0 1 1.5-1.5z"/>',
        'mail' => '<rect x="4" y="6" width="16" height="12" rx="2"/><path d="m4 8 8 5.5L20 8"/>',
        'user-badge' => '<circle cx="12" cy="8" r="3"/><path d="M6 18a6 6 0 0 1 12 0"/><rect x="15" y="3" width="6" height="6" rx="1.5"/>',
        'menu' => '<path d="M4 7h16M4 12h16M4 17h16"/>',
    ];

    $inner = $paths[$name] ?? $paths['heart'];

    return sprintf(
        '<svg class="icon-svg" viewBox="0 0 24 24" width="%d" height="%d" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">%s</svg>',
        $size,
        $size,
        $inner
    );
}
